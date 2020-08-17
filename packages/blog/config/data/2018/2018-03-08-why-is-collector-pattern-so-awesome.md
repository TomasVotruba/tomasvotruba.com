---
id: 80
title: "Why is Collector Pattern so Awesome"
perex: |
    How to achieve *open for extension* and *closed for modification* [one of sOlid principals](https://github.com/jupeter/clean-code-php#openclosed-principle-ocp)?
    <br><br>
    Why Collector pattern beats config tagging? How to use the in Symfony application? How it turns locked architecture into scaling one?

tweet: "New post on my blog: Why is Collector Pattern so Awesome #symfony #colletor #compilerpass #rector #solid #decoupling"
tweet_image: "/assets/images/posts/2018/collector/quote.jpg"

deprecated_since: "August 2020"
deprecated_message: |
    The feature is not in Rector any more, yet the collector is still valid to SOLID code extending.
---

I already [wrote about Collector pattern as one we can learn from Symfony or Laravel](/blog/2017/04/14/3-symfony-and-laravel-patterns-that-make-code-easy-to-extends-without-modification/#3-like-collecting-stamps-just-on-steroids). But they're so useful and underused I have need to write a more about them.

Yesterday I worked on [Rector](https://github.com/rectorphp/rector) and **needed an entry point to add one or more Rectors by user**.

<br>

To give you a context, now you can register particular Rectors to config as in Symfony:

```php
<?php

// rector.php

declare(strict_types=1);

use Rector\Privatization\Rector\MethodCall\PrivatizeLocalGetterToPropertyRector;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

return static function (ContainerConfigurator $containerConfigurator): void {
    $services = $containerConfigurator->services();
    $services->set(PrivatizeLocalGetterToPropertyRector::class);
};
```

## Towards Scalable Architecture

Well, we could accept PR and hard-code it into application, that would work too. But the point is to allow end-user to **add as many customs services of specific type as he or she wants without need to modify our application**.

### Open to Extension, Closed to Modification

This is how *open/closed principle* looks like. If you still don't have the idea, see very nice and descriptive examples in [jupeter/clean-code-php](https://github.com/jupeter/clean-code-php#openclosed-principle-ocp).

Let's start with ideas:

## 1. Add a Provider and Collect it in CompilerPass?

My first idea was a provider that would return such Rector:

```php
<?php declare(strict_types=1);

namespace App\Rector;

use Rector\Contract\Rector\RectorInterface;

final class SymfonyRectorProvider implements RectorInterface
{
    public function provide()
    {
        $rector = new CustomSymfonyRector;
        // some custom modifications

        return $rector;
    }
}
```

<br>

Such service is registered by user to the config:

```php
<?php

// rector.php

declare(strict_types=1);

use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

return static function (ContainerConfigurator $containerConfigurator): void {
    $services = $containerConfigurator->services();
    $services->defaults()
        ->autowire();

    $services->set(\App\Rector\SymfonyRectorProvide::class);
};
```

<br>

And collected by our application via `CompilerPass`:

```php
<?php declare(strict_types=1);

namespace Rector\RectorBuilder\DependencyInjection\CompilerPass;

use Rector\Rector\RectorCollector;
use Rector\RectorBuilder\Contract\RectorProviderInterface;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\ExpressionLanguage\Expression;
use Symplify\PackageBuilder\DependencyInjection\DefinitionFinder;

final class RectorProvidersCompilerPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $containerBuilder): void
    {
        $rectorCollectorDefinition = $containerBuilder->getDefinition(RectorCollector::class);

        $rectorProviderDefinitions = DefinitionFinder::findAllByType(
            $containerBuilder,
            RectorProviderInterface::class
        );

        foreach ($rectorProviderDefinitions as $rectorProviderDefinition) {
            $providedRector = new Expression(
                sprintf('service("%s").provide()', $rectorProviderDefinition->getClass())
            );
            $rectorCollectorDefinition->addMethodCall('addRector', [$providedRector]);
        }
    }
}
```

Are you curious what `DefinitionFinder`? It's just [a helper class](https://github.com/symplify/symplify/blob/3d058becb57efefe2307c88ee94acbfbd15ebd1c/packages/PackageBuilder/src/DependencyInjection/DefinitionFinder.php) around `ContainerBuilder`.

## 2. Use Expression Language?

Wait, what is this?

```php
$providedRector = new Expression(
    sprintf('service("%s").provide()', $rectorProviderDefinition->getClass())
);
```

That is part of [Symfony Expression Language](https://symfony.com/doc/current/service_container/expression_language.html) that allows calling methods on services before container compilation.

Could you guess, how the final code in compiled container would look like?

<br>

Something like this:

```php
$rectorCollector = new Rector\Rector\RectorCollector;
$rectorCollector->addRector((new App\Rector\SymfonyRectorProvider)->provide());
```

<br>

**To be honest, it's magic and unclear code to me.** It also needs `symfony\expression` package to be installed manually. I don't want to refer people to this paragraph just to understand 3 lines in `CompilerPass`. That code smells bad.

But what now?

### From One-to-One to One-to-Many

To simulate real life we should have at least 2 problems at once :)

The most common case is product in e-commerce. Product *JBL Charge 3* has 1 category - *speaker*. Ok, you write a code with Doctrine Entity that each product has one category. But as it happens in life, *change is the only constant*, website grows and search expands with new request from your boss: "A product needs to have multiple categories". What now?

The same happened for Rector - **I need to add multiple Rectors in `RectorProvider`**. What now?

Damn! Mmm, tell people to use one provider per Rector?

```php
<?php

// rector.php

declare(strict_types=1);

use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

return static function (ContainerConfigurator $containerConfigurator): void {
    $services = $containerConfigurator->services();
    $services->defaults()
        ->autowire();

    $services->set(\App\Rector\SymfonyRectorProvider::class);

    $services->set(\App\Rector\AnotherSymfonyRectorProvider::class);
};
```

Quick solution, yet smelly:

 - But how to share common code between similar RectorProvider?
 - Duplicate and decouple services?
 - And what is the point of provider, if it can only add 1 new Rector?
 - Why not register them in `services:` directly like the others?

And flow of *WTFs* is coming at you.

## 3. Does Collector Scale?

Let's try a different approach that Colletor pattern screams at us. We now have one-to-one `RectorColletor` implementation:

```php
<?php declare(strict_types=1);

namespace Rector\Rector;

use Rector\Core\Contract\Rector\RectorInterface;

final class RectorCollector
{
    // ...

    public function addRector(RectorInterface $rector): void
    {
        $this->rectors[] = $rector;
    }
}
```

### What do we want?

- drop that expression language magic
- support one-to-many case
- have clear API with priority in PHP code rather in `CompilerPass` or config
- have typehint control

### Drop that Expression Language Magic

Thanks to Collector pattern we now **have 1 place to solve these problems** at:

```diff
 <?php declare(strict_types=1);

 namespace Rector\Rector;

 use Rector\Contract\Rector\RectorInterface;
 use Rector\RectorBuilder\Contract\RectorProviderInterface;

 final class RectorCollector
 {
     public function addRector(RectorInterface $rector): void
     {
         $this->rectors[] = $rector;
     }
+
+    public function addRectorProvider(RectorProviderInterface $rectorProvider): void
+    {
+         $this->addRector($rectorProvider->provide());
+    }
 }
```

<br>

And thanks to that, **we can cleanup** `CompilerPass`:

```diff
<?php declare(strict_types=1);

namespace Rector\RectorBuilder\DependencyInjection\CompilerPass;

use Rector\Rector\RectorCollector;
use Rector\RectorBuilder\Contract\RectorProviderInterface;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\ExpressionLanguage\Expression;
use Symplify\PackageBuilder\DependencyInjection\DefinitionFinder;

final class RectorProvidersCompilerPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $containerBuilder): void
    {
        $rectorCollectorDefinition = $containerBuilder->getDefinition(RectorCollector::class);

        $rectorProviderDefinitions = DefinitionFinder::findAllByType(
            $containerBuilder,
            RectorProviderInterface::class
        );

        foreach ($rectorProviderDefinitions as $rectorProviderDefinition) {
-           $providedRector = new Expression(
-               sprintf('service("%s").provide()', $rectorProviderDefinition->getClass())
-           );
-           $rectorCollectorDefinition->addMethodCall('addRector', [$providedRector]);
+           $rectorCollectorDefinition->addMethodCall('addRectorProvider', [
+               '@' . $rectorProviderDefinition->getClass()
+           ]);
        }
    }
}
```

<br>

### How do we Provide Multiple Items?

I didn't forget, our dear manager. Do you have idea how would you add it?


```php
<?php declare(strict_types=1);

namespace Rector\RectorBuilder\Contract;

use Rector\Core\Contract\Rector\RectorInterface;


interface RectorProviderInterface
{
   /**
    * @return RectorInterface[]
    */
   public function provide(): array
}
```

<br>

And update `RectorCollector` class:

```diff
 <?php declare(strict_types=1);

 namespace Rector\Rector;

 use Rector\Contract\Rector\RectorInterface;
 use Rector\RectorBuilder\Contract\RectorProviderInterface;

 final class RectorCollector
 {
     public function addRector(RectorInterface $rector): void
     {
         $this->rectors[] = $rector;
     }

     public function addRectorProvider(RectorProviderInterface $rectorProvider): void
     {
-         $this->addRector($rectorProvider->provide());
+         foreach ($rectorProvider->provide() as $rector) {
+             $this->addRector($rector);
+         }
     }
 }
```

Now we have:

<em class="fas fa-fw fa-lg fa-check text-success"></em> single entry point for `Collector` + `Provider`

<em class="fas fa-fw fa-lg fa-check text-success"></em> typehinted `RectorInterface` control in code

<em class="fas fa-fw fa-lg fa-check text-success"></em> clean config for use and compiler for our code

<em class="fas fa-fw fa-lg fa-check text-success"></em> removed `symfony/expression-language` dependency

## 4. Add Tagging?

We forget tagging, right? The most [spread useless code in Symfony configs](/blog/2017/02/12/drop-all-service-tags-in-your-nette-and-symfony-applications).

<br>

<blockquote class="blockquote text-center">
    "Perfection is achieved, not when there is nothing more to add, but when there is nothing left to take away."
    <footer class="blockquote-footer">Antoine de Saint-Exupery</footer>
</blockquote>

<br>

Why would you add it and where? I don't take arguments like "well, it's historical reasons" and [`!tagged`](https://symfony.com/blog/new-in-symfony-3-4-simpler-injection-of-tagged-services), since it add more coupling.

Try to convince me though if you're sure about its advantages.

### How was our Path from the End?

<em class="fas fa-fw fa-lg fa-check text-success"></em> Add provider

<em class="fas fa-fw fa-lg fa-times text-danger"></em> Use expression language?

<em class="fas fa-fw fa-lg fa-check text-success"></em> Does collector scale?

<em class="fas fa-fw fa-lg fa-times text-danger"></em> Add tagging

## "Git Story" over git history

I want to share with you one last idea. I could show you the final commit - or even worse - just the final versions of `RectorProviderInterface`, `RectorCollector` and `RectorProvidersCompilerPass`. But what could you take from such a code? **Nothing, because only when we fail, we learn something new.**

**Same can be applied to git history. When I see 2 final files with 2 commits, I learn nothing new.** And pose questions and comments on blind paths, that author already took (and explains me again in words to my comments).

Next time you squash 20 commits to 1, remember:

<blockquote class="blockquote text-center">
    Git should tell the story, as the human history does.
</blockquote>

<br><br>

Happy collecting!
