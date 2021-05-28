---
id: 157
title: "Fatal error: Uncaught Error: [] operator not supported for strings in"
perex: |
    That's right! PHP 5.6 and 7.0 are entering EOL - end of ~~line~~ life this December. Social networks, Slacks, Twitter, Reddit are [full](https://www.reddit.com/r/PHP/comments/9syr3m/php_56_eol_end_of_life_end_of_2018_and_php_7) of it. Are you running PHP 7.1? Good, come next year when PHP 7.1 is *eoling*.
    <br><br>
    For the rest of you, what will you do when PHP will tell you the message in the title?
tweet: "New Post on My Blog: Fatal error: Uncaught Error: [] operator not supported for strings in #php56 #php70 #eol #rector"
tweet_image: "/assets/images/posts/2018/upgrade-php/swap.png"

updated_since: "November 2020"
updated_message: |
    Switched deprecated `--set` option to `rector.php` config.
    Switched **YAML** to **PHP** configuration.
---

<div class="text-center">
    <img src="/assets/images/posts/2018/upgrade-php/important.png" class="img-thumbnail">
    <p>The most important info from <a href="http://php.net/supported-versions.php">PHP.net</a> nowadays</p>
</div>

<br>

You see all this social boom, your boss is scared by "no security support" and **you finally have a *go* to upgrade your PHP code** to PHP 7.1. You upgrade your PHP locally to see if everything works:

```bash
"Warning: count(): Parameter must be an array or an object that implements Countable in"
```

```bash
"Fatal error: Uncaught Error: [] operator not supported for strings in"
```

```bash
"Deprecated: Methods with the same name as their class will not be constructors in a future version of PHP; Filter has a deprecated constructor in"
```

```bash
"Fatal error: Uncaught Error: Call to undefined function ereg() in"
```

```bash
"Fatal error: Cannot use empty list in"
```

<br>

You're probably thinking *lets jump to PHP 7.2, while you're at it*:

```bash
"Deprecated: The each() function is deprecated. This message will be suppressed on further calls in"
```

Don't do it, **always jump by minor versions** - for both PHP and packages.

<br>

Actually, when you see a message - that's a good sign. How else would you notice this?

```php
// PHP 5.6-
list($a[], $a[]) = [1, 2];

// to get same result in PHP 7.0+
list($a[], $a[]) = array_reverse([1, 2])
```

True story - see [3v4l.org](https://3v4l.org/H1hfA). The nice silent error just for you!

<img src="/assets/images/posts/2018/upgrade-php/swap.png" class="img-thumbnail">

## "I Got This"

But let's say you know that "Deprecated: The each() function is deprecated. This message will be suppressed on further calls in" means **refactor *each* `each()` usage to `foreach()`.**

(Often it's more complicated, but keep this simple for now.)

Some cases are easy, if your **variables are well-named**:

```php
while (list($key, $callback) = each($callbacks)) {
    // ...
}
```

↓

```php
foreach ($callbacks as $key => $callback) {
    // ...
}
```

<br>

But some... how would you change this one?

```php
while (list($callback) = each($callbacks)) {
    // ...
}
```

↓

```php
foreach ($callbacks as $callback) {
    // ...
}
```

Are you sure? I think it should be like this:

```php
foreach (array_keys($callbacks) as $callback) {
    // ...
}
```

<a href="https://github.com/rectorphp/rector/pull/661/" class="btn btn-dark btn-sm">
    See pull-request #661
</a>

**Honestly, I'm not sure either.** But I took time to test all possible `each()` combinations with `list()`, `while()` and `do/while`, put them into awesome [3v4l.org](https://3v4l.org), wrote a bunch of tests and wrote tested rules for Rector.

<br>

```bash
"Fatal error: Uncaught Error: Call to undefined function ereg() in"
```

## How Rector got into pure PHP Upgrades

At the [PHP Asia Conference](/blog/2018/10/18/how-i-almost-missed-my-talk-in-php-asia-conference/) Rasmus Lerdorf spoke about **upgrading PHP as a big problem**. Much bigger than upgrading particular frameworks. Many WTF namings in PHP are just for BC sake. I struck me, that there is much more legacy PHP code in every company than there is framework-bound code.

I instantly created an issue at Rector, that deals with [PHP 5.3 to 7.4 upgrades](https://github.com/rectorphp/rector/issues/638).
I went full-time on writing PHP upgrade rules - in the train, in the buss, in the plane (the best place to code actually, wonder why).

<br>
<p class="bigger">
   <strong>Today I'm proud to announce 7 new Rector levels</strong> that were not here a month ago:
</p>

```php
use Rector\Core\Configuration\Option;
use Rector\Set\ValueObject\SetList;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

return function (ContainerConfigurator $containerConfigurator): void {
    $containerConfigurator->import(SetList::PHP_54);
    $containerConfigurator->import(SetList::PHP_55);
    $containerConfigurator->import(SetList::PHP_56);
    $containerConfigurator->import(SetList::PHP_70);
    $containerConfigurator->import(SetList::PHP_71);
    $containerConfigurator->import(SetList::PHP_72);
    $containerConfigurator->import(SetList::PHP_73);
    $containerConfigurator->import(SetList::PHP_74);
};
```

<br>

I finally looked over my small framework bubble and **learned a lot about problems of the [world PHP community](https://friendsofphp.org)**.

### How to *ereg* Correctly?

One example for all:

<a href="https://github.com/rectorphp/rector/pull/661/" class="btn btn-dark btn-sm">
    See pull-request #661
</a>

```bash
"Fatal error: Uncaught Error: Call to undefined function ereg() in"
```

That's easy, just add `#` around and change the function name, right?

```diff
-ereg('hi', $string);
+preg_match('#hi#', $string);
```

But what about?

```php
ereg('[]-z]', $string);
ereg('^[a-z]+[.,][a-z]{3,}$', $string);
```

Don't reinvent the wheel! Did you know that 8 years ago some guy [wrote ereg → preg patterns converter](https://gist.github.com/lifthrasiir/704754/7e486f43e62fd1c9d3669330c251f8ca4a59a3f8)? That *some guy* is Kang Seonghoon and helped hundreds if not thousands of people *to not to give a fuck*. Including me. **Amazing work** and I learned about it just by accidental googling. I wonder how many hidden gems are out there.

## Harder, Better, Faster, Stronger... PHP Community

Take the Rector out, run it on your code, let it fix what it can and **report the rest you had to do manually in the issues**. Maybe it can be automated.
You'll be helping each other developer, who upgrades the same PHP version you did. Imagine PHP version 5.3 would be shipped with set like this, covering 100 % of all changes. It's up to us to make a brighter future now.

<br>

Happy coding!
