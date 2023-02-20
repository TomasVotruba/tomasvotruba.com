---
id: 346
title: "How to Run Naked Unit&nbsp;Tests in a&nbsp;New&nbsp;Legacy&nbsp;Project"
perex: |
    You know the situation. You come to a new project that you should upgrade and refactor. It has some tests that ~~you~~ long-term developer can run locally. But the automated CI that runs tests on every commit is missing.
    <br>
    <br>
    Shall we start refactoring and adding CI tools like PHPStan or ECS? **How about using what is already there**? The tests.
    <br>
    <br>
    But the tests require this PHP extension, those environment variables, this external service, and these few Docker images to be running.


    What if we can **find the naked unit tests** and run them today by ourselves?

tweet: "New Post on the 🐘 blog: How to Run Naked Unit Test in a New Legacy Project"
---

<div class="card border-warning mt-4">
    <div class="card-header text-black bg-warning shadow">
        <strong>Proof over theory?</strong>
        This technique allowed us to run 41 tests without any setup. That's 41&nbsp;more than we usually have before investing a lot of learning time. Maybe it will help your legacy project too.
    </div>
</div>

<br>

When we come to a project, the first thing to do is run tests. Just in case they work without any setup:

```bash
vendor/bin/phpunit
```

<br>

If we get a positive response and tests run successfully, we can thank the developers of this project and start upgrading and refactoring. But more likely, we get one of the following responses:

* *function/extension is missing*
* *ENV is missing*
* *Redis is missing*

Then we can check for `.env`, `docker-compose.yml`, `phpunit.xml` extensions in `composer.json` or even `README.md` to figure out what extension we need to run the tests. But maybe, we don't have to.

## External and Local test Dependencies

Our tests depend on some manual steps we have to do. They do not work out of the box, but they might run after we complete this value here and run this CLI command.

The test dependency can be split into 2 categories:

* External: those which depend on **on dynamic content**
* Local: those that depend on the **static source code** itself

The first group requires our manual input and research, so there is nothing we can do right now to run them except to learn more about the project.

However, we can run the second group right now:

```php
use PHPUnit\Framework\TestCase;

use App\ValueObject\Tool;

final class SomeTest extends TestCase
{
    public function test()
    {
        $tool = new Tool('Rector');
        $this->assertSame('Rector', $tool->getName());
    }
}
```

...and we can see if a test fails or passes. How?

```bash
vendor/bin/phpunit
```

Looks so easy, and it is.

But there is a catch. We **have to know which of these are the local ones** and which are hiding a fractal of dependencies.

<br>

## How to detect Local Test?

Let's look at examples. What dependencies does the following test have?

```php
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

final class ControllerTest extends KernelTestCase
{
    public function tests()
    {
    }
}
```

From `KernelTestCase` we can assume that:

* it depends on a framework
* that depends on the dependency container being built
* that depends on loaded bundles
* that depends on a database
* that depends on installed PHP extensions
* that usually depends on Docker build
* that depends on correct `.env` variables

If the test meets one of these conditions, we can skip it.

<br>

All the other tests left are **the ones we want to test**. We put them explicitly into `phpunit.xml` into the new test suite:

```xml
<?xml version="1.0"?>
<phpunit
    xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
    xsi:noNamespaceSchemaLocation="vendor/phpunit/phpunit/phpunit.xsd"
    bootstrap="vendor/autoload.php"
>
    <testsuites>
        <testsuite name="unit">
            <file>tests/Services/ConfigPrinterTest.php</file>
            <file>tests/Services/PackageUpgradeTest.php</file>
        </testsuite>
    </testsuites>
</phpunit>
```

So we go one test by one and carefully review their parent class...

That sounds like a lot of work. Hmm, how could we automate it?

## 4 Steps to Automate Local Test Detection

In previous post, we learned about few [useful commands for Easy CI setup](/blog/5-commands-from-easy-ci-that-makes-your-ci-stronger/). There is one more just for our use case.

<br>

**1. Install Composer Package**

```bash
composer require symplify/easy-ci --dev
```

<br>

**2. Run Command on your tests directory**

```bash
vendor/bin/easy-ci detect-unit-tests tests
```

This command generated a `phpunit-unit-files.xml` file that contains an XML list of detected test files.

<br>

**3. Open the `phpunit-unit-files.xml` and move files to your `phpunit.xml`**

```xml
<?xml version="1.0"?>
<phpunit
    xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
    xsi:noNamespaceSchemaLocation="vendor/phpunit/phpunit/phpunit.xsd"
    bootstrap="vendor/autoload.php"
>
    <testsuites>
        <testsuite name="unit">
            <file>tests/Services/ConfigPrinterTest.php</file>
            <file>tests/Services/PackageUpgradeTest.php</file>
        </testsuite>
    </testsuites>
</phpunit>
```

<br>

**4. Run `--testsuite` unit group**

```bash
vendor/bin/phpunit --testsuite unit
```

Then set up your CI to run all your local tests we've detected this way.

Your next refactoring is now a bit safer, and you can continue to learn more about the new project.

<br>

Happy coding!
