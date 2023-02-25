---
id: 224
title: "Still on PHPUnit 4? Come to PHPUnit 8 Together in a Day"
perex: |
    Last month I was on [PHPSW meetup](https://twitter.com/akrabat/status/1181998973588037632) in Bristol UK with Rector talk. To be honest, [Nette to Symfony migration under 80 hours](/blog/2019/08/26/how-we-migrated-54-357-lines-of-code-nette-to-symfony-in-2-people-under-80-hours/) was not a big deal there.


    To my surprise, **upgrading PHPUnit tests was**. So I was thinking, let's take it from the floor in one go, from PHPUnit 4 to the latest PHPUnit 8.


updated_since: "November 2020"
updated_message: |
    Switch from deprecated `--set` option to `rector.php` config.
---

<img src="/assets/images/posts/2019/phpunit/tweet.png" class="mt-4 mb-4">

## 1. Planning

Before we dive into the upgrading of our tests, we need to **look at minimal PHP version** required by each PHPUnit.
The [PHPUnit release process](https://github.com/sebastianbergmann/phpunit/wiki/Development-and-Release-Process) states, **that each new PHPUnit major version requires a newer minor PHP version**.

What does that mean?

<table class="table table-bordered table-responsive">
    <thead class="thead-inverse">
        <tr>
            <th>PHPUnit</th>
            <th>Required PHP version</th>
            <th>Relase Year</th>
        </tr>
    </thead>
    <tr>
        <td>PHPUnit 4</td>
        <td>PHP 5.3-5.6</td>
        <td>2015</td>
    </tr>
    <tr>
        <td>PHPUnit 5</td>
        <td>PHP 5.6-7.4</td>
        <td>2016</td>
    </tr>
    <tr>
        <td>PHPUnit 6</td>
        <td>PHP 7.0-7.4</td>
        <td>2017</td>
    </tr>
    <tr>
        <td>PHPUnit 7</td>
        <td>PHP 7.1-7.4</td>
        <td>2018</td>
    </tr>
    <tr>
        <td>PHPUnit 8</td>
        <td>PHP 7.2-7.4</td>
        <td>2019</td>
    </tr>
</table>

We need to plan and combine the PHP upgrade.

This is **the full path** we'll go through:

- upgrade PHP 5.3 → 5.6
- upgrade PHPUnit 4 → 5
- upgrade PHP 5.6 → 7.0
- upgrade PHPUnit 5 → 6
- upgrade PHP 7.0 → 7.1
- upgrade PHPUnit 6 → 7
- upgrade PHP 7.1 → 7.2
- upgrade PHPUnit 7 → 8

*Note: To keep this post simple, we'll focus on PHPUnit upgrade only here. But it's possible you'll need to use Rector for PHP upgrades between PHPUnit upgrade steps too.*

## 2. Single Version Upgrade = 1 pull-request

Do you enjoy *the rush* of changing a thousand files at once? It drives to do even more and more changes.

The same thing happens with upgrading code - we upgrade PHP and tests passes, great! Let's do upgrade PHPUnit. Oh, maybe we could refactor this method so it's more readable. Oh, now this other method looks silly, let's clean that up too...

<img src="/assets/images/posts/2019/phpunit/explode.jpg" class="img-thumbnail">

<span class="text-danger">
    <strong>STOP!</strong>
</span>

This is the easiest way **to get stuck** in broken tests, with dug up pieces of code all over the project, overheat our brain in [huge cognitive complexity](/blog/2018/05/21/is-your-code-readable-by-humans-cognitive-complexity-tells-you/) and ~~give up~~ rage quit the upgrade saying "it's hard as they say".


### The Golden Rule of Successful Upgrade

- one minor version at a time
- one pull-request at a time
- tested, our CI passes
- **only after the PR is merged, we go to next minor version**

Unless you're upgrading 10 test files, of course. But in the rest of the case, this approach would save me many failed attempts in the paths. That's why **planning is the most important step of all** for huge upgrade operations.


## 3. PHPUnit 4 to 5

### From `getMock()` to `getMockBuilder()`

```diff
 final class MyTest extends PHPUnit_Framework_TestCase
 {
     public function test()
     {
-        $someClassMock = $this->getMock('SomeClass');
+        $someClassMock = $this->getMockBuilder('SomeClass')->getMock();
     }
 }
```

<br>

These changes can be delegated to Rector:

```bash
composer require phpunit/phpunit "^5.0" --dev
```

Update set in `rector.php`

```php
use Rector\PHPUnit\Set\PHPUnitSetList;
use Rector\Config\RectorConfig;

return function (RectorConfig $rectorConfig): void {
    $rectorConfig->import(PHPUnitSetList::PHPUNIT_50);
};
```

Run Rector

```bash
vendor/bin/rector process tests
```

## 4. PHPUnit 5 to 6

This is the release where **PHPUnit got namespaces**, yay! Well, yay for the project, nay for the upgrades.

### From Underscore to Slash

Although there is a lot of underscore <=> slash aliases for a smoother upgrade, it will be removed in the future PHPUnit version, so we better deal with it right now.

First, we need to replace all:

```diff
-PHPUnit_Framework_TestCase
+\PHPUnit\Framework\TestCase # mind the pre-slash "\"
```

Without pre-slash, your code might fail.

<a class="btn btn-primary" href="https://3v4l.org/5Sjjh">
    See 3v4l.org to understand why
</a>

<br>

Second, replace the rest of the `PHPUnit_*` classes with a namespace. Listeners, test suites, exceptions... etc.
This is hell for us human, luckily easy-pick for Rector.

### Add `doesNotPerformAssertions` to test With no Assertion

```diff
+    /**
+     * @doesNotPerformAssertions
+     */
     public function testNoError()
     {
         new SomeObject(25);
     }
```

### `setExpectedException()` to `expectException()`

And not only that! Also, split arguments to own method:

```diff
 <?php

 class MyTest extends \PHPUnit\Framework\TestCase
 {
     public function test()
     {
-        $this->setExpectedException('SomeException', $message, 101);
+        $this->expectException('SomeException');
+        $this->expectExceptionMessage($message);
+        $this->expectExceptionCode(101);
     }
 }
```

Also `setExpectedExceptionRegExp()` was removed.


### `@expectedException` to `expectException()`

These changes are a real challenge for simple human attention:

```diff
-expectedException
+expectException
```

Mind the missing "ed".

```diff
 <?php

 class MyTest extends \PHPUnit\Framework\TestCase
 {
-    /**
-     * @expectedException SomeException
-     */
     public function test()
     {
+        $this->expectException('SomeException');
     }
 }
```

<br>

These changes can be delegated to Rector:

```bash
composer require phpunit/phpunit "^6.0" --dev
```

Update set in `rector.php`

```php
use Rector\PHPUnit\Set\PHPUnitSetList;
use Rector\Config\RectorConfig;

return function (RectorConfig $rectorConfig): void {
    $rectorConfig->import(PHPUnitSetList::PHPUNIT_60);
};
```

Run Rector

```bash
vendor/bin/rector process tests
```

## 5. PHPUnit 6 to 7

### Remove "test" prefix on Data Providers

I have no idea how tests and data provider methods were detected before this:

```diff
 class WithTestAnnotation extends \PHPUnit\Framework\TestCase
 {
     /**
-     * @dataProvider testProvideDataForWithATestAnnotation()
+     * @dataProvider provideDataForWithATestAnnotation()
      */
     public function test()
     {
         // ...
     }

-    public function testProvideDataForWithATestAnnotation()
+    public function provideDataForWithATestAnnotation()
     {
         return ['123'];
     }
 }
```

### Rename `@scenario` annotation to `@test`

```diff
 class WithTestAnnotation extends \PHPUnit\Framework\TestCase
 {
     /**
-     * @scenario
+     * @test
      */
     public function test()
     {
         // ...
     }
 }
```

### Change `withConsecutive()` Arguments to Iterable

This rather small change can cause a huge headache. It's [a fix of silent false positive](https://github.com/sebastianbergmann/phpunit/commit/72098d80f0cfc06c7e0652d331602685ce5b4b51).

How would you fix the following code, if you know that the argument of `withConsecutive()` must be iterable (array, iterator...)?

```php
class SomeClass
{
    public function run($one, $two)
    {
    }
}

class SomeTestCase extends \PHPUnit\Framework\TestCase
{
    public function test()
    {
        $someClassMock = $this->createMock(SomeClass::class);
        $someClassMock
            ->expects($this->exactly(2))
            ->method('run')
            ->withConsecutive(1, 2, 3, 5);
    }
}
```

Like this?

```diff
-->withConsecutive(1, 2, 3, 5);
+->withConsecutive([1, 2, 3, 5]);
```

Well, the tests would pass it, but it would be another silent positive. Look at `SomeClass::run()` method. How many arguments does it have?

Two. So we need to create array chunks of size 2.

```diff
-->withConsecutive(1, 2, 3, 5);
+->withConsecutive([1, 2], [3, 5]);
```

<br>

These changes can be delegated to Rector:

```bash
composer require phpunit/phpunit "^7.0" --dev
```

Update set in `rector.php`

```php
use Rector\PHPUnit\Set\PHPUnitSetList;
use Rector\Config\RectorConfig;

return function (RectorConfig $rectorConfig): void {
    $rectorConfig->import(PHPUnitSetList::PHPUNIT_70);
    $rectorConfig->import(PHPUnitSetList::PHPUNIT_75);
};
```

Run Rector

```bash
vendor/bin/rector process tests
```

## 6. PHPUnit 7 to 8

### Ignore Cache File

The [optional caching of test results](https://github.com/sebastianbergmann/phpunit/pull/3147) added in PHPUnit 7.3 is not enabled by default. We need to add the cache file to `.gitignore`:

```txt
# .gitignore
.phpunit.result.cache
```

### Remove type `$dataName` in `PHPUnit\Framework\TestCase` Constructor Override

```diff
 <?php

 abstract class MyAbstractTestCase extends \PHPUnit\Framework\TestCase
 {
-    public function __construct(?string $name = null, array $data = [], string $dataName = '')
+    public function __construct(?string $name = null, array $data = [], $dataName = '')
     {
     }
 }
```

### Replace `assertContains()` with Specific `assertStringContainsString()` Method

```diff
 <?php

 final class SomeTest extends \PHPUnit\Framework\TestCase
 {
     public function test()
     {
-        $this->assertContains('foo', 'foo bar');
+        $this->assertStringContainsString('foo', 'foo bar');

-        $this->assertNotContains('foo', 'foo bar');
+        $this->assertStringNotContainsString('foo', 'foo bar');
     }
 }
```

### Replace `assertInternalType()` with Specific Methods

This change [is huge](https://github.com/sebastianbergmann/phpunit/commit/a406c85c51edd76ace29119179d8c21f590c939e).

**2 methods were removed, 22 methods are added.**

```diff
 <?php

 final class SomeTest extends \PHPUnit\Framework\TestCase
 {
     public function test()
     {
-        $this->assertInternalType('string', $value);
+        $this->assertIsString($value);
     }
 }
```

### Change `assertEquals()` method Parameters to new Specific Methods

```diff
 final class SomeTest extends \PHPUnit\Framework\TestCase
 {
     public function test()
     {
         $value = 'value';
-        $this->assertEquals('string', $value, 'message', 5.0);
+        $this->assertEqualsWithDelta('string', $value, 5.0, 'message');

-        $this->assertEquals('string', $value, 'message', 0.0, 20);
+        $this->assertEquals('string', $value, 'message', 0.0);

-        $this->assertEquals('string', $value, 'message', 0.0, 10, true);
+        $this->assertEqualsCanonicalizing('string', $value, 'message');

-        $this->assertEquals('string', $value, 'message', 0.0, 10, false, true);
+        $this->assertEqualsIgnoringCase('string', $value, 'message');
     }
 }
```

### Last `_` is now Namespaced

This removes the last piece of back-compatible underscore class:

```diff
-PHPUnit_Framework_MockObject_MockObject
+PHPUnit\Framework\MockObject\MockObject
```

### Replace `assertArraySubset()` with

This method was removed [because of its vague behavior](https://github.com/sebastianbergmann/phpunit/issues/3494).
The proposed solution is [rdohms/phpunit-arraysubset-asserts](https://github.com/rdohms/phpunit-arraysubset-asserts) polyfill.

```diff
 namespace Acme\Tests;

+use DMS\PHPUnitExtensions\ArraySubset\Assert;

 final class AssertTest extends \PHPUnit\Framework\TestCase
 {
     public function testPreviouslyStaticCall(): void
     {
-        $this->assertArraySubset(['bar' => 0], ['bar' => '0'], true);
+        Assert::assertArraySubset(['bar' => 0], ['bar' => '0'], true);
     }
 }
```

To use this package and upgrade to it, run:

```bash
composer require --dev dms/phpunit-arraysubset-asserts
```

Update set in `rector.php`

```php
use Rector\PHPUnit\Set\PHPUnitSetList;
use Rector\Config\RectorConfig;

return function (RectorConfig $rectorConfig): void {
    $rectorConfig->import(PHPUnitSetList::PHPUNIT80_DMS);
};
```

Run Rector

```bash
vendor/bin/rector process tests
```

### Add `void` to `PHPUnit\Framework\TestCase` Methods

This one hits 2 common methods we often use:

```diff
-setUp()
+setUp(): void
```

```diff
-tearDown()
+tearDown(): void
```

Also less common ones:

- `setUpBeforeClass()`
- `assertPreConditions()`
- `assertPostConditions()`
- `tearDownAfterClass()`
- `onNotSuccessfulTest()`

For this one, we'll use little help from Symplify:

```bash
composer require symplify/phpunit-upgrader --dev
vendor/bin/phpunit-upgrader voids /tests
```

That's it!

<br>

Then back to Rector - Update set in `rector.php`

```php
use Rector\PHPUnit\Set\PHPUnitSetList;
use Rector\Config\RectorConfig;

return function (RectorConfig $rectorConfig): void {
    $rectorConfig->import(PHPUnitSetList::PHPUNIT_80);
};
```

Then:

```bash
composer require phpunit/phpunit "^8.0" --dev
```


<br>

In the end, you should see at least PHPUnit 8.5+:

```bash
vendor/bin/phpunit --version
$ PHPUnit 8.5.15 by Sebastian Bergmann and contributors.
```

That's it! Congrats!

<br>

**Did you find a change that we missed here?** Share it in comments, so we can make this upgrade path complete and smooth for all future readers. Thank you for the whole PHP community!

<br>

Happy coding!
