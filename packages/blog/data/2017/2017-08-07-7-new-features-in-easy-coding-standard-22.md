---
id: 49
title: "7 New Features in Easy Coding Standard 2.2"
perex: |
    After extensive cooperation with [David Grudl on Nette\CodingStandard](https://twitter.com/geekovo/status/885152407948333056) ECS got new features with **focus on developer experience**. Smart experience.

    Prepared configs, reduction of config to few lines, `--config` option and more.
tweet: "7 New Features in Easy Coding Standard #codingStandard #php #solid"

updated_since: "November 2020"
updated_message: |
    Switched from deprecated `--set` option to PHP config.
    Switched from YAML to PHP configs in **Symplify 9.**
---

Today we'll look on **new features it uses from ECS**.

## 1. Shorter Bin

```bash
# before
vendor/bin/easy-coding-standard

# now
vendor/bin/ecs
```

## 2. Prepared Rule Sets

Before you had to name all the checkers manually in your config. There was no *PSR2* group nor *Symfony* like there is in other tools. **Now you can pick from 10 prepared configs**.

*PHP_CodeSniffer + PHP CS Fixer*

```php
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Symplify\EasyCodingStandard\ValueObject\Option;
use Symplify\EasyCodingStandard\ValueObject\Set\SetList;

return static function (ContainerConfigurator $containerConfigurator): void {
    $parameters = $containerConfigurator->parameters();

    $parameters->set(Option::SETS, [
        SetList::PSR_12,
        SetList::COMMON,
        SetList::SYMPLIFY,
        // ...
        // explore the constants on `SetList` class
    ]);
};
```

## 3. Use Whole Set But 1 Checker

Imagine you love the PSR-12 set, **except that 1 checker**. Do you skip the set completely or copy all the rules manually except the one?

Not anymore!

```php
use PhpCsFixer\Fixer\Operator\UnaryOperatorSpacesFixer;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Symplify\EasyCodingStandard\ValueObject\Option;
use Symplify\EasyCodingStandard\ValueObject\Set\SetList;

return static function (ContainerConfigurator $containerConfigurator): void {
    $parameters = $containerConfigurator->parameters();

    $parameters->set(Option::SETS, [
        SetList::PSR_12,
    ]);

    $parameters->set(Option::SKIP, [
        // ignore 1 rule everywhere
        UnaryOperatorSpacesFixer::class => null,
    ]);
};

```

## 4. Skip More Than 1 File For Specific Checker

Do you need to skip more files for 1 specific checker? **Now you can [`fnmatch`](https://php.net/manual/en/function.fnmatch.php) pattern**:

```php
use SlevomatCodingStandard\Sniffs\TypeHints\TypeHintDeclarationSniff;

// ...
$parameters->set(Option::SKIP, [
    TypeHintDeclarationSniff::class => [
        '*packages/CodingStandard/src/Sniffs/*/*Sniff.php'
    ],
]);
```

## 5. New Command `Show` Display Used Checkers

Do you know, what checkers do you use?

```bash
vendor/bin/ecs show
```

This is rather debug or info tool, but it might come handy.

**You can find [more options of this command in README](https://github.com/symplify/easy-coding-standard)**.


## 6. Scan `*.php` and `*.phpt` Files

EasyCodingStandard checks only `*.php` files by default. But what if you want to check `*.phpt` as well as in case of [Nette\CodingStandard](https://github.com/nette/coding-standard)?

Use `Option::FILE_EXTENSIONS`:

```php
// ...
$parameters->set(Option::FILE_EXTENSIONS, ['php', 'phpt']);
```

## 7. Are you Tabs Person?

There you go:

```php
// ...
$parameters->set(Option::INDENTATION, Option::INDENTATION_TAB);
```

### Like it? Try It

If you find these 7 news useful, try [ECS](https://github.com/symplify/easy-coding-standard) right now.

<br>

Happy coding!
