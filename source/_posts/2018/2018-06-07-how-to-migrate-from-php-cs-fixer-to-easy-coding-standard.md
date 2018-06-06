---
id: 112
title: "How to Migrate From PHP CS Fixer to EasyCodingStandard in 5 Steps"
perex: |
    We looked on how to migrate from PHP_CodeSniffer to Easy Coding Standard on Monday. But what if your weapon of choice is PHP CS Fixer and you'd to run also some sniffs?  
    <br>
    <br>
    There are **a few simple A → B changes**, but one has to know about them or will get stuck. Let's learn about them.
tweet: "New Post on my Blog: How to Migrate From PHP CS Fixer to EasyCodingStandard in 5 Steps #ecs #codingstandard #ci"
related_items: [37, 49, 86, 111]
---

ECS is a tool build on Symfony 3.4 components that [combines PHP_CodeSniffer and PHP CS Fixer](/blog/2017/05/03/combine-power-of-php-code-sniffer-and-php-cs-fixer-in-3-lines/). It's super easy to start to use from scratch:

```bash
composer require symplify/easy-coding-standard --dev
vendor/bin/ecs check src --level psr12 # yes 12!
```

But what if you already have PHP CS Fixer on your project and want to switch?

## 1. From String Codes to Autocompleted Classes

You use string references like `strict_types` in your `.php_cs` file. You need to remember them, copy paste them and **copy-paste them right**.

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

How to do that in EasyCodingStandard? Copy paste the name, capitalize first letter and remove `_`:

```yaml
# ecs.yml
services:
    DeclareStrictTypes<cursor-here>:
```

Then hit the "ctlr" + "space" for class autocomplete in PHPStorm (it works even now when I write this post in markdown, nice!).

```yaml
services:
    PhpCsFixer\Fixer\Strict\DeclareStrictTypesFixer: ~
```

That way [Symfony plugin](https://plugins.jetbrains.com/plugin/7219-symfony-plugin) will autocomplete the class for you:

<img src="https://github.com/Symplify/EasyCodingStandard/raw/master/docs/yaml-autocomplete.gif">

No more typos with strong over string typing.

## 2. From `notPath()` to `skip` Parameter

If you'd like to skip nasty code from being analyzed, you'd probably use this.

```php
$finder = PhpCsFixer\Finder::create()
    ->exclude('somedir')
    ->notPath('my-nasty-dirty-file.php')
    ->in(__DIR__);
```

One big cons of this is **that all fixers will skip this code**, not just one. So even if here we need to skip only `DeclareStrictTypesFixer`, all fixer will skip it.

To skip this in EasyCodingStandard just use `skip` parameter:

```yaml
parameters:
    skip:
        PhpCsFixer\Fixer\Strict\DeclareStrictTypesFixer:
            - 'my-nasty-dirty-file.php'
```

Do you have more such cases?

```yaml
parameters:
    skip:
        PhpCsFixer\Fixer\Strict\DeclareStrictTypesFixer:
            - 'my-nasty-dirty-file.php'
            - 'sooo-dirty-file.php'
            - 'teribly-dirty-file.php'
            - 'sexy-dirty-file.php'
```

You don't have to list them all like a typing monkey. Just use `fnmatch()` format instead:

```yaml
parameters:
    skip:
        PhpCsFixer\Fixer\Strict\DeclareStrictTypesFixer:
            - '*dirty-file.php'
```

For all other `skip` options, [see README](https://github.com/symplify/easyCodingStandard/#ignore-what-you-cant-fix).

In case you need to **skip 1 fixer**, put it under `exclude_checkers`:

```yaml
parameters:
    exclude_checkers:
        - PhpCsFixer\Fixer\Strict\DeclareStrictTypesFixer
```


## 3. From . to YML Config Paths

PHP CS Fixer looks for `.php_cs` file in the root directory by default.

**And EasyCodingStandard look for:**

```bash
- ecs.yml
- ecs.yaml
- easy-coding-standard.yml
- easy-coding-standard.yaml
```

What about non-default locations or names?

From:

```bash
vendor/bin/php-cs-fixer fix /path/to/project --config=custom/location.yml --dry-run
```

**to:**

```bash
vendor/bin/ecs check /path/to/project --config custom/location.yml
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

**to YML parameters in EasyCodingStandard:**

```yaml
services:
    PhpCsFixer\Fixer\ArrayNotation\ArraySyntaxFixer:
        syntax: short
```

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

<br>

### Give it a Try...

...and you won't regret it. Sylius, LMC, Shopsys, Nette and SunFox did and never came back.

<br>

Did I forget a step that you had to fight with? **Please, let me know in the comments or just send PR to this post to add it**, so we help other readers.

<br>
<br>

Happy code sniffixing!
