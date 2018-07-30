---
id: 127
title: "Hidden Gems of PHP Packages: Nette\\Utils"
perex: |
    In this series, I will show you **not-so-known PHP packages, that I happily use in my daily workflow**. They're hard to describe in few words for their various features, but awesome and simple to use.
    <br><br>
    Today we start with [Nette\Utils](https://github.com/nette/utils) package.
tweet: "New Post on my Blog: Hidden Gems of PHP Packages: Nette\Utils #nettefw #utils #php #regulars #fails #wtf"
tweet_image: "/assets/images/posts/2018/nette-utils/warning.png"
---

## Why I Use it

1. Do you know how the PHP function that checks the presence of a string in another string?

2. Do you know what is the difference between those 3 calls?

```php
$contains = strpos('content', $lookFor) === 0;
```

```php
$contains = strpos('content', $lookFor) == 0;
```

```php
$contains = strpos('content', $lookFor) === false;
```

**If you can tell the difference under 1 s**, good job!

<br>

I can't. I have to think hard to remember what was that danger WTF of [`strpos()` function](http://php.net/manual/en/function.strpos.php#refsect1-function.strpos-returnvalues)...

<img src="/assets/images/posts/2018/nette-utils/warning.png" class="img-thumbnail">

...that makes this code...

```php
$contains = strpos('content', $lookFor);
if ($contains) {
   // ... should I be here?
}
```

...run into condition, even though you don't want to.

3. Do you prefer exceptions over `false`? Do you prefer exceptions over learning various errors codes in PHP native functions like `file_get_contents()` or `preg_replace`?

<img src="/assets/images/posts/2018/nette-utils/preg_replace.png" class="img-thumbnail">

4. Do you prefer thinking less about PHP language details and prefer doing more effective work while being safe?

5. Do you prefer PHPStan not reporting forgotten validation of `bool|string` return type?

```php
if (file_exists($filePath)) {
    // can return `false|string`
    $fileContent = file_get_contents($filePath);
}
```

**Thanks to Nette\Utils I can be lazy, safe and much faster** in all these cases above and more.

## How to Install

```bash
composer require nette/utils
```

## How I Use it

We can look at [the Nette\Utils documentation](https://doc.nette.org/en/2.4/utils) to find 12 classes with many methods and study their API... and leave the page overwhelmed by details and over-bored.

Instead, **we can look at real-life examples in Symplify code**. That's much more interesting and relevant, right? When we use Github search, we see that Symplify packages use Nette\Utils package at [100+ places](https://github.com/search?l=&q=Nette%5CUtils+repo%3Asymplify%2Fsymplify+extension%3Aphp&type=Code). What are these cases?

## `Strings` class

### A `replace()` Method

It accepts content, pattern to look for and replacement. This is basic building stone for packages like [LatteToTwigConverter](/blog/2018/07/05/how-to-convert-latte-templates-to-twig-in-27-regular-expressions/):

```php
// in Latte: {var $var = $anotherVar}
// in Twig: {% set var = anotherVar %}
$content = Nette\Utils\Strings::replace($content, '#{var \$?(.*?) = \$?(.*?)}#s', '{% set $1 = $2 %}');
```

It's nice that you don't have to deal with PHP native edge-cases of regular expressions. I think regulars are difficult enough to work with, so this piece comes very handy.

### A `contains()` Method

It accepts the content and the string we look for:

```php
if (Nette\Utils\Strings::contains($key, '.')) {
    // is a code
    $this->skippedCodes[$key] = $settings;
} else {
    // is a class
    $this->skipped[$key] = $settings;
}
```

### A `startsWith()` Method

How to detect a nullable type?

```php
# < 1 s of thinking
return Nette\Utils\Strings::startsWith($type, '?');
```

```php
# > 1 s of thinking
return strpos('Content', $lookingFor) === ?;
# or was it this one?
return strpos('Content', $lookingFor) === strlen($lookingFor);
```

### An `endsWith()` Method

```php
if (Strings::endsWith($class, 'Interface')) {
    // is interface
}
```

## 2. A `FileSystem` class

### A `read()` Method

If we know the file will be there (e.g. it's convention or we just put it there), we can use `file_get_contents()`.

```php
$content = file_get_contents($accidentallyMissingFile);
// $content is `FALSE`
```

But we find out 35 lines later in this method:

```php
private function processContent(string $content)
{
    // ...
}
```

But all we see is *$content should be string, bool passed* error. Just great, isn't it?

Of course, you can put `file_exists()` and `is_file()` validation everywhere and teach every programmer in your team to use them and also create a sniff, that will enforce this behavior in CI level (which nobody really does) and spend hundreds of hours on regression bugs or...

...you could use helper method and make yourself useful instead:

```php
Nette\Utils\FileSystem::read($accidentallyMissingFile);
```

<em class="fas fa-fw fa-check text-success fa-lg"></em> Kaboom! An Exception!

### A `createDir()` method

Do you want to create a directory for your cache?

```php
mkdir($cacheDiretory);
```

Oh, but what if that already exists?

<em class="fas fa-fw fa-times text-danger fa-lg"></em> **Already exists error!**

Ok, let's say you're lucky, your hard drive was wiped out and it doesn't exist yet.

```php
mkdir($cacheDiretory);
```

But what if the directory is `some/cache`?

<em class="fas fa-fw fa-times text-danger fa-lg"></em> **Nested directory error!**


Ok, but what if we don't care about these because **all we need is to create a directory**?

```php
Nette\Utils\FileSystem::createDir($cacheDirectory);
```

<em class="fas fa-fw fa-check text-success fa-lg"></em>

### A `delete()` Method

We want to delete temporary data in tests or a gallery of pictures. All we have is a `$source` variable.

I admit I often have to Google the name of this function because it's super counter-intuitive to first that pops to my mind - `delete(file|directory)`.

So let's try:

```php
unlink($source);
```

<em class="fas fa-fw fa-times text-danger fa-lg"></em> **It's a directory error.**

Ah, let's try this then:

```php
rmdir($source);
```

<em class="fas fa-fw fa-times text-danger fa-lg"></em> **The directory is not empty error.**

Doh, I already imagine some `glob()` of `Finder` madness.

Or maybe **we just want to delete it**:

```php
Nette\Utils\FileSystem::delete($source);
```

<em class="fas fa-fw fa-check text-success fa-lg"></em>

<br>

Do you like it? **Go and give [Nette\Utils](https://github.com/nette/utils) a try**. It's the only package I allow to use static methods and that's a lot, since [I'm very strict to them](/blog/2018/04/26/how-i-got-into-static-trap-and-made-fool-of-myself/).

<blockquote class="blockquote text-center mt-5 mb-5">
    Do you want to play with details someone else already solved for you<br>
    or<br>
    <strong>build your awesome application instead</strong>?
</blockquote>


Happy coding!
