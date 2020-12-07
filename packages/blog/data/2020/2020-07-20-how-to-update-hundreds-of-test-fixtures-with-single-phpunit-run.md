---
id: 270
title: "How to Update Hundreds of Test Fixtures with Single PHPUnit run"
perex: |
    In [previous post](/blog/2020/07/13/the-most-effetive-test-i-found-in-7-years-of-testing/), we look at the benefits of visual snapshot testing for lazy people. How bare *input/output* code in a single file makes tests easy to read for new contributors.
    <br><br>
    Today, we look at **how to maintain visual snapshot tests**.
    <br><br>
    Let's say we need to add `declare(strict_types=1);` to output part of 100 test fixtures? Would you add it manually in every single file?

tweet: "New Post on #php 🐘 blog: How to Update Hundreds of Test Fixtures with Single PHPUnit run"
tweet_image: "/assets/images/posts/2020/update_tests_example.gif"
---

Short quiz from last week: **what is the visual snapshot test**?

A test where the new test case is a single fixture file:

```bash
before
-----
after
```

<br>

Let's say we test a service that multiplies the input number by 5.

How would the fixture look like?

```bash
10
-----
50
```

Correct! Now let's learn something new.

<blockquote class="blockquote text-center">
    "It's easy to write tests that are hard to maintain."
</blockquote>

## Use Case: Add 1 Line to 100 Files

I'm currently working on a tool [that migrates YAML configs to PHP](https://twitter.com/VotrubaT/status/1285190524627025925). It's almost finished... but there is one thing missing in all those configs. *PHP* configs.

<br>

I forgot to add the `declare(strict_types=1);` line. So now, every time you generate a PHP config, you have to run coding standards too on these files. **So much extra work you, end-developers**.

When was my mission changed to *adding* developers extra tedious work? **We need to handle it**.

<br>

### What Can We Do Now?

- add it to the PHP script, that converts the file to PHP, so the line is there
- now tests start failing because the line is missing in the expected output

```yaml
parameters:
    key: 'value'
-----
<?php

use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

return static function (ContainerConfigurator $containerConfigurator): void {
    $parameters = $containerConfigurator->parameters();

    $parameters->set('key', 'value');
};
```

- go through test fixtures and **manually add the line**

If we are lucky and the pattern is unique, we can use PHPStorm find/replace or even regular expressions. This might work for this simple case, **but soon fails for real-life cases** like "add extra method call under each $service->set()".

We can do better.

<br>

## Automated Test Fixture Updates

- How can we automate the update **under few seconds**?
- How can we do it **without thinking** about what needs to be changed and how?

<br>

With visual snapshot tests this is piece of cake. All we need is `UPDATE_TESTS=1` env and normal PHPUnit run:

<img src="/assets/images/posts/2020/update_tests_example.gif" class="img-thumbnail">

Now, all the 100 files have completed `declare(strict_types=1);`:

```yaml
parameters:
    key: 'value'
-----
<?php

declare(strict_types=1);

use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

return static function (ContainerConfigurator $containerConfigurator): void {
    $parameters = $containerConfigurator->parameters();

    $parameters->set('key', 'value');
};
```

## How did that Happened?

In the previous post, we looked on [how to split and test fixture files](/blog/2020/07/13/the-most-effetive-test-i-found-in-7-years-of-testing/#code-time).

We only update this code with a single method, that will handle the fixture updates:

```diff
 <?php

 use PHPUnit\Framework\TestCase;

 final class FirstTryTest extends TestCase
 {
     public function test(): void
     {
         $filePath = __DIR__ . '/fixture/first_try.php';

         $fixtureContent = file_get_contents($filePath);
         [$input, $expectedOutput] = explode("\n-----\n", $fixtureContent);

         // test your main domain service
         $output = $this->processInputInYourDomain($input);

+        $this->updateFixture($input, $output, $filePath);

         $this->assertSame($expectedOutput, $output);
     }
 }
```

And add the `updateFixture()` method:

```php
private function updateFixture(
    string $input,
    string $currentOutput,
    string $fixtureFilePath
): void {
    // only runs when UPDATE_TESTS=1 is put before PHPUnit run
    if (! getenv('UPDATE_TESTS')) {
        return;
    }

    // update changed output content part
    $newOriginalContent = $input . PHP_EOL .
        '-----' . PHP_EOL .
        $currentOutput . PHP_EOL;

    // update the fixture file
    file_put_contents($fixtureFilePath, $newOriginalContent);
}
```

And that's it!

The best place to add `updateFixture()` is an abstract test case, e.g., `AbstractVisualSnapshotTestCase`. So we have one place to change.

<br>

Now you can do massive changes in your business logic, and even you rewrite the output completely, all you need to run is:

```bash
UPDATE_TESTS=1 vendor/bin/phpunit
```

Now we know the simplest way to maintain tests that are easy to read there is... or is it?

<br>

Happy coding!
