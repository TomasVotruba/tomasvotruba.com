---
id: 132
title: "What's New in PHP 7.3 in 30 Seconds in Diffs"
perex: |
    No time but eager to hear PHP news? PHP 7.3 is out in December 2018 and it brings [173 changes](https://github.com/php/php-src/blob/PHP-7.3/NEWS). Which are the most useful ones?
tweet: "New Post on my Blog: What's New in #PHP 7.3 in 30 Seconds in Diffs #rfc #nettefw #learnIn30secs"

tested: true
---

From features that might be the most interesting to those lesser ones, that anybody will rarely use.

## 1. Comma After the Last Argument

<a href="https://wiki.php.net/rfc/trailing-comma-function-calls" class="btn btn-info btn-sm">
    <em class="fab fa-php fa-fw"></em>
    &nbsp;
    See RFC
</a>

Do you know this?

```diff
 $array = [
     1,
+    2,
 ];
```

We'll be able to do this:

```diff
 $this->someFunction(
     $arg,
+    $arg2,
 );
```

But **still not this**:

```diff
 function someFunction(
     $arg,
+    $arg2,
 ) {}
```

Thanks [Jacob](/blog/2018/08/16/whats-new-in-php-73-in-30-seconds-in-diffs/#comment-4056622976) for pointing [this difference](https://wiki.php.net/rfc/trailing-comma-function-calls#wait_didn_t_we_just_vote_on_this) out.

<br>

*25 seconds to go...*

<br>

## 2. First and Last Array Key

<a href="https://wiki.php.net/rfc/array_key_first_last" class="btn btn-info btn-sm">
    <em class="fab fa-php fa-fw"></em>
    &nbsp;
    See RFC
</a>

- [`array_key_first`](http://php.net/manual/en/function.array-key-first.php)

```diff
 $items = [
     1 => 'a',
     2 => 'b',
 ];

-reset($items);
-$firstKey = key($items);
+$firstKey = array_key_first($items);
 var_dump($firstKey); // 1
```

- [`array_key_last`](http://php.net/manual/en/function.array-key-last.php)

```diff
 $items = [
     1 => 'a',
     2 => 'b',
 ];

-end($items);
-$lastKey = key($items);
+$lastKey = array_key_last($items);
 var_dump($lastKey); // 2
```

These will be handy in [coding standard](https://github.com/Symplify/Symplify/blob/84987acb99b68748997fe205e9e5506035a36cfc/packages/TokenRunner/src/Wrapper/FixerWrapper/ClassWrapper.php#L118-L120) [tools](https://github.com/Symplify/Symplify/blob/84987acb99b68748997fe205e9e5506035a36cfc/packages/CodingStandard/src/Fixer/Strict/BlankLineAfterStrictTypesFixer.php#L59-L60).

<br>

*Still 15 seconds...*

<br>

## 3. Countable for Risky Variables

<a href="https://wiki.php.net/rfc/is-countable" class="btn btn-info btn-sm">
    <em class="fab fa-php fa-fw"></em>
    &nbsp;
    See RFC
</a>

I don't think having a variable of 2 forms is a good idea:

```php
<?php

$items = null; // same as "private $items;" in a class

echo sprintf('There is %d items', count($items));
// error Warning: count(): Parameter must be an array or an object that implements Countable
```

But in case of that smelly (3rd party) code, there is a help:

- [`is_countable`](http://php.net/manual/en/function.is-countable.php)

```diff
 $items = null;

+if (is_countable($items)) {
-echo sprintf('There is %d items', count($items));
+     echo sprintf('There is %d items', count($items));
+}
```

*Only 5 seconds, hurry!*

<br>

## 4. Safer JSON Parsing

<a href="https://wiki.php.net/rfc/json_throw_on_error" class="btn btn-info btn-sm">
    <em class="fab fa-php fa-fw"></em>
    &nbsp;
    See RFC
</a>

```diff
-json_encode($data);
+json_encode($data, JSON_THROW_ON_ERROR);
```

```diff
-json_decode($json);
+json_decode($json, false, 512, JSON_THROW_ON_ERROR);
```

So you'll be able to do:

```php
try {
    return json_decode($json, false, 512, JSON_THROW_ON_ERROR);
} catch (JsonException $exception) {
    // ...
}
```

I've used similar technique for years thanks to [Nette\Utils](https://doc.nette.org/en/2.4/json) and I've never complained:

```php
<?php

try {
    return Nette\Utils\Json::encode($value);
} catch (Nette\Utils\JsonException $exception) {
    // ...
}
```

*...0, you made it! Congrats, now get back to pwning the world!*

<br>

Did I miss a feature you plan to use from day 1 of your PHP 7.3? I might too, drop it in the comments!
