---
id: 79
title: "Why is Collector Pattern so Awesome"
perex: '''
    How to achieve *open for extension* and *closed for modification* [one of sOlid principals](https://github.com/jupeter/clean-code-php#openclosed-principle-ocp)?
   <br><br>
    Why Collector pattern beats config tagging? How to use the in Symfony application? How it turns locked architecture into scaling one?
'''
todo_tweet: "..."
related_items: [36, 27]
---

I already [wrote about Collector pattern as one we can learn from Symfony or Laravel](/blog/2017/04/14/3-symfony-and-laravel-patterns-that-make-code-easy-to-extends-without-modification/#3-like-collecting-stamps-just-on-steroids). But they're so useful and underused I have need to write a more about them.

Yesterday I worked on [Rector](https://github.com/rectorphp/rector) and **needed an entry point to add one or more Rectors by user**. Now user can register it to config where [extension adds it as autowired service](https://github.com/rectorphp/rector/blob/77925afdc9d8032a36b92110e4fb3b905897f445/src/DependencyInjection/Extension/RectorsExtension.php#L53):

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

But **how would you add [dynamically built](https://github.com/rectorphp/rector/pull/324)** Rectors?

```php
$rector = $this->builderRectorFactory->create()
    ->matchMethodCallByType('Nette\Application\UI\Control')
    ->matchMethodName('invalidateControl')
    ->changeMethodNameTo('redrawControl');
```

### Towards Scalable Architecture

Well, we could hard-code that into application. That would work too. But the point is to allow end-user to **add as many customs Rectors as he or she wants without need to modify our application**. 

This is how *open/closed principle* looks like. You can explore it more in examples of [jupeter/clean-code-php](https://github.com/jupeter/clean-code-php#openclosed-principle-ocp) on Github I help to maintain.

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

Such services is registered by user his config:
 
```yaml 
# rector.yml
services:
    _defaults:
        autowire: true
    
    App\Rector\NetteRectorProvider: ~
```

And add collected by our application via `CompilerPass`: 

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

Are you curious what `DefinitionFinder`? It's just [a helper class](https://github.com/Symplify/Symplify/blob/3d058becb57efefe2307c88ee94acbfbd15ebd1c/packages/PackageBuilder/src/DependencyInjection/DefinitionFinder.php). 

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

**To be honest, it's crazy and unclear code to me.** It also needs `symfony\expression` package to be installed. I don't want to refer people to this paragraph just to understand 3 lines in `CompilerPass`. That smells bad. 

But what now?

### From One-to-One to One-to-Many 

To simulate real life we should have at lest 2 problems at once :)
 
The most common case is product in e-commerce. Product *JBL Charge 3* has 1 category - *speaker*. Ok, you write a code with Doctrine Entity that each product has one category. But as it happens in life that change is the only constant, website grows and search expands. "A product needs to have multiple categories". What now?

Same happened for Rector - **how to add multiple Rectors in `RectorProvider`**?
  
```php
<?php declare(strict_types=1);

namespace App\Rector;

use Rector\Contract\Rector\RectorInterface;

final class NetteRectorProvider implements RectorInterface
{
    // `$builderRectorFactory` passed via contructor + property
    
    public function provide(): RectorInterface
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

Damn! Oh, people have to use one provider per Rector:

```yaml 
# rector.yml
services:
    _defaults:
        autowire: true
    
    App\Rector\NetteRectorProvider: ~
    App\Rector\AnotherNetteRectorProvider: ~
```
  
Quick solution, nice. But how to share common code between similar RectorProvider? Duplicate and decouple services? And what is the point of provider, if it can only add 1 new Rector? Why not register them in `rectors:` directly like the others?

And flow of *WTFs* is coming at you.

## 3. Does Collector Scale?

We now have one-to-one `RectorColletor` implementation:

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

### What we want?

- drop that expression language magic
- support one-to-many case
- have clear API with priority in PHP code rather in `CompilerPass` or config
- have typehint control 

### Drop that Expression Language Magic

This is why Collector pattern is so awesome. You have single place to solve all your problems (or at least those 2 we have):  

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

And cleanup `CompilerPass`:
 
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
-               sprintf('service("%s").provide()', )
-           );
-           $rectorCollectorDefinition->addMethodCall('addRectorProvider', [$providedRector]);
+           $rectorCollectorDefinition->addMethodCall('addRectorProvider', ['@' . $rectorProviderDefinition->getClass()]);
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

## 5. Add Tagging?
  
We forget tagging, right? The most [spread useless code in Symfony configs](/blog/2017/02/12/drop-all-service-tags-in-your-nette-and-symfony-applications/).

<blockquote>
    Perfection is achieved, not when there is nothing more to add, but when there is nothing left to take away.
    <note>Antoine de Saint-Exupery</note> 
</blockquote>

Why would you add it and where? I don't take arguments like "well, it's historical reasons". Try to convince me though if you're sure about it's advantages. 


## To sum up

@todo

- yes
- no
- yes
- no
- yes


### git story over git history
 
- todo - i could show you the final commit or evern worse - just the final files of (former) [`RectorCollector`](https://github.com/rectorphp/rector/blob/7305cf40d22cd1f241fcf8dcdebdc22b935616d8/src/NodeTraverser/RectorNodeTraverser.php) and [`RectorProvidersCompilerPass`](https://github.com/rectorphp/rector/blob/7305cf40d22cd1f241fcf8dcdebdc22b935616d8/packages/RectorBuilder/src/DependencyInjection/CompilerPass/RectorProvidersCompilerPass.php)
- but that would teach nothing, because only when we fail, we learn something new

- same applied for git history... :)
 
<br><br>
 
Happy collecting!