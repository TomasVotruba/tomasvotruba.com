---
id: 326
title: "Why do we Scope PHP Tools"
perex: |
    Do you know PHPStan, ECS, [Monorepo Builder](https://github.com/symplify/monorepo-builder), PHPUnit, [Config Transformer](https://github.com/symplify/config-transformer) or Rector?


    What do they have in common? They're PHP tools you install with composer and then run in your command line. Hm, what else? **They're all scoped with help of [php-scoper](https://github.com/humbug/php-scoper).**


    Do you want to make your tool resistant to any conflict with any project dependencies? Today I'll show you how.

---

Let's say you want to install `symplify/monorepo-builder` to any of your projects with composer:

```bash
composer require symplify/monorepo-builder --dev
```

What can happen?

* *[ERROR] conflicts with your PHP version*
* *[ERROR] conflicts with your symfony/console, must be 5.2+, you have 4.4*
* *[ERROR] conflicts with your another-dependency, must be X, you have Y*

Now you have to go to your `composer.json`, figure out what conflicts you can solve. You can also try to go to the project repository and ask for lowering the minimal required version of this or that package. This is typical for unscoped packages and natural results for semver strategy of PHP packages ecosystem.

<br>

But do we care about the dependencies of the tool we use? No, our goal is **to run the tool**:

```bash
composer require symplify/monorepo-builder --dev
vendor/bin/monorepo-builder <command>
```

## How Can We Get rid of Tool's Dependencies?

<div class="row">
    <div class="col-12 col-sm-6">
        <em>Before scoping</em>
        <img src="https://user-images.githubusercontent.com/924196/124739507-35d18780-df1a-11eb-9ff8-6c91b6159e78.png" class="img-thumbnail mt-3">
    </div>
    <div class="col-12 col-sm-6">
        <em>After scoping</em>
        <img src="https://user-images.githubusercontent.com/924196/124739467-2eaa7980-df1a-11eb-90ce-e62b76292b95.png" class="img-thumbnail mt-3">
    </div>
</div>

<br>

We'll scope the tool! Only then we're conflict-free! Our project can have `symfony/console` 3.4 or 5.4-dev. Nobody cares because the only requirement is of the tool is the PHP version.

## How is that possible?

Wait, how can we have 2 versions of `symfony/console`? Let's look at the file structure we'll find in `/vendor` if we install the following packages together:

```bash
composer require symfony/console:^3.4
composer require symplify/monorepo-builder # new scoped one
```

## 1. Your `Command` class

These steps will produce 2 different `symfony/console` directories with 2 different `Symfony\Component\Console\Command\Command` classes.

```bash
/vendor
    /symfony
        /console # version 3.4, autoloaded by your project
            /Command
                Command.php
```

That contains typical `Command` class as you know it

```php
namespace Symfony\Component\Console\Command;

class Command
{
    // ...
}
```

Pretty standard, right?

## 2. Scoped `Command` class

Here the scoping magic happens. The `symplify/monorepo-builder` package it's own Command in it's own scoped `/vendor`. Like this:

```bash
/vendor
    /symplify
        /monorepo-builder
            /vendor # this vendor is scoped and loaded only by monorepo-builder
                /symfony
                    /console
                        /Command
                            Command.php
```

Now, this `Command` class is a bit different. It's *scoped*. What does that mean exactly? It has its unique namespace prefix that makes class name unique to other `Command`:

```php
namespace Scope1234\Symfony\Component\Console\Command;

class Command
{
    // ...
}
```

So now in the whole project we now have 2 `Command` classes:

* `Symfony\Component\Console\Command`
    * loaded by your project autoload
    * in a version defined in your projects `composer.json`
    * you can use this class in your project, e.g. to create your own commands

<br>

* `Scope1234\Symfony\Component\Console\Command`
    * loaded by monorepo-builder
    * in a version required by monorepo-builder
    * you will never see this class in your code

The second class was scoped by `php-scoper`, that makes all classes unique and accessible exclusively in the tool. This process removes dependency from `composer.json` and thus avoid conflicts on install.

## Scope All The Things?

Now, should we get crazy and scope everything that pops up a conflict on `composer require <x>`? No. This process is valid only for **PHP tools** in the command line. We should not scope classic dependencies that we use directly in our project, e.g. `nette/utils`.

<br>

## Where is Scoping Useful?

* if we create an open-source PHP tool for the community
* if our community uses composer
* if we want to ease installation on both super modern and well-grown legacy projects

<br>

Now that we know *why* and *what* we scope, we'll look at [*how* to do the scoping in the next post](/blog/how-to-scope-your-php-tool-in-10-steps).

<br>

Happy coding!
