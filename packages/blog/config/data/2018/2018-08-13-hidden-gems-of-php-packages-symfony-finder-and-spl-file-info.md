---
id: 131
title: "Hidden Gems of PHP Packages: Symfony\\Finder and SplFileInfo"
perex: |
    The series on not-so-well-known packages that might save your ass more than you think continues.
    Today we look on **files as objects**.
tweet: "New Post on my Blog: Hidden Gems of PHP Packages: Symfony\\Finder and SplFileInfo #symfony #filesystem #php #spl"


---

## Why I Use it?

1. Do you work with files in 5 different places in your application in a single way and you miss consistent naming?

    ```php
    <?php declare(strict_types=1);

    function processFile(string $file) {
         // is absolute?
         // relative to what?
    }

    function processFile(string $fileAbsolute) {}
    function processFile(string $filePath) {}
    function processFile(string $absoluteFile) {}
    function processFile(string $relativePath) {}
    function processFile(string $filename) {}
    ```

2. Do you need to test file paths on various CI machines?

    ```diff
    Expected test string didn't match:
    -Error was found in: /home/im-very-cool-guy/my-website/www/my-home-porn-web/public/index.php
    +Error was found in: /travic-ci/travis-directory-for-this-web/public/index.php
    ```

3. Do you want to report the user the relative path instead of hard to read absolute one?

    ```diff
    -Error was found in: /home/im-very-cool-guy/my-website/www/my-home-porn-web/public/index.php
    +Error was found in: public/index.php
    ```

4. Do you want to forget all those `file_*` functions and just work with the file instead?

    ```php
    <?php declare(strict_types=1);

    file_exists($file);
    is_file($file);
    is_directory($file); // actually: is_dir($file);
    is_readable($file);
    is_absolute($file); // well, you have to create this one yourself, and don't forget the Windows and Linux differences!
    ```

**Thanks to Symfony\Finder and its custom `SplFileInfo` I can be lazy, use object API and work safer and faster** in all these cases above and more. Actually, I started to using `SplFileInfo` over *stringly* file paths/names after too many bugs appeared in my code and I'm happier and more relaxed ever since.

What is [*splFileInfo*](http://php.net/manual/en/class.splfileinfo.php)? It's native object in PHP - like `DateTime` - that wraps the file and provides nice object API for it. The Symfony\Finder package adds 3 extra methods that make work with files just a bit smoother. They go very well together since the package creates all the `splFileInfo` instances for you.

## How to Install

```bash
composer require symfony/finder
```

## How I Use it

We'll find all available feature in [the documentation](https://symfony.com/doc/current/components/finder.html), but basic usage it like this:

```php
<?php declare(strict_types=1);

use Symfony\Component\Finder\Finder;

$finder = Finder::create()
    ->files()
    ->in(__DIR__)
    ->name('composer.json')
    ->getIterator();

foreach ($finder as $splFileInfo) {
    var_dump($splFileInfo); // instance of "Symfony\Component\Finder\SplFileInfo"
}
```

## `Symfony\Component\Finder\Finder`

### `name()`

This method accepts also regular expressions. Do you want to find all YAML files?

```php
<?php declare(strict_types=1);

use Symfony\Component\Finder\Finder;

$finder = Finder::create();
$finder->name('#\.(yaml|yml)$#');
```

### `append()`

Do you want to add just a single file that finder criteria would not find? Normally, you'd have to create `SplFileInfo` manually, think of relative/absolute paths etc. So much work. Instead, you can just append it and Finder will add it for you.

```php
<?php declare(strict_types=1);

use Symfony\Component\Finder\Finder;

return Finder::create()
    ->name('#\.php$#')
    ->in(__DIR__ . '/Source')
    ->append([__DIR__ . '/Source/SomeClass.twig']);
```

### `notPath()` or `exclude()`

It's common and bad practice to put tests files into `/src`. It's historical reason mostly, but we still have to deal with that.
You don't want to work with 3rd party code tests, right?

```php
<?php declare(strict_types=1);

use Symfony\Component\Finder\Finder;

$finder = Finder::create()
    // directories
    ->exclude('spec')
    ->exclude('test')
    ->exclude('Tests')
    ->exclude('tests')
    ->exclude('Behat')
    ->name('*.php');

// or match path name
$finder->notPath('#tests#');
```

## `Symfony\Component\Finder\SplFileInfo`

### `getRelativePath()`

Let's get back to the `composer.json` example above (from in [MonorepoBuilder](https://github.com/Symplify/MonorepoBuilder/blob/71d81fe279b43b3353d107560198fd5cf52d487c/src/PackageComposerFinder.php#L23-L38)). This is how we get relative **directory**:

```php
<?php declare(strict_types=1);

/** @var \Symfony\Component\Finder\SplFileInfo $splFileInfo */
$splFileInfo->getRelativePath(); // "/"
```

### `getRelativePathname()`

This method is bit different - it returns **relative filename**:

```php
<?php declare(strict_types=1);

/** @var \Symfony\Component\Finder\SplFileInfo $splFileInfo */
$splFileInfo->getRelativePath(); // "composer.json"
```

This is very handy for output reporting in PHP CLI Apps like ECS, PHP CS Fixer, PHP_CodeSniffer or PHPStan. Compare yourself - the computer absolute scope:

```diff
An error was found in /home/im-very-cool-guy/my-website/www/my-home-porn-web/public/index.php
```

and human relative scope:

```diff
An error was found in public/index.php
```

### Honorable Mentions

There are few more methods that I use from time to time:

```php
<?php declare(strict_types=1);

/** @var \Symfony\Component\Finder\SplFileInfo $splFileInfo */
$splFileInfo->getRealPath();
// absolute path - returns "/var/www/this-post/composer.json"

$splFileInfo->getContents();
// gets the content of file with error propagated to an exception - very nice!

$splFileInfo->getBasename('.' . $splFileInfo->getExtension());
// returns "composer"
```

<br>

Do you like it? **Go and give [Symfony\Finder](https://github.com/symfony/finder) or at least [`SplFileInfo`](http://php.net/manual/en/class.splfileinfo.php) a try**.

Happy coding!
