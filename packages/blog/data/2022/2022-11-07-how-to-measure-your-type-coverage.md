---
id: 369
title: "How to Measure Your Type Coverage"
perex: |
    When we come a to a new code base, we look for a code quality metric that will tell us, how healthy the code-base is. We can have CI tools like PHPStan and PHPUnit. PHPStan reports missing types or method call on invalid types. PHPUnit reports failing tests.
    <br><br>
    But how do we know, if 10 passing or 100 passing tests is enough? What if there are over 10 000 cases we should test?
---

That's where **test coverage** gives us a hint. Which project would you join if you could pick: the one with 20 % test coverage or the one with 80 % test coverage? I'd always go with the latter, as test give great confidence.

<br>

Yet, tests are the only thing that can help us access the code quality quickly.
With PHP 7.0, 7.4 and 8.0, type declarations became the sign of project health. But how can we measure those?

<br>

<blockquote class="twitter-tweet"><p lang="en" dir="ltr">Do you measure your type declaration completeness with <a href="https://twitter.com/phpstan?ref_src=twsrc%5Etfw">@phpstan</a> already? <br><br>You should 😉<br><br>It&#39;s such a great and safe feeling to see 99 % type-coverage 😎 <a href="https://t.co/cYyDVYKqG8">pic.twitter.com/cYyDVYKqG8</a></p>&mdash; Tomas Votruba 🇺🇦 (@VotrubaT) <a href="https://twitter.com/VotrubaT/status/1585255758303825922?ref_src=twsrc%5Etfw">October 26, 2022</a></blockquote>


<script async src="https://platform.twitter.com/widgets.js" charset="utf-8"></script>

<br>

## What is the "Type Coverage"?

If the test coverage is % of all possible code runs, what is the type coverage then?

```php
function run($name)
{
    return $name;
}
```

Here we have 1 param, and 1 function return. That's 2 possible type declarations that we're missing:

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

This code has 100 % type declaration coverage. How is that possible? Nullable, union and callable docblock type declarations are valid and the most strict types.

Pretty simple, right?

<br>

We can run type coverage easily with PHPStan on any project, however legacy or magic - no autoloading needed.

<br>

## I love this Metric, Because...

* it is **fast** - we known instantly the result
* it is **simple** - we known the value is between 0-100
* it is **explanatory** - we know the potential type is missing, and where exactly we can fix it
* it is **code sustainability predictor** - based on this number, we know how easy or complicated will be to work with the codebase

## 3 Steps to Measure it?

There type coverage is measured by 3 custom PHPStan rules with 3 custom [collectors](https://phpstan.org/developing-extensions/collectors). They work exactly the same way as described above in the code sample.

1. Install the `symplify/phpstan-rules` package

```bash
composer require symplify/phpstan-rules --dev
```

The package is available on PHP 7.2+, [as downgraded](/blog/how-to-develop-sole-package-in-php81-and-downgrade-to-php72/).

<br>

2. Add Rules to `phpstan.neon`

The most easiest type declaration to add is a return one, then the param one. The typed property on the other hand is available as late as PHP 7.4. That's why we have 3 different rules for them, with one collector per each:

```yaml
services:
    -
        class: Symplify\PHPStanRules\Rules\Explicit\PropertyTypeDeclarationSeaLevelRule
        tags: [phpstan.rules.rule]
        arguments:
            minimalLevel: 0.99

    -
        class: Symplify\PHPStanRules\Rules\Explicit\ParamTypeDeclarationSeaLevelRule
        tags: [phpstan.rules.rule]
        arguments:
            minimalLevel: 0.99

    -
        class: Symplify\PHPStanRules\Rules\Explicit\ReturnTypeDeclarationSeaLevelRule
        tags: [phpstan.rules.rule]
        arguments:
            minimalLevel: 0.99
```

The required level is defined with `minimalLevel` argument in every rule. Notice the value `0.99`, that's at least 99 % type coverage. We'll get back to that later.

<br>

3. Add Collectors to `phpstan.neon`

At the moment, the rules are registered, but they do not have any effect. We have to add collector services too:

```yaml
services:
    -
        class: Symplify\PHPStanRules\Collector\FunctionLike\ParamTypeSeaLevelCollector
        tags: [phpstan.collector]
    -
        class: Symplify\PHPStanRules\Collector\FunctionLike\ReturnTypeSeaLevelCollector
        tags: [phpstan.collector]
    -
        class: Symplify\PHPStanRules\Collector\ClassLike\PropertyTypeSeaLevelCollector
        tags: [phpstan.collector]
```

<br>

Now run to see the results:

```bash
vendor/bin/phpstan
```

The failed error message is more than meets the eye. It shows **you the places where you can complete the type declarations**, so you can just find them in the code and improve.

## How to Find Your Current Type Coverage

Now we get back to the `0.99`, resp. 99 % required type coverage. The CI fails on such a high value, but that's our intention. The error message actually tells us current type coverage value:

```bash
Out of 81 possible param types, only 60 % actually have it. Add more param types to get over 99 %
```

In this case, we take current value of 60 and put it to the config, so our codebase will remain on this code coverage:

```diff
 services:
     -
         class: Symplify\PHPStanRules\Rules\Explicit\ParamTypeDeclarationSeaLevelRule
         tags: [phpstan.rules.rule]
         arguments:
-            minimalLevel: 0.99
+            minimalLevel: 0.60
```

This value can be different for param, return and property, so adjust it accordingly to make the CI pass.

Now we re-run PHPStan, everything is fine. We commit, open pull-request and merge.

## Lean Type Coverage Improvement

Once a week, we run the same command again, trying to bump it 2-3 % (depending on your code-base size) and open pull-request. This way, we can improve the codebase gradually, without any big bang.

We also use these rules **to monitor type coverage improvement** on the project we work on. That way we as developers and the client knows, we're going the right direction.

<br>

Happy coding!
