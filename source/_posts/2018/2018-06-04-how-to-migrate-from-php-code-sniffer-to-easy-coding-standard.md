---
id: 111
title: "How to Migrate From PHP_CodeSniffer to EasyCodingStandard in 7 Steps"
perex: |
    Last year, I helped [Shopsys Coding Standards](https://github.com/shopsys/coding-standards) and [LMC PHP Coding Standard](https://github.com/lmc-eu/php-coding-standard) to migrate from PHP_CodeSniffer to EasyCodingStandard.
    <br><br>
    There are **a few simple A → B changes**, but one has to know about them or will get stuck.
    <br><br> 
    **Do you also use PHP_CodeSniffer and give it EasyCodingStandard a try**? Today we look at how to migrate step by step.
tweet: "New Post on my Blog: How to Migrate From #PHP_CodeSniffer to EasyCodingStandard in 7 Step #ecs #codingstandard #ci"
---

ECS is a tool build on Symfony 3.4 components that combines PHP_CodeSniffer and PHP CS Fixer. It's super easy to start to use from scratch:

```bash
composer require symplify/easy-coding-standard --dev 
vendor/bin/ecs check src --level psr12 # yes 12! 
```

But what if you already have PHP_CodeSniffer on your project and want to switch?

## 1. From String Codes to Autocompleted Classes

You probably use string references to sniffs in your `*.xml` configuration for PHP_CodeSniffer. You need to remember them, copy paste them and **copy-paste them right**.

```xml
<rule ref="Generic.Comenting.DocComment"/>
```

That can actually cause typos like:

```diff
-<rule ref="Generic.Comenting.DocComment"/>
+<rule ref="Generic.Commenting.DocComment"/>
```

How to do that in EasyCodingStandard? Copy past the last name `DocComment`, add a "Sniff" and `:`:

```yaml
# ecs.yml
services:
    DocCommentSniff<cursor-here>:
```

Then hit the "ctlr" + "space" for autocomplete in PHPStorm. That way [Symfony plugin](https://plugins.jetbrains.com/plugin/7219-symfony-plugin) will autocomplete the class for you:

```yaml
# ecs.yml
services:
    PHP_CodeSniffer\Standards\Generic\Sniffs\Commenting\DocCommentSniff:
```

<img src="https://github.com/Symplify/EasyCodingStandard/raw/master/docs/yaml-autocomplete.gif">

No more typos with strong over string typing.

## 2. From `@codingStandardsIgnoreStart` to `skip` Parameter

If you'd like to skip nasty code from being analyzed, you'd use `@codingStandardsIgnoreStart` in PHP_CodeSniffer. 

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

To skip this in EasyCodingStandard just ass skip parameter:

```yaml
paramters:
    skip:
        PHP_CodeSniffer\Standards\Squiz\Sniffs\Strings\DoubleQuoteUsageSniff:
            - 'packages/framework/src/Component/Constraints/EmailValidator.php'
```

Do you have more such cases?

```yaml
paramters:
    skip:
        PHP_CodeSniffer\Standards\Squiz\Sniffs\Strings\DoubleQuoteUsageSniff:
            - 'packages/framework/src/Component/Constraints/EmailValidator.php'
            - 'packages/framework/src/Component/Constraints/NameValidator.php'
            - 'packages/framework/src/Component/Constraints/SurnameValidator.php'
```

You don't have to list them all like a typing monkey. Just use `fnmatch()` format instead:

```yaml
paramters:
    skip:
        PHP_CodeSniffer\Standards\Squiz\Sniffs\Strings\DoubleQuoteUsageSniff:
            - '*packages/framework/src/Component/Constraints/*Validator.php'
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

In EasyCodingStandard, we put that again under `skip` parameter:

```yaml
# ecs.yml
paramters:
    skip:
        PHP_CodeSniffer\Standards\Generic\Sniffs\Commenting\DocCommentSniff.ContentAfterOpen: ~
```

For all other `skip` options, [see README](https://github.com/symplify/easyCodingStandard/#ignore-what-you-cant-fix).

<br>

In case you need to **skip the whole sniff**: 

```xml
<?xml version="1.0" encoding="UTF-8"?>
<ruleset name="ruleset">
    <rule ref="Generic.Commenting.DocComment">
        <severity>0</severity>
    </rule>
</ruleset>
```

or

```xml
<?xml version="1.0" encoding="UTF-8"?>
<ruleset name="ruleset">
    <rule ref="ruleset.xml">
        <exclude name="Generic.Commenting.DocComment"/>
	</rule>
</ruleset>
```

Put it under `exclude_checkers`:

```yaml
# ecs.yml
parameters:
    exclude_checkers:
        PHP_CodeSniffer\Standards\Generic\Sniffs\Commenting\DocCommentSniff: ~
```


## 4. From XML to YML Config Paths

These names are looked for in the root diretory by PHP_CodeSniffer:

```bash
- .phpcs.xml
- phpcs.xml
- .phpcs.xml.dist
- phpcs.xml.dist
```

And these by EasyCodingStandard:

```bash
- ecs.yml
- ecs.yaml
- easy-coding-standard.yml
- easy-coding-standard.yaml
```

What about non-default locations or names?

From:

```bash
vendor/bin/phpcs /path/to/project --standard=custom/location.xml
```

to:

```bash
vendor/bin/phpcs check /path/to/project --config custom/location.yml
```

## 5. Configuring Sniff Values

From XML configuration in PHP_CodeSniffer:

```xml
<?xml version="1.0" encoding="UTF-8"?>
<ruleset name="ruleset">
    <rule ref="Generic.Metrics.CyclomaticComplexity">
        <properties>
            <property name="complexity" value="13"/>
            <property name="absoluteComplexity" value="13"/>
        </properties>
    </rule>
</ruleset>
```

to YML parameters in EasyCodingStandard:

```yaml
# ecs.yml
services:
    PHP_CodeSniffer\Standards\Generic\Sniffs\Metrics\CyclomaticComplexitySniff:
        complexity: 13
        absoluteComplexity: 13
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
CI server either passes or not. The rule is required and respected or removed. Simple, clear and without any confusion.

Saying that you don't need to fill values for warning properties:

```diff
 # ecs.yml
 services:
     PHP_CodeSniffer\Standards\Generic\Sniffs\Metrics\CyclomaticComplexitySniff:
-        complexity: 13
         absoluteComplexity: 13
```


## 7. From Beautifier to `--fix` option

Do you need to fix the code? From 2 commands in PHP_CodeSniffer:

```bash
vendor/bin/phpcs /path/to/project --standard=custom/location.xml
vendor/bin/phpcbf /path/to/project --standard=custom/location.xml
```

to 1 in EasyCodingStandard:

```bash
vendor/bin/phpcs check /path/to/project --config custom/location.yml
vendor/bin/phpcs check /path/to/project --config custom/location.yml --fix
```

<br>

### Give it a Try...

...and you won't regret it. Sylius, LMC, Shopsys, Nette and SunFox did and never came back.

<br>

Did I forget a step that you had to fight with? **Please, let me know in the comments or just send PR to this post to add it**, so we help other readers. 

<br> 
<br> 

In the next post we look on how to migrate from PHP CS Fixer!
