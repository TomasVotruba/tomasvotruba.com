---
id: 323
title: "Doctrine Annotations and Attributes Living Together in Peace"
perex: |
    In previous post [How to Refactor Custom Doctrine Annotations to Attributes](/blog/how-to-refactor-custom-doctrine-annotations-to-attributes) we looked on how to make the `@annotation` to `#[Attribute]` transition.


    Last week, we started refactoring in [my favorite long-term project](https://www.startupjobs.cz/startup/scrumworks-s-r-o), and we came to a challenging situation. When we started to move all annotations to attributes at once, we lost control over the results. It was also impossible because 3rd party annotations were not attributes ready.


    We had to **support annotations and attributes at the same time**. Do you have plenty of custom annotations yourself? In this post, you'll learn to build a bridge with both annotations and attributes on board.

tweet: "New Post on the 🐘 blog: Doctrine Annotations and Attributes Living Together in Peace"
tweet_image: "/assets/images/posts/2021/named_argument_constructor.png"
---

<blockquote class="blockquote text-center">
    "United we stand,<br>
    Divided we fall"
</blockquote>

## Safety First

Before we start, we'll make sure we have [instant feedback](/blog/2020/01/13/why-is-first-instant-feedback-crucial-to-developers) about code. We'll prepare a special kind of test designed for refactoring, where we need 2 mechanism to work at once - [a bridge testing](/blog/how-to-make-upgrade-safe-with-bridge-testing). In linked post we'll build a test that we use here, so if you're coding with me, be sure to read it first.




## Teaching Annotation Attributes

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

What can we do about different construction needs?

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

Try both options, and you'll soon see that **they both smell**:

* First is nice and clean but gives you extra work with duplicated classes ❌
* Second is a headache to write and read. A that's only one value, imagine there are 2 or 3 values ❌

Hmm, what can we do now?

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

Thanks to the `@NamedArgumentConstructor`, the constructors are now the same for both annotation and attribute ✅

<br>

Big thanks to Alexander M. Turek, who [contributed this feature](https://github.com/doctrine/annotations/pull/391) for `doctrine/annotations` and Vincent who added support for [default value](https://github.com/doctrine/annotations/pull/402) unwrap.

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
