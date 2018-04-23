---
id: 94
title: "How Quickly Turn Project to Legacy With Action Injection"
perex: |
    The other day I saw question on Reddit about [Symfony's controller action dependency injection](https://www.reddit.com/r/PHP/comments/8dw8x5/symfonys_controller_action_dependency_injection/). More people around me are hyped about [this new feature in Symfony 3.3](https://symfony.com/doc/current/service_container/3.3-di-changes.html#controllers-are-registered-as-services) that allows to autowire services via action argument typehints. It's new, it's cool and no one has bad experience with it. Ideal candidate for any code you write today.
    <br><br>
    Since [Nette](https://forum.nette.org/en/19365-nette-framework-2-1-0-finally-released) and [Laravel introduced](https://mattstauffer.com/blog/laravel-5.0-method-injection/) similar feature in 2014, there are empirical data that we learn from.
    <br><br>
    **Today I'll share experience I have from consulting few Nette applications with dangerous overuse of this pattern and how this one thing turned the code to complete mess.**
tweet: "New Post on My Blog: ..."
---

*Disclaimer: this post is not about Symfony, nor critics of it's feature. It's rather about teaching, thinking about knowledge embodied in the code, an aware approach of critical thinking to information from authorities.*  

What is wrong about this code?

```php
class SomeService extends SomeAbstractParentService
{
    public function someMethod(SomeArgument $someArgument, SomeService $someService)
    {
        return $someService->process($someArgument);
    }
}
```

It's not unreal that this code will appear in your project in next 2 years, if you start using action injection. But we'll get to that later, let's start from the beginning.

## Welcome Action Injection

Since Symfony 3.3 there is [new feature](https://github.com/symfony/symfony/pull/21771) that allows to inject services to controller actions. It's important to this post, that Symfony [documentation includes important warning](https://symfony.com/doc/current/service_container/3.3-di-changes.html#controllers-are-registered-as-services): "This is only possible in a controller, and your controller service must be tagged with `controller.service_arguments` to make it happen."   

### Wait, Wait... What is this Feature Again?

Oh, sorry. In case you don't know what I'm talking about, here is a little example. If you do, skip right to [the pitfall of such approach](#injection-everywhere) bellow. 

If not, let's look at this example. This is the most simple and clearway to register controller as services:

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

As the name suggest, dependencies won't be passed by constructor, as it's common in every service, but via method - the action method!

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
- manual wiring of only used services - with no benchmark this is has similar value as statements like "Symfony is 3x faster than Laravel, it's true"
- smaller controllers

### What are Already Known Disadvantages?

Paul M. Jones wrote that [“Action Injection” As A Code Smell](http://paul-m-jones.com/archives/6589). Why? 

<blockquote class="blockquote">
"The fact that your controller has so many dependencies, used only in some cases and not in others, <strong>should be an indicator that the class is doing too much</strong>. Indeed, it’s doing so much that you cannot call its action methods directly; you have to use the dependency injection container not only to build the controller object but also to invoke its action methods."
</blockquote>

And I agree. It's the same code smell as adding 10th action method to the `ProductController` that now has 300 lines. Maybe you should split it to 2 classes and add [sniff](https://github.com/object-calisthenics/phpcs-calisthenics-rules#7-keep-your-classes-small) to make sure this won't happen in production code ever again (because no-one else will do it better than continuous integration). 

But that's just words and ideas, nothing real, no legacy (yet).

What might really happen with *autowired arguments* approach?

## 1. Injection Everywhere

<img src="/assets/images/posts/2018/action-injection/everywhere.jpg" class="img-thumbnail">

## The Nette-Framework-Tried-It-For-You story

Nette "inject" feature released in 2014 in Nette 2.1 started very similar. It has 2 ways to inject dependencies:

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

If you prepare some "dirty-hack-that-none-should-use" or even better "don't-ever-use-this-unless-you-know-why" and make it public, you can be sure people will ignore it and use it a very creative way. Unless, there is `new ForbiddenUseException` thrown.  

This effect appeared in Nette many months before 2.1 even beame stable and [method injection was born](https://forum.nette.org/cs/13084-presentery-property-lazy-autowire-na-steroidech#p93574) (many months before Nette 2.1 even became stable):

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

Do you have children? If so, you know that "be careful with that fire" repeated 10 times in a 60 seconds will mostly lead to the exact opposite. Human brains works on ["Neurons that Fire Together Wire Together"](https://www.youtube.com/watch?v=o9K6GDBnByk) principle - so the final version can sounds like "fire".  

Programmers use the feature you provided. They don't now what you wrote in that single post 2 years ago, nor explore documentation for any reference they found. Sorry jako. 

### Property/Method Injection in all Services

Back to our story - it didn't take long to [new idea appeared on forum](https://forum.nette.org/cs/17817-jak-dostat-do-basecontrol-sluzbu-aniz-by-se-ji-museli-potomci-zabyvat#p125658) (Czech only): "I have 6 methods in `SomeService`, why should I inject all dependencies every time one public method is called? I want to use inject there as well, it's shorter and faster" This is the same argument to use *action injection* in controllers, remember?

<blockquote class="blockquote">
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

I confess, [I liked this idea too](https://forum.nette.org/cs/17817-jak-dostat-do-basecontrol-sluzbu-aniz-by-se-ji-museli-potomci-zabyvat#p139678). But it's too much writing... how could we add to every service? 

A `Extension` (~= `CompilerPass`) solved it:

```php
foreach ($this->getContainerBuilder() as $definition) {
    $definition->addTag('inject');
}
```

Now we can remove this annoying long constructors and use property/method injection everywhere. Be careful, there is different between [this visual debt](https://ocramius.github.io/blog/eliminating-visual-debt/) and cognitive overload.

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
    public function someMethod(SomeArgument $someArgument, SomeServie $someService)
    {
        return $someService->process($someArgument);
    }
}
```

3 lines and documentation is ignored. Great job!

To achieve similar thing in Symfony - you'd probably have to override [this `CompilerPass`](https://github.com/symfony/symfony/pull/21771/files#diff-58d1479352b43b312746ea6ceb4ada96). I won't show you nor try on purpose, so I don't spread too much black magic here.  

### Most People are to Lazy and Unskilled Write own CompilePasses

Let's say you're right and I live in micro-opne-source cosmos, where people are too lazy not to do it. Also, it's possible, that framework checks the service against interface (`IPresenter`) or a class (`Controller`), that it really is a controller.
 
But it's still possible. How? Take 3 breaths to think about it, you'll find a way.

<br><br>

Yes, our favorite [composition pattern](https://ocramius.github.io/blog/when-to-declare-classes-final/).

```php
namespace App\Model;

final class SomeService extends SomeAbstractService
{
    public function someMethod(SomeArgument $someArgument, SomeServie $someService)
    {
        return $someService->process($someArgument);
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

<blockquote class="blockquote">
    "Everything which is not forbidden is allowed."
</blockquote>

Know you know it all, how to take advantage of architecture backdoor and save you lot of writing. At least in present moment. Or not fall into this trap? It's up to you, what you like and what code you love to write.

## Is there a Way To Save Your Code From this Instant Retirement? 

There are 2 ways ho to avoid this completely and still use your framework:

- 
Paul M. Jones has written [many posts Action-Domain-Responder](http://paul-m-jones.com/archives/category/programming/adr) and even created a [micro-site devoted to ADR topic](http://pmjones.io/adr/).
- very similar approach is [RequestHandler](https://jenssegers.com/85/goodbye-controllers-hello-request-handlers)
- my favorite approach that [Symfony](https://symfony.com/doc/current/controller/service.html#invokable-controllers) and [Laravel](https://dyrynda.com.au/blog/single-action-controllers-in-laravel) supports by default for a long time are **invokable controllers**, also called *single action controllers*

## What is Your Experience with Action Injects?

I really recommend checking the [Reddit thread](https://www.reddit.com/r/PHP/comments/8dw8x5/symfonys_controller_action_dependency_injection/), there are few experiences worth reading: 

<blockquote class="blockquote">
I've abandoned this [action inject] approach, because it makes it harder to differentiate request parameters from services. It also makes this method definition in most cases spread to multiple lines. And it makes it harder to inject non-autowirable services
    <a href="https://www.reddit.com/r/PHP/comments/8dw8x5/symfonys_controller_action_dependency_injection/dxsauf2/">gadelat</a>
</blockquote>

What inject approach do you prefer? What stories you find along *your* inject way? Let me know in the comments.

<br><br>

Happy Injecting!
