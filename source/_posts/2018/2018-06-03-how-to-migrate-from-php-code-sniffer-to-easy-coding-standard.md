---
id: 111
title: "..."
perex: |
    ...
tweet: "New Post on my Blog: ..."
tweet_image: "..."
---


Recently I helped Shopsys to migrate from combinations of 3 to ECS:
https://github.com/shopsys/shopsys/pull/143/files

Pull requests :)


ECS is a tool that combines PHP_CodeSniffer and PHP CS Fixer. It's super easy to start to use from scratch:

```bash
composer require symplify/easy-coding-standard --dev 
vendor/bin/ecs check src --level psr12 # yes 12! 
```

But what if you already have PHP_CodeSniffer on your project and want to switch?

## 3 steps

### 1. From String Codes to Autocompleted Classes

In root `ruleset.xml` for PHP_CodeSniffer you can use string references to sniffs. You need to remember them, copy paste them and copy-paste them right.

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

And hit "cltr + space" for autocomplete in PHPStorm. That way Symfony plugin will autocomplete the class for you:

```yaml
# ecs.yml
services:
    PHP_CodeSniffer\Standards\Generic\Sniffs\Commenting\DocCommentSniff:
```

<img src="https://github.com/Symplify/EasyCodingStandard/raw/master/docs/yaml-autocomplete.gif">

No more typos with strong over string typing.

### 2. From `@codingStandardsIgnoreStart` to `skip` Parameter

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

```yamlSnif
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

### 3. From `<severity>0</severity>` to `skip` Parameter

Do you need to skip only 1 part of the sniff? In PHP_CodeSniffer:

```xml
<rule ref="Generic.Commenting.DocComment.ContentAfterOpen">
    <severity>0</severity>
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

In case you skip the whole sniff: 

```xml
<rule ref="Generic.Commenting.DocComment">
    <severity>0</severity>
</rule>
```

Put it under `exclude_checkers`:

```yaml
# ecs.yml
parameters:
    exclude_checkers:
        PHP_CodeSniffer\Standards\Generic\Sniffs\Commenting\DocCommentSniff: ~
```




### 4. Values

@todo

 <rule ref="Generic.Metrics.CyclomaticComplexity">
        <properties>
            <property name="complexity" value="13"/>
            <property name="absoluteComplexity" value="13"/>
        </properties>
        
        https://github.com/shopsys/coding-standards/pull/9/files#diff-28e7c9181fd509e70dc0f5ad1dc7b517