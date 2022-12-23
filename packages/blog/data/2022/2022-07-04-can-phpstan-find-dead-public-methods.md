---
id: 365
title: "Can PHPStan find Dead Public Methods?"
perex: |
    This bold question has been around PHP Internet for many years... [at least since 2008](https://stackoverflow.com/questions/11532/how-can-i-find-unused-functions-in-a-php-project). In 2017 I added [dead public method sniff](https://github.com/symplify/symplify/pull/466) to Symplify Coding Standard.
    <br><br>
    It runs on bare PHP tokens without any type or AST, so the capability was limited. Yet it was able to detect a few unused methods. Now, 5 years later, we maybe have a better solution.

updated_since: "December 2022"
updated_message: |
    Update to new package with simple PHPStan configuration.
---

[The sniff rule](/blog/2019/03/14/remove-dead-public-methdos-from-your-code/) looked for unused public method with very basic algorithm:

### 1. Collect all Public Methods

```php
public function speedUp()
{
}

public function slowDown()
{
}
```

↓

- `speedUp`
- `slowDown`

<br>

### 2. Collect all Method Calls

```php
$someObject->speedUp();
```

↓

- `speedUp`

<br>

### 3. Subtract First List from Second

There we have a list of unused public methods:

- `slowDown`

<br>

That's it!

<br>

This sniff helped to [detect many dead methods](https://github.com/rectorphp/rector-src/commit/3ef5f555b729dfc7758043674c15ea2354af71f2).

<br>

But without types, it misses essential information like this:

```php
class Car
{
    public function speedUp() { ... }
}
```

vs

```php
class Bus
{
    public function speedUp() { ... }
}
```

<br>

The same-named methods are in 2 different classes. **Even if only one is used, both are reported as used**. That's not good enough, so we've removed the token-based solution.

<br>

## Turning Tide

A week ago [PHPStan 1.8](https://github.com/phpstan/phpstan/releases/tag/1.8.0) introduced similar feature, [called collectors](https://phpstan.org/developing-extensions/collectors).
The principle is simple:

* collect one group of data
* collect another group of data
* compare both groups and show the result

<br>

I got excited and wanted to try this feature as soon as I had some free time.

Last week I made the first rule that detects unused public constants:

<blockquote class="twitter-tweet"><p lang="en" dir="ltr">I&#39;m roughly playing with new collectors from PHPStan 1.8 😇<br><br>This is how &quot;unused public constant&quot; is found ↓<a href="https://t.co/SkTCITcLh4">https://t.co/SkTCITcLh4</a> <a href="https://t.co/zt4yyq0Hmh">pic.twitter.com/zt4yyq0Hmh</a></p>&mdash; Tomas Votruba 🇺🇦 (@VotrubaT) <a href="https://twitter.com/VotrubaT/status/1542459395203911682?ref_src=twsrc%5Etfw">June 30, 2022</a></blockquote>

<br>

Detecting constant call is relatively easy because there is always one exact class and constant name:

```php
SomeClass::SOME_CONSTANT
```

<br>

We've added rules to a few projects, and **the results are fantastic**.

How about trying the same approach to public methods?

<br>

## PHPStan Collector for Unused Method

With method calls, this is a bit complex, as caller type could be anything:

```php
$value->speedUp(); // $value is mixed type

function run(?Car $value) {
    $value->speedUp(); // $value is null|Car
}

/** @var Car $value */
$value->speedUp(); // $value is probably Car
```

<br>

I wanted to see how many false positives this rule will catch and try out collectors. Take this as a joyful learning experience.

<br>

To keep **the rule simple and reliable**, we've narrowed the scope further down:

- public methods only
- skip `Trait_`, `Enum` and `Interface` methods
- skip methods overriding parent class or required by interface
- skip methods that have `@api` annotation, or the class has `@api` annotation
- skip methods in PHPUnit test case
- skip methods with an attribute

<br>

```php
#[Required]
public function autowire(...)
{
    // ...
}
```

<br>

Are you maintaining an open-source project with a method that is designed for external use?

Mark it with `@api` to make the rule skip it.


## How to run it on Your Project

Are you willing to try this rule on your code base? Even if there will be lot of removal work?

You've been warned. There you go...

```bash
composer require tomasvotruba/unused-public --dev
```


<br>

<br>

Run PHPStan and see the results.

Do you have a tip to improve this rule or architecture? It's my 2nd collector rule, and it has flaws in the design, so don't hesitate and share it in the comments so that I can improve it. Thank you, good luck, and have fun!

<br>

Happy coding!

<script async src="https://platform.twitter.com/widgets.js" charset="utf-8"></script>
