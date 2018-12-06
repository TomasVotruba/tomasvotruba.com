---
id: 2
title: 4 Hot News in Symfony 3
perex: |
    In November 2015 except for a [PHP 7](https://wiki.php.net/rfc/php7timeline) and [Drupal 8](https://www.drupal.org/node/2605142),
    Symfony 3 is about to come.
    <br><br>
    **What changes and news it brings?**

deprecated_since: "December 2018"
deprecated_message: "3 years later, Symfony 4.2 is just released."
---

Symfony already knows that a lot. The new version places great emphasis on the [DX (developer experience)](https://symfony.com/blog/making-the-symfony-experience-exceptional). It brings us a **simpler and more straightforward API**, **better decoupled components**, **standards [PSR-3](https://www.php-fig.org/psr/psr-3/) and [PSR-7](https://symfony.com/doc/current/cookbook/psr7.html) integration**. A lot of other innovations that will make writing applications just got more fun.


## When is What Version Out?

Have you migrated from Symfony 1 to 2 and do you want to avoid a similar massacre? Don't worry - although there are lot of news, Symfony now respects [backward compatibility promise](https://symfony.com/doc/current/contributing/code/bc.html).

Migration Symfony 2 to 3 will be greatly simplified by the fact that **along with the version 3 will be released and version 2.8**. **It will have all the new feature in version 3 and will include BC layer to the 2.x series**. Version 2.8 will be long term supported (LTS) - so you can **count on the support until the end of 2018**.

<br>

<div class="text-center">
    <img src="/assets/images/posts/2015/symfony3/release-plan.png">
    <br>
    <em>Version 2.8 will LTS. The first LTS new series will be up to 3.3 (to be released in May 2017).</em>
</div>

<br>

What are the 2 main differences between 3.0 and 2.8 then?

- min. PHP version 5.5
- removing all deprecated code that provides compatibility to the BC 2.x (~ 10% of the code)


And now 4 the most expected news.

## 1. Service Autowiring

<a href="https://github.com/symfony/symfony/pull/15613" class="btn btn-dark btn-sm">
    <em class="fab fa-github fa-fw"></em>
    See pull-request
</a>

Symfony now supports constructor autowiring. When you are creating a service definition so you can turn on `autowiring` and skip the manual passing arguments.

Autowiring is quite popular in the Czech Republic due to bundles like [Skrz](https://github.com/skrz/autowiring-bundle) or [Kutny](https://github.com/kutny/autowiring-bundle)


**How it looks like in practice?**

Earlier long registration

```yaml
# services.yml

services:
    myService:
        class: MyBundle\MyService
        arguments: [ @dependency1, @dependency2 ]

    dependency1:
        class: MyBundle\Dependency1

    dependency2:
        class: MyBundle\Dependency2
```

Now you can cut to

```yaml
# services.yml

services:
    myService:
        class: MyBundle\MyService
        autowiring: true
```

**How does it work?**

Dependency Injection container analyze constructor and services:

- if the services are available → forward them
- if not → is registered as a private service

**What about the interface?**

Instead of a specific type of service you require an interface that implements the service. But what if we have multiple services to one interface (typical chain pattern)? Just for the service explicitly state:

```yaml
# services.yml

services:
    dependency1:
        class: MyBundle\Dependency1
        autowiring_types: MyBundle\MyInterface
```

## 2. More Logical Folders

Symfony 3 full-stack brings order. Get rid of chaos in the `/app` folder.

**How?**

Temporary files, logs, settings for PHPUnit, console files...

All this is now obvious location separate from the code of our application.

<br>

<div class="text-center">
    <img src="/assets/images/posts/2015/symfony3/directory-structure.png" class="img-thumbnail">
</div>

<br>

Tests you can run simply in command line via `vendor/bin/phpunit`.

## 3. Symfony Profiler in a New Jacket

<a href="https://github.com/symfony/symfony/pull/15523" class="btn btn-dark btn-sm">
    <em class="fab fa-github fa-fw"></em>
    See pull-request
</a>


For the programmer it is important not only to clear the code, but also arranged the meta-information about the application. Those in Symfony easily displayed using Symfony Profiler.

He had displayed so much information that it began to lose programmer. After 4 years he has finally made flat design.

Important information and above all error messages are now much easier to read.

<br>

<div class="text-center">
    <img src="/assets/images/posts/2015/symfony3/profiler-before-after.png" class="img-thumbnail">
</div>

<br>

<div class="text-center">
    <img src="/assets/images/posts/2015/symfony3/profiler-old-new.png" class="img-thumbnail">
</div>

<br>

<div class="text-center">
    <img src="/assets/images/posts/2015/symfony3/profiler-go-back.gif" class="img-thumbnail">
    <br>
    <em>Easy to get from profiler back to page</em>
</div>

## 4. Micro Kernel

<a href="https://github.com/symfony/symfony/pull/15990" class="btn btn-dark btn-sm">
    <em class="fab fa-github fa-fw"></em>
    See pull-request
</a>

Great joy will have smaller applications developers, who enjoy the comfort of the ecosystem full-stack Symfony. A few days ago, on November 5, was added to FrameworkBundle **Micro Kernel**.

That is precisely suited to applications that require simple configuration, bundle and that Silex enough.

Micro Kernel namely:

- requires no additional configuration files
- allows extension without adding bundles
- supports routing


**How does the Micro Kernel looks like?**

```php
<?php

use Symfony\Bundle\FrameworkBundle\Kernel\MicroKernelTrait;
use Symfony\Bundle\FrameworkBundle\FrameworkBundle;
use Symfony\Component\Config\Loader\LoaderInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Kernel;
use Symfony\Component\Routing\RouteCollectionBuilder;

class ConcreteMicroKernel extends Kernel
{
    use MicroKernelTrait;

    public function halloweenAction()
    {
        return new Response('halloween');
    }

    public function registerBundles()
    {
        return [new FrameworkBundle()];
    }

    protected function configureRoutes(RouteCollectionBuilder $routes)
    {
        $routes->add('/', 'kernel:halloweenAction');
    }

    protected function configureContainer(ContainerBuilder $containerBuilder, LoaderInterface $loader)
    {
        $containerBuilder->loadFromExtension('framework', [
            'secret' => '$ecret',
        ]);

        $containerBuilder->setParameter('title', 'Symfony 3 is painless');
    }
}
```


<hr>

## Now you know...

- That version 2.8 will LTS and released along with version 3.0.
- How did autowiring saves work when writing the definition of services.
- How do you clean up the ingredients `/app` to make it make sense.
- That work with the profiler will be much clearer.
- And for small applications you available Micro Kernel.


### In Symfony they Know...

*When the programmer may resort to more simple solution, he or she will.*

Therefore, they are trying to use it without its obstacles.

<br>

<div class="text-center">
    <img src="/assets/images/posts/2015/symfony3/you-got-this-meme.png" class="img-thumbnail">
</div>
