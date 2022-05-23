---
id: 361
title: "How to Test Latte Macro in 4 Steps"
perex: |
    We're upgrading a couple of Latte macros into Latte 3. None of them have tests, and all of them will change entirely because of a complete rewrite of the Latte parser.
    <br><br>
    To get ready, we want to prepare tests first. Although writing Latte macros is the most complicated feature in Latte, testing is easier than you think.
---

In another post, we focused on how to change the macro contents from Latte 2 to Latte 3 syntax. Today we'll see how to **prepare a test that helps you upgrade easily**. I never wrote any Latte macro in my life, but tests proved very helpful.

## 1. Create Test Case with Container

We'll need a `Latte\Engine` instance to load the Latte extension from provided `config.neon`.

We could create manual registration of `Latte\Engine`, but using `nette/di` will be closer to the actual use of Latte in our framework and scales easily in case of adding more extensions.

First, create a `ContainerFactory` that accepts config:

```php
namespace Tests\DI;

use Nette\Bootstrap\Configurator;
use Nette\DI\Container;
use Nette\Utils\FileSystem;

final class ContainerFactory
{
    public function create(string $config): Container
    {
        $tempDirectory = __DIR__ . '/../temp';

        // clear before factory create to invoke cache rebuild
        FileSystem::delete($tempDirectory);

        $configurator = new Configurator();
        $configurator->addConfig($config);
        $configurator->setTempDirectory($tempDirectory);

        return $configurator->createContainer();
    }
}
```

## 2. Prepare `Latte\Engine` Service

We cannot get `Latte\Engine` directly, as the service does not exist.
Instead, we'll use `LatteFactory` to create it. Create a simple test case and prepare the `Latte\Engine` in `setUp()`:

```php
namespace Tests\Latte\Macros;

use PHPUnit\Framework\TestCase;
use Latte\Engine;
use Latte\Loaders\StringLoader;
use Nette\Bridges\ApplicationLatte\LatteFactory;

final class EmbeddedSvgMacroTest extends TestCase
{
    private Engine $latteEngine;

    protected function setUp(): void
    {
        $containerFactory = new ContainerFactory();
        $container = $containerFactory->create(__DIR__ . '/config/svg_macro.neon');

        /** @var LatteFactory $latteFactory */
        $latteFactory = $container->getByType(LatteFactory::class);
        $this->latteEngine = $latteFactory->create();

        $this->latteEngine->setLoader(new StringLoader());
    }
}
```

<br>

### What is the Loader change for?

You might have noticed that we've added the extra sauce to the `Latte\Engine`:

```php
$this->latteEngine->setLoader(new StringLoader());
```

<br>

Latte uses by default `Latte\Loaders\FileLoader`, which accepts the name of the template or path. So when we try to render string:

```php
$this->latteEngine->setLoader('{embeddedSvg "file.svg"}');
```

It looks for the `{embeddedSvg "file.svg"}` file and obviously - fails to find.

<br>

With the `StringLoader`, on the other hand, it will render the Latte input string directly.

## 3. Dump Your Macro Contents

The macro has a straightforward job, and it converts input Latte to PHP contents of the cached template. When we test macro, we expect specific output.

We have to create PHP output first from the first test run.

```php
public function test(): void
{
    $compiledPhpCode = $this->latteEngine->render('{embeddedSvg "file.svg"}');

    // this creates first fixture file - run this only once, then remove!
    file_put_contents(__DIR__ . '/expected_file.php', $compiledPhpCode);

    $this->assertStringEqualsFile(__DIR__ . '/expected_file.php', $compiledPhpCode);
}
```

<br>

Latte uses tabs by default. Depending on your code style, you might want to convert them to 4 spaces:

```php
$compiledPhpCode = Strings::replace($compiledPhpCode, "#\t#", '    ');
```

<br>

Run PHPUnit to create the first fixture file:

```bash
vendor/bin/phpunit
```

The test should pass, as the run just created the fixture.

Good job!

## 4. Extend Test for Various Situations

One more thing. Just to be sure, we'll cover all various input situations:

```html
// basic
{embeddedSvg "file.svg"}

// no key argument
{embeddedSvg "file.svg", "single_no_value"}

// multiple arguments
{embeddedSvg "file.svg", "class" => "blue", "size" => "medium"}
```

<br>

Now our tests cover all possible situations this macro can use.

Commit, push and merge. That's it!

<br>

Happy coding!
