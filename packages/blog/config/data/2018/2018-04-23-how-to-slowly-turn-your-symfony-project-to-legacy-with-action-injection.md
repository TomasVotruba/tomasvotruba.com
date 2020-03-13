---
id: 94
title: "How to Slowly Turn your Symfony Project to Legacy with Action Injection"
perex: |
    The other day I saw the question on Reddit about [Symfony's controller action dependency injection](https://www.reddit.com/r/PHP/comments/8dw8x5/symfonys_controller_action_dependency_injection/). More people around me are hyped about [this new feature in Symfony 3.3](https://symfony.com/doc/current/service_container/3.3-di-changes.html#controllers-are-registered-as-services) that allows to autowire services via action argument typehints. It's new, it's cool and no one has a bad experience with it. The ideal candidate for any code you write today.
    <br><br>
    Since [Nette](https://forum.nette.org/en/19365-nette-framework-2-1-0-finally-released) and [Laravel introduced](https://mattstauffer.com/blog/laravel-5.0-method-injection/) a similar feature in 2014, there are empirical data that we learn from.
    <br><br>
    **Today I'll share the experience I have from consulting few Nette applications with dangerous overuse of this pattern and how this one thing turned the code to complete mess.**

tweet: "New Post on My Blog: How Quickly Turn #Symfony Project to Legacy With Action Injection #adr #invokable #methodinjection #laravel"
tweet_image: "/assets/images/posts/2018/action-injection/everywhere.jpg"
---

*Disclaimer: this post is not about Symfony, nor critics of its feature. It's rather about teaching, thinking about knowledge embodied in the code, an aware approach of critical thinking to information from authorities.*

What is wrong with this code?

```php
class SomeService extends SomeAbstractParentService
{
    public function someMethod(SomeArgument $someArgument, SomeOtherService $someOtherService)
    {
        return $someOtherService->process($someArgument);
    }
}
```

It's not unreal that this code will appear in your project in next 2 years, if you start using action injection. But we'll get to that later, let's start from the beginning.

## Welcome Action Injection

Since Symfony 3.3 there is [a new feature](https://github.com/symfony/symfony/pull/21771) that allows injecting services to controller actions. It's important to this post, that Symfony [documentation includes a warning](https://symfony.com/doc/current/service_container/3.3-di-changes.html#controllers-are-registered-as-services): "This is only possible in a controller, and your controller service must be tagged with `controller.service_arguments` to make it happen."

### Wait, Wait... What is this Feature Again?

Oh, sorry. In case you don't know what I'm talking about, here is a little example. If you do, skip right to [the pitfall of such approach](#injection-everywhere) below.

If not, let's look at this example. This is the most simple and clear way to register controller as services:

```php
<?php

# app/Controller/SomeController.php

namespace App\Controller;

use App\Model\SomeService;

final class SomeController
{
    /**
     * @var SomeService
     */
    private $someService;

    public function __construct(SomeService $someService)
    {
        $this->someService = $someService;
    }

    public function someAction()
    {
        $someData = $this->someService->getSomeData();
        // ...
    }
}
```

with basic PSR-4 autodiscovery registration:

```yaml
# app/config/services.yml
services:
    App\:
        resource: '../'

    # include all controllers and model services
```

The *argument autowire* (also called *method injection* or *action injection*) will save us some writing.

As the name suggests, dependencies won't be passed by a constructor, as it's common in every service, but via method - the action method!

```diff
 # app/Controller/SomeController.php

 namespace App\Controller

 use App\Model\SomeService;

 final class SomeController
{
-    /**
-     * @var SomeService
-     */
-    private $someService;
-
-    public function __construct(SomeService $someService)
-    {
-        $this->someService = $someService;
-    }

-    public function someAction()
+    public function someAction(SomeService $someService)
     {
-        $someData = $this->someService->getSomeData();
+        $someData = $someService->getSomeData();
         // ...
     }
 }
```

On the other hand, service registration is now 3x more complex:

```diff
 # app/config/services.yml
 services:
-    App\:
+    App\Controller\:
-        resource: '../'
+        resource: '../Controller'
+        tags: ['controller.service_arguments']
+
+    App\Model\:
+        resource: '../Model'
```

### What are Propagated Advantages?

- less writing
- manual wiring of only used services - with no benchmark this has similar value as statements like "Symfony is 3x faster than Laravel, it's true"
- smaller controllers

### What are Already Known Disadvantages?

Paul M. Jones wrote that [“Action Injection” As A Code Smell](http://paul-m-jones.com/archives/6589). Why?

<blockquote class="blockquote">
"The fact that your controller has so many dependencies, used only in some cases and not in others, <strong>should be an indicator that the class is doing too much</strong>. Indeed, it’s doing so much that you cannot call its action methods directly; you have to use the dependency injection container not only to build the controller object but also to invoke its action methods."
</blockquote>

And I agree. It's the same code smell as adding 10th action method to the `ProductController` that now has 300 lines. Maybe you should split it into 2 classes and add [sniff](https://github.com/object-calisthenics/phpcs-calisthenics-rules#7-keep-your-classes-small) to make sure this won't happen in production code ever again (because no-one else will do it better than continuous integration).

But that's just words and ideas, no legacy (yet).

What might really happen with *autowired arguments* approach?

## 1. Injection Everywhere

<img src="/assets/images/posts/2018/action-injection/everywhere.jpg" class="img-thumbnail">

## The Nette-Framework-Tried-It-For-You story

Nette "inject" feature released in 2014 in Nette 2.1 started very similarly. It has 2 ways to inject dependencies:

### A. `@inject` Annotatoin

```php
namespace App\Controller;

use App\Model\SomeService;

final class SomeController
{
    /**
     * @var SomeService
     * @inject
     */
    public $someService;
}
```

### B. `inject*()` Method

```php
namespace App\Controller;

use App\Model\SomeService;

final class SomeController
{
    /**
     * @var SomeService
     */
    private $someService;

    public function injectSomeService(SomeService $someService)
    {
        $this->someService = $someService;
    }
}
```

It also have to be activated in config manually with specific `tags: ['inject']` tag, as in Symfony:

```yaml
# app/config/services.neon
services:
    App\Controller\SomeController:
        tags: ['inject']

    - App\Model\SomeService
```

Can you see the difference to Symfony? Well, almost none. But so far so good.

Note to Nette programmers: [`@inject` is often a code smell and you should do it cleaner](/blog/2016/12/24/how-to-avoid-inject-thanks-to-decorator-feature-in-nette/)

## Inspire by (Good/Bad) Example

If you prepare some "dirty-hack-that-none-should-use" or even better "don't-ever-use-this-unless-you-know-why" and make it public, you can be sure people will ignore it and use it in a very creative way. Unless there is `new ForbiddenUseException` thrown.

This effect appeared in Nette many months before 2.1 even became stable and [method injection was born](https://forum.nette.org/cs/13084-presentery-property-lazy-autowire-na-steroidech#p93574) (many months before Nette 2.1 even became stable):

```php
final class SomeController
{
	public function someAction(SomeService $someService)
	{
		$someData = $someService->getSomeData();
		// ...
	}
}
```

So far so good, right?

## Use in Controllers, Nowhere Else!

Do you have children? If so, you know that "be careful with that fire" repeated 10 times in 60 seconds will mostly lead to the exact opposite. Human brain works on ["Neurons that Fire Together Wire Together"](https://www.youtube.com/watch?v=o9K6GDBnByk) principle - so the final version can sound like "fire".

Programmers use the feature you provided. They don't know what you wrote in that single post 2 years ago, nor explore documentation for any reference they found. Sorry jako.

### Property/Method Injection in all Services

Back to our story - it didn't take long to [new idea appeared on Nette forum](https://forum.nette.org/cs/17817-jak-dostat-do-basecontrol-sluzbu-aniz-by-se-ji-museli-potomci-zabyvat#p125658) (Czech only): "I have 6 methods in `SomeService`, why should I inject all dependencies every time one public method is called? I want to use inject there as well, it's shorter and faster" This is the same argument to use *action injection* in controllers, remember?

<blockquote class="blockquote text-center">
    "Where is no exception, there is a way."
</blockquote>

It was super easy to turn it on:

```diff
 # app/config/services.neon
     services:
     App\Controller\SomeController:
         tags: ['inject']

     App\Model\SomeService:
+        tags: ['inject']
```

I confess [I liked this idea too](https://forum.nette.org/cs/17817-jak-dostat-do-basecontrol-sluzbu-aniz-by-se-ji-museli-potomci-zabyvat#p139678). But it's too much writing... how could we add to every service?

A `Extension` (~= `CompilerPass`) solved it:

```php
foreach ($this->getContainerBuilder() as $definition) {
    $definition->addTag('inject');
}
```

Now we can remove these annoying long constructors and use property/method injection everywhere. Be careful, [this visual debt](https://ocramius.github.io/blog/eliminating-visual-debt/) is different from [cognitive overload](https://blog.sonarsource.com/cognitive-complexity-because-testability-understandability).

Now our code looks like this:

```php
class SomeService
{
    /**
     * @inject
     */
    public $someOtherService;
}
```

or in Symfony

```php
namespace App\Model;

final class SomeService
{
    public function someMethod(SomeArgument $someArgument, SomeOtherService $someOtherService)
    {
        return $someOtherService->process($someArgument);
    }
}
```

**3 lines and documentation is ignored. Great job!**

To achieve similar functionality in Symfony, you'd probably have to override [this `CompilerPass`](https://github.com/symfony/symfony/pull/21771/files#diff-58d1479352b43b312746ea6ceb4ada96). I won't show you nor try on purpose, so I don't spread too much black magic here.

### Most People are to Lazy and Unskilled Write own CompilePasses

Let's say you're right and I live in micro-open-source cosmos, where people are too lazy not to do it. Also, it's possible, that framework checks the service against interface (`IPresenter`) or a class (`Controller`), that it really is a controller.

But it's still possible. How? Take 3 breaths to think about it, you'll find a way.

<br><br>

Yes, our favorite [composition pattern](https://ocramius.github.io/blog/when-to-declare-classes-final/).

```php
namespace App\Model;

final class SomeService extends SomeAbstractService
{
    public function someMethod(SomeArgument $someArgument, SomeOtherService $someOtherService)
    {
        return $someOtherService->process($someArgument);
    }
}
```

Do you know where this goes?

```php
use Symfony\..\Controller;

abstract SomeAbstractService extends Controller
{
}
```

Kaboom! Method injection works in `SomeService` and we bypassed the frameworks internals :).

### True Story

I must add, I'm not writing this post because I'm bored and need a topic to babble about. **This is true story, such code exists and it was so much WTF to me, that I don't want my deepest enemy to have to work on project like that**. And I see that Symfony framework is slowly heading a way that opened these doors.

It was (maybe still is) famous Czech project (can't tell you which one, but you know who you are ;)).

<blockquote class="blockquote text-center">
    Everything which is not forbidden is allowed.
</blockquote>

Now you know how to take advantage of framework architecture backdoor and save you lot of writing. At least in the present moment. And also how not to fall into this. It's up to you, what you like and what code you love to write.

## Is there a Way To Save Your Code From this Instant Retirement?

There are 2 ways ho to avoid this completely and still use your framework:

-
Paul M. Jones has written [many posts Action-Domain-Responder](http://paul-m-jones.com/archives/category/programming/adr) and even created a [micro-site devoted to ADR topic](http://pmjones.io/adr/).
- another approach is [RequestHandler](https://jenssegers.com/85/goodbye-controllers-hello-request-handlers)
- my favorite approach that [Symfony](https://symfony.com/doc/current/controller/service.html#invokable-controllers) and [Laravel](https://dyrynda.com.au/blog/single-action-controllers-in-laravel) support by default for a long time are **invokable controllers**, also called *single action controllers*

## What is Your Experience with Action Injects?

I really recommend checking the [Reddit thread](https://www.reddit.com/r/PHP/comments/8dw8x5/symfonys_controller_action_dependency_injection/), there are few experiences worth reading that will save your time and energy of personal research:

<blockquote class="blockquote">
I've abandoned this [action inject] approach because it makes it harder to differentiate request parameters from services. It also makes this method definition in most cases spread to multiple lines. And it makes it harder to inject non-autowirable services
    <footer class="blockquote-footer text-right">
        <a href="https://www.reddit.com/r/PHP/comments/8dw8x5/symfonys_controller_action_dependency_injection/dxsauf2/">gadelat</a>
    </footer>
</blockquote>

What inject approach do you prefer? What happy or WTF inject stories do *you* have? Let me know in the comments.

<br><br>

Happy Injecting!
