---
id: 112
title: "How to Migrate From PHP&nbsp;CS&nbsp;Fixer to&nbsp;ECS&nbsp;in&nbsp;6&nbsp;Steps"
perex: |
    We looked at how to migrate from PHP_CodeSniffer to Easy Coding Standard on Monday. But what if your weapon of choice is PHP CS Fixer and you'd to run also some sniffs?

    There are **a few simple A → B changes**, but one has to know about them or will get stuck. Let's learn about them.

updated_since: "January 2023"
updated_message: |
    Updated with ECS 12 and `ECSConfig::configure()` simple way to work with configs.
---

ECS is a PHP CLI tool that [combines PHP_CodeSniffer and PHP CS Fixer](/blog/2017/05/03/combine-power-of-php-code-sniffer-and-php-cs-fixer-in-3-lines/). It's easy to use from scratch:

```bash
composer require symplify/easy-coding-standard --dev
```

ECS uses simple PHP config format:

```php
// ecs.php
use Symplify\EasyCodingStandard\Config\ECSConfig;

return ECSConfig::configure()
    ->withPaths([__DIR__ . '/src');
    ->withPreparedSets(psr12: true);
```

And runs as CLI command:

```bash
vendor/bin/ecs
```

Do you use PHP-CS-Fixer on your project and want to switch? Let's jump right into it:

## 1. From String Codes to Autocompleted Classes

You use string references like `strict_types` in your `.php_cs` file. You need to remember them, [copy paste them from README](https://github.com/PHP-CS-Fixer/PHP-CS-Fixer/blob/master/doc/rules/index.rst) and copy-paste them correctly.

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

How to do that in ECS? We use FQN class names instead. So you can check the rule class right from your IDE.


```php
// ecs.php
use Symplify\EasyCodingStandard\Config\ECSConfig;
use PhpCsFixer\Fixer\Strict\DeclareStrictTypesFixer;

return ECSConfig::configure()
    ->withRules([
        DeclareStrictTypesFixer::class
    ]);
```

No more typos with *strong native class reference*.

## 2. From `notPath()` to `withSkip()` Method

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
// ecs.php
use Symplify\EasyCodingStandard\Config\ECSConfig;
use PhpCsFixer\Fixer\Strict\DeclareStrictTypesFixer;

return ECSConfig::configure()
    ->withSkip([
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
```

Do you want to skip **1 fixer** for all files?

```php
// ecs.php
use Symplify\EasyCodingStandard\Config\ECSConfig;
use PhpCsFixer\Fixer\Strict\DeclareStrictTypesFixer;

return ECSConfig::configure()
    ->withSkip([
        DeclareStrictTypesFixer::class,
    ]);
```

[Check README](https://github.com/easy-coding-standard/easy-coding-standard#less-common-options) for more options to use in skip.

## 3. From `.php_cs` to PHP Config

PHP CS Fixer looks for `.php_cs` file in the root directory by default.

**And ECS looks for `ecs.php`**

<br>

What about non-default locations or names?

From:

```bash
vendor/bin/php-cs-fixer fix /path/to/project --config=custom/location.php --dry-run
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

**to `withConfiguredRule()` method in ECS:**

```php
// ecs.php
use Symplify\EasyCodingStandard\Config\ECSConfig;
use PhpCsFixer\Fixer\ArrayNotation\ArraySyntaxFixer;

return ECSConfig::configure()
    ->withConfiguredRule(ArraySyntaxFixer::class, [
        'syntax' => 'short',
    ]);
```

Nice and clear!

## 5. From no `--dry-run` to `--fix` option

From PHP CS Fixer:

```bash
vendor/bin/php-cs-fixer fix --dry-run
vendor/bin/php-cs-fixer fix
```

to ECS equivalent:

```bash
vendor/bin/ecs check
vendor/bin/ecs check --fix
```

## 6. From `@Rules` to `withPhpCsFixerSets()` Method

Do you like to use standards like PSR-2 or even [PSR-12](/blog/2018/04/09/try-psr-12-on-your-code-today/)?

From `@strings` in PHP CS Fixer:

```php
$config = PhpCsFixer\Config::create()
    ->setRules([
        '@PSR2' => true,
    ]);
```

**to autocompleted method in ECS**:

```php
// ecs.php
use Symplify\EasyCodingStandard\Config\ECSConfig;

return ECSConfig::configure()
    ->withPhpCsFixerSets(psr12: true);
```

<br>

That's it.

<br>

Happy coding!
