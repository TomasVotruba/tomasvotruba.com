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

Do you think this is ok? Why or why not?

```php
class SomeService 
{
    public function someMethod(SomeArgument $someArgument, SomeServie $someService)
    {
        return $someService->process($someArgument);
    }
}
```


We'll get to that in the end of the show, but now, let's start from the beggining.

The Symfony feature is [officially described](https://symfony.com/doc/current/service_container/3.3-di-changes.html#controllers-are-registered-as-services) as "This is only possible in a controller, and your controller service must be tagged with `controller.service_arguments` to make it happen."

See [Symfony PR](https://github.com/symfony/symfony/pull/21771) for more technical details.  

## Wait wait, what is this feature again?

Oh, in case you don't know what I'm talking about, here is a little example. In the other case, skip right to [the polemic of such approach](#@todo link bellow).

This is the most simple and clearway to register controller as services:

```php
# app/Controller/SomeController.php

namespace App\Controller

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
        
        return new Response('someTemplate.twig', ['someData' => $someData]);
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

The feature I talk about is called *argument autowiring* or method injection. What will change?   

Well as the name suggest, dependencies won't be passed by constructor, as it's common in every service, but via method - the action method!


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
        
         return new Response('someTemplate.twig', ['someData' => $someData]);
     } 
 }
```

with new split and tag in our PSR-4 autodiscover registration: 

```diff
 # app/config/services.yml
 services:
-    App\:
+    App\Controller\:
-        resource: '../'
+        resource: '../Controller'
+
+    App\Model\:
+        resource: '../Model'
```


Propagated advantages are

- less writing
- manual wiring of only used serivvces - with no benchmark this is has simliar value as statements like "Symfony is 3x faster than Laravel, it's true"
- smaller controllers

On the other hand, I agree with Paul M. Jontes that [“Action Injection” As A Code Smell](http://paul-m-jones.com/archives/6589). Why?

-  "The fact that your controller has so many dependencies, used only in some cases and not in others, **should be an indicator that the class is doing too much**. Indeed, it’s doing so much that you cannot call its action methods directly; you have to use the dependency injection container not only to build the controller object but also to invoke its action methods."

It's the same code smell as adding 10th action method to the `ProductController`, maybe you should split it to 2 classes and add [sniff](https://github.com/object-calisthenics/phpcs-calisthenics-rules#7-keep-your-classes-small) to check that, because no-one else will do it better 

But that's just words and ideas, nothing real.

What might really happen with such approach?


## 1. Injectoin Everywhere

Nette "inject" feature started very similar. It had 2 modes that it worked on:

In pseudo-code:

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

or a bit more SOLID

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

and to be actived in config with `inject: true`

```yaml
# app/config/services.neon
services:
    App\Controller\SomeController:
        inject: true
        
    App\Model\SomeService:
```

Can you see the differnce? Well, almost none.

So far so good.


## Inspire by Example

If you prepare some "dirty-hack-that-none-should-use" and make it public, make sure people will use it very creative way (unless you throw an exception otherwise).  

Well, it didn't [take long to get such approach to method injeciton](https://forum.nette.org/cs/13084-presentery-property-lazy-autowire-na-steroidech#p93574) (many months before Nette 2.1 stable).

```php
final class SomeController
{
	public function someActoin(SomeService $someService)
	{
		$someData = $someService->getSomeData();
		// ...
	}
}
``` 


So far so good, right?


## Do not use everywhere!

If you have a children, you know that "be careful with that fire" will only lead to the opposite. In human brain, the shorter versoin sounds like "use everywhere".  

People ask and question the feature, because people don't now what you write in that single post or in documentation, they probably don't even know there is the post with "use only in controllers" request (or pray rather). 

Again, it didn't take long to hear such command and came with this: "why not use that everywhere, since there is 1 container anyway and it's shorter and faster? I have 6 methods in SomeService, why should I inject all dependencies everytime one method is called?" (the same argument behind controlers, remember?)

After all, it was super easy to turn it on:

```diff
 # app/config/services.neon
    services:
    App\Controller\SomeController:
          inject: true
        
     App\Model\SomeService:
+        inject: true
```


Since "where is not exception, there is a way"

I write about this missue everywhere: https://www.tomasvotruba.cz/blog/2016/12/24/how-to-avoid-inject-thanks-to-decorator-feature-in-nette/
But again, this beahvior should be embodies in the code by validation and exception, not in external post.


Hm, how to make it dfeault?

https://forum.nette.org/cs/17817-jak-dostat-do-basecontrol-sluzbu-aniz-by-se-ji-museli-potomci-zabyvat#p125658


So now we can remove this annojying "to many words" constuctor injection and use property (reps. method) injectoin everywhere - (eliminate [this visual debt](https://ocramius.github.io/blog/eliminating-visual-debt/)) - freedom!

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

## But, this is possible only in controllers

Ha, that is true, but still easily overrided by extension.
@todo check the controller action tag internalls and try to simulare it on non-controller service

But let's say people are not lazy enough to write own compilerpasses, that would enabled this feature on every service. And that framework check the service against interface or class, that it is really is a controller.
 
But it's still possible. How? Take 10 secs to think about it, you'll find a way.

<br><br>

Yes, our favorite [composition](https://ocramius.github.io/blog/when-to-declare-classes-final/) pattern.

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
use Symfony\Controller;

abstract SomeAbstractService extends Controller
{
} 
```

And voliá - method injection works in `SomeServivce` :).

I've seen very famous Czech project with this pattern (can't tell you which one, but you know who you are ;)).

Again, what is not forbidden, it is possible (@todo Churchil quote) 

## Is there a Way You Can't Fuck Up? 

@invokable - single actoin controller
    some positive sources
    - https://www.reddit.com/r/PHP/comments/8dw8x5/symfonys_controller_action_dependency_injection/dxsauf2/ 

@Action-Domain-Responder http://paul-m-jones.com/archives/5970


- Wisdom is characterized as ability to build on other's expericne

You've been informe. It's your call.

<br><br>

Happy Injecting! 