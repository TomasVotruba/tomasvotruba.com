---
id: 206
title: "Don't Give Up Your PHP Code for Compiler Passes so Easily"
perex: |
    Sometimes you need to achieve very simple operation - e.g. get all services of a certain type in a certain order or key name. When we start to use a PHP framework, we tend to underestimate our PHP skills and look for *the framework* way.
    <br><br>
    **Who cares if we use 50 lines in 3 files PHP files and 1 YAML file instead of 1 factory in 20 lines.** We're cool!
tweet: "New Post on #php 🐘 blog: Don't Give up Your PHP Code for Compiler Passes so Easily      #symfony #laravel #nettfw"
---

This mini-series started in [Why Config Coding Sucks](/blog/2019/02/14/why-config-coding-sucks/). **There we learned to move ~~weakly~~ un-typed strings to strict-typed PHP code**. It's not only about YAML or NEON files, but about any config-like syntax in general (XML, in...).

Today we move to PHP-only land, that suffers a similar problem.

## What We Talk About?

So we talk about [Compiler Passes in Symfony](https://symfony.com/doc/current/service_container/compiler_passes.html)? Well, yes and no. Not only about them, but about any PHP code that moves around services in the DI container.

- in Nette it's a class that extends [`CompilerExtension`](/blog/2017/02/15/minimalistic-way-to-create-your-first-nette-extension/)
- in Symfony it's class that implements [`CompilerPassInterface`](https://github.com/symfony/symfony/blob/fba11b4dc34e7c589d8c30d5b6a090387d52e648/src/Symfony/Component/DependencyInjection/Compiler/CompilerPassInterface.php) or extends [`Extension`](https://github.com/symfony/symfony/blob/fba11b4dc34e7c589d8c30d5b6a090387d52e648/src/Symfony/Component/DependencyInjection/Extension/Extension.php)
- in Laravel it can be [service providers](https://laravel.com/docs/master/providers)

They have their useful use-cases, but people tend them to use *as a bazooka to mouse*. Just look at [answers under this StackOverflow question](https://stackoverflow.com/questions/54590981/symfony-4-how-to-access-the-service-from-controller-without-dependency-injectio).

<br>

Let's look at an example that is not far from the reality of your work with. But still it's only an example, it could be apples in a basket instead.

## Make Price Calculation easy to Extend and Maintain without Changing it

Based on [my experience with my clients](/trainings), this is the biggest problem in e-commerce projects. The ideal wishes of company owners clash with limits programmers and architecture:

- "The price calculation must be ready for use"
- "I need to add different price to product B if they're in combination with product A"
- "It must be easy to maintain"
- "The business must be able to update dependency on our code"
- "We can't predict how the price will develop"

~~This not possible!~~ - How can we do it as close as possible now?

Let's say the solution is fairly easy. Same as [Voters are to Security](https://symfony.com/doc/current/security/voters.html), we introduce 1 service `PriceCalculator` that collects all the little one `PriceModifierInterface`.

How would such implementations look like in *framework-way*?

### 1. In Symfony

```php
<?php

namespace App\DependencyInjection;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

final class PriceCompilerPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $containerBuilder)
    {
        $priceCalculator = $containerBuilder->get(PriceCalculator::class);

        foreach ($containerBuilder->findTaggedServiceIds('price_modifier') as $service => $tags) {
            $priceCalculator->addMethodCall('add', [new Reference($service)]);
        }
    }
}
```

**Again, we need to create some legacy code that is hard to maintain:**

- add tagging in extension/bundle or better type resolution ❌
- register this in Kernel ❌
- remember the tag name ([don't remember anything](/blog/2018/08/27/why-and-how-to-avoid-the-memory-lock)/) ❌
- use the tag everywhere (YAML) ❌
- vendor-lock the metadata in the config (YAML) ❌
- people maintaining project after you leave have to learn this *Symfony way* ❌

<br>

### 2. In Nette

```php
<?php

namespace App\DI;

use Nette\DI\CompilerExtension;

final class PriceExtension extends CompilerExtension
{
    public function beforeCompile()
    {
        $containerBuilder = $this->getContainerBuilder();
        $priceCalculator = $containerBuilder->get(PriceCalculator::class);

        $priceModifiers = $containerBuilder->findByType(PriceModifierInterface::class);
        foreach ($priceModifiers as $service) {
            $priceCalculator->addSetup('add', [$service]);
        }
    }
}
```

**Also, we create legacy code that is hard to maintain:**

- register extension to a config (Neon) ❌
- vendor-lock the metadata in the config (Neon) ❌
- people maintaining project after you leave have to learn this *Nette way* ❌

I need to take a break, my brain is tired just **by making up this complicated and non-sense code**. I mean, I used to write this code in my every project for 5 years in Symfony and Nette projects, because it was "the best practice" and I didn't question it, but **there was always something scratching in the back of my head**.

<br>
<br>

## Keep Simple Things Simple

Now imagine you've ended in a train crash, hit your head and forget all the frameworks you know. All you have left is actually the best you can achieve in any mastery - a [mind of the begginer](https://zenhabits.net/beginner).

- "How would you get all services of a certain type in a certain order or key name?"

In our specific example:

- "How would you get all `PriceModifierInterface` services into `PriceCalculator` sorted by priority?"

```php
<?php

final class PriceCalculatorFactory
{
    /**
     * @var PriceModifierInterface[]
     */
    private $priceModifiers = [];

    /**
     * @param PriceModifierInterface[] $priceModifiers
     */
    public function __construct(array $priceModifiers)
    {
        $this->priceModifiers = $priceModifiers;
    }

    public function create(): PriceCalculator
    {
        $priceModifiersByPriority = [];
        foreach ($this->priceModifiers as $priceModifier) {
            $priority = $priceModifier->getPriority(); // this could be "getKey()" or any metadata
            $priceModifiersByPriority[$priority] = $priceModifier;
        }

        // sort them in any way
        ksort($priceModifiersByPriority);

        return new PriceCalculator($priceModifiersByPriority);
    }
}
```

In some framework we have still have to add 1 config vendor-lock ❌ :

```yaml
services:
    App\Price\PriceCalculator:
        factory: ['@App\Price\PriceCalculatorFactory', 'create']
```

I use [compiler pass](https://github.com/symplify/package-builder#do-not-repeat-simple-factories) for now, but if you know how to remove it, let me know.

How we get `$priceModifiers` is not that important now, it's [an implementation detail](/blog/2018/11/12/will-autowired-arrays-finally-deprecate-tags-in-symfony-and-nette/).

## Durable & Readable

The important thing is we got a code that:

- is **strictly typed** ✅
- we all understand it ✅
- will not be affected by any syntax/architecture changes in our favorite framework ✅
- can be checked by **coding standard tools** ✅
- can be analysed by **static analysis tools** ✅
- and refactored by **instant upgrade tools** ✅

<br>

Happy coding!
