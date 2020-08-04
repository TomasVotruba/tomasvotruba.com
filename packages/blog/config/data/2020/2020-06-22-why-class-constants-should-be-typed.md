---
id: 264
title: "Why Class&nbsp;Constants Should&nbsp;be&nbsp;Typed"
perex: |
    Do you use PHP 7.4 [typed properties](/blog/2018/11/15/how-to-get-php-74-typed-properties-to-your-code-in-few-seconds/)? Do you know why?
    <br>
    <br>
    I use them, so **I don't have to think and validate the property type** every time. We just know its type or PHP would crash otherwise.
    <br>
    <br>
    Until PHP 7.4 this was not possible and code was kinda crappy.
    Where are we now with constant type? Do you trust your class constants type?

tweet: "New Post on #php 🐘 blog: Why Class Constants Should be Typed"
tweet_image: "/assets/images/posts/2020/typed_constants.png"
---

With typed properties, the incorrect type-bug was completely removed:

```php
<?php

declare(strict_types=1);

final class Person
{
    public string $name;

    public function __construct(string $name)
    {
        $this->name = $name;
    }
}
```

```php
$name = new Person(20_20);
```

You would not use the `Name` object like this, right?

<br>

## What About Constants?

**What** are constants? They hold value, like properties, but **the value doesn't change**. We can change the value in the code, but not during runtime.

*Pro-tip: Do you think all your properties have values that can change? [Dare Rector to find them](https://github.com/rectorphp/rector/blob/master/docs/rector_rules_overview.md#changereadonlypropertywithdefaultvaluetoconstantrector).*

How do you define a class constant?

```php
<?php

final class SomeClass
{
    private const ORDER = 'first';

    // ...
}
```

How is this dangerous? Sometimes, in 2 years future from today, there will be pull-request that changes it like this:

```php
<?php

final class SomeClass
{
    private const ORDER = 1;
}
```

Because if you're first, you're 1, right? I had the pleasure to debug such cases only to figure out the constant type was changed. Why? Because it could.

**Constants value can be changed manually - so their type.** It's rarely desired behavior, moreover, when using these constants in 3rd party code or from one.

## How Can We Prevent Constant Re-type?

```php
<?php

final class SomeClass
{
    /**
     * Should be a string
     */
    private const ORDER = 'first';
}
```

We read comments, READMEs, or manuals... only when something goes wrong.

```php
<?php

final class SomeClass
{
    /**
     * @var string
     */
    private const ORDER = 'first';
}
```

Slightly better, but still no way to enforce it.

```php
<?php

final class SomeClass
{
    private string const ORDER = 'first';
}
```

Great! But maybe in 2025?

## Future Scoping

The [PHP 8.0 release will be a blast](https://stitcher.io/blog/new-in-php-8). It's already ~30 merged features, and feature freeze is still [23 days ahead of us](https://thephp.website/en/issue/php8-release-schedule/).

If we look at selected features in PHP 8:

- [Constructor Property Promotion](https://wiki.php.net/rfc/constructor_promotion)
- [Union Types 2.0](https://wiki.php.net/rfc/union_types_v2)
- [Mixed Type v2](https://wiki.php.net/rfc/mixed_type_v2)
- [Static return type](https://wiki.php.net/rfc/static_return_type)
- [Ensure correct signatures of magic methods](https://wiki.php.net/rfc/magic-methods-signature)
- [Make constructors and destructors return void](https://wiki.php.net/rfc/make_ctor_ret_void) (WIP)

We can see there is a handful of changes **towards more strict and reliable types**. We are moving from docblocks to in-code types.

**We can expect** similar future for constants. Maybe even sooner before `TypedArrays[]` will be added.

**In 2015, we wished for typed properties, and in 2020 we have union strictly typed properties**. We can go for typed constants now.


<blockquote class="blockquote text-center mt-5 mb-5">
Check trends to predict code syntax,<br>
and write future compatible code your children will thank you for.
</blockquote>

<br>

## Let Computer work For You

[The sooner you discover the error](/blog/2020/01/13/why-is-first-instant-feedback-crucial-to-developers/), the better.

Do you use continuous integration (Github Actions, Gitlab CI, Travis CI...) and PHPStan? Add [PHPStan rule that checks `@var` definition vs the real value](https://github.com/symplify/coding-standard#constant-type-must-match-its-value).

```diff
 <?php

 final class SomeClass
 {
     /**
      * @var string
      */
-    private const ORDER = 'first';
+    private const ORDER = 1;
 }
```

If this change occurs in pull-request, the CI will tell you it's not consistent with the `@var` type.

This way, you'll be **aware of the type change right before the merge**, which is much better than 2 years later after 4 hours of debugging. Trust me.

<br>

Happy coding!
