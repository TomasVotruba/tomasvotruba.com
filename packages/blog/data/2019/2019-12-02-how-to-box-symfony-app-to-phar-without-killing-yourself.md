---
id: 228
title: "How to Box Symfony App to PHAR without Killing Yourself"
perex: |
    Do you have a Symfony Application like Composer and you want to ship it as a PHAR?
    Composer is actually pretty simple - just see the [`Compiler`](https://github.com/composer/composer/blob/master/src/Composer/Compiler.php) class.
    <br><br>
    **But what if** you use Symfony Dependency Injection with PSR-4 **autodiscovery** like [Rector](https://github.com/rectorphp/rector) does? Well, better be ready for **nasty traps**.
tweet: "New Post on #php 🐘 blog: How to Box #Symfony App to PHAR without Killing Yourself"
---

*Note: all these tips take 5 minutes to apply (in total), but took us ~6 hours to discover. I'd like to thank Jan Linhart from Mautic, Kerrial Becket Newham and Ivan Kvasnica for cooperation that made [this happen](https://github.com/rectorphp/rector/pull/2373).*

Rector [needs prefixed PHAR](https://github.com/rectorphp/rector/issues/177) for the same reasons PHPStan does.

Let's say your `composer.json` looks like this:

```json
{
    "require": {
        "symfony/console": "2.8"
    }
}
```

If you want to install Rector:

```bash
composer require rector/rector --dev
```

You'll end up with an error:

```bash
rector/rector cannot be installed, because it requires symfony/* ^3.4|^4.4... but you have 2.8
```

This leads to many issues reported on Github, mostly [grouped around this one](https://github.com/rectorphp/rector/issues/177).

## PHP Version Conflicts

If you have PHP 5.6, you'll get a different error:

```bash
rector/rector needs at least PHP 7.2, you have PHP 5.6
```

That's where Docker becomes useful. Yet, it still **doesn't solve the Symfony 2.8 in your project vs Symfony 4.4 in Rector project issue**.

That's why **prefixed `rector.phar` is needed**. With such a file, you don't care about Rector's dependencies, you just use it.

## How Does "Scoping" Work?

Basically any `Symfony\Component\Console\Command` becomes `UniquePrefix\Symfony\Component\Console\Command`. That way there will never be conflicts between your code without prefix and unique Rector code.


## Box + Scope Industry Standard

To make it happen, we **don't need to re-invent the wheel**. There are 2 amazing tools maintained and developed by [Théo Fidry](https://github.com/theofidry) (thank you!):

- [box](https://github.com/humbug/box) - a tool that creates PHAR (~= PHP zip) from an input directory
- [php-scoper](https://github.com/humbug/php-scoper) - box *plugin* that adds namespace prefix to all the files

It takes around 10 seconds to scope + wraps 5 000 files of Rector to `rector.phar`. **This speed is amazing.**

### Nobody Ever used Symfony in PHAR Before

These 2 tools work very well for PHP-based *manual* containers like PHPStan has. But fails for Symfony autodiscovery that uses globs. It's not the fault of these tools, but rather Symfony, because nobody ever tested it to compiled PHAR :).

**Where and how to overcome it?** There are 4 steps you need to watch out for:

## 1. From `excluded` files to Globs

If you have following config:

```yaml
services:
    Rector\TypeDeclaration\:
        resource: '../src'
        exclude:
            - '../src/ValueObject/SpecificFile.php'
```

You'll end up with an error:

```bash
Directory "../src/ValueObject/SpecificFile.php" was not found.
```

Where does it come from? It's an error from [Symfony/Finder](https://github.com/symfony/symfony/blob/c62bb82e27974ef4e389da523f0de451b6632266/src/Symfony/Component/Finder/Finder.php#L589).

But how did Symfony got there? Well, the Symfony takes missing files from ["excluded" as directory](https://github.com/symfony/symfony/blob/c62bb82e27974ef4e389da523f0de451b6632266/src/Symfony/Component/DependencyInjection/Loader/FileLoader.php#L162) and the rest is history.

My super random guess is for missing local `phar://` prefix.


### How to Fix it?

Just change the relative path to each file to glob (`*`) and move your files there:

```diff
 services:
     Rector\TypeDeclaration\:
         resource: '../src'
         exclude:
-          - '../src/ValueObject/SpecificFile.php'
+          - '../src/ValueObject/*'
```

### Positive Side-Effect

In the end, it was architecture improvement, as we had to move files to a generic directory, that clearly states it's not a service - here `ValueObject`.

## 2. Symfony Autodiscovery Slash Fail

This one give me an headache, but is simple to fix:

```diff
 services:
     Rector\TypeDeclaration\:
-        resource: '../src/'
+        resource: '../src'
```

## 3. SHA1 cannot be Verified...

This one is not strictly related to Symfony, but it happened while we shipped `box.phar`:

```bash
Error: Fatal error: Uncaught PharException: phar "compiler/build/box.phar" SHA1
the signature could not be verified: broken signature in ...
```

What the `box.phar` worked locally but doesn't work on Travis?

I re-downloaded files many times and it worked in other CI. WTF?

Is that corrupted version of `box.phar`? I tried version before/after, still the same error.

<br>

2 hours later...

<br>

Damn. Spaces? Line-ending? **Yes!**

The solution is to remove this from `.gitattributes`:

```diff
-# Set default behavior, in case of users, don't have core.autocrlf set.
-* text=auto
-* text eol=lf
```

Because [it changed line-endings](https://stackoverflow.com/questions/24763948/git-warning-lf-will-be-replaced-by-crlf) in the `box.phar` on commit and thus made it valid locally, but broken remotely on Travis CI.

## 4. Don't do Multiple `bin` Files

Rector had multiple bin files, just to split the complexity:

- `bin/rector` that included
    - `bin/autoload.php`
    - `bin/container.php`


The Box takes only the file in `compser.json` > `bin` section, so the latter 2 were missed. I tried to change configuration many times, but it mostly failed on malformed paths.


### How to solve it?

Now we have just **single file**:

- `bin/rector`

With use strict typed classes written on the bottom of the file and use them in the same file. Also, nice side effects as we moved from many-functions to few classes.

<br>

Do you want to know more about Box + Scoper automated Travis CI deploy in practice?

<a href="https://github.com/rectorphp/rector/pull/2373" class="btn btn-dark mt-3 mb-3">
    Check this PR on Rector
</a>

<br>

Happy coding!
