---
id: 106
title: "How to Get Private Services in Symfony Test"
perex: |
    2 versions of Symfony are affected by this dissonance between services and tests.
    **Do you use Symfony 3.4 or 4.0? Do you want to test your services, but struggle to get them do it clean way?**
    <br><br>
    Today we look on possible solutions.
tweet: "New Post on My Blog: ..."
---

Since [Symfony 3.4 all services are private by default](https://symfony.com/blog/new-in-symfony-3-4-services-are-private-by-default).
That means you can't get service by `$this->get(App\SomeService::class)` or `$this->container->get(App\SomeService::class)` anymore, but **only only via constructor**. 

That's ok, until you need to test such service:

```php
use App\SomeService;
use PHPUnit\Framework\TestCase;

final class SomeServiceTest extends TestCase
{
    public functoin testSomeMethod()
    {
        $kernel = new AppKernel;
        $kernel->boot();
        $container = $kernel->getContainer();
        
        // this line is important ↓
        $someService = $container->get(SomeService::class);
        // ...
    }
}
```

When we run the test:

```bash
vendor/bin/phpunit tests
```

This exception will stop us:

```yaml
The "App\SomeService" service or alias has been removed or inlined when the container
was compiled. You should either make it public, or stop using the container directly 
and use dependency injection instead.
```

...*make it public*...

Ok!

```diff
 # app/config/config.yml
 services:
     _defaults:
         autowire: true
         
     App\:
         resource: ..
+
+    App\SomeService:
+        public: true    
```

And run tests again:

```bash
vendor/bin/phpunit tests
```

<p class="text-success pt-3 pb-3">
    <em class="fa fa-fw fa-lg fa-check"></em> Voilá!
</p>

## Down the Smelly Rabbit Hole

As you can see, we can load dozens of service from `App\` by 2 lines. But to test 1, we need to add 2 extra lines to config. 

```diff
 # app/config/config.yml
 services:
     _defaults:
         autowire: true
         
     App\:
         resource: ..
+         
+    # for tests only
+    App\SomeService:
+        public: true    
+
+    App\AnotherService:
+        public: true    
+
+    App\YetAnotherService:
+        public: true    
```

This is *one to many* code smell.

Also, we can **extract it to test config** `tests/config/config.yml`, so it's easier to hide the smell.

Or just **make everything public**, like [I did in Symplify 6 months ago](https://github.com/Symplify/Symplify/commit/d0457773915fa32df08e7342d5cd0093f97850ff): 

```diff
 services:
     _defaults:
         autowire: true
+        # for tests only
+        public: true

      App\:
          resource: ..
```

It's fast and easy solution, but... 

<p class="text-danger pt-3 pb-3">
    <em class="fa fa-fw fa-lg fa-times"></em> Not a way to go in long-term or bigger projects.
</p>

Don't worry, you're not alone. There is over [36 results for "symfony tests private services" on StackOverflow](https://stackoverflow.com/search?q=symfony+tests+private+services) at the time being:

<img src="/assets/images/posts/2018/private-services/popular.png" class="img-thumbnail">

But what other saint options we have? 
 
## 1. In Symfony 4.1 with FrameworkBundle

This is now fixed in 
<a href="https://symfony.com/blog/new-in-symfony-4-1-simpler-service-testing">Symfony 4.1 with Simpler service testing</a>.
 
Do you use
 
- `Symfony\Bundle\FrameworkBundle\Test\KernelTestCase` or
- `Symfony\Bundle\FrameworkBundle\Test\WebTestCase` 

for your tests? **Just upgrade to Symfony 4.1 and you're done.** 

## 2. In Symfony 4.1 Standalone or Symfony 3.4/4.0

But what if you don't use `FrameworkBundle`, e.g. you develop [standalone packages](/blog/2017/12/25/composer-local-packages-for-dummies/) and you only use Symfony\Console and Symfony\DependencyInjection?

It's reasonable we want to **keep all configs untouched**, no matter if we're in dev or tests.

```yaml
# app/config/config.yml
services:
    _defaults:
        autowire: true

    App\:
        resource: ..
```

And tests as well:

```php
use App\SomeService;
use PHPUnit\Framework\TestCase;

final class SomeServiceTest extends TestCase
{
    public functoin testSomeMethod()
    {
        $kernel = new AppKernel;
        $kernel->boot();
        $container = $kernel->getContainer();
        
        $someService = $container->get(SomeService::class);
        // ...
    }
}
```

If there would only be one place with a switch, that would make that all code smells go away and let us test. That would be awesome, right? 

How can we achieve that? Any ideas?

<br><br>

There is new class in [Symplify\PackageBuilder 4](/blog/2018/04/05/4-ways-to-speedup-your-symfony-development-with-packagebuilder/#2-drop-manual-code-public-true-code-for-every-service-you-test). 

```php
 use Symfony\Component\HttpKernel\Kernel;
+use Symplify\PackageBuilder\DependencyInjection\CompilerPass\PublicForTestsCompilerPass;

 final class AppKernel extends Kernel
 {
     protected function build(ContainerBuilder $containerBuilder): void
     {
         $containerBuilder->addCompilerPass('...');
+        $containerBuilder->addCompilerPass(new PublicForTestsCompilerPass());
     }
 }
```

This removed all `public: true` code smells from Symplify, just [see this commit](https://github.com/Symplify/Symplify/pull/680/commits/eddc6d0db92000d4b1eb01c820863838ff37ac92):

<img src="/assets/images/posts/2018/private-services/gone.png" class="img-thumbnail">

<p class="text-success pt-3 pb-3">
    <em class="fa fa-fw fa-lg fa-check"></em> Voilá!
</p>

There is also similar package by friend of mine [Tobias Nyholm](http://tnyholm.se/) called [SymfonyTest/symfony-bundle-test](https://github.com/SymfonyTest/symfony-bundle-test). Check at least [the super short CompilerPass](https://github.com/SymfonyTest/symfony-bundle-test/blob/master/src/CompilerPass/PublicServicePass.php).

### Other Existing Solutions

I don't find them scalable nor useful, but maybe you will.

- [add per service manual alias as in `doctrine/DoctrineBundle` tests](https://github.com/doctrine/DoctrineBundle/blob/1f504e5dc538b8ee151e0943dea5127ceffd1436/Tests/ServiceRepositoryTest.php#L71)
- [set propreties via `@inject` annotations with `jakzal/phpunit-injector` package](https://github.com/jakzal/phpunit-injector)

**Do you have another solution? Just drop a comment, all add it here.**

I'm very curious to find all the solutions people make for 1 problem they have common. How these solutions are diverse, but also similar.

<br><br>

Happy Symfony service testing!
