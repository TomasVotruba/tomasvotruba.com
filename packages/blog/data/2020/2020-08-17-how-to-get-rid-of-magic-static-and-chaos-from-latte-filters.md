---
id: 274
title: "How to Get Rid of Magic, Static and Chaos from Latte Filters"
perex: |
    [In the previous post](/blog/2020/08/10/4-ways-to-make-your-nette-project-more-readable/), we looked at how to avoid array magic and duplicates of Latte in Presenter and Components.
    <br>
    <br>
    Today we'll leverage those tips to make your code around Latte filters **easy and smooth to work with**.

tweet: "New Post on #php 🐘 blog: How to Get Rid of Magic, Static and Chaos from Latte Filters #nettefw"

updated_since: "September 2020"
updated_message: |
    Added options 6 - **invokable filter providers**.
---

Do you have your `LatteFactory` service ready? If not, [create it first](/blog/2020/08/10/4-ways-to-make-your-nette-project-more-readable#4-move-latte-engine-tuning-from-presenter-control-to-lattefactory), because we'll build on it.

<br>

```php
<?php

declare(strict_types=1);

namespace App\Latte;

use Latte\Engine;
use Latte\Runtime\FilterInfo;

final class LatteFactory
{
    public function create(): Engine
    {
        $engine = new Engine();
        $engine->setStrictTypes(true);

        return $engine;
    }
}
```

## How to register a new Latte Filter?

This simple question can add easily add an anti-pattern to your code, that spreads like COVID and [inspires developers to add more anti-patterns](https://blog.codinghorror.com/the-broken-window-theory/). It's easy to [submit to static infinite loop](/blog/2018/04/26/how-i-got-into-static-trap-and-made-fool-of-myself//), I did it too.

But let's look at practice... **how to add a filter**?

Let's say we want to format money. The filter code is not relevant here, so we go with the simplest version possible:

```php
class SomeFilter
{
    public function money(int $amount): string
    {
        return $amount . ' €';
    }
}
```

We'll use it template like this:

```html
You're total invoice amount:
<strong>{$amount|money}</strong>

Thank you
```

## 1. Register Static Magic Loader

This used to be the best practice in 2014. Just add a class and magically delegate called filter name:

```diff
 namespace App\Latte;

 use Latte\Engine;
 use Latte\Runtime\FilterInfo;

 final class LatteFactory
 {
     public function create(): Engine
     {
         $engine = new Engine();
         $engine->setStrictTypes(true);
+        $engine->addFilter(null, SomeFilter::class . '::loader');

         return $engine;
    }
 }
```

And add `loader()` method:

```diff
 class SomeFilter
 {
-    public function money(int $amount): string
+    public static function money(int $amount): string
     {
         return $amount . ' €';
     }

+    public static function loader($arg)
+    {
+        $arg = \func_get_args();
+        $func = \array_shift($arg);
+        if (\method_exists(self::class, $func)) {
+            return \call_user_func_array([self::class, $func], $arg);
+        }
+
+         return null;
     }
 }
```

This is my favorite magic part:

```php
$engine->addFilter(null, SomeFilter::class . '::loader');
```

Do you have any what is happening there? I don't.

<br>

#### Pros & Cons

- We have to use `static` ❌
- We can't use any service in `SomeFilter`, we have to use static only ❌
- We violate `addFilter()` method with magic and make it harder to read, maintain and refactor ❌
- We have one place to add filters <em class="fas fa-fw fa-check text-success "></em>

<br>

Can we do better?

## 2. Register Function manually with `addFilter()`

The `addFilter()` can be used in the way [it's designed for](https://github.com/nette/latte/blob/82a85d31caeaf9a9d307e910a2d1476f5460cee0/src/Latte/Engine.php#L267):

```diff
 namespace App\Latte;

 use Latte\Engine;
 use Latte\Runtime\FilterInfo;

 final class LatteFactory
 {
     public function create(): Engine
     {
         $engine = new Engine();
         $engine->setStrictTypes(true);
+        $engine->addFilter('money', function (int $amount): string {
+             return $amount . ' €';
+        });

         return $engine;
    }
 }
```

Straight forward, transparent, and a few lines of code.

#### Pros & Cons

- We have very little code <em class="fas fa-fw fa-check text-success"></em>
- The framework part (Latte) is now directly bounded to our application domain - this makes code hard to refactor, decopule from framework or re-use in another context ❌
- We break dependency inversion principle - we have to edit `LatteFactory` to add a new filter ❌
- We made a seed for God class antipattern - soon our `LatteFactory` will have over 100 of lines with various filters ❌
- We think it's a good idea, because of short-code-is-the-best fallacy ❌

<br>

Can we do better?

## 3. Add Filter Provider Service?

The previous solution looks fine, if only we could get rid of coupling between framework and our code.

```diff
 namespace App\Latte;

 use Latte\Engine;
 use Latte\Runtime\FilterInfo;

 final class LatteFactory
 {
+    private FilterProvider $filterProvider;
+
+    public function __construct(FilterProvider $filterProvider)
+    {
+        $this->filterProvider = $filterProvider;
+    }

     public function create(): Engine
     {
         $engine = new Engine();
         $engine->setStrictTypes(true);

+        foreach ($this->filterProvider->provide() as $filterName => $filterCallback) {
+            $engine->addFilter($filterName, $filterCallback);
+        }

         return $engine;
    }
 }
```

```php
<?php

final class FilterProvider
{
    /**
     * @return array<string, callable>
     */
    public function provide(): array
    {
        return [
            'money' => function (int $amount): string {
                return $amount . ' €';
            }
        ];
    }
}
```

The filter class is decoupled - no more hard-coded filters!

#### Pros & Cons

- We can add a new filter without every touching `LatteFactory` <em class="fas fa-fw fa-check text-success"></em>
- We can use services in filters <em class="fas fa-fw fa-check text-success"></em>
- We **only moved a seed for God class antipattern** - soon our `FilterProvider` will have over 100 of lines with various filters ❌

<br>

Can we do better?

## 4. Filter Provider Contract

The ultimate solution is almost perfect. We only need to get rid of the God class completely. How can we do that?

The goal is simple:

- each domain should have its filters, e.g., filters for text should have their class, filters for money should have their class, etc.
- we can't touch the `LatteEngine` to add a new filter, nor a new filter service

<br>

What if we use [autowired arrays feature from Nette 3.0](/blog/2018/11/12/will-autowired-arrays-finally-deprecate-tags-in-symfony-and-nette//)?

<br>

```diff
 namespace App\Latte;

+use App\Contract\FilterProviderInterface;
 use Latte\Engine;
 use Latte\Runtime\FilterInfo;

 final class LatteFactory
 {
+    private array $filterProvider;
+
+    /**
+     * @param FilterProviderInterface[] $filterProviders
+     */
+    public function __construct(array $filterProviders)
+    {
+        $this->filterProviders = $filterProviders;
+    }

     public function create(): Engine
     {
         $engine = new Engine();
         $engine->setStrictTypes(true);

+        foreach ($this->filterProviders as $filterProvider) {
+            foreach ($filterProvider->provide() as $filterName => $filterCallback) {
+                $engine->addFilter($filterName, $filterCallback);
+            }
+        }

         return $engine;
    }
 }
```

```php
namespace App\Contract;

interface FilterProviderInterface
{
    /**
     * @return array<string, callable>
     */
    public function provide();
}
```

```diff
+use App\Contract\FilterProviderInterface;

-final class FilterProvider
+final class MoneyFilterProvider implements FilterProviderInterface
 {
     /**
      * @return array<string, callable>
      */
     public function provide(): array
     {
         return [
             'money' => function (int $amount): string {
                 return $amount . ' €';
             }
         ];
     }
 }
```

#### Pros & Cons

- We have decoupled framework and our domain-specific filter <em class="fas fa-fw fa-check text-success"></em>
- To add a new filters, we only need to create a new service <em class="fas fa-fw fa-check text-success"></em>
- We finally use dependency injection at its best - Nette handles registering filters and collecting service for us <em class="fas fa-fw fa-check text-success"></em>
- We **add a seed for God method** - soon `provide()` will be full of weird callbacks and long methods ❌

<br>

Can we do better?

<br>


## 5. From Callbacks to Private Methods


```diff
 use App\Contract\FilterProviderInterface;

 final class MoneyFilterProvider implements FilterProviderInterface
 {
     /**
      * @return array<string, callable>
      */
     public function provide(): array
     {
         return [
             'money' => function (int $amount): string {
-                return $amount . ' €';
+                return $this->money($mount);
             }
         ];
     }

+    private function money(int $amount): string
+    {
+        return $amount . ' €';
+    }
 }
```

This looks like a duplicated code, right?

But what if money filters grow, included timezones and logged in user country? Is `MoneyFilterProvider` the best place to handle all this logic?


```diff
 use App\Contract\FilterProviderInterface;

 final class MoneyFilterProvider implements FilterProviderInterface
 {
+    private MoneyFormatResolver $moneyFormatResolver;
+
+    public function __construct(MoneyFormatResolver $moneyFormatResolver)
+    {
+       $this->moneyFormatResolver = $moneyFormatResolver;
+    }

     /**
      * @return array<string, callable>
      */
     public function provide(): array
     {
         return [
             'money' => function (int $amount): string {
-                return $this->money($mount);
+                return $this->moneyFormatResolver->resolve($amount);
             }
         ];
     }

-    private function money(int $amount): string
-    {
-        return $amount . ' €';
-    }
 }
```

#### Pros & Cons

- We have decoupled domain logic from filters <em class="fas fa-fw fa-check text-success"></em>
- We can re-use the used-to-be filter logic with `MoneyFormatResolver` in other places of application <em class="fas fa-fw fa-check text-success"></em>
- We are motivated to use DI and decouple code clearly to new service, if it ever becomes too complex <em class="fas fa-fw fa-check text-success"></em>
- We are ready for any changes that come in the future <em class="fas fa-fw fa-check text-success"></em>
- ~~We think this is the best way, just because it's last ❌~~ Not anymore ↓

<br>

My question is: can we do better...?

---

**Update 1 month later with new option:**

## 6. Invokable Filter Providers

In fashion of [single-action controller](https://symfony.com/doc/current/controller/service.html#invokable-controllers) a tip from [@FrantisekMasa](https://twitter.com/FrantisekMasa) and [@dada_amater](https://twitter.com/dada_amater) for similar approach in filters. It look weird, new... so I had to try it in practise to see for myself.

```php
namespace App\Contract;

interface FilterProviderInterface
{
    public function getName(): string;
}
```

The filter itself - 1 filter = 1 class:

```php
use App\Contract\FilterProviderInterface;

final class MoneyFilterProvider implements FilterProviderInterface
{
    private MoneyFormatResolver $moneyFormatResolver;

    public function __construct(MoneyFormatResolver $moneyFormatResolver)
    {
       $this->moneyFormatResolver = $moneyFormatResolver;
    }

    public function __invoke(int $amount): string
    {
        return $this->moneyFormatResolver->resolve($amount);
    }
}
```

The `LatteFactory` now acceps the whole filter as callable object:

```php
namespace App\Latte;

use Latte\Engine;
use Latte\Runtime\FilterInfo;

final class LatteFactory
{
    /**
     * @var FilterProviders[]
     */
    private array $filterProviders = [];

    /**
     * @param FilterProvider[]
     */
    public function __construct(array $filterProviders)
    {
        $this->filterProviders = $filterProviders;
    }

    public function create(): Engine
    {
        $engine = new Engine();
        $engine->setStrictTypes(true);

        foreach ($this->filterProviders as $filterProvider) {
            $engine->addFilter($filterProvider->getName(), $filterProvider);
        }

        return $engine;
    }
}
```

#### Pros & Cons

- All of the advantages of previous approaches <em class="fas fa-fw fa-check text-success"></em>
- 1 class = 1 rule, this is really challenge to clutter <em class="fas fa-fw fa-check text-success"></em>
- It's very intuitive to use  <em class="fas fa-fw fa-check text-success"></em>
- We don't have to maintain duplicated `provideFilters()` callables with private methods <em class="fas fa-fw fa-check text-success"></em>
- The `__invoke()` method has no contract, so we can forget to implement it ❌

We compensate this in `LatteFactory` itself:

```php
public function create(): Engine
{
    $engine = new Engine();
    $engine->setStrictTypes(true);

    foreach ($this->filterProviders as $filterProvider) {
        if (! method_exists($filterProvider, '__invoke')) {
            $message = sprintf('Add "__invoke()" method to filter provider "%s"', get_class($filterProvider));
            throw new ShouldNotHappenException($message);
        }

        $engine->addFilter($filterProvider->getName(), $filterProvider);
    }

    return $engine;
}
```

That's it!

<br>

Happy coding!
