---
id: 43
layout: post
title: "Symbiotic Controller: Nette&nbsp;Presenter&nbsp;with&nbsp;Freedom"
perex: '''
    Symfony and Laravel allow decoupled controllers by default thanks to simple principle: <em>controller/presenter = callback</em>. No base class or interface is needed. 
    <br><br>
    People around me are already using single action presenters, but still depend on Nette. Why? Coupling of <code>IPresenter</code> in Application and Router. 
    <br><br>
    I think framework should help you and not limit you in a way how you write your code.
    <strong>Today we look how to make that happen even for Nette presenters and how to set them free</strong>.
'''
related_posts: [28]
---

## 3 Misconceptions First

When I talked about [single action or rather invokable presenters in Nette](https://www.facebook.com/pehapkari/videos/1285464581503349/) on 87. Posobota meetup in Prague, **people were talking about 3 missconceptions**. I'd like clarify them first.
 
### 1. Nette needs `IPresenter`

My first attempt decouple presenter from Nette [failed on `PresenterFactory`](https://github.com/nette/application/blob/0941b6b7023a43ddd0627ad5ac3ffba606709ef5/src/Application/PresenterFactory.php#L78-L79):

```php
// ...
if (!$reflection->implementsInterface(IPresenter::class)) {
    throw new InvalidPresenterException("Cannot load presenter '$name', class '$class' is not Nette\\Application\\IPresenter implementor.");
}
```

This took me few weeks to figure out because of coupling to latte, providers and layout autodiscovery. 
I needed to modify `PresenterFactory`, `Application->run()` and create own `PresneterRoute`.
   
**So yes, when you modify few places, you can use it without `IPresenter` interface.**


### 2. What about ajax? What about Components?

**If you use ajax and components, this approach is not probably for you application**. 

I thought it's impossible to use Nette without components as well, but [Ondrej Mirtes](https://ondrej.mirtes.cz) from Slevomat take me out of my misery: "We don't use components in Slevomat, just presenters." So feel free to ask him how they do it.

**You'll appreciate this approach in applications, where**:
 
- frontend is managed not by Nette ajax, but by **ReactJS, Angular or any other frontend framework**  
- **API, Rest or GraphQL** is the main entry point to the application
- your application is growing and you want to **avoid god classes like presenters with 10 actions methods** in next couple of years


### 3. There should be an Interface for that

Even when some people agreed with invokable/single action approach, they still missed some interface that would enforce a method. I must say `__invoke()` method seemed weird to me at first too.

**Give `invoke()` a try, it's Fine**

But I've learned what [`__invoke()` is](http://php.net/manual/en/language.oop5.magic.php#object.invoke) and that [Symfony](http://symfony.com/doc/current/controller/service.html#invokable-controllers) and [Laravel use](https://laravel.com/docs/5.4/controllers#single-action-controllers) it for years. 

Also, **using an interface would only create a new dependency** for something that is already used in specific way. Moreover for controller which [every](https://book.cakephp.org/2.0/en/controllers.html#controller-actions) [framework](http://www.yiiframework.com/doc-2.0/guide-structure-controllers.html#actions) [bend](https://book.cakephp.org/3.0/en/controllers.html) [to](https://laravel.com/docs/5.4/controllers#defining-controllers) [its](https://doc.nette.org/en/2.4/presenters#toc-processing-presenter-action) [own](http://symfony.com/doc/current/best_practices/controllers.html#what-does-the-controller-look-like) needs.
 
`__invoke()` is normal method, just like `__constructor()` is normal for passing dependencies nowadays.


## Inspiration from Symfony Community - Matthias Noback & Keving Dunglas

**Why Decouple Controller From Framework?**

If you look for reasons to decouple from framework, read [this 3 parts series: Framework Independent Controllers](https://php-and-symfony.matthiasnoback.nl/tags/controller/) by [Matthias Noback](https://matthiasnoback.nl) about independent controllers in Symfony.

**Why are Single Action Presenters Great for Growing Projects?**

Similar [package and post](https://dunglas.fr/2016/01/dunglasactionbundle-symfony-controllers-redesigned/) was made by Kevin Dunglas exactly 1,5 year ago. You'll find your answers there.

No more questions, right to the code.


## How Single Action Controller looks like?

The goal was:

- **don't depend on any interface or class**
- have **1 public method** `__invoke()`

Ideal code for Nette-agnostic presenter would look like this:

```php
namespace App\Presenter;

use Symplify\SymbioticController\Adapter\Nette\Template\TemplateRenderer;

final class StandalonePresenter
{
    /**
     * @var TemplateRenderer
     */
    private $templateRenderer;

    public function __construct(TemplateRenderer $templateRenderer)
    {
        $this->templateRenderer = $templateRenderer;
    }

    public function __invoke(): string
    {
        return $this->templateRenderer->renderFileWithParameters(
            __DIR__ . '/templates/Contact.latte'
        );
    }
}
```

Or if you use API and json:

```php
namespace App\Presenter;

use Nette\Application\Responses\JsonResponse;

final class ApiPresenter
{
    public function __invoke(): JsonResponse
    {
        return new JsonResponse('Hi!');
    }
}
```


### Clickable template paths as Positive Side-Effect

`Module:Presenter:template` => `__DIR__ . '/templates/template.latte`  

Instead of using magic notation, you can go right with absolute path for templates.

If this would be used by every controller and framework, there would be much lower entry barrier for front-end developers. Another new way to use your IDE the right-way:

<div class="text-center">
    <img src="/../../../../assets/images/posts/2017/symbiotic-controller/presenter.gif" class="img-thumbnail">
</div>


## And Router?

But how to register this presenter in router? Since the called action is now *not a method in a class* but *a class*, we cannot use common way to add route:

```php
# this won't work
$routes[] = new Route('/contact', 'Contact:default');
$routes[] = new Route('/contact', 'Contact:__invoke');
```

We need to use presenter as target:

```php
use App\Presenter\ContactPresenter; 

$routes[] = new Route('/contact', ContactPresenter::class);
```

But that won't work either as `Route` class requires `<presenter>:<method>` format for target.

### Presenter Route

To solve this, **we'll use custom Route that accepts Presenter class as argument**.

```php
# app/Router/RouterFactory.php

use App\Presenter\ContactPresenter;
use Symplify\SymbioticController\Adapter\Nette\Routing;

final class RouterFactory
{
    public function create(): RouteList
    {
        $routes = new RouteList;
        $routes[] = new PresenterRoute('/contact', ContactPresenter::class);
        $routes[] = new Route('<presenter>/<action>', 'Homepage:default');

        return $routes;
    }
}
```

It has 2 important tasks:
 
- **it validates that class has `__invoke()` method**
- you can **click right to the presenter class** in your IDE


## From Stringly Route to Strongly Route 

Same way you can use your IDE to open the template, you can use it to open presenter target.

From magic `Homepage:default` to clickable class: 

<div class="text-center">
    <img src="/../../../../assets/images/posts/2017/symbiotic-controller/router.gif" class="img-thumbnail">
</div>

## Do you want to try this?

You have 2 options: check [nette/sandbox based demo on Github](https://github.com/TomasVotruba/nette-single-action-presenter) or install to your application yourself:

## 3 Steps To Your First Framework Agnostic Presenter in Nette 

### 1. Install [Symplify\SymbioticController](https://github.com/Symplify/SymbioticController) package 

```yaml
composer require symplify/symbiotic-controller
```
  
### 2. Register Needed Extensions

```yaml
# app/config/config.neon

extensions:
    - Symplify\SymbioticController\Adapter\Nette\DI\SymbioticControllerExtension
    - Contributte\EventDispatcher\DI\EventDispatcherExtension
```

### 3. Create Your Presenter and Use It

That's all :)


## Takeaways

- Using framework agnostics controllers has its use cases. **The best are: API, backend-mostly and fast growing applications**. 
- Don't be afraid of `__invoke()`. It is your friend, mainly for future.
- **Use Strong types over String types and forget the magic**. 


### How do YOU approach using controller (in Nette)?

- Do you use components?
- Do you know why you depend on Nette?
- Do you try some other approach than traditional one?

Let me know in the comments. I always like to hear different opinions.
