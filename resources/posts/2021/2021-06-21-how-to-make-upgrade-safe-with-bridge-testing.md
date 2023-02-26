---
id: 324
title: "How to make Upgrade Safe with Bridge&nbsp;Testing"
perex: |
    Upgrading can go smooth without no bugs and just work. We can make our customer happy, even though we don't have any tests.

    **The older I am, the more I care about safety**. Not just for now, but for tomorrow and for my safety of my colleagues. Also for the developers, who will work on the project even though I'm long gone.

    That's why before I start upgrading one approach to another, I want to prepare a safe environment. No razors, no matches, and a couple of tests. The bridge testing technique is one of the safety nets I use while refactoring to new technology.

---

The idea is simple - in total, we should have 3 tests:

* test for current behavior - compare with exact output
* *add alternative feature*
* test new behavior - compare with exact output
* test current and new behavior - compare both results together

<br>

It will be more clear from an example. Let's say we use `@AttentionPrice` annotation to express the amount of attention we should spend while reading the code:

```php
/**
 * @Annotation()
 */
class AttentionPrice
{
    private int $amount;

    public function __construct(array $values)
    {
        $this->amount = $values['amount'];
    }

    public function getAmount(): int
    {
        return $this->amount;
    }
}
```

In our code, we'll use annotation to express required attention for specific elements:

```php
final class DesignPattern
{
    /**
     * @AttentionPrice(1000)
     */
    public $publicProperty;

    /**
     * @AttentionPrice(100)
     */
    private $privateProperty;
}
```

Here we can see that public property takes much more attention than private one. Public properties can be used in the class, outside the class, and changed anytime. On the other hand, private property can be used exclusively in this class.

## Bridge Test

Let's add a bridge test so we are sure the refactoring works for both annotations and attributes. How would the bridge test look like for public property?

```php
use PHPUnit\Framework\TestCase;

final class BridgeTest extends TestCase
{
    private Reader $reader;

    protected function setUp(): void
    {
        // create Reader instance or get it from container
        $this->reader = // ...;
    }

    public function testCurrent(): void
    {
        $resolvedValue = $this->reader->resolveAnnotationValue(
            'DesignPattern', 'publicProperty'
        );
        $this->assertSame($resolvedValue, 1000);
    }
}
```

Let's run the test:

```bash
vendor/bin/phpunit tests/BridgeTest.php
```

It should pass - it only confirms already existing behavior.

<br>

Let's add a new method, `resolveAttributeValue()` that will be able to read PHP 8 attribute values too:

```php
    // ...

    public function testNew(): void
    {
        $resolvedValue = $this->reader->resolveAttributeValue(
            'DesignPattern', 'publicProperty'
        );
        $this->assertSame($resolvedValue, 1000);
    }

    // ...
```

Now the test should fail because we have yet to implement the `resolveAttributeValue()` method:

```bash
vendor/bin/phpunit tests/BridgeTest.php
```

<br>

Last but not least, the bridge test compares the results of both methods:

```php

    // ...

    public function testBridge(): void
    {
        $resolvedAnnotationValue = $this->reader->resolveAnnotationValue(
            'DesignPattern', 'publicProperty'
        );
        $resolvedAttributeValue = $this->reader->resolveAttributeValue(
            'DesignPattern', 'publicProperty'
        );

        $this->assertSame($resolvedAnnotationValue, $resolvedAttributeValue);
    }

    // ...
```

## Why Not Just The Bridge Test?

Looking at the test, it seems the `testBridge()` already includes the test of their former two methods. Why not delete those two? ...
Wait, wouldn't that be like cutting our safety ropes?

<img src="https://images.squarespace-cdn.com/content/v1/5a54afc2e9bfdf89573d7cf7/1551374451263-U7I7L1NLOJY6XWNL028T/ke17ZwdGBToddI8pDm48kJUlZr2Ql5GtSKWrQpjur5t7gQa3H78H3Y0txjaiv_0fDoOvxcdMmMKkDsyUqMSsMWxHk725yiiHCCLfrh8O1z5QPOohDIaIeljMHgDF5CVlOqpeNLcJ80NK65_fV7S1UfNdxJhjhuaNor070w_QAc94zjGLGXCa1tSmDVMXf8RUVhMJRmnnhuU1v2M8fLFyJw/Rope-Bridge-project-Salwa+Resort-Abu+Samra-Qatar-5.jpeg?format=500w" class="img-thumbnail">

<br>

At first, the test passes, and both methods return `1000` and `assertSame()` works correctly.

```php
$resolvedAnnotationValue = ...; // 1000
$resolvedAttributeValue = ...; // 1000
$this->assertSame($resolvedAnnotationValue, $resolvedAttributeValue);
```

<br>

Later that day, we do a bit of refactoring. Let's run tests now:

```bash
vendor/bin/phpunit tests/BridgeTest.php
```

It still passes, yay!

<br>

A few weeks later, we find out the production annotations and attributes **were ignored**, your admin was publicly available, and the test passes with these values:

```php
$this->assertSame(null, null);
```

That's why it's essential to have two previous methods and compare exact values.
Now that we have safety rules defined let's start the dirty work!

<br>

Happy coding!
