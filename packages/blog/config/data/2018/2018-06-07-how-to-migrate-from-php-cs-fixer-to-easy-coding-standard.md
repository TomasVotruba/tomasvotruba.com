---
id: 112
title: "How to Migrate From PHP CS Fixer to EasyCodingStandard in 6 Steps"
perex: |
    We looked at how to migrate from PHP_CodeSniffer to Easy Coding Standard on Monday. But what if your weapon of choice is PHP CS Fixer and you'd to run also some sniffs?
    <br>
    There are **a few simple A → B changes**, but one has to know about them or will get stuck. Let's learn about them.
tweet: "New Post on my Blog: How to Migrate From PHP CS Fixer to EasyCodingStandard in 6 Steps #ecs #codingstandard #ci"

updated_since: "August 2020"
updated_message: |
    Updated ECS YAML to PHP configuration since **ECS 8**.
---

ECS is a tool build on Symfony 3.4 components that [combines PHP_CodeSniffer and PHP CS Fixer](/blog/2017/05/03/combine-power-of-php-code-sniffer-and-php-cs-fixer-in-3-lines/). It's super easy to start to use from scratch:

```bash
composer require symplify/easy-coding-standard --dev
vendor/bin/ecs check src --set psr12
```

But what if you already have PHP CS Fixer on your project and want to switch?

## 1. From String Codes to Autocompleted Classes

You use string references like `strict_types` in your `.php_cs` file. You need to remember them, [copy paste them from README](https://github.com/friendsofphp/php-cs-fixer) and **copy-paste them right**.

```php
return PhpCsFixer\Config::create()
    ->setRules([
        'strict_types' => true,
    ])
    ->setFinder($finder);
```

That can actually cause typos like:

```diff
 return PhpCsFixer\Config::create()
     ->setRules([
-        'strict_types' => true,
+        'declare_strict_types' => true,
     ])
     ->setFinder($finder);
```

How to do that in ECS? Copy paste the fixer name, capitalize first letter and remove `_`:

Then hit the "ctrl" + "space" for class autocomplete in PHPStorm (it works even now when I write this post in markdown, nice!).

```php
<?php

// ecs.php

declare(strict_types=1);

use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

return static function (ContainerConfigurator $containerConfigurator): void {
    $services = $containerConfigurator->services();
    $services->set(PhpCsFixer\Fixer\Strict\DeclareStrictTypesFixer::class);
};
```

No more typos with *strong* over *string typing*.

## 2. From `notPath()` to `skip` Parameter

If you'd like to skip nasty code from being analyzed, you'd probably use this in PHP CS Fixer.

```php
$finder = PhpCsFixer\Finder::create()
    ->exclude('somedir')
    ->notPath('my-nasty-dirty-file.php')
    ->in(__DIR__);
```

Do you need `DeclareStrictTypesFixer` to skip this file? Sorry, PHP CS Fixer will skip it for ever rule.

ECS solves this common case - to skip a file, just use `skip` parameter:

```php
<?php

// ecs.php

declare(strict_types=1);

use PhpCsFixer\Fixer\Strict\DeclareStrictTypesFixer;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Symplify\EasyCodingStandard\Configuration\Option;

return static function (ContainerConfigurator $containerConfigurator): void {
    $parameters = $containerConfigurator->parameters();

    $parameters->set(Option::SKIP, [
        DeclareStrictTypesFixer::class => [
            __DIR__ . '/my-nasty-dirty-file.php',
            // you can add more files
            __DIR__ . '/Legacy/too-legacy-to-look-at.php',

            // or directories
            __DIR__ . '/Legacy',

            // or mask paths with fnmatch()
            __DIR__ . '/*/Command',
        ]
    ]);
};
```

Do you really want to skip **1 fixer** for all files?

```php
<?php

// ecs.php

declare(strict_types=1);

use PhpCsFixer\Fixer\Strict\DeclareStrictTypesFixer;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Symplify\EasyCodingStandard\Configuration\Option;

return static function (ContainerConfigurator $containerConfigurator): void {
    $parameters = $containerConfigurator->parameters();

    $parameters->set(Option::SKIP, [
        DeclareStrictTypesFixer::class => null,
    ]);
};
```

For all other `skip` options, [see README](https://github.com/symplify/easy-coding-standard/#ignore-what-you-cant-fix).

## 3. From `.php_cs` to PHP Config

PHP CS Fixer looks for `.php_cs` file in the root directory by default.

**And ECS looks for `ecs.php`**

What about non-default locations or names?

From:

```bash
vendor/bin/php-cs-fixer fix /path/to/project --config=custom/location.yml --dry-run
```

**to:**

```bash
vendor/bin/ecs check /path/to/project --config custom/location.php
```

## 4. Configuring Fixer Values

From PHP configuration in PHP CS Fixer:

```php
return PhpCsFixer\Config::create()
    ->setRules([
        'array_syntax' => ['syntax' => 'short'],
    ])
    ->setFinder($finder);
```

**to explicit Symfony service parameters in EasyCodingStandard:**

```php
<?php

// ecs.php

declare(strict_types=1);

use PhpCsFixer\Fixer\ArrayNotation\ArraySyntaxFixer;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

return static function (ContainerConfigurator $containerConfigurator): void {
    $services = $containerConfigurator->services();

    $services->set(ArraySyntaxFixer::class)
        ->call('configure', [[
            'syntax' => 'short'
        ]]);
};
```

Nice and clear!

## 5. From no `--dry-run` to `--fix` option

From PHP CS Fixer:

```bash
vendor/bin/php-cs-fixer fix /path/to/project --dry-run
vendor/bin/php-cs-fixer fix /path/to/project
```

to EasyCodingStandard equivalent:

```bash
vendor/bin/ecs check /path/to/project
vendor/bin/ecs check /path/to/project --fix
```

### 6. From `@Rules` to `imports`

Do you like to use standards like PSR-2 or even [PSR-12](/blog/2018/04/09/try-psr-12-on-your-code-today/)?

From `@strings` in PHP CS Fixer:

```php
$config = PhpCsFixer\Config::create()
    ->setRules([
        '@PSR2' => true,
    ]);
```

**to autocompleted set constant in PHP file in ECS**:

```php
<?php

// ecs.php

declare(strict_types=1);

use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Symplify\EasyCodingStandard\Configuration\Option;use Symplify\EasyCodingStandard\ValueObject\Set\SetList;

return static function (ContainerConfigurator $containerConfigurator): void {
    $parameters = $containerConfigurator->parameters();
    $parameters->set(Option::SETS, [
        SetList::PSR_12,
    ]);
};
```

<br>

### Give it a Try...

...and you won't regret it. Sylius, [PestPHP](https://github.com/pestphp/drift), LMC, Shopsys and Nette did and never came back.

<br>

Did I forget a step that you had to fight with? **Please, let me know in the comments or just send PR to this post to add it**, so we help other readers.

<br>
<br>

Happy code sniffixing!
