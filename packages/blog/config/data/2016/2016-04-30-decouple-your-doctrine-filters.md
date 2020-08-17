---
id: 8
title: Decouple Your Doctrine Filters
perex: "Doctrine filters are powerful tool. Yet their registration and management are bit overcomplicated. Today I will show you how to decouple them to standalone services that can take care of everything you need."

deprecated_since: "March 2017"
deprecated_message: |
    I have deprecated this package, because I see Doctrine ORM as over-complex tool not useful for new projects - **[read a post here](/blog/2017/03/27/why-is-doctrine-dying)**.

    It's been revived by Tomáš Pilař on [Lekarna/DoctrineFilters](https://github.com/Lekarna/DoctrineFilters) though.
---

## Standard Process to Enable Filter

If you don't know Doctrine Filters, [KnpUniversity](https://knpuniversity.com) has very nice, short and funny tutorial about them. [Go check it](https://knpuniversity.com/screencast/doctrine-queries/filters), I'll wait here...

> Are you busy and smart? Just check [slides 13 to 31](https://www.slideshare.net/rosstuck/extending-doctrine-2-for-your-domain-model-13257781/13) from [@RossTuck](https://twitter.com/rosstuck)'s presentation about cool features of Doctrine.

So now you know, that to enable filter in Symfony you have to:

1. register them manually under DoctrineBundle configuration (in one global config file `app/config/config.yml`)
2. get Doctrine's EntityManager in Controller
3. get filter by it's name previously defined in `app/config/config.yml`
4. enable it

You have to do all these steps just to turn something on. Imagine you'd have to do this for every Voter, Command or EventSubscriber.


## Could We Make It Easier?

In the tutorial from KnpUniversity, there is way to skip enabling filters in controller - by creating own [BeforeRequestListener](https://knpuniversity.com/screencast/doctrine-queries/filters#enabling-a-filter-globally).

It's quite nice, but it just moves all these steps from controller's responsibility somewhere else. So you have to enable them again, just in different place.

Let's say this is fine enough. **But what about modular applications with own per module filters?** Not so easy.


## Minimal Viable Product

For better understanding what is really important, let's break down the purpose of the filter.

- it's a **piece of code in class that must inherit from `Doctrine\ORM\Query\Filter\SQLFilter`**
- it **decorates SQL queries** with custom code
- **sometimes it's conditional** - you want it to be enabled or disabled

That's all it does. Everything else is just syntax sugar, glue code or entry point to work with them.

Saying that, we can **get rid of Controllers, Subscribers, DoctrineBundle, `app/config/config.yml`** and yet still make use of them.


## Decouple Your Doctrine Filter... to Service

When we remove everything we don't need, we could end up with simple service:

```php
use Doctrine\ORM\Mapping\ClassMetadata;
use Symplify\DoctrineFilters\Contract\Filter\FilterInterface;

final class ActiveFilter implements FilterInterface
{
    /**
     * {@inheritdoc}
     */
    public function addFilterConstraint(ClassMetadata $entity, $alias)
    {
        return "$alias.active = 1";
    }
}
```

And register it as service:

```yaml
# Resoureces/config.yml
services:
    module.softdeletable_filter:
        class: SoftdeletableFilter
```

That's all we really need to do.

## Decoupling of Doctrine Filter in 4 steps

This is already possible thanks to [Lekarna/ModularDoctrineFilters](https://github.com/Lekarna/DoctrineFilters) package.

Let's try it together!

### 1. Install package

```bash
composer require symplify/modular-doctrine-filters
```

### 2. Register Bundle

```php
// app/AppKernel.php
class AppKernel extends Kernel
{
    public function registerBundles()
    {
        $bundles = [
            new Symplify\ModularDoctrineFilters\SymplifyModularDoctrineFiltersBundle(),
            // ...
        ];
    }
}
```

### 3. Create Service

```php
// src/SomeBundle/Doctrine/Filter/SoftdeletableFilter.php
namespace SomeBundle\Doctrine\Filter;

use Symplify\DoctrineFilters\Contract\Filter\FilterInterface;

final class SoftdeletableFilter implements FilterInterface
{
    /**
     * {@inheritdoc}
     */
    public function addFilterConstraint(ClassMetadata $entity, $alias)
    {
        if ($entity->getReflectionClass()->hasProperty('isDeleted')) {
            // or another condition to integrate enable/disable process
            return "$alias.isDeleted = 0";
        }
        return '';
    }
}
```

> This could be filter for [Softdeletable](https://github.com/KnpLabs/DoctrineBehaviors#softDeletable) from [DoctrineBehaviors](https://github.com/KnpLabs/DoctrineBehaviors).

### 4. Register it as a service to your module

```yaml
# src/SomeBundle/Resources/config/services.yml
services:
    some_module.softdeletable_filter:
        class: SomeBundle\Doctrine\Filter\SoftdeletableFilter
```

And that's it! Now your filter will be reflected in whole application.

For further use **just check Readme for [Symplify/ModularDoctrineFilters](https://github.com/Symplify/ModularDoctrineFilters)**.


## What Have You Learned Today?

- that Doctrine Filters can decorate every query in your application from single place
- that Doctrine Filter is basically just an object that might add some code to query
- **that you can add filter via simple service with [Symplify/ModularDoctrineFilters](https://github.com/Symplify/ModularDoctrineFilters)**

If you have some tips how to this simpler or want to share your experience with filters, just let me know below.

Happy coding!
