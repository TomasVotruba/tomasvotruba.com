---
id: 323
title: "Doctrine Annotations and Attributes Living Together in Peace"
perex: |
    In previous post [How to Refactor Custom Doctrine Annotations to Attributes](/blog/how-to-refactor-custom-doctrine-annotations-to-attributes) we looked on how to make the `@annotation` to `#[Attribute]` transition.
    <br><br>
    Last week we started such refactoring in [my favorite long-term project](https://www.startupjobs.cz/startup/scrumworks-s-r-o) and we came hard situation. When we started to move all annotations to attributes at once, we lost control over the results. It was also impossible because 3rd party annotations were not attributes ready.
    <br><br>
    We had to **support annotations and attributes at the same time**. Do you have plenty of custom annotations yourself? In this post you'll learn to build a bridge with both annotations and attributes on board.

tweet: "New Post on 🐘 blog: Doctrine Annotations and Attributes Living Together in Peace"
tweet_image: "/assets/images/posts/2021/named_argument_constructor.png"
---

<blockquote class="blockquote text-center">
    "United we stand,<br>
    Divided we fall"
</blockquote>

## Safety First

Before we start, we have to prepare safe environment. No razors, no matches and couple of test.
The bridge testing technique is one of safety nets I use while refactoring to a new technology.

The idea is simple - in total we should have 3 tests:

* test for current behavior - compare with exact output
* *add alternative feature*
* test new behavior - compare with exact output
* test current and new behavior - compare both results together

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

Here we can clearly see that public property takes much more attention than a private one. Public properties can be used in the class, outside the class and changed basically anytime. On the other hand, private property can be used exclusively in this class.

## Bridge Test

Let's add a bridge test, so we are sure the refactoring works for both annotations and attributes. How would the bridge test look like for a public property?

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

Let's add a new method `resolveAttributeValue()` that will be able to read PHP 8 attribute values too:

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

Now the test should fail, because we have to yet implement the `resolveAttributeValue()` method:

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

Looking at the test, it seems the `testBridge()` already includes the test of their former 2 methods. Why not delete those two? ...
Wait, wouldn't that be like cutting our safety ropes?

<img src="https://images.squarespace-cdn.com/content/v1/5a54afc2e9bfdf89573d7cf7/1551374451263-U7I7L1NLOJY6XWNL028T/ke17ZwdGBToddI8pDm48kJUlZr2Ql5GtSKWrQpjur5t7gQa3H78H3Y0txjaiv_0fDoOvxcdMmMKkDsyUqMSsMWxHk725yiiHCCLfrh8O1z5QPOohDIaIeljMHgDF5CVlOqpeNLcJ80NK65_fV7S1UfNdxJhjhuaNor070w_QAc94zjGLGXCa1tSmDVMXf8RUVhMJRmnnhuU1v2M8fLFyJw/Rope-Bridge-project-Salwa+Resort-Abu+Samra-Qatar-5.jpeg?format=500w" class="img-thumbnail">

<br>

At first the test passes, both of used methods return `1000` and `assertSame()` works correctly.

```php
$resolvedAnnotationValue = ...; // 1000
$resolvedAttributeValue = ...; // 1000
$this->assertSame($resolvedAnnotationValue, $resolvedAttributeValue);
```

<br>

Later that day we do a bit refactoring. Let's run tests now:

```bash
vendor/bin/phpunit tests/BridgeTest.php
```

Still passes, yay!

<br>

Few week later we find out the production annotations and attributes **were ignored**, your admin was publicly available and test passes with these values:

```php
$this->assertSame(null, null);
```

That's why it's important to have 2 previous methods and compare exact values.
Now that we have safety rules defined, lets start the dirty work.

## 1. Teaching Annotation Attributes

We already know the first steps [from previous post](/blog/how-to-refactor-custom-doctrine-annotations-to-attributes) - add `#[Attribute]`:

```php
/**
 * @Annotation()
 */
#[Attribute]
class AttentionPrice
{
    // ...

    public function __construct(array $values)
    {
        $this->amount = $values['amount'];
    }

    // ...
}
```

The problem is that annotation requires an array in the constructor - `array $values`.
But attributes accept specific value:

```php
    /**
     * @AttentionPrice(1000) // array with int
     */
    #[AttentionPrice(1000)] // int
    public $publicProperty;
```

<br>

What can we do about different needs for construction?

### 2 Different Classes?

An annotation class with `array` contract:

```php
/**
 * @Annotation()
 */
class AttentionPrice
{
    public function __construct(array $values)
    {
        // ...
    }
}
```

And an attribute class with `int` contract:

```php
#[Attribute]
class AttentionPrice
{
    public function __construct(int $amount)
    {
        // ...
    }
}
```


### 2 Constructors?

```php
    public function __construct($values)
    {
        // annotatoin
        if (is_array($values)) {
            $this->amount = $values['amount'];
        // attribute
        } elseif (is_int($values)) {
            $this->amount = $values;
        } else {
            // exception
        }
    }
```

<br>

Try both options and you'll soon see that **they both smell**:

* First is nice and clean, but gives you extra work with duplicated classes ❌
* Second is headache to write and read. A that's only 1 value, imagine there are 2 or 3 values ❌

Hm, what can we do now?

<br>

## `@NamedArgumentConstructor` to the Rescue

We're lucky, `doctrine/annotations` got us covered since `1.12`. I've learned this trick from [Koriym](https://github.com/koriym/Koriym.Attributes) - thanks!

```diff
+use Doctrine\Common\Annotations\NamedArgumentConstructor;

 /**
  * @Annotation()
+ * @NamedArgumentConstructor()
  */
+#[Attribute]
 class AttentionPrice
 {
     private int $amount;

-    public function __construct(array $values)
+    public function __construct(int $amount)
     {
         $this->amount = $amount;
     }

     // ...
 }
```

Thanks to the `@NamedArgumentConstructor` the constructors are now the same for both annotation and attribute ✅

<br>

Big thanks to Alexander M. Turek who [contributed this feature](https://github.com/doctrine/annotations/pull/391) to `doctrine/annotations` and Vincent who added support for [default value](https://github.com/doctrine/annotations/pull/402) unwrap.

<br>

Now we can use both annotation and attributes in our code with the same result:

```php
    /**
     * @AttentionPrice(1000) // int
     */
    #[AttentionPrice(1000)] // int
    public $publicProperty;

```

<br>

Happy coding!
