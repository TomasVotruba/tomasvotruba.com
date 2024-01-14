---
id: 111
title: "How to Migrate From PHP_CodeSniffer to ECS in 7 Steps"
perex: |
    Last year, I helped [Shopsys Coding Standards](https://github.com/shopsys/coding-standards) and [LMC PHP Coding Standard](https://github.com/lmc-eu/php-coding-standard) to migrate from PHP_CodeSniffer to ECS.


    There are **a few simple A → B changes**, but one has to know about them or will get stuck.


    **Do you also use PHP_CodeSniffer and give it EasyCodingStandard a try**? Today we look at how to migrate step by step.

updated_since: "January 2023"
updated_message: |
    Updated with ECS 12 and `ECSConfig::configure()` simple way to work with configs.
---

ECS is a tool build on Symfony components that [combines PHP_CodeSniffer and PHP CS Fixer](/blog/2017/05/03/combine-power-of-php-code-sniffer-and-php-cs-fixer-in-3-lines/). It's easy to use from scratch:

```bash
composer require symplify/easy-coding-standard --dev
```

ECS uses simple config:

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

But what if you already have PHP_CodeSniffer on your project and want to switch?

## 1. From String Codes to Autocompleted Classes

You probably use string references to sniffs in your `*.xml` configuration for PHP_CodeSniffer. You need to remember them, copy paste them and **copy-paste them right**.

```xml
<!-- phpcs.xml -->
<rule ref="Generic.Commenting.DocComment"/>
```

That can actually cause typos like:

```diff
-<rule ref="Generic.Commenting.DocComment"/>
+<rule ref="Generic.Commenting.DocComment"/>
```

How to do that in EasyCodingStandard? Copy paste the last name `DocComment` and add rule in `set()` method. Hit CTRL + Space and  PHPStorm will autocomplete class for you:

```php
// ecs.php
use Symplify\EasyCodingStandard\Config\ECSConfig;
use PHP_CodeSniffer\Standards\Generic\Sniffs\Commenting\DocCommentSniff::class;

return ECSConfig::configure()
    ->withRules([
        DocCommentSniff::class
    ]);
```

No more typos with strong over string typing.

## 2. From `@codingStandardsIgnoreStart` to `withSkip()` Method

If you'd like to skip some code from being analyzed, you'd use `@codingStandardsIgnoreStart` in PHP_CodeSniffer.

```php
#  packages/framework/src/Component/Constraints/EmailValidator.php

private function isEmail($value)
{
    // @codingStandardsIgnoreStart
    $atom = "[-a-z0-9!#$%&'*+/=?^_`{|}~]"; // RFC 5322 unquoted characters in local-part
    // @codingStandardsIgnoreEnd
}
```

One big cons of this is **that all sniffs will skip this code**, not just one. So even if here we need to only allow double quotes `"`, all other checks will miss it.

In ECS put this into `withSkip()` method:

```php
// ecs.php
use Symplify\EasyCodingStandard\Config\ECSConfig;
use PHP_CodeSniffer\Standards\Squiz\Sniffs\Strings\DoubleQuoteUsageSniff;

return ECSConfig::configure()
    ->withSkip([
        DoubleQuoteUsageSniff::class => [
            __DIR__ . '/packages/framework/src/Component/Constraints/EmailValidator.php',

            // or whole directory
            __DIR__ . '/packages/framework/src/Component',

            // or for mask directory
            __DIR__ . '/packages/*/src/Component',
        ]
    ]);
```

## 3. From `<severity>0</severity>` and `<exclude name="...">` to `skip` Parameter

Do you need to skip only 1 part of the sniff? In PHP_CodeSniffer:

```xml
<rule ref="Generic.Commenting.DocComment.ContentAfterOpen">
    <severity>0</severity>
</rule>
```

or

```xml
<rule ref="Generic.Commenting.DocComment">
    <exclude name="Generic.Commenting.DocComment.ContentAfterOpen"/>
</rule>
```

