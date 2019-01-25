---
id: 14
title: How to get more than Request in Controller Action
perex: |
    You already know you can get <code>Request</code> object in your controller action. Cool, but there is more.
    In <em>Symfony 3.1</em> is new <a href="https://symfony.com/doc/current/controller/argument_value_resolver.html">Action Argument Resolving feature</a>,
    so you can <strong>get any service you need</strong>. With a bit of work. Today I will show you how.

deprecated_since: "May 2017"
deprecated_message: |
    You can [inject services into actions](https://github.com/symfony/symfony/pull/21771) using `controller.service_arguments` tag since **Symfony 3.3**.  
    
    Combined with [PSR4-based service discovery and registration](https://github.com/symfony/symfony/pull/21289) and [autoconfiguration](https://symfony.com/blog/new-in-symfony-3-3-service-autoconfiguration) this is amazing feature.  
    
    I recommend using it instead!
---

## Disclaimer: What happened to controller constructor injection?

**I still prefer constructor injection in controllers**. Are you asking why and how to achieve that in Symfony?

In 3 minutes [you will find out in my other article](/blog/2016/03/10/autowired-controllers-as-services-for-lazy-people).
I will wait here...

<br>
<br>
<br>

...ok. Now you are probably in one of these 2 groups:

### 1. You want to use constructor injection in controllers, because you like advantages of service design

Stop reading, because this article describes only method injection, which is not so clean. It would add unnecessary complexity to your
already solid system with no added value.
So go [install Symplify/ControllerAutowire bundle](https://github.com/Symplify/ControllerAutowire) and enjoy your day!

### 2. You consider constructor injection in controller too much writing with not much added value

**This is article for you. Keep reading!**

To be honest, first I was completely ignoring this second group, but [Jáchym](https://twitter.com/enumag) explained me why it's important.


## How named services bring less writing

Typical controller in Symfony looks like this:

```php
// src/AppBundle/Controller/MeetupController.php
namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class MeetupController extends Controller
{
    public function listAction()
    {
        $meetupRepository = $this->get('meetup_repository');

        return $this->render('meetup/list.twig', [
            'meetups' => $meetupRepository->findAll()
        ]);
    }
}
```

These 2 lines are the most important:

```php
$meetupRepository = $this->get('meetup_repository');
['meetups' => $meetupRepository->findAll()]
```

## Hidden vendor lock

**How do we know, that `meetup_repository` service has `findAll()` method?**

You need **3 things** to work at once:

- PHPStorm
- Symfony plugin
- correct setup for particular project structure
    - Symfony 2 and Symfony 3 have [different directory structure](https://knpuniversity.com/screencast/symfony3-upgrade/new-dir-structure), so plugin has to be setup manually to suit it


## Is this really necessary?

Nope. **Any IDE can autocomplete on object or interface typehint. And without plugins.**

But how to get that there? We can use constructor injection with typehints, **but it would be too much writing**.

Compare yourself this:

```php
/**
 * @var MeetupRepository
 */
private $meetupRepository;

public function __construct(MeetupRepository $meetupRepository)
{
    $this->meetupRepository = $meetupRepository;
}

public function listAction()
{
    // $this->meetupRepository;
}
```

to this:

```php
public function listAction()
{
    // $this->get('meetup_repository');
}
```

I've counted that for you:

**It's 11 lines to 1 just to just get a service!**

I must admit, this is killer argument **why not to use constructor injection in controllers**.


### Meet me half way?

What if you could inject the service via method?

```php
public function listAction(MeetupRepository $meetupRepository)
{
    $meetups = $meetupRepository->findAll();
    // ...
}
```

### Result?

- Now we are down to **0 extra lines** to get a dependency
- We can use **any IDE**
- We are **independent on project structure**

This all brings greater secondary advantages:

- Less stuff to remember, **less complexity**.
- **Faster on-boarding** of new programmers.
- **More joy** to your daily work.

To make this happened, I made [Symplify\ActionAutowire bundle](https://github.com/Symplify/ActionAutowire).

## How to enable controllers action autowiring in 3 steps

### 1. Install package

```yaml
composer require symplify/action-autowire
```

### 2. Register bundle

```php
// app/AppKernel.php
class AppKernel extends Kernel
{
    public function registerBundles()
    {
        $bundles = [
            new Symplify\ActionAutowire\SymplifyActionAutowireBundle(),
            // ...
        ];
    }
}
```

### 3. Add some dependency for your controller via constructor

```php
// src/AppBundle/Controller/MeetupController.php
namespace AppBundle\Controller;

class MeetupController extends Controller
{
    public function listAction(MeetupRepository $meetupRepository)
    {
        return $this->render('meetup/list.twig', [
            'meetups' => $meetupRepository->findAll()
        ]);
    }
}
```

And that's it!

For further use, **just check Readme for [Symplify/ActionAutowire](https://github.com/Symplify/ActionAutowire).**


## Made for you

Missing some feature or found a bug? Let me know. I want to make this package suit your needs and work as best as possible.
