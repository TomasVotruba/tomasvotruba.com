---
id: 148
title: "Hi, my name is Tom - Contact vs. Sprintf vs. In-String Variable"
perex: |
    My recent post about [lovely exceptions](/blog/2018/09/17/7-tips-to-write-exceptions-everyone-will-love) opened very interesting question. [In comments bellow the post](/blog/2018/09/17/7-tips-to-write-exceptions-everyone-will-love/#comment-4100904216), [in Reddit thread](https://www.reddit.com/r/PHP/comments/9hehv6/7_tips_to_write_exceptions_everyone_will_love/e6d3hic) and [on  Twitter](https://mobile.twitter.com/geekovo/status/1043185111309713408).
    <br>
    <br>
    A questions about **connecting string with variables**.
    You have 3 options. Each has its strong and weak points. How do you pick the right one?
tweet: "New Post on my Blog: Hi, my name is Tom - Contact vs. Sprintf vs. In-String Variable #php - in code examples"
tweet_image: "/assets/images/posts/2018/connect-strings/dual.png"
---

In how many ways you can register a service in Symfony or call a service method in Laravel?
PHP and PHP frameworks are so free, that you often have 3+ ways to do one 1 thing. But that comes with a price.

**The more approaches you use, the more has to the reader learn about rules of coding and the less space he or she has for real algorithms**. You can be really cool by using marginal PHP features, but if nobody except you understands it, you only hurt your code.

## How to Pick The Best Solution?

Instead of using what you already use (the most common non-sense argument for everything), try to imagine that **your colleague is about to create a pull-request with PHP features you've never seen before**.

What should it be like?

- **easy to learn**
- **easy to maintain** = changed by somebody else who might see it for the first time
- **hard to fuck up**

<br>

Let's try the simplest example possible:

<blockquote class="blockquote text-center">
    "Hi, my name is Tom."
</blockquote>

## 1. Easy To Learn

```php
<?php

$name = 'Tom';

# 1. concat
$message = 'Hi, my name is ' . $name;

# 2. sprintf
$message = sprintf('Hi, my name is %s', $name);

# 3. in-string variable
$message = 'Hi, my name is $name';
```

### <em class="fas fa-fw fa-check text-success fa-lg"></em> Concat

Just like a `+` for numbers, but for strings.

### <em class="fas fa-fw fa-times text-danger fa-lg"></em> Sprintf

What the hell is `%s`? I'd have to read [the manual](http://php.net/manual/en/function.sprintf.php)... `%s` for strings, `%d` for numbers.

### <em class="fas fa-fw fa-check text-success fa-lg"></em> In-String Variable

I guess I copy the variable inside the string.

## 2. Easy To Maintain

Let's say, I'd like to tell more about myself.

```diff
 <?php

 $name = 'Tom';
+$love = 'PHP';

 # 1. concat
-$message = 'Hi, my name is ' . $name;
+$message = 'Hi, my name is ' . $name . ' and I love ' . $love;

 # 2. sprintf
-$message = sprintf('Hi, my name is %s', $name);
+$message = sprintf('Hi, my name is %s and I love %s', $name, $love);

 # 3. in-string variable
-$message = 'Hi, my name is $name';
+$message = 'Hi, my name is $name and I love $php';
```

Or quote the name to express it's a variable string:

```diff
 <?php

 $name = 'Tom';
 $love = 'PHP';

 # 1. concat
-$message = 'Hi, my name is ' . $name . ' and I love ' . $love;
+$message = 'Hi, my name is "' . $name . '" and I love ' . $love;

 # 2. sprintf
-$message = sprintf('Hi, my name is %s and I love %s', $name, $love);
+$message = sprintf('Hi, my name is "%s" and I love %s', $name, $love);

 # 3. in-string variable
-$message = 'Hi, my name is $name and I love $php';
+$message = 'Hi, my name is "$name" and I love $php';
```

### <em class="fas fa-fw fa-times text-danger fa-lg"></em> Concat

1 new element = 2 new dots `.`. I have to think where to put it. Imagine there will be 4 elements one day.
I also type like a dyslexic, so seeing `'"'` hurts.

### <em class="fas fa-fw fa-check text-success fa-lg"></em> Sprintf

Nice to read.

### <em class="fas fa-fw fa-check text-success fa-lg"></em> In-String Variable

Still the same.

## 3. Hard to Fuck Up

### <em class="fas fa-fw fa-times text-danger fa-lg"></em> Concat

```php
<?php

$name = 'Tom';
$love = 'PHP';
$also =  'to travel';

$message = 'Hi, my name is ' . $name . ' and I love ' . $love . 'and also' . $also;
```

Can you spot it? This already happened me million times. I never make extra spaces anywhere else in the code.

### <em class="fas fa-fw fa-check text-success fa-lg"></em> Sprintf

This also happens...

```php
<?php

$name = 'Tom';
$love = 'PHP';

$message = sprintf('Hi my name is %s and I love %s', $name);
// PHP Warning: sprintf(): Too few arguments
```

...or this...

```php
<?php

$name = 'Tom';
$love = 'PHP';

$message = sprintf('Hi my name is %s and I love s', $name, $love);
// PHPStan: Call to sprintf contains 1 placeholder, 2 values given.
```

...but PHP and PHPStan got us covered.

### <em class="fas fa-fw fa-times text-danger fa-lg"></em><em class="fas fa-fw fa-times text-danger fa-lg"></em> In-String Variable

I must confess, I've tricked you (and myself too until I tried [running the code](https://3v4l.org/JEGJu)):

<img src="/assets/images/posts/2018/connect-strings/quote-fuckup.png" class="img-thumbnail">

**And PHP tells you... *nothing*, it happily prints the wrong strings.**

From removing magic quotes, I'm very happy that I don't have to think about what is really working.

This problem is [mentioned in PHP Doc](http://php.net/manual/en/migration70.incompatible.php#migration70.incompatible.variable-handling.indirect) though, but who of you even read what `%f` in `sprintf` stands for:

<img src="/assets/images/posts/2018/connect-strings/php-fuckup.png" class="img-thumbnail">

And what about this, will it be escaped?

<img src="/assets/images/posts/2018/connect-strings/escaped.png" class="img-thumbnail">

Why stopping there. What about **method calls**?

```php
<?php

$nameProvider = new NameProvider();

# 1. concat
$message = 'Hi, my name is ' . $nameProvider->provide();

# 2. sprintf
$message = sprintf('Hi, my name is %s', $nameProvider->provide());

# 3. in-string variable
$message = 'Hi, my name is $nameProvider->provide()';
$message = 'Hi, my name is ${nameProvider}->provide()';
$message = 'Hi, my name is ${nameProvider->provide()}';
$message = 'Hi, my name is {$nameProvider->provide()}';
# ?
```

This is so complex, that there is even a **coding standard fixer for such cases**:

<img src="/assets/images/posts/2018/connect-strings/fixer.png" class="img-thumbnail">

Next image is not exclusively related to strings, but it shines the same *instant coolness* that will hunt you down later:

<img src="/assets/images/posts/2018/connect-strings/fuckup.png">

And it's a hell to upgrade such a code (even with Rector). Read more about it in [PHP Doc](https://secure.php.net/manual/en/language.types.string.php#language.types.string.parsing)

<br>

## <em class="fas fa-fw fa-check text-success fa-lg"></em> Whatever You Pick, Stick With It

We all **start with one approach, then jump to another whenever we need**. *Damn you, brain!*

That's how such code is born:

<img src="/assets/images/posts/2018/connect-strings/dual.png" class="img-thumbnail">

And your code readers will have to think:

- Is there some hidden reason I don't know?
- When is the line?
- What to use when?

[Don't make them think](https://www.amazon.com/Dont-Make-Me-Think-Usability/dp/0321344758) and stick with one approach in your code.
Your readers will have **50 % more brain energy to improve your code** then they'd have otherwise.

<br>

But still, **which one of**...

- concat
- `sprintf`
- in-string variable

...**is your favorite and why?** Tell me in comments, I've definitely missed many fuckups.

<br>

Happy connecting!
