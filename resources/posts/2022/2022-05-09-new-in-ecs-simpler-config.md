---
id: 352
title: "New in ECS: Simpler Config"
perex: |
    ECS runs on Symfony container configuration to build the service model. While it brings automated autowiring, array autowiring, and native container features, the downside is that ECS configuration syntax is complex and talkative.


    We decided to simplify it so ECS is truly easy to use.

---

How do we configure ECS now?

```php
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Symplify\EasyCodingStandard\ValueObject\Option;

// ups, possible conflict with ContainerConfigurator
return static function (ContainerConfigurator $containerConfigurator): void {
    $parameters = $containerConfigurator->parameters();

    // too verbose params, constants and possible typo in param value
    $parameters->set(Option::PATHS, [[ // ups, "[[" typo
        __DIR__ . '/src/',
    ]]);

    $services = $containerConfigurator->services();
    $services->set(ArraySyntaxFixer::class);
};
```

<br>

Phew! Such complexity is confusing just to read and easy to make an error in.

## The new Way to Configure ECS

The new ECS version introduces its own `ECSConfig`object that wraps around Symfony configuration and makes setup even more straightforward.

<br>

It brings:

* full IDE autocomplete,
* isolation from Symfony - useful for Symfony projects
* validation of configuration on entry
* makes sure there are no duplicated rules in a single config that might lead to unexpected behavior

<br>

How does it look?

```php
use Symplify\EasyCodingStandard\Config\ECSConfig;
use Symplify\EasyCodingStandard\ValueObject\Set\SetList;

return static function (ECSConfig $ecsConfig): void {
    $ecsConfig->paths([
        __DIR__ . '/src',
    ]);

    $ecsConfig->rule(ArraySyntaxFixer::class);

    $ecsConfig->sets([SetList::PSR_12]);
};
```

## How to upgrade to `ECSConfig`?

First, upgrade your ECS to 10.2 to enjoy this feature. Then, replace `ContainerConfigurator` in your `ecs.php` with `ECSConfig`. And then? Use your IDE - **autocomplete methods** will lead you:

<img src="/assets/images/posts/2022/ecs_config.gif" class="img-thumbnail mb-2" style="max-width: 45em">

<br>

Do you configure your rules? Use `ruleWithConfiguration()` method:

```php
    $ecsConfig->ruleWithConfiguration(ArraySyntaxFixer::class, [
        'syntax' => 'short',
    ]);
```

<br>

What if you miss upgrading an imported config? Don't worry. We've added a **safe checker** that goes through provided configs and warns you about an old configuration.

<img src="/assets/images/posts/2022/ecs_warning.png" class="img-thumbnail mb-2" style="max-width: 45em">

So you won't miss any of your configs to upgrade.

<br>

Happy coding!
