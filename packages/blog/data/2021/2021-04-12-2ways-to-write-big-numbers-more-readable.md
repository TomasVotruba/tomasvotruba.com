---
id: 311
title: "2 Ways to Write Big Numbers More Readable"
perex: |
    Have you seen a number in your code that does not make any sense? We don't talk about 1, 2, or 3. I mean big numbers like 965039008. How would you spell it when support asks you for your account ID?
    <br><br>
    **There are two ways to make big numbers more readable**. Instant and easy ways that I found mostly by accident. We use them both in one big project, and it makes our daily number work so much easier I want to share them with you.

tweet: "New Post on #php 🐘 blog: 2 Ways to Write Big Numbers more Readable"
---

## 1. Context-Aware Spaces

Thefirsttrickiscontextspacesforourbraincaneasilyseelogic. Like spaces in a sentence makes it easier to read it word by word.

<br>

Let's have a little real-life experience. Today we have less and less time to get through our day. We barely read section titles and **text in bold**.

<br>

To simulate that, read the next number in a blink of an eye (~200 ms):

```php
$housePrice = 10000000;
```

<br>

Now answer: **how much does your new house costs?**

- a million
- 10 million
- hundred thousands

<br>

I can't answer that for sure, and I wrote that number just a few seconds ago.

<br>

Now let's see the same number with context spaces:

```php
$housePrice = 10_000_000;
```

The answer is obvious now - 10 million.

<br>

This **little trick is called [the underscore separator](https://php.watch/versions/7.4/underscore_numeric_separator)** and we can use it since PHP 7.4+.

## Use Underscore Separator to Save The Future

Do you want to make your **future code** more readable?

Use `_` in numbers while you're writing them. It will save your from guessing in future months:

```php
$creditCardNumber = 1234_1234_1234_1234;
$phoneNumber = 420_776_778_333;
$price = 99_99;
```

<br>

Do you want to make your **current code more readable**?

Use [`AddLiteralSeparatorToNumberRector`](https://github.com/rectorphp/rector/blob/main/docs/rector_rules_overview.md#addliteralseparatortonumberrector) from Rector to upgrade your numbers:

```diff
-$int = 500000;
+$int = 500_000;

-$float = 1000500.001;
+$float = 1_000_500.001;
```

<br>

## 2. Time Constants

What is the magic behind these numbers?

```php
31104000
604800
```

From the paragraph title, we know it's about time... **but how much precisely**? No calculators are allowed!

## Do you Use Nette\Utils?

Some packages are very powerful, but only very few developers are aware of them. One of these packages is Nette\Utils. You don't use it for every project yet? Read [hidden gem post](/blog/2018/07/30/hidden-gems-of-php-packages-nette-utils/) and look at 1 more reason [on Reddit from last week](https://www.reddit.com/r/PHP/comments/mya4gb/preg_last_error_and_json_last_error/).

It can help to make these numbers more readable:

```php
const TIMEOUT = 86400;

const PING = 3600;
```

What are these? One of them might be an hour in seconds. The second... maybe a day in seconds? "Maybe" is not for coders, it's for gamblers. **We play it strictly!**

<br>

```diff
+use Nette\Utils\DateTime;

-const TIMEOUT = 86400;
+const TIMEOUT = DateTime::DAY;

-const PING = 3600;
+const PING = DateTime::HOUR;
```

Suddenly, random numbers will get a sense:

```diff
+use Nette\Utils\DateTime;

-604800
+DateTime::WEEK

-31104000
+DateTime::DAY * 30 * 12
```

## Use `HOUR`, `DAY`, `WEEK`, `MONTH` and `YEAR` Constants

Next time you'll write time in your code, give time to think about the time constants.

Would you like to **upgrade your current numbers to constants instantly**? Use [`ReplaceTimeNumberWithDateTimeConstantRector`](https://github.com/rectorphp/rector-nette/blob/main/docs/rector_rules_overview.md#replacetimenumberwithdatetimeconstantrector) from Rector to handle it.

<br>

Happy coding!