In ECS, we put that again under `skip()` parameter in format `<Sniff>.<CodeName>`:

```php
// ecs.php
use Symplify\EasyCodingStandard\Config\ECSConfig;
use PHP_CodeSniffer\Standards\Generic\Sniffs\Commenting\DocCommentSniff;

return ECSConfig::configure()
    ->withSkip([
        DocCommentSniff::class . '.ContentAfterOpen'
    ]);
```

[Check README](https://github.com/easy-coding-standard/easy-coding-standard#less-common-options) for more options to use in skip.

<br>

Do you **skip the whole sniff**?

```xml
<?xml version="1.0" encoding="UTF-8"?>
<ruleset name="ruleset">
    <rule ref="Generic.Commenting.DocComment">
        <severity>0</severity>
    </rule>

    <!-- or -->

    <rule ref="ruleset.xml">
        <exclude name="Generic.Commenting.DocComment"/>
    </rule>
</ruleset>
```

**Put it under `withSkip()` method:**

```php
// ecs.php
use Symplify\EasyCodingStandard\Config\ECSConfig;
use PHP_CodeSniffer\Standards\Generic\Sniffs\Commenting\DocCommentSniff;

return ECSConfig::configure()
    ->withSkip([
        DocCommentSniff::class,
    ]);
```

## 4. From XML to PHP Config Paths

These names are looked for in the root directory by PHP_CodeSniffer:

```bash
- .phpcs.xml
- phpcs.xml
- .phpcs.xml.dist
- phpcs.xml.dist
```

**And by ECS just plain `ecs.php` PHP file**

What about non-default locations or names?

From:

```bash
vendor/bin/phpcs /path/to/project --standard=custom/location.xml
```

**to:**

```bash
vendor/bin/ecs check /path/to/project --config custom/location.php
```

## 5. Configuring Sniff Values

From XML configuration in PHP_CodeSniffer:

```xml
<?xml version="1.0" encoding="UTF-8"?>
<ruleset name="ruleset">
    <rule ref="Generic.Metrics.CyclomaticComplexity">
        <properties>
            <property name="complexity" value="13"/>
        </properties>
    </rule>
</ruleset>
```

**to `withConfiguredRule()` method in ECS:**

```php
// ecs.php
use Symplify\EasyCodingStandard\Config\ECSConfig;
use PHP_CodeSniffer\Standards\Generic\Sniffs\Metrics\CyclomaticComplexitySniff;

return ECSConfig::configure()
    ->withConfiguredRule(CyclomaticComplexitySniff::class, [
        'complexity' => 13,
    ]);
```

## 6. From Severity and Warning to Just Errors

There are different levels in PHP_CodeSniffer. You can set severity, make sniff report as warning or as an error.

```xml
<?xml version="1.0" encoding="UTF-8"?>
<ruleset name="ruleset">
    <rule ref="Generic.Commenting.DocComment">
        <severity>5</severity>
    </rule>
</ruleset>
```

This complex matrix leveling lead to confused questions for many people:

- Is it a warning or is an accepted error?
- What is this warning even active when it doesn't fail CI?
- Why do we have an accepted error - is it like the tests that are allowed to fail?

And so on.

Thus these confusing options are not supported and EasyCodingStandard simplifies that to **errors only**
CI server either passes or not. **The rule is required and respected or removed. Simple, clear and without any confusion.**

Saying that you don't need to fill values for warning properties.

## 7. From Beautifier to `--fix` option

Do you need to fix the code? From 2 commands in PHP_CodeSniffer:

```bash
vendor/bin/phpcs /path/to/project
vendor/bin/phpcbf /path/to/project
```

...to 1 in ECS:

```bash
vendor/bin/ecs check /path/to/project
vendor/bin/ecs check /path/to/project --fix
```

That's it!

<br>

Happy coding!
