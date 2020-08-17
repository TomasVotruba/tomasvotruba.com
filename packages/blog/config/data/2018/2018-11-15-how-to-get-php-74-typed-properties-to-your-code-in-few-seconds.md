---
id: 159
title: "How to Get PHP 7.4 Typed Properties to Your Code in Few Seconds"
perex: |
    PHP 5.6 and 7.0 is going to be dangerous [since 2019](http://php.net/supported-versions.php), PHP 7.1 is [new baseline](https://gophp71.org) and PHP 7.3 is just about to be released in the end of 2018.
    <br><br>
    Is this **the best time to upgrade your code to PHP 7.4**?
tweet: "New Post on My Blog: How to Get #PHP 7.4 Typed Properties to Your Code in Few Seconds   #futurecompatibility #codequality #symfony"
tweet_image: "/assets/images/posts/2018/php74-typed/v2.png"
---

**Probably not**. Unless you're able to compile [PHP repository](https://github.com/php/php-src) yourself and live on the edge of the edges. PHP 7.4 can be the smoothest upgrade you've experienced. **If you'll think about in your coding since today**.

## Can `@var` Annotations be Really Useful?

Annotations were always in the bottom, ashamed and not considered a *real code*. They help us to guess the type of the property. Not only to us, but also to [static analysis tools](/blog/2018/10/22/brief-history-of-tools-watching-and-changing-your-php-code/#2-static-analysis-tools).

```php
<?php

final class SomeClass
{
   /**
    * @var int
    */
   private $count;
}
```

Here we can see that `$count` is an `int` number.

**In PHP 7.4** we could change this code to get a [strict typing](https://wiki.php.net/rfc/typed_properties_v2):

```diff
 <?php declare(strict_types=1);

 final class SomeClass
 {
-   /**
-    * @var int
-    */
-   private $count;
+   private int $count;
 }
```

When you write `@var` annotation today, you'll be preparing your code for future refactoring. You might laugh now: "everyone is doing that today, why do you even write about this obvious standard".

Well, not everyone...

<div class="text-center">
    <img src="/assets/images/posts/2018/php74-typed/symfony-lacking.png" class="img-thumbnail">
    <p>This is my favorite class to extend in whole Symfony - a <a href="https://github.com/symfony/symfony/blob/dbf053bc854f6768ddcd8ed39f7cbb2c21e500e6/src/Symfony/Component/Console/Command/Command.php#L37-L51"><code>Command</code> class</a>.
</div>

This is Symfony 4.2 (dev) we're talking about with PHP 7.1+. Lot's of Symfony code is ~~weakly~~ *not* typed, but that's not the biggest problem.

## "*My-favorite-framework* Uses It, It must be the Best Practise in PHP!"

The problem is *framework-way* approach to learning PHP. Many developers I've met **think their framework uses the best practices of the PHP itself**. Why? **Have you ever tried to learn some other PHP framework than the one you're using today?** I mean learn, not just try on the workshop, during the weekend, but to really build a big application with it.

Saying that, **many developers adopt Symfony practices thinking *it the best form of PHP there is***. There's no place to blame developers, it's just how society works - with *observational learning*. We should not blame Symfony either since there is no *code quality engineer*, who would push *non-feature* code changes. Unfortunately, Symfony team itself is strictly against these changes as you can see in PR comments.

Recently I'm very happy to see these engineers around me - [LMC](https://www.lmc.eu) to name one for all.

## Visualize Future Compatibility

When you code, think about the way to write ***future compatible* code**. What is happening in PHP for the last 2-3 years? Type hints, ?nullables, `void`, AST and static analysis suggest, there is more coming. I wouldn't be surprised if these annotations turn out to be useful in pure PHP one day:

```php
<?php

final class SomeClass
{
    /**
     * @var int
     */
    private const NUMBER = 5;

    /**
     * @var Product[]
     */
    private $products = [];
}
```

In PHP 8 or 9 this might come:

```diff
 <?php declare(strict_types=1);

 final class SomeClass
 {
-    /**
-     * @var int
-     */
-    private const NUMBER = 5;
+    private const int NUMBER = 5;

-    /**
-     * @var Product[]
-     */
-    private $products = [];
+    private Product[] $products = [];
}
```

In [PHP Asia](/blog/2018/10/18/how-i-almost-missed-my-talk-in-php-asia-conference), *typed arrays* was the most desired feature in next versions of PHP. So were *typed properties* once and so were *strict types* before them. In 2015 we would only dream about those 2, now one is part of our every-day life, second coming soon.

##"...in Seconds"

Yeah, right, I made a promise - I'll get back to nearer future with *typed properties*.

Let's say we're thinking about the future and adding all `@var` annotations we can. Is that enough? You'd still have to do these diffs manually:

```diff
 <?php declare(strict_types=1);

 final class SomeClass
 {
-    /**
-     * @var boolean
-     */
-    public $a;
+    public bool $a;

     /**
-     * @var bool
      * another comment
      */
-    private $b = false;
+    private bool $b = false;

     /**
      * @var callable
      */
     private $c;

-    /**
-     * @var AnotherClass|null
-     */
-    private $d = null;
+    private ?AnotherClass $d = null;

     /**
      * @var int
      */
     public $e = 'string';
}
```

Or you could **be actually rewarded for your daunting `@var` work**. Good news! Rector will do this one for you:

```bash
composer require rector/rector --dev
vendor/bin/rector process src --set php74
# few seconds...
#
# Done!
```

See [pull-request #643](https://github.com/rectorphp/rector/pull/643) to get more insight into how this very nice AST use case works.

<br>

I started this post to show how Rector helps with huge refactorings like `@var` annotations to types that are about to come next year. But the message is far being that - observing trends, seeing the future and working towards that.

## How To Make Your Future-Self Happy?

- **write `@var` above each property**
- write `@var` or any other `@value` info **above each constant**
- **write `@var Type[]` above each array property**
    - not `mixed`, not `object`, but scalar or specific class or interface
- think about rules in your code that you've **non-critically copy-pasted from the framework**

You're saving work to yourself, to your colleagues and to tools like Rector, that will do the work for you. If you're ready...

<br>

Do you have some own future compatibility tips you use in your code today? I'd love to hear them ↓
