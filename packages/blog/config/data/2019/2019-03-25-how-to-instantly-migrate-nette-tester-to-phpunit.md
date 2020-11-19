---
id: 197
title: 'How to Instantly Migrate Nette\Tester to PHPUnit'
perex: |
    We had 🍺 after PHP meetup in Prague and Tomas asked me:
    <br>
    "We don't use Nette, but we still have many tests in Tester. Can Rector migrate them to PHPUnit?"
    <br>
    "Hold my 🍺"
tweet: "A new Post on #php 🐘 blog: How to Instantly Migrate #nettefw Tester to #phpunit"

updated_since: "November 2020"
updated_message: |
    Switch from deprecated `--set` option to `rector.php` config.
---

In the last post we looked on [instant migration of PhpSpec to PHPUnit](/blog/2019/03/21/how-to-instantly-migrate-phpspec-to-phpunit).

PhpSpec has a different architecture than PHPUnit - e.g.

- PHPUnit creates mocks in body of tested method `$this->createMock()` ,
- but PhpSpec puts them in parameters `public function is_should_work(Category $categoryMock)`.

This has a huge influence on the code, so **it took a week to cover these differences in migration path**.

How does Tester compare to PHPUnit?

## Trends Revealed

In the last post we looked at absolute downloads and trends of 3 PHP unit test frameworks:

<div class="row text-center mb-5 mt-5">
    <div class="col-md-4 col-sm-4">
        <a href="https://packagist.org/packages/nette/tester/stats">
            <img src="/assets/images/posts/2019/unit-mig/tester.png">
            <br>
            <em>1 mil. downloads - Tester</em>
        </a>
    </div>
    <div class="col-md-4 col-sm-4">
        <a href="https://packagist.org/packages/phpspec/phpspec/stats">
            <img src="/assets/images/posts/2019/unit-mig/spec.png">
            <br>
            <em>14 mils. downloads - PhpSpec</em>
        </a>
    </div>
    <div class="col-md-4 col-sm-4">
        <a href="https://packagist.org/packages/phpunit/phpunit/stats">
            <img src="/assets/images/posts/2019/unit-mig/phpunit.png">
            <br>
            <em>117 mils. downloads - PHPUnit</em>
        </a>
    </div>
</div>

Putting numbers and trends aside - **this is about your needs**. Do you need to change from Doctrine to Eloquent? From Symfony 4.2 to Laravel 5.8? From Symfony to Nette? Go for it, Rector will help you with the boring PHP work you'd love to skip.

The guy in the pub that night needed this, so...  *challenge accepted*!

## Single Test Case

Luckily, Tester and PHPUnit are like twins:

- share the same approach in configuring tests - `setUp` & `tearDown`
- do assert with a call - `Assert::true($value)` vs. `self::assertTrue($value)`
- do share naming  - `public function testSomething()`
- do share data providers - `@dataProvider`

So all **we need to do is rename a few methods**? There are still a few gotchas:

- `Assert::exception()` uses code inside anonymous function, while in PHPUnit it's just above the code that should fail
- Tester includes bootstrap itself, PHPUnit include in `phpunit.xml`
- Tester creates the test under the test `(new SomeTest())->run()`, PHPUnit creates them automatically

Luckily, last 2 operations are subtractions, so we can just remove them.

### And the Result?

```diff
 <?php

 namespace App\Tests;

 use App\Entity\SomeObject;
-use Tester\Assert;
-use Tester\TestCase;

-require_once __DIR__ . '/bootstrap.php';

-class ExpensiveObjectTest extends TestCase
+class ExpensiveObjectTest extends \PHPUnit\Framework\TestCase
 {
     public function testSwitches()
     {
-        Assert::false(5);
+        $this->assertFalse(5);

-        Assert::falsey('value', 'some messsage');
+        $this->assertFalse((bool) 'value', 'some messsage');

-        Assert::truthy(true);
+        $this->assertTrue(true);
     }

     public function testTypes()
     {
         $value = 'x';
-        Assert::type('array', $value);
+        $this->assertIsArray($value);

-        Assert::type(SomeObject::class, $value);
+        $this->assertInstanceOf(SomeObject::class, $value);
     }

     public function testException()
     {
         $someObject = new SomeObject;
-        Assert::exception(function () use ($someObject) {
-            $someObject->setPrice('twenty dollars');
-        }, InvalidArgumentException::class, 'Price should be string, you know');
+        $this->expectException(InvalidArgumentException::class);
+        $this->expectExceptionMessage('Price should be string, you know');
+        $someObject->setPrice('twenty dollars');
     }

+    /**
+     * @doesNotPerformAssertions
+     */
     public function testNoError()
     {
-        Assert::noError(function () {
-             new SomeObject(25)
-        });
+        new SomeObject(25);
     }
 }

-(new ExpensiveObjectTest())->run();
```

## How to Instantly Migrate from Nette\Tester to PHPUnit?

1. Install Rector

```bash
composer require rector/rector --dev
```

2. Add `rector.php` config:

```php
// rector.php
use Rector\Core\Configuration\Option;
use Rector\Set\ValueObject\SetList;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

return static function (ContainerConfigurator $containerConfigurator): void {
    $parameters = $containerConfigurator->parameters();
    $parameters->set(Option::SETS, [SetList::NETTE_TESTER_TO_PHPUNIT]);
};
```

3. Run it on `/tests` directory

```bash
vendor/bin/rector process tests
```

Then take few minutes to polish the details that Rectors missed and send the PR to your project
<em class="fas fa-fw fa-check text-success fa-lg"></em>

<br>

Happy coding!
