---
id: 106
title: "How to Test Private Services in Symfony"
perex: |
    2 versions of Symfony are affected by this dissonance between services and tests.
    **Do you use Symfony 3.4 or 4.0? Do you want to test your services, but struggle to get them in a clean way?**
    <br><br>
    Today we look at possible solutions.
tweet: "New Post on My Blog: How to Test Private Services in #Symfony #phpunit"
tweet_image: "/assets/images/posts/2018/private-services/gone.png"

updated_since: "April 2019"
updated_message: |
    After trying all the options in this post I settled down with simple solution:
    <br>
    **`public: true` in all my configs.**
    <br>
    <br>
    The only approach that works out of the box and requires 0-setup.
---

Since [Symfony 3.4 all services are private by default](https://symfony.com/blog/new-in-symfony-3-4-services-are-private-by-default).
That means you can't get service by `$this->get(App\SomeService::class)` or `$this->container->get(App\SomeService::class)` anymore, but **only only via constructor**.

That's ok until you need to test such service:

```php
use App\SomeService;
use PHPUnit\Framework\TestCase;

final class SomeServiceTest extends TestCase
{
    public function testSomeMethod()
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

<br>

And run tests again:

```bash
vendor/bin/phpunit tests
```

<p class="text-success pt-3 pb-3">
    ✅ Voilá!
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

Or just **make everything public**, like [I did in Symplify 6 months ago](https://github.com/symplify/symplify/commit/d0457773915fa32df08e7342d5cd0093f97850ff):

```diff
 services:
     _defaults:
         autowire: true
+        # for tests only
+        public: true

      App\:
          resource: ..
```

But Symfony folks will not be happy to see this, because they need people to use private services. Why? So they learn to use constructor injection in services instead of `$this->get(...)`. So how should we do it *the Symfony-way*?

**We're not alone asking this question**. There are over [52 results for "symfony tests private services" on StackOverflow](https://stackoverflow.com/search?q=symfony+tests+private+services+is%3Aquestion) at the time being:

<img src="/assets/images/posts/2018/private-services/popular.png" class="img-thumbnail">

But what saint options we have?

## 1. In Symfony 4.1 with FrameworkBundle

This is now fixed in
<a href="https://symfony.com/blog/new-in-symfony-4-1-simpler-service-testing">Symfony 4.1 with Simpler service testing</a>.

Do you use

- `Symfony\Bundle\FrameworkBundle\Test\KernelTestCase` or
- `Symfony\Bundle\FrameworkBundle\Test\WebTestCase`

for your tests? **Just upgrade to Symfony 4.1 and you're done.**

## 2. In Symfony 4.1 Standalone or Symfony 3.4/4.0

But if you create open-source, you usually stick with last LTS, Symfony 3.4. How to solve it there?

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
    public function testSomeMethod()
    {
        $kernel = new AppKernel;
        $kernel->boot();
        $container = $kernel->getContainer();

        $someService = $container->get(SomeService::class);
        // ...
    }
}
```

If there would only be one place with a switch, that would make that all code smells go away and let us test. That would be awesome, right? How can we achieve that? Any ideas?

### Compiler Pass = Possible Solution?

Compiler pass allows us to write nice, decoupled and reusable code. After all, the solution for Symfony 4.1 is done by [a compiler pass](https://github.com/symfony/symfony/pull/26499/files#diff-ce4ed09b11d8fa531159e96df52124f3), that creates public 'test.service-name' aliases.

Let's create one for our PHPUnit test cases:

```php
<?php

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

final class PublicForTestsCompilerPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $containerBuilder): void
    {
        if (! $this->isPHPUnit()) {
            return;
        }

        foreach ($containerBuilder->getDefinitions() as $definition) {
            $definition->setPublic(true);
        }

        foreach ($containerBuilder->getAliases() as $definition) {
            $definition->setPublic(true);
        }
    }

    private function isPHPUnit(): bool
    {
        // there constants are defined by PHPUnit
        return defined('PHPUNIT_COMPOSER_INSTALL') || defined('__PHPUNIT_PHAR__');
    }
}
```

And register it in our Kernel:

```php
<?php

final class AppKernel extends Kernel
{
    protected function build(ContainerBuilder $containerBuilder): void
    {
        $containerBuilder->addCompilerPass(new PublicForTestsCompilerPass());
    }
}
```

This removes all `public: true` lines from all your configs.

<img src="/assets/images/posts/2018/private-services/gone.png" class="img-thumbnail">

<p class="text-success pt-3 pb-3">
    ✅ That's it!
</p>

## But Why?

- "So we can remove the `public: true` from our configs."
- "That's a consequence, not a reason. So why?"
- "So we can get services from container in tests."
- "That's a goal. I ask for why this way?"
- "Well, it's universal and just works."

It is. But in 6 months of using this method **I got different feedback from the PHP community**:

- Why there are no public services, but we can get them from container anyway? ❌
- Why test and dev services behave differently? ❌
- Why I can't get the service from container in the second application as in the first one? ❌ (They [forgot to add](/blog/2018/08/27/why-and-how-to-avoid-the-memory-lock/) a compiler pass to new Kernel.)
- Why I have to add `public: true` for Symfony\Console\Application in bin file, but not in tests? ❌

**People were confused 😕🤔**. The trade of compiler pass feature was putting too much knowledge pressure on the programmers. **The application uses constructor injection everywhere, so there is no real added value by working with term *public/private* services**.

## Final Proven Practise

In the end **I removed the compiler pass and moved back to `public: true` in all configs** right bellow `autowire: true`:

```diff
 services:
     _defaults:
         autowire: true
+        public: true
```

Thanks to that, the **whole process became clear**:

- We're using native Symfony syntax, you don't have to learn compiler passes ✅
- Configs are clear and people know what to expect ✅
- The location is always behind `autowire: true` → all configs have the same setup ✅

<br><br>

Happy coding!
