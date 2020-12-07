---
id: 281
title: "How to Reveal Static Call Relationships in Your Code"
perex: |
    Static methods are easy to use. Have a guess. How long would it take to make 700 static methods in your code? 2-3 years? Now imagine you need a [replace one with dependency injection](/blog/2018/04/26/how-i-got-into-static-trap-and-made-fool-of-myself/).
    <br><br>
    You're [dead](/blog/2020/08/31/how-static-methods-kills-you-like-corona/). Well, at first, it feels like it. Then you can start to [analyze the problem](/blog/2019/04/01/removing-static-there-and-back-again/) and make a refactoring plan. **To increase plan chances for success, we needed data.**
    <br><br>
    How can we get more data about static in our code?
    <br><br>
    Meet *Static Detector*.

tweet: "New Post on #php 🐘 blog: How to Reveal Static Call Relationships in Your Code"
tweet_image: "/assets/images/posts/2020/static_detector_result.png"
---

"3 developers try to find one static method they can safely change."

<img src="/assets/images/posts/2020/static_gordian_knot.jpg" class="img-thumbnail">

<br>

Where is the end of it? Where to start?

Yay, **we found one static method that seems independent on others**. Let's send pull-requests for review.

<br>

Later that day, on code review...

- "Well, it depends on one more method. Let's refactor it too..."
- "Oh, now we need to change 2 methods in the template too... Hm, how do we get a service into that template?"
- "Hm, this template is created in a static method as well. Let's register it as a service..."
- "Ups, 3 more classes use this static service..."

<br>

Turmoil.

<br>

## Stop & Relax

In a situation like this, let's take time to step back, breathe, and relax. There is a way to get out this, but brute force is not one of them.

<blockquote class="blockquote text-center">
    "A journey of a thousand miles begins with a single step."
</blockquote>

<br>

## Recipe for Static Success

If we refactor 100+ static methods manually, we'll probably end up in the stress and frustration like in the story above.

The goal is to take one method at a time. But not just any static method. The easiest possible. How do we find it?

## Take Low Hanging Fruit

The easiest static method is the **one with the least coupling**, or with a coupling that is easiest to remove, e.g.

- static method that is never used → remove it
- static method that is used only locally → remove `static`
- static method that is used only in local static method → make it `private`
- static method that is used only in template → refactor is to [filter service](/blog/2020/08/17/how-to-get-rid-of-magic-static-and-chaos-from-latte-filters/)

But you already know that, right? Anyone can remove method that is never used.
What is the hard problem? How can we be sure the method is not really used?

## Static Detector to the Rescue

To get these data, we use a handy tool [symplify/static-detector](https://github.com/symplify/static-detector).

<br>

Install it:

```bash
composer require symplify/static-detector --dev
```

Run it on your directory:

```bash
vendor/bin/static-detector detect src
```

We'll see the overview of all static methods, with all related static calls.

From the most spread one in the top, into **the least coupled in the bottom**.

<br>

For following file:

```php
<?php

// src/SomeStatic.php
final class SomeStatic
{
    public static function neverCalled()
    {
    }

    public function run()
    {
        self::calledJustOnce();
    }

    public static function calledJustOnce()
    {
    }
}
```

We get this result:

<img src="/assets/images/posts/2020/static_detector_result.png" class="img-thumbnail">


<br>

Now even 700+ static methods do seem like such a big problem, right?

<img src="/assets/images/posts/2020/static_gordian_knot_2.jpg" class="img-thumbnail">

<br>

Happy coding!
