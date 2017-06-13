---
layout: post
title: "Symbitoic Controller: Nette Presenter with Freedom"
perex: '''
    Symfony and Laravel have decoupled controllers by default, thanks to simple principle of controller = callback. No required base class nor interface. 
    <br><br>
    People around me are already using single action presenters, but still depend on Nette. Why? Coupling of <code>IPresenter</code> in Application and Router. 
    <br><br>
    I always struggle for freedom and to framework use me, not <strong>Today, I will show you when how addictive and sexy presenters independent are and get them event to Nette</strong>.
'''
lang: en
---

@todo ask Honza Mikeš on feedback
@todo wait for Symplify 2.0 stable before release this
@todo update example repository o Github
@todo complete gifs
 
 


When first I talked about [single action or rather invokable presenters in Nette](https://www.facebook.com/pehapkari/videos/1285464581503349/) on 8x. posobota in Prague, **people were talking about 3 miss-conceptions**. I'd like clarify them first.
 
### Nette needs `IPreseneter`

My first attempt decouple presenter from Nette failed `PresenterFactory`:

```php
// ...
if (!$reflection->implementsInterface(IPresenter::class)) {
    throw new InvalidPresenterException("Cannot load presenter '$name', class '$class' is not Nette\\Application\\IPresenter implementor.");
}
```

This took me few weeks to figure out, because coupling to latte, providers and layout autodiscovery. 
But when you modify `PresenterFactory`, `Application->run()` and create own `PresneterRoute`, it can be done.   


### What about ajax? What about Components?

If you use ajax and components, this approach is not probably for you application. 
I thought it's impossible to use Nette without components as well, but Ondrej Mirtes from Slevomat take me out of my misery. "We don't use components in Slevomat, just presenters." So feel free to ask him how they do it. It helpled me a lot to move forward.

**You'll appreciate this approach in applications, where**:
 
- front-end is managed not by Nette ajax, but by ReactJS, Angular or any other frontent
- API is the main entry point to the application
- your application is growing and you want to avoid god classes like presenters with 10 actions methods in next 2-3 years


### There should be and Interface for that

Even if some people agreed with invokable/single action approach, they still missed some interface, that would enforce a method. `__invoked()` seemed to weird for them. I actually remember I had similar idea to make it standard somehow.

But I've learned what `__invoke()` is and that is used by Symfony and Laravel for years. And using and interface would not really make it independent. Moreover for presenter/controller, which every framework bend to it's own need.
 
`__invoke()` is normal method, just like `__constructor()` is normal for passing dependencies nowadays. But in static days, it "was too much writing", remember?


## How did Symfony community moved to Decoupling

### Why Decouple Controller From Framework?

If you look for more reasons to decouple from framework, read this [3 parts series: Framework Independend Controllers](https://php-and-symfony.matthiasnoback.nl/tags/controller/) by Matthias Noback about Symfony controlelrs. 

### Why are Single Action Presenters Great for Growing Projects

Exactly 1,5 year ago, similar package and [post](https://dunglas.fr/2016/01/dunglasactionbundle-symfony-controllers-redesigned/) was made by Kevin Dunglas.    


## How it looks like?

The goal was:

- don't depend on any interface or class
- have single public method `__invoke()`

```php
namespace App\Presenter;

use Symplify\SymbioticController\Contract\Template\TemplateRendererInterface;

final class StandalonePresenter
{
    /**
     * @var TemplateRendererInterface
     */
    private $templateRenderer;

    public function __construct(TemplateRendererInterface $templateRenderer)
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

@todo: add gif for file click-through


Or if you use API and json:

```php
namespace App\Presenter;

use Nette\Application\Responses\JsonResponse;

final class ContactPresenter
{
    public function __invoke(): TextResponse
    {
        return new JsonResponse('Hi!');
    }
}
```

But how to register this presenter in router? Since the called action is now *not a method in a class* but *a class*, we cannot use common way to add route:

```php
# this won't work
$routes[] = new Route('/contact', 'Contact:default');
$routes[] = new Route('/contact', 'Contact:__invoke');
```

We need to use presenter as target:
  
```php
use App\Presenter\ContactPresenter; 

# this won't work either, as Route require <presenter>:<method>, format for target
$routes[] = new Route('/contact', ContactPresenter::class);
```

### Preseneter Route

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

@todo: add gif for route click-through

## 3 Steps To Your First Framework Agnostic Presenter in Nette 

### 1. Install package

```yaml
composer require symplify/symbiotic-controller
```
  
### 2. Register extension

```yaml
# app/config/config.neon
extensions:
    - Symplify\SymbioticController\DI\IndependentSingleActionPresenterExtension
    - Symplify\SymfonyEventDispatcher\Adapter\Nette\DI\SymfonyEventDispatcherExtension
```

### 3. Create Your Presenter and Use It

That's all :)


## Check Live Demo on Github

I've also prepared demo based on Nette Sandbox on Github - see [TomasVotruba/nette-single-action-presenter](https://github.com/TomasVotruba/nette-single-action-presenter).

Or click right on [HomepagePresenter](https://github.com/TomasVotruba/nette-single-action-presenter/blob/master/app/presenters/HomepagePresenter.php)


## How is Your Presenter Doing?

And how do you approach using presenters/controllers in Nette?

- Do you use components?
- Do you know why you depend on Nette?
- Do you try some other approach than traditional one?

Let me know in the comments please.
