---
id: 285
title: "New in Symplify 9: Composer Json Manipulator - In Object API"
perex: |
    Have you ever needed to modify `composer.json` with PHP code? Then you're family `$json['require']['php'] ?? null` structures.
    They're hell to work with as any other array - verify the value if it's null, how deeply nested it is, etc.
    <br>
    <br>
    This is typical use case for [a monorepo](/2019/10/28/all-you-always-wanted-to-know-about-monorepo-but-were-afraid-to-ask), where we need to **merge many nested `composer.json` files** into a root `composer.json`.

tweet: "New Post on #php 🐘 blog: New in Symplify 9: Composer Json Manipulator - In Object API"
tweet_image: "/assets/images/posts/2020/composer-json-manipulator-tested.png"
---

With an array, it's mission impossible. What about intuitive methods on simple value object? You'd expect such package to exist in PHP world - but there [is none](https://packagist.org/?query=composer-json). So we made **an object wrapper that handles all your worries**:

```php
/** @var \Symplify\ComposerJsonManipulator\ValueObject\ComposerJson $composerJson */
$composerJson = ...;

// get directories that should exists
$composerJson->getAbsoluteAutoloadDirectories();

// get minimum stability
$composerJson->getMinimumStability();

// it's ready for print
$composerJson->getJsonArray();

// change value
$composerJson->setLicense('MIT');
```

We've been testing this package for a couple of ~~weeks~~ months, just to be sure it works well in practise.

<img src="/assets/images/posts/2020/composer-json-manipulator-tested.png" class="img-thumbnail">

It has over [240 000 downloads](https://packagist.org/packages/symplify/composer-json-manipulator/stats) now.

## 3 Steps to Create `ComposerJson` object

### 1. Install the Package

```bash
composer require symplify/composer-json-manipulator --dev
```

Register bundle:

```php
// config/bundles.php

declare(strict_types=1);

return [
    Symplify\ComposerJsonManipulator\ComposerJsonManipulatorBundle::class => [
        'all' => true,
    ],
];
```

### 2. Create `ComposerJson` with a Factory

```php
declare(strict_types=1);

namespace App;

use Symplify\ComposerJsonManipulator\ComposerJsonFactory;
use Symplify\ComposerJsonManipulator\ValueObject\ComposerJson;
use Symplify\SmartFileSystem\SmartFileInfo;

final class SomeClass
{
    /**
     * @var ComposerJsonFactory
     */
    private $composerJsonFactory;

    public function __construct(ComposerJsonFactory $composerJsonFactory)
    {
        $this->composerJsonFactory = $composerJsonFactory;
    }

    public function createFromExisting(): ComposerJson
    {
        $fileInfo = new SmartFileInfo(getcwd() . '/composer.json');
        $composerJson = $this->composerJsonFactory->createFromFileInfo($fileInfo);

        // analyse/modify $composerJson

        return $composerJson;
    }

    public function createNew(): ComposerJson
    {
        $composerJson = $this->composerJsonFactory->createEmpty();
        $composerJson->setPreferStable(true);

        return $composerJson;
    }
}
```

### 3. Print modified `ComposerJson`

```php
namespace App;

use Symplify\ComposerJsonManipulator\Printer\ComposerJsonPrinter;

class SomeClass
{
    /**
     * @var ComposerJsonPrinter
     */
    private $composerJsonPrinter;

    public function __construct(ComposerJsonPrinter $composerJsonPrinter)
    {
        $this->composerJsonPrinter = $composerJsonPrinter;
    }

    public function printAndReport(ComposerJson $composerJson)
    {
        // the file is saved + printed content returned
        $printedContent = $this->composerJsonPrinter->print(
            $composerJson,
            getcwd() . '/composer.json'
        );

        // show it to user $printedContent, so they know what was printed
    }
}
```

That's it! The `ComposerJson` is not 1:1 to full composer schema. That would be a mess full of methods and constants that are rarely used in practice. Instead, it has only the method we used.

**Do you miss some method?** No problem, [send a pull-request to add it](https://github.com/symplify/symplify). That way, we'll know someone will use it and we'll be happy to merge it.

<br>


Happy coding!
