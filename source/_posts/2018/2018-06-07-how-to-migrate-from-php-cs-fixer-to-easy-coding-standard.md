---
id: 112
title: "How to Migrate From PHP CS Fixer to EasyCodingStandard in 6 Steps"
perex: |
    We looked at how to migrate from PHP_CodeSniffer to Easy Coding Standard on Monday. But what if your weapon of choice is PHP CS Fixer and you'd to run also some sniffs?
    <br>
    There are **a few simple A → B changes**, but one has to know about them or will get stuck. Let's learn about them.
tweet: "New Post on my Blog: How to Migrate From PHP CS Fixer to EasyCodingStandard in 6 Steps #ecs #codingstandard #ci"
related_items: [37, 49, 86, 111]
---

ECS is a tool build on Symfony 3.4 components that [combines PHP_CodeSniffer and PHP CS Fixer](/blog/2017/05/03/combine-power-of-php-code-sniffer-and-php-cs-fixer-in-3-lines/). It's super easy to start to use from scratch:

```bash
composer require symplify/easy-coding-standard --dev
vendor/bin/ecs check src --level psr12 # yes 12!
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

How to do that in EasyCodingStandard? Copy paste the name from README, capitalize first letter and remove `_`:

```yaml
# ecs.yml
services:
    DeclareStrictTypes<cursor-here>:
```

Then hit the "ctrl" + "space" for class autocomplete in PHPStorm (it works even now when I write this post in markdown, nice!).

```yaml
services:
    PhpCsFixer\Fixer\Strict\DeclareStrictTypesFixer: ~
```

That way [Symfony plugin](https://plugins.jetbrains.com/plugin/7219-symfony-plugin) will autocomplete the class for you:

<img src="https://github.com/Symplify/EasyCodingStandard/raw/master/docs/yaml-autocomplete.gif">

No more typos with *strong* over *string typing*.

## 2. From `notPath()` to `skip` Parameter

If you'd like to skip nasty code from being analyzed, you'd probably use this in PHP CS Fixer.

```php
$finder = PhpCsFixer\Finder::create()
    ->exclude('somedir')
    ->notPath('my-nasty-dirty-file.php')
    ->in(__DIR__);
```

One big cons of this is **that all fixers will skip this code**, not just one. Do you need `DeclareStrictTypesFixer` to skip thisf ile? Sorry, all fixers will skip it.

EasyCodingStandard solves this common case - to skip a file, just use `skip` parameter:

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

In case you need to **skip 1 fixer**, put it under `exclude_checkers`:

```yaml
parameters:
    exclude_checkers:
        - PhpCsFixer\Fixer\Strict\DeclareStrictTypesFixer
```

For all other `skip` options, [see README](https://github.com/symplify/easyCodingStandard/#ignore-what-you-cant-fix).

## 3. From `.php_cs` to YML Config

PHP CS Fixer looks for `.php_cs` file in the root directory by default.

**And EasyCodingStandard looks for:**

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

**to autocompleted config in YAML file in Easy Coding Standard**:

```yaml
imports:
    - { resource: 'vendor/symplify/easy-coding-standard/config/psr2.yml' }
```

Do you want to see all the PSR-2 rules? Easy, just click on the file.

<br>

### Give it a Try...

...and you won't regret it. Sylius, LMC, Shopsys, Nette, and SunFox did and never came back.

<br>

Did I forget a step that you had to fight with? **Please, let me know in the comments or just send PR to this post to add it**, so we help other readers.

<br>
<br>

Happy code sniffixing!
