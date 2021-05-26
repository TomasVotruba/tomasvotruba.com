---
id: 267
title: "Cleaning Lady Notes: From Class Mess to PSR-4 Step by Step With Confidence"
perex: |
    Today I'm starting a new post series - *Cleaning Notes*. These posts are for people who are [aspiring legacy migrators](/blog/2020/06/29/how-will-programming-look-like-in-2025/) with a vision to improve private PHP ecosystem and bring joy to coding with gigantic applications again. The same vision we have in the Rector team.
    <br><br>
    In this series, you can learn about my experience, tricks, tips, and what fucked me up. So you **save some frustration, where is not needed, discover hidden shortcuts and cool tools you never saw before**.
    <br><br>
    We start with the most problematic topic in PHP legacy, that every project needs, but almost none has - **transition to [PSR-4](https://www.php-fig.org/psr/psr-4)**.

tweet: "New Post on #php 🐘 blog: Cleaning Lady Notes: From Class Mess to PSR-4 Step by Step With Confidence"

updated_since: "August 2020"
updated_message: |
    Updated Rector YAML to PHP configuration, as current standard.
---

*Dedicated to [Kerrial](https://github.com/Kerrialn), my great friend who teaches me so much about not giving a f_ck and just do the stuff.<br>Thanks, dude!*

<br>

## How to Approach the Migration Itself?

### Know Your Enemy

There are many use cases that we have to handle to get to PSR-4. Honestly, I find it easier to [switch a framework](/blog/2019/08/26/how-we-migrated-54-357-lines-of-code-nette-to-symfony-in-2-people-under-80-hours/), where start is clear and goal is clear.

In PSR-4 migration, we have a clear goal:

- PSR-4: in 1 file, there is 1 class/trait/interface
- the class/trait/interface has a unique fully qualified name, that reflects file location
- nothing else exists

### Start has Many Ugly Forms

- **in 1 file, there is a dozen classes** - very popular for exceptions or test fixtures
- some classes have **identical name** and custom class loader gives one or the other preference (e.g., Magento and Drupal use this in some version)
- there are classes without any namespace
- there are classes with `Fake_Namespace`
- file has a different name than the class, e.g. `random_file.php` with `class SomeClass {}` in it
- in 1 file, there are classes and functions, so the file has to be manually included to "autoload" the functions
- there are conditional classes, e.g.

```php
if (! class_exists('SomeClass')) {
    final class SomeClass
    {
        // ...
    }
}
````

A lot to suck in, right? Don't worry; each of them has a guide to follow.

### Low Hanging Fruit

Each project is different, some of them has [functions mixed with HTML](/blog/2020/04/13/how-to-migrate-spaghetti-to-304-symfony-5-controllers-over-weekend/), some is missing composer completely, some needs to [switch from custom-framework autoloading](/blog/2020/06/08/drop-robot-loader-and-let-composer-deal-with-autoloading/).

But you should always apply basic rule:

<blockquote class="blockquote text-center mt-5 mb-5">
    "Take the low hanging fruit first."
</blockquote>

Always **go for a simple target first**. Don't be a hero. A hero falls from the sky after a massive battle over Atlantic, forgot to charge his smartphone... and dies alone.

**Be professional, close quickly, close early**. Are there 3 files with 20 classes in them?

- split them to 20 classes
- don't deal with namespaces, don't care about file naming
- create pull-request
- merge it

✅

Done. You've just made a first small step. Cross [one step of your list](/cleaning-lady-checklist), 9/10 is left.
But all those 9 steps are now 10 % less complicated.

<blockquote class="blockquote text-center mt-5 mb-5">
    "Even if you die, the code you wrote is merged."
    <footer class="blockquote-footer text-center">Bus Boy Scout Factor</footer>
</blockquote>

I love this coding principle. Why? Because it takes minimalism and productivity to the practical world. It narrows our focus, so any developer becomes a 10x programmer effortlessly.

<br>

## What Exactly to Do? - Case Study

Enough theory. **Let's look at the project we've recently migrate to PSR-4 and how exactly we did it**.


This is not paid promo, but [Amateri are hiriging](https://www.startupjobs.cz/en/startup/scrumworks-s-r-o). We're far enough with the migration, so I'm confident it would be fun to work with such codebase.

## 1. Split Multiple Classes in 1 File

How does it look?

```diff
 // SomeFile.php
 class SomeClass
 {
 }

 class AnotherClass
 {
 }
```

```diff
+// AnotherClass.php
+class AnotherClass
+{
+}
```

<br>

The **1st cool tool** we look at today is [symplify/psr4-switcher](https://github.com/symplify/psr4-switcher).
It doesn't need projects' autoloader so that it can be installed outside the project, e.g., in `/var/www/tools`, while your project is in `/var/www/old_project`.

Install it:

```bash
composer require symplify/psr4-switcher --dev
```

It would be great to have a list of all such multi-class files, right?

```bash
vendor/bin/psr4-switcher find-multi-classes /src
```

↓

```bash
* SomeFile.php
    * SomeClass
    * AnotherClass
```

Now we know how big a problem we're dealing with.

- Are there 3 files with 20 classes? **Separate them manually**.
- Is that 50 files with 300 classes? Use **Rector rule** - [`MultipleClassFileToPsr4ClassesRector`](https://github.com/rectorphp/rector/blob/master/docs/rector_rules_overview.md#multipleclassfiletopsr4classesrector).

Now 1 file has exactly 1 class/interface/trait.

Send pull request, make sure your project's autoloader autoloads them, and tests are passing. Merge it, and you're done.

## 2. Check Class Short name vs. Filename

```diff
 // Cucumber.php
-class Car
+class Cucumber
 {
 }
```

If we only knew how many such files are there and where... back to PSR4-Switcher:

```bash
vendor/bin/psr4-switcher check-file-class-name src
```

You will get a list of files that don't match. Use PHPStorm refactoring to change the class name everywhere:

<img src="/assets/images/posts/2020/psr4_rename_class.gif" class="img-thumbnail">

Commit, PR, CI passes, merge.

✅

## 3. Upper-case Directories First Letter

In PSR-4, any non-root directory must start with the first big letter. Root is e.g. `/app`, `/src`.

```diff
-/app/form/someForm
+/app/Form/SomeForm
```

Go through directory in the left panel in PHPStorm and rename the directories there:

<img src="/assets/images/posts/2020/psr4_rename_dirs.gif" class="img-thumbnail">

Commit, PR, CI passes, merge.

✅

## 4. Check PSR-4 root

We've done 3 steps so far. Now comes the biggest one, actually adding PSR-4 roots to `composer.json`.

It will not be as pretty as 1 root line, but that's not what we go here now. Our goal is **to have all classes loaded with PSR-4, no matter how many lines** in `composer.json` does it need.

```json
{
    "autoload": {
        "psr-4": {
            "Amateri\\Payment\\": "src/somewhere-else/Payment",
            "Amateri\\Delivery\\": "src/another-dir/Delivery"
        }
    }
}
```

We can guess what namespace roots (`"Amateri\\Payment\\"`) should be loaded from which directory (`"src/somewhere-else/Payment"`)... or we can use science!

```bash
vendor/bin/psr4-switcher generate-psr4-paths project/src --composer-json project/composer.json
```

The command will generate such paths for us, based on existing namespaces and file locations.
There may be over 10 or even 50 of those. **Don't worry about it now**.

- put generated output to `composer.json` instead of `classmap`,
- run `composer dump-autoload` to let composer know about news paths
- run PHPStan to see if all classes are loaded

If everything passes... Commit, PR, CI passes, merge.

✅

## 5. Narrow the Namespace Root and Directories in `composer.json`

Now comes my favorite part. Here we move all directories **to use as little namespace root as possible**.

It might be a little bit unclear, but give it time and it will fit in. Let's look at the example:

```diff
 {
     "autoload": {
         "psr-4": {
-            "Amateri\\Payment\\": "src/somewhere-else/Payment",
-            "Amateri\\Delivery\\": "src/another-dir/Delivery",
+            "Amateri\\": "src"
        }
    }
}
```

What happens with files?

```diff
-src/somewhere-else/Payment
+src/Payment
```

```diff
-src/another-dir/Delivery
+src/Delivery
```

Here use PHPStorm refactoring on the directory as in step 3.

- run `composer dump-autoload` to let composer know about news paths
- run PHPStan to see if all classes are loaded

If everything passes... Commit, PR, CI passes, merge.

✅

## 6. What if there are No Namespaces or Are Very Very Bad?

In many codebases, there are just random files—no namespace, no fake namespace, etc.

For these, we have help of Rector with these 2 rules:

- [`NormalizeNamespaceByPSR4ComposerAutoloadRector`](https://github.com/rectorphp/rector/blob/master/docs/rector_rules_overview.md#normalizenamespacebypsr4composerautoloadfilesystemrector)
- [`NormalizeNamespaceByPSR4ComposerAutoloadFileSystemRector`](https://github.com/rectorphp/rector/blob/master/docs/rector_rules_overview.md#normalizenamespacebypsr4composerautoloadrector)

<br>

Register them in `rector.php`:

```php
<?php

// rector.php

declare(strict_types=1);

use Rector\PSR4\Rector\FileSystem\NormalizeNamespaceByPSR4ComposerAutoloadFileSystemRector;
use Rector\PSR4\Rector\Namespace_\NormalizeNamespaceByPSR4ComposerAutoloadRector;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

return function (ContainerConfigurator $containerConfigurator): void {
    $services = $containerConfigurator->services();
    $services->set(NormalizeNamespaceByPSR4ComposerAutoloadRector::class);
    $services->set(NormalizeNamespaceByPSR4ComposerAutoloadFileSystemRector::class);
};
```

And **manually add the desired namespace** to your `composer.json`:

```diff
 {
+   "autoload": {
+        "psr-4": {
+            "Amateri\\": "src"
+        }
+    }
 }
```

When you run the Rector, it will try to autocomplete all the namespaces to respect your `composer.json`:

```bash
vendor/bin/rector p src
```

This is one of **the most significant changes in your application**, so **be sure to check it carefully**. Not all cases are covered by Rector yet.

- run `composer dump-autoload` to let composer know about news paths
- run PHPStan to see if all classes are loaded

If everything passes... Commit, PR, CI passes, merge.

✅

<br>

Then we added few manual tweaks here and there, and we were PSR-4 compliant with ~7 lines in PSR-4 in `composer.json`.

<br>

**Have you found a case that is not covered or a better way to this**? Let me know in the comments.

<br>

Happy coding!
