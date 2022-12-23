---
id: 369
title: "How to Measure Your Type Coverage"
perex: |
    When we come to a new code base, we look for a code quality metric that will tell us how healthy the code base is. We can have CI tools like PHPStan and PHPUnit. PHPStan reports missing or invalid types, and PHPUnit reports failing tests.
    <br><br>
    But how do we know if 10 passing or 100 passing tests is enough? What if there are over 10 000 cases we should test?

updated_since: "December 2022"
updated_message: |
    Update to new package with simple PHPStan configuration.
---

That's where **test coverage** gives us a hint. Which project would you join if you could pick: the one with 20 % test coverage or the one with 80 % test coverage? I'd always go with the latter, as tests give great confidence.

<br>

Yet, tests are not the only thing that can help us **access code quality quickly**.
With PHP 7.0, 7.4, and 8.0, type declarations became a sign of project health. But how can we measure those?

<br>

<blockquote class="twitter-tweet"><p lang="en" dir="ltr">Do you measure your type declaration completeness with <a href="https://twitter.com/phpstan?ref_src=twsrc%5Etfw">@phpstan</a> already? <br><br>You should 😉<br><br>It&#39;s such a great and safe feeling to see 99 % type-coverage 😎 <a href="https://t.co/cYyDVYKqG8">pic.twitter.com/cYyDVYKqG8</a></p>&mdash; Tomas Votruba 🇺🇦 (@VotrubaT) <a href="https://twitter.com/VotrubaT/status/1585255758303825922?ref_src=twsrc%5Etfw">October 26, 2022</a></blockquote>


<script async src="https://platform.twitter.com/widgets.js" charset="utf-8"></script>

<br>

## What is the "Type Coverage"?

If the test coverage is % of all possible code runs, what is the "type coverage" then?

```php
function run($name)
{
    return $name;
}
```

Here we have 1 param and 1 function return. That's 2 possible type declarations that we're missing:

* 0/2 = **0 % type coverage**

<br>

How can we increase it? We add a type declaration into the function param:

```php
function run(string $name)
{
    return $name;
}
```

Here we have 1 param with type declaration, and 1 return without it.

* 1/2 = **50 % type coverage**

<br>

How do we get to 100 %? Exactly, we add the return type declaration:

```php
function run(string $name): string
{
    return $name;
}
```

<br>

We do the same for typed properties as well:

```php
private $name;

private $surname;

private $age;
```

* 0/3 types are completed = **0 % type coverage**

<br>

What about this code?

```php
private string|Name $name;

private ?string $surname = null;

/**
 * @var callable
 */
private $addressCallable;
```

This code has 100 % type declaration coverage. How is that possible? Nullable, union, and callable docblock type declarations are valid and the most strict types.

Pretty simple, right?

<br>

We can run the type coverage check quickly with PHPStan on any project. Even if it's legacy or full of magic - no autoloading is needed.

<br>

## I love this Metric, Because...

* it is **fast** - we know instantly the result
* it is **simple** - we know the value is between 0-100
* it is **explanatory** - we know the potential type is missing, and where exactly can we fix it
* it is **code sustainability predictor** - based on this number, we know how easy or complicated it will be to work with the codebase

<br>

It's amazing to see it grow in time.
This is how type coverage evolved in one project I work on:

<img src="/assets/images/posts/2022/type_coverage.png" class="img-thumbnail">

<br>

## 3 Steps to Measure it

The type coverage is measured by 3 custom PHPStan rules. They work the same way as described above in the code sample.

<br>

1. Install the [`tomasvotruba/type-coverage`](https://github.com/TomasVotruba/type-coverage) package

```bash
composer require tomasvotruba/type-coverage --dev
```

*The package is available on PHP 7.2+, [as downgraded](/blog/how-to-develop-sole-package-in-php81-and-downgrade-to-php72/).*

<br>

2. With PHPStan extension installer, the rules are already installed.

To enable them, increase the minimal coverage on particular location:

```yaml
# phpstan.neon
parameters:
    type_coverage:
        return_type: 50
        param_type: 30
        property_type: 70
```

The number defines minimal required type coverage in particular group. E.g. 30 means at least 30 % type coverage is required.

<br>

Now run to see the results:

```bash
vendor/bin/phpstan
```

The failed error message is more than meets the eye. It shows **you where you can complete the type declarations**, so you can find them in the code and improve.

## How to Find Your Current Type Coverage

Now we get back to the `99` resp. 99 % required type coverage. The CI fails on such a high value, but that's our intention. The error message actually tells us the current type coverage value:

```bash
Out of 81 possible param types, only 60 % actually have it. Add more param types to get over 99 %
```

<br>

In this case, we take the current value of `60` and put it into the config, so our codebase will remain on this code coverage:

```diff
 # phpstan.neon
 parameters:
     type_coverage:
+        return_type: 99
-        return_type: 60
```

Adjust values accordingly to make the CI pass.

Then re-run PHPStan, and everything is fine. We commit, open pull-request, and merge.

## Lean Type Coverage Improvement

Once a week, we run the same command again, trying to bump it 2-3 % (depending on your codebase size) and open a pull request. This way, we can improve the codebase gradually, without any big bang.

We also use these rules **to monitor type coverage improvement** on the project we work on. That way, both developers and the client knows we're going in the right direction.

<br>

Happy coding!
