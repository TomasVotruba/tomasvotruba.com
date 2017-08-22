---
id: 49
layout: post
title: "7 New Features in Easy Coding Standard 2.2"
perex: '''
    After extensive cooperation with <a href="https://twitter.com/geekovo/status/885152407948333056">David Grudl on Nette\CodingStandard</a> EasyCodingStandard got new features, that <strong>moved the project to completely new level of comfort</strong>.  
    <br><br>
    Prepared configs, reduction of config to few lines, <code>--config</code> option and more.       
'''
related_posts: [37]
---

Huge thanks to [David Grudl](https://github.com/dg) who gave me the feedback, ideas and Windows bug fixes while working on [Nette\CodingStandard](https://github.com/nette/coding-standard) package. I'll write "how to" for Nette\CodingStandard later, but today we'll look on **new features it uses from EasyCodingStandard 2.2**. 

## 1. Shorter Bin

Are you tired of tyops in `vendor/bin/easy-coding-standard`?

**Now you can use `ecs` bin instead**: 

```bash
vendor/bin/ecs
```


## 2. Prepared Configs

Before you had to name all the checkers manually in your config. There was no *PSR2* group nor *Symfony* like there is in other tools.

**Now you can pick from 9 prepared configs**.

*PHP_CodeSniffer + PHP-CS-Fixer* 

```yaml
vendor/symplify/easy-coding-standard/config/php54-checkers.neon
vendor/symplify/easy-coding-standard/config/php70-checkers.neon
vendor/symplify/easy-coding-standard/config/php71-checkers.neon
vendor/symplify/easy-coding-standard/config/psr2-checkers.neon
vendor/symplify/easy-coding-standard/config/symfony-checkers.neon
vendor/symplify/easy-coding-standard/config/symfony-risky-checkers.neon
```

*Custom*

```
vendor/symplify/easy-coding-standard/config/symplify.neon
vendor/symplify/easy-coding-standard/config/spaces.neon
vendor/symplify/easy-coding-standard/config/common.neon
```

This shortened Symplify config from [256 lines](https://github.com/Symplify/Symplify/blob/v2.0.0/easy-coding-standard.neon#L1-L256) **to [just 22 lines](https://github.com/Symplify/Symplify/blob/458082a5d534182e4ad723958c417399442abc82/easy-coding-standard.neon#L1-L22)**.


## 3. Use Whole Set But 1 Checker

I like Symfony set from PHP-CS-Fixer, but **I'd like to remove 4 checkers**. Do I have to put all checkers I want to use explicitly to the config?
 
Not anymore!

**Use the set and exclude unwanted checkers in `exclude_checkers` option**: 

```yaml
includes:
    - vendor/symplify/easy-coding-standard/config/symfony-checkers.neon

parameters:
    exclude_checkers:
        # from PHP-CS-Fixer Symfony set
        - PhpCsFixer\Fixer\PhpTag\BlankLineAfterOpeningTagFixer
        - PhpCsFixer\Fixer\Operator\NewWithBracesFixer
        - PhpCsFixer\Fixer\Phpdoc\PhpdocAlignFixer
        - PhpCsFixer\Fixer\Operator\UnaryOperatorSpacesFixer
```


## 4. Skip More Than 1 File For Specific Checker

If you need to skip more files, just **use [`fnmatch`](http://php.net/manual/en/function.fnmatch.php) pattern** in `skip` section.

```yaml
parameters:
    skip:
        SlevomatCodingStandard\Sniffs\TypeHints\TypeHintDeclarationSniff:
            - "*packages/CodingStandard/src/Sniffs/*/*Sniff.php"
```


## 5. New Command `Show` Display Used Checkers

Do you know, what checkers do you use?

```bash
vendor/bin/ecs show
```

Or what checkers are in particular config?

```bash
vendor/bin/ecs show --config vendor/nette/coding-standard/coding-standard-php71.neon
```

This is rather debug or info tool, but it might come handy.

**You can find [more options of this command in README](https://github.com/Symplify/EasyCodingStandard#show-command-to-display-all-checkers)**. 


## 6. Scan `*.php` and `*.phpt` Files

EasyCodingStandard checks only `*.php` files by default. But what if you want to check `*.phpt` as well as in case of [Nette\CodingStandard](https://github.com/nette/coding-standard)?

**To add files with another suffixes you need to add own source provider**:

```php
namespace App\Finder;

use IteratorAggregate;
use Nette\Utils\Finder;
use SplFileInfo;
use Symplify\EasyCodingStandard\Contract\Finder\CustomSourceProviderInterface;

final class PhpAndPhptFilesProvider implements CustomSourceProviderInterface
{
    /**
     * @param string[] $source
     */
    public function find(array $source): IteratorAggregate
    {
        # $source is "source" argument passed in CLI
        # inc CLI: "vendor/bin/ecs check /src" => here: ['/src']
        return Finder::find('*.php', '*.phpt')->in($source);
    }
}
```

And register it as a normal Symfony service:

```yaml
# easy-coding-standard.neon
services:
    App\Finder\PhpAndPhptFilesProvider: ~
```

[Explore README](https://github.com/Symplify/EasyCodingStandard#do-you-need-to-include-tests-php-inc-or-phpt-files) or [`SourceProvider`](https://github.com/nette/coding-standard/blob/2f935070b82fbe4b1da8e564a8dc6dcb9bbeca25/src/Finder/SourceProvider.php) in Nette\CodingStandard for more.
 

## 7. Are you Tabs Person?

You're welcomed:

```yaml
parameters:
    indentation: tab # "spaces" by default
```


<br>

You can find [these features in README](https://github.com/Symplify/EasyCodingStandard) with more detailed use examples.
  

### Like it? Try It

If you find these 7 news useful, you can try [EasyCodingStandard](https://github.com/Symplify/EasyCodingStandard) right now:
 

```bash
composer require symplify/easy-coding-standard
vendor/bin/ecs
```

Happy code-style checking!
