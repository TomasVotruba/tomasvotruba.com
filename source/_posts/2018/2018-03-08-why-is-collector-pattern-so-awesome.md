---
id: 80
title: "Why is Collector Pattern so Awesome"
perex: '''
    How to achieve *open for extension* and *closed for modification* [one of sOlid principals](https://github.com/jupeter/clean-code-php#openclosed-principle-ocp)?
   <br><br>
    Why Collector pattern beats config tagging? How to use the in Symfony application? How it turns locked architecture into scaling one?
'''
tweet: "New post on my blog: Why is Collector Pattern so Awesome #symfony #colletor #compilerpass #rector #solid #decoupling"
related_items: [36, 27]
tweet_image: "/assets/images/posts/2018/collector/quote.jpg"
---

I already [wrote about Collector pattern as one we can learn from Symfony or Laravel](/blog/2017/04/14/3-symfony-and-laravel-patterns-that-make-code-easy-to-extends-without-modification/#3-like-collecting-stamps-just-on-steroids). But they're so useful and underused I have need to write a more about them.

Yesterday I worked on [Rector](https://github.com/rectorphp/rector) and **needed an entry point to add one or more Rectors by user**. 

<br>

To give you little bit of context, now user can register it to config under `rectors:` section. Then [extension adds it as autowired service](https://github.com/rectorphp/rector/blob/77925afdc9d8032a36b92110e4fb3b905897f445/src/DependencyInjection/Extension/RectorsExtension.php#L53):

```yaml
# rector.yml
rectors:
    Rector\Rector\Contrib\Symfony\HttpKernel\GetterToPropertyRector: ~
```

Why not directly under `services`? To allow configuring:

```yaml
# rector.yml
rectors:
    Rector\Rector\Dynamic\ClassReplacerRector:
        'DeprecatedClass': 'NewClass'
```

<br>

But **how would you add [dynamically built](https://github.com/rectorphp/rector/pull/324)** Rectors?

```php
$rector = $this->builderRectorFactory->create()
    ->matchMethodCallByType('Nette\Application\UI\Control')
    ->matchMethodName('invalidateControl')
    ->changeMethodNameTo('redrawControl');
```

### Towards Scalable Architecture

Well, we could accept PR and hard-code it into application, that would work too. But the point is to allow end-user to **add as many customs Rectors as he or she wants without need to modify our application**. 

This is how *open/closed principle* looks like. If you still don't have the idea, see very nice and descriptive examples in [jupeter/clean-code-php](https://github.com/jupeter/clean-code-php#openclosed-principle-ocp) on Github I help to maintain.

Let's start with ideas:

## 1. Add a Provider?

My first idea was a provider that would return such Rector:

```php
<?php declare(strict_types=1);

namespace App\Rector;

use Rector\Contract\Rector\RectorInterface;

final class NetteRectorProvider implements RectorInterface
{
    // `$builderRectorFactory` passed via contructor + property
    
    public function provide(): RectorInterface
    {
        return $this->builderRectorFactory->create()
            ->matchMethodCallByType('Nette\Application\UI\Control')
            ->matchMethodName('invalidateControl')
            ->changeMethodNameTo('redrawControl');
    }
}
```

Such service is registered by user to the config:
 
```yaml 
# rector.yml
services:
    _defaults:
        autowire: true
    
    App\Rector\NetteRectorProvider: ~
```

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

Are you curious what `DefinitionFinder`? It's just [a helper class](https://github.com/Symplify/Symplify/blob/3d058becb57efefe2307c88ee94acbfbd15ebd1c/packages/PackageBuilder/src/DependencyInjection/DefinitionFinder.php) aroud `ContainerBuilder`. 

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
$rectorCollector->addRector((new App\Rector\NetteRectorProvider)->provide());
```

**To be honest, it's crazy and unclear code to me.** It also needs `symfony\expression` package to be installed manually. I don't want to refer people to this paragraph just to understand 3 lines in `CompilerPass`. That smells bad. 

But what now?

### From One-to-One to One-to-Many 

To simulate real life we should have at least 2 problems at once :)
 
The most common case is product in e-commerce. Product *JBL Charge 3* has 1 category - *speaker*. Ok, you write a code with Doctrine Entity that each product has one category. But as it happens in life that *change is the only constant*, website grows and search expands. "A product needs to have multiple categories" says the product manager. What now?

The same happened for Rector - **I need to add multiple Rectors in `RectorProvider`**. What now?
  
```php
<?php declare(strict_types=1);

namespace App\Rector;

use Rector\Contract\Rector\RectorInterface;

final class NetteRectorProvider implements RectorInterface
{
    // `$builderRectorFactory` passed via contructor + property
    
    public function provide(): array // RectorInterface
    {
        $firstRector = $this->builderRectorFactory->create()
            ->matchMethodCallByType('Nette\Application\UI\Control')
            ->matchMethodName('validateControl')
            ->changeMethodNameTo('redrawControl');
    
        $secondRector = $this->builderRectorFactory->create()
            ->matchMethodCallByType('Nette\Application\UI\Control')
            ->matchMethodName('invalidateControl') // prefix "in*"
            ->changeMethodNameTo('redrawControl');
          
        // return ?; 
    }
}
```

Damn! Mmm, tell people to use one provider per Rector:

```yaml 
# rector.yml
services:
    _defaults:
        autowire: true
    
    App\Rector\NetteRectorProvider: ~
    App\Rector\AnotherNetteRectorProvider: ~
```
  
Quick solution, yet smelly:
 
 - But how to share common code between similar RectorProvider? 
 - Duplicate and decouple services? 
 - And what is the point of provider, if it can only add 1 new Rector? 
 - Why not register them in `rectors:` directly like the others?

And flow of *WTFs* is coming at you.

## 3. Does Collector Scale?

Let's try a different approach that Colletor pattern screams at us. We now have one-to-one `RectorColletor` implementation:

```php
<?php declare(strict_types=1);

namespace Rector\Rector;

use Rector\Contract\Rector\RectorInterface;
use Rector\RectorBuilder\Contract\RectorProviderInterface;
 
final class RectorCollector
{
    public function addRector(RectorInterface $rector): void
    {
        $this->retcors[] = $rector; 
    }
}
```

### What do we want?

- drop that expression language magic
- support one-to-many case
- have clear API with priority in PHP code rather in `CompilerPass` or config
- have typehint control 

### Drop that Expression Language Magic

This is why Collector pattern is so awesome. **You have 1 place to solve all your problems** (or at least those 2 we have):  

```diff
 <?php declare(strict_types=1);

 namespace Rector\Rector;

 use Rector\Contract\Rector\RectorInterface;
 use Rector\RectorBuilder\Contract\RectorProviderInterface;
 
 final class RectorCollector
 {
     public function addRector(RectorInterface $rector): void
     {
         $this->retcors[] = $rector; 
     }
+     
+    public function addRectorProvider(RectorProviderInterface $rectorProvider): void
+    {
+         $this->addRector($rectorProvider->provide());
+    }
 }
```

Move config and expression language smell to new method that works with `RectorProviderInterface` directly. Good old PHP.

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

### Support one-to-many case
        
I know you already know how to solve this now. So let's put it together here:

```diff
 <?php declare(strict_types=1);

 namespace Rector\Rector;

 use Rector\Contract\Rector\RectorInterface;
 use Rector\RectorBuilder\Contract\RectorProviderInterface;
 
 final class RectorCollector
 {
     public function addRector(RectorInterface $rector): void
     {
         $this->retcors[] = $rector; 
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

Great! Now we have:
 
- single entry point for `Collector` + `Provider`
- typehinted `RectorInterface` control in code
- clean config for use and compiler for our code  
- removed `symfony/expression-language` dependency

## 4. Add Tagging?
  
We forget tagging, right? The most [spread useless code in Symfony configs](/blog/2017/02/12/drop-all-service-tags-in-your-nette-and-symfony-applications/).

<br>

<blockquote class="blockquote text-center">
    "Perfection is achieved, not when there is nothing more to add, but when there is nothing left to take away."
    <footer class="blockquote-footer">Antoine de Saint-Exupery</footer>
</blockquote>

<br>

Why would you add it and where? I don't take arguments like "well, it's historical reasons" and [`!tagged`](https://symfony.com/blog/new-in-symfony-3-4-simpler-injection-of-tagged-services), since it add more coupling.

Try to convince me though if you're sure about its advantages. 

### How was our Path from the End?

<em class="fa fa-fw fa-lg fa-check text-success"></em> Add provider

<em class="fa fa-fw fa-lg fa-times text-danger"></em> Use expression language?
 
<em class="fa fa-fw fa-lg fa-check text-success"></em> Does collector scale?

<em class="fa fa-fw fa-lg fa-times text-danger"></em> Add tagging

## "Git Story" over git history

I want to share with you one last idea. I could show you the final commit - or even worse - just the final files of (former) [`RectorCollector`](https://github.com/rectorphp/rector/blob/7305cf40d22cd1f241fcf8dcdebdc22b935616d8/src/NodeTraverser/RectorNodeTraverser.php) and [`RectorProvidersCompilerPass`](https://github.com/rectorphp/rector/blob/7305cf40d22cd1f241fcf8dcdebdc22b935616d8/packages/RectorBuilder/src/DependencyInjection/CompilerPass/RectorProvidersCompilerPass.php).

But that would teach nothing, because only when we fail, we learn something new.

**Same can be applied to git history. When I see 2 final files with 2 commits, I learn nothing new.** And pose questions and comments on blind paths, that author already took (and explains me again in words to my comments).

Next time you squash 20 commits to 1, remember: *Git should tell the story, as the human history does*.  
 
<br><br>
 
Happy collecting!
