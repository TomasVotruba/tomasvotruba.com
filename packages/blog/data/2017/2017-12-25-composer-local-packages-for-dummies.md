---
id: 69
title: "Composer Local Packages for Dummies"
perex: |
    This is the simplest way to start using `/packages` directory in your application, that **leads to cleaner code, maintainable architecture** and is **the best to start testing**.
---


I wrote about [pros and cons of local packages before](/blog/2017/02/07/how-to-decouple-monolith-like-a-boss-with-composer-local-packages/).
After year of using this in [practice](https://github.com/symplify/symplify) and mentorings I polished this approach to even **simpler version that is easy to start with**.


### Do You Have?

- **monolithic code in `/app`**
- **no unit tests**
- code that is using 3rd party services, like payments, invoice API and coding standards
- namespaces
- old application you maintain for many years

### Do You Want to?

- **start testing**
- **have decoupled code**
- board new programmer with **no explaining**
- understand how to scale architecture by decreasing code complexity

<br>

There is no need to use Github, [love open-source](/blog/2017/01/31/how-monolithic-repository-in-open-source-saved-my-laziness/), understand [package design](https://leanpub.com/principles-of-package-design) or understand [composer beyond PSR-4](/blog/2020/06/08/drop-robot-loader-and-let-composer-deal-with-autoloading/).
No [symlink issues](https://johannespichler.com/developing-composer-packages-locally), no forgotten `composer update`. **Anyone can start using this!**

<div class="text-center">
    <img src="/assets/images/posts/2017/composer-local-packages/composer.png">
</div>

## 4 Steps to first Dummy Local Package

Your application now looks similar to this:

```bash
/app
/temp
/vendor
composer.json
```



### 1. Create `Packages` directory

```diff
/app
/temp
+/packages
/vendor
composer.json
```

### 2. Create First Package

Start with something simple like filesystem or string utils.

```diff
/app
/temp
/packages
+   /file-system
+       /src
/vendor
composer.json
```

### 3. Move first Class to new package directory

```diff
/app
/temp
/packages
    /file-system
        /src
+           FileSystem.php
/vendor
composer.json
```

<br>

The best practise is to use your **company or application name** as namespace, e.g. [`EntryDo`](https://www.entrydo.com).
Second part of namespace will be **package name** (`file-system`) in **CamelCaps format**.

```php
namespace EntryDo\FileSystem;

final class FileSystem
{
    public function readFile(string $filePath): string
    {
        // is file or directory?
        // is readable?
        // ...
    }
}
```


**You're awesome! Congratulations**, you've just made your first local packages and you're definitely not a dummy anymore.


### 4. Autoload with Composer

The class is now decoupled. Now we have to **tell composer where to find it**!

This is your `composer.json`:

```json
{
    "require": {
        "favorite/framework": "^4.0"
    },
    "autoload": {
        "classmap": "app"
    }
}
```

Maybe your already have PSR-4 structure (great job if you do!), but let's say you maintain an old application.

<br>

Add our new package:

```diff
{
    "require": {
        "favorite/framework": "^4.0"
    },
    "autoload": {
        "classmap": "app",
+       "psr-4": {
+           "EntryDo\FileSystem\": "packages/file-system/src
+       }
    }
}
```

<br>

And now the answer to most questions on StackOverflow around this topic- **rebuild the composer autoload file** (`/vendor/autoload.php`) from CLI:

```bash
composer dump
# which is shortcut for:
# composer dump-autoload
```

That's it. You are ready to go!


## 5. Bonus: Add Your First Test

Add test for a class was never easier. Create Test file for `FileSystem` class:

```diff
/app
/temp
/packages
    /file-system
        /src
            FileSystem.php
+       /tests
+           FileSystemTest.php
/vendor
composer.json
```

<br>

Add `\Tests` to `EntryDo\FileSystem` namespace:

```php
namespace EntryDo\FileSystem\Tests;

use PHPUnit\Framework\TestCase;

final class FileSystemTest extends TestCase
{
    public function testReadFile(): void
    {
        // test readFile() method
    }
}
```

<br>

Update `phpunit.xml` to cover all tests of local packages:

```xml
<phpunit bootstrap="vendor/autoload.php" colors="true">
   <testsuite>
       <directory>packages/*/tests</directory>
   </testsuite>
</phpunit>
```

<br>

And run tests:

```bash
vendor/bin/phpunit
```


<br>

So this is the easiest way how to use composer local packages in 2018. I hope you enjoy it the same way your application does.

<br>



Happy packaging!
