---
id: 255
title: "Drop RobotLoader and let Composer Deal with Autoloading"
perex: |
    Using 2 tools for one thing, in this case 2 packages to autoload classes, are sign of an architecture smell. Many applications I see contain RobotLoader for historical reasons. I will borrow this from psychology: pathological behavioral patterns tear us down in the present, but were useful in past.
    <br><br>
    The best way to deal with them is acknowledge their purpose and then, let them go and enjoy the gift of present.

tweet: "New Post on #php 🐘 blog: Drop RobotLoader and let #composerphp Deal with Autoloading #nettefw #psr4 Dispatcher"

updated_since: "June 2020"
---

## Where is RobotLoader Useful

[RobotLoader](https://doc.nette.org/en/auto-loading#toc-nette-loaders-robotloader) is a Nette Component that is used to autoload classes. Its killer feature is simple: **in whatever file it is and whatever the class name is, I will load it**. You can have 20 classes in 1 file or classes located in various locations, they won't hide.

## Before Composer appeared, it was The Best

RobotLoader was a very useful tool in times before Composer, because **there were not so many efficient tools for loading classes**.

Also, when people could not agree upon where to put their classes, how to name them, whether use or don't use namespace and how many classes to put in one file, we can say **this tool saved a lot of argument-hours**.

After many discussions, followed by the first standard, [PSR-0](http://www.php-fig.org/psr/psr-0/), people agreed upon [PSR-4](http://www.php-fig.org/psr/psr-4/), a more mature replacement of PSR-0.

## Why it's not Anymore

Have your heard about PSR-4? It is a *PHP Standard Recommendation* about naming and location of the classes. This says you completely nothing, but in the simplest form it means:

**1 class** (or interface/trait) = **1 file**

**class name** = **file name**.php

**namespace\class name** = **directory/file name**.php

```bash
# class => file
MyClass => MyClass.php
App\MyClass => App/MyClass.php
App\Presenter\MyClass => App/Presenter/MyClass.php
```

I know I can rely on this in 99% of places when `composer.json` is used.

When I see `App\Presenter\MyClass` I  it's located in `App/Presenter/MyClass.php` file.

And this is the place where **RobotLoader** (or any custom ultimate loader) **fails**. I came around many applications where classes are located at random. And I had to use my brain to find them. But I don't want to focus my mental activity on thinking about their location, **I want to develop my application**.


## How to move to Composer in a Nette application?

There are 2 levels of how to achieve this.

### Level 1: Change your Composer

The first level requires 3 small steps.

#### 1. Tune `composer.json` Autoloading

```json
{
    "require": {
        "..."
    },
    "autoload": {
        "psr-4": {
            "App\\Forms\\": "app/forms",
            "App\\Model\\": "app/model",
            "App\\Presenters\\": "app/presenters",
            "App\\Router\\": "app/router"
        }
    }
}
```

This means, all classes residing in `App\\Forms` namespace have to be located in `app/forms` directory.

One important rule - it works in **case-sensitive** manner.

So this will work:

```bash
App\Presenters\HomepagePresenter => app/presenters/HomepagePresenter.php
```

But this will not:

```bash
App\Presenters\HomepagePresenter => app/presenters/homepagePresenter.php
```

#### 2. Disable RobotLoader

Now you can clean up your `app/bootstrap.php`:

```php
// $configurator->createRobotLoader()
//      ->addDirectory(__DIR__)
//      ->register();
```

But RobotLoader is still silently enabled for presenters. We don't want that either now:

```yaml
# app/config/config.neon
application:
    scanDirs: false
```


#### 3. Refresh Composer Autoloading

And tell Composer, to regenerate its autoloader:

```bash
composer dump-autoload
```

Note: This command is run by default after `composer update`, `composer require ...` and similar commands. Since we'd manually changed our `autoload` section, we had to run it manually.

Now try our application and it should run.

**You are finished and all your classes are loaded by Composer.** Congratulations!

### Level 2: Rename Directories to capital case, to Respect PSR-4

There is one more level I do with my applications, so my `composer.json` is nice and clear. But this is optional. Do it only if you'd like to write better code with lower WTF factor!

Turn this:

```bash
/app
    /forms
    /model
    /presenters
    /routing
    /...
```

Into this:

```bash
/app
    /Forms
    /Model
    /Presenters
    /Routing
    /...
```

After these steps, you can simplify your `autoload` section as such:

```json
{
    "autoload": {
        "psr-4": {
            "App\\": "app"
        }
    }
}
```

Don't forget to run:

```bash
composer dump-autoload
```

And you've unlocked Level 2.

<br>

Happy coding!
