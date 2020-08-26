---
id: 51
title: "5 Useful Rules From Symplify Coding Standard"
perex: |
     <a href="https://github.com/symplify/coding-standard">Symplify Coding Standard</a> was born from Zenify, back from the days I was only Nette programmer. It focuses on <strong>maintainability and clean architecture</strong>. I try to make them simple: <strong>each of them does one job</strong>.
     <br><br>
     With over 108 000 downloads I think I should write about 5 of them you can use in your projects today.
tweet: "Add Final Interface, Class Constant fixer and more to your Coding Standard #php #architecture #php_codesniffer"

updated_since: "December 2018"
updated_message: |
    Updated with **EasyCodingStandard 5**, Neon to YAML migration and `checkers` to `services` migration.

deprecated_since: "September 2018"
deprecated_message: |
    [Symplify 5.0](https://github.com/symplify/symplify/tree/v5.0.0) was released and with that, many checkers were replaced by better ones.

    Checkers 2, 4 and 5 were replaced by `SlamCsFixer\FinalInternalClassFixer` - **class is either final or abstract**.

    `@inject` refactoring was replaced by `AnnotatedPropertyInjectToConstructorInjectionRector` from [Rector](https://github.com/rectorphp/rector).
---

I wrote about [Object Calisthenics](/blog/2017/06/26/php-object-calisthenics-rules-made-simple-version-3-0-is-out-now/) few weeks ago - they are very strict and not very handy if you're beginner in coding standard worlds.

**Symplify Coding standard is complete opposite.** You can start with 1st checker today and your code will be probably able to handle it. It's combination of 40+ Code Sniffer Sniffs, PHP CS Fixer Fixers and PHPStan rules.

The simplest would be...

### 1. Array property should have default value `[]` to prevent undefined array issues

<em class="fas fa-lg fa-times text-danger"></em>


```php
class SomeClass
{
    /**
     * @var string[]
     */
    public $apples:

    public function run()
    {
        foreach ($this->apples as $mac) {
            // ...
        }
    }
}
```

<em class="fas fa-lg fa-check text-success"></em>

```php
class SomeClass
{
    /**
     * @var string[]
     */
    public $apples = [];
}
```

**Use it**:

```php
<?php

// ecs.php

declare(strict_types=1);

use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Symplify\CodingStandard\Fixer\Property\ArrayPropertyDefaultValueFixer;

return static function (ContainerConfigurator $containerConfigurator): void {
    $services = $containerConfigurator->services();

    $services->set(ArrayPropertyDefaultValueFixer::class);
};

```

### 2. Final Interface

**Note: this Symplify rule was replace by more advanced Rector rule, that also checks for child classes.**

Once I read [When to declare classes final](https://ocramius.github.io/blog/when-to-declare-classes-final) by [Marco Pivetta](http://ocramius.github.io) with **tl;dr;**:

<blockquote class="blockquote text-center mt-5 mb-5">
    "Make your classes always final, if they implement an interface, and no other public methods are defined."
</blockquote>

I was working at [Lekarna.cz](https://www.lekarna.cz) in that time (finally shipped in the beginning of August, congrats guys!) and we used a lot of interfaces and had lots of code reviews. **So I made a sniff to save us some work.**

<em class="fas fa-lg fa-times text-danger"></em>

```php
class SomeClass implements SomeInterface
{
}
```

<em class="fas fa-lg fa-check text-success"></em>

```php
final class SomeClass implements SomeInterface
{
}
```

**Use it**

```bash
composer require rector/rector --dev
```

```php
<?php

// rector.php

declare(strict_types=1);

use Rector\SOLID\Rector\Class_\FinalizeClassesWithoutChildrenRector;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

return static function (ContainerConfigurator $containerConfigurator): void {
    $services = $containerConfigurator->services();
    $services->set(FinalizeClassesWithoutChildrenRector::class);
};
```

and run [Rector](https://github.com/rectorphp/rector):

```bash
vendor/bin/rector process src
```

### ~~4. Equal Interface~~

**Note: this rule was removed, as very un-reliable.**

~~What happens if you implement and interface and add few extra public methods?~~

### Sold? Try Them

They are used the best with [EasyCodingStandard](/blog/2017/08/07/7-new-features-in-easy-coding-standard-22/):

```bash
composer require --dev symplify/easy-coding-standard symplify/coding-standard
```

Check your code:

```bash
vendor/bin/ecs check --set psr12
```

Fix your code:

```bash
vendor/bin/ecs check --set psr12 --fix
```

Let me know how much errors will you find in the comments. I dare you to get to 0! :)

## Rest of the Rules

You can find more rules like Abstract Class, Exception, Trait and Interface naming, indexed array indentation, Controllers with 1 method or invoke and so on in [README](https://github.com/symplify/coding-standard).

<br>

Happy coding!
