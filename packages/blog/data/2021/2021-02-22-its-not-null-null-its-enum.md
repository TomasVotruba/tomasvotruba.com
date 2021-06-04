---
id: 304
title: "It's not Null, it's Enum"
perex: |
    Last weekend I got into reading a good old post [Null Hell](https://afilina.com/null-hell) by Afilina, a fellow legacy archeologist. Null parameters are evil, which turns code into "maybe" and "just in case" conditions with ifs everywhere.
    <br><br>
    I was wondering how difficult it is to get rid of nullable parameters in a project. I made myself a challenge: **get rid of nullable params over the weekend**. This is what happened.

tweet: "New Post on #php 🐘 blog: It's not Null, it's Enum"
tweet_image: "/assets/images/posts/2021/priority_enum.gif"
---

<blockquote class="blockquote text-center">
    "Complain about the way other people make software
    <br>
    by making software."
</blockquote>

This post will be a hands-on brain insight, how I practically code step by step. I'll share my inner thoughts as they come by.

## 1. Set a Goal

Symplify is a monorepo of [~40 split packages](https://github.com/symplify/symplify). The goal is to remove as many nullable parameters as possible.

What is a nullable parameter? A params like these in any custom method or function:

```php
function run(
    ?string $param,
    string $optionalParam = null,
    string|null $unionParam
) {
}
```

That's for theory.

## 2. Prepare PHPStan Rule

In Symplify, before we start to apply any "just-made-up" rule I've read or heard somewhere, we have to **formalize it first**. With PHP code, that means "write a PHPStan rule for that".

<br>

Why is this step necessary for success? When you read a post about ["every class should be final"](/blog/2019/01/24/how-to-kill-parents), you might think that's a good idea. It's easy to get hyped in software, so you try to put `final` everywhere in your code right from Monday. Soon, your project has `final` in some classes that you worked with. Your colleagues are not hyped yet, and you also broke few Doctrine entities by making them `final`. Your argument is not very trustworthy now, and people don't like to keep the rule that doesn't have clear boundaries.

<br>

By writing a **PHPStan rule first, we avoid this whole mess**. Also, we realize the ideal statements like "every *x* should be *y*" have to be adapted into practical coding habits. While I was writing a `ForbiddenNullableParameterRule` I learned that:

- nullable `$param` required by `interface` from `/vendor` - we should skip these because refactoring the world is not a weekend goal
- we need to forbid specific types first to keep refactoring gradually
- we need to allow some types to keep refactoring gradually

We integrated these criteria into the rule itself, and after a couple of hours of coding and testing, it was ready to become part of our CI.

## 3. Make PHPStan rule part of CI

The PHPStan rule must run in CI. There is no excuse. I've seen a couple of projects where they used PHPStan, had their own PHPStan rules, but they were missing in `phpstan.neon`.

Let's avoid that mistake:

```yaml
services:
    -
        class: Symplify\PHPStanRules\Rules\ForbiddenNullableParameterRule
        tags: [phpstan.rules.rule]
        arguments:
            forbiddenTypes:
                - ...
            allowedTypes:
                - ...
```

We run PHPStan, and... it reported 130 cases. Yay! That's a doable number of PHPStan errors for a weekend job. If it would be 300+, we'd tune the `forbiddenTypes` and `allowedTypes` parameters until the number fits under a hundred.

There is no point in stressing yourself or ignoring everything. Refactoring should be fun!

## 4. Remove the `Null` Parameter

Let's look at one typical example that could be spot on many places:

<img src="/assets/images/posts/2021/priority_enum.gif" class="img-thumbnail">

First step would be to remove the `?`:

```diff
-public function sort(array $changes, ?string $priority): array
+public function sort(array $changes, string $priority): array
 {
     // ...
 }
```

We've removed the nullable parameter - mission complete! All we need to do now is to fix dozens of new PHPStan errors.

I realized that `?string` is a code smell for enum only in hindsight, but I started to be suspicious.

I looked into code and tests for the values that the `$priority` argument can take. To my surprise, they were the same values over and over again:

```php
$this->sort($changes, 'packages');

$this->sort($changes, 'categories');

$this->sort($changes, null);
```

It seems the priority was chosen or not. If we put this sentence in the code:

```diff
-$this->sort($changes, null);
+$this->sort($changes, 'none');
```

But should we use strings around the project without any boundaries?

## 5. Extract the Enum

No. There should be at least constants:

```diff
-$this->sort($changes, 'packages');
+$this->sort($changes, PackageCategoryPriority::PACKAGES);

-$this->sort($changes, 'categories');
+$this->sort($changes, PackageCategoryPriority::CATEGORIES);

-$this->sort($changes, null);
+$this->sort($changes, PackageCategoryPriority::NONE);
```

I've extracted three repeated values to a standalone class:

```php
namespace Symplify\ChangelogLinker\ValueObject;

/**
 * @enum
 */
final class PackageCategoryPriority
{
    /**
     * @var string
     */
    public const CATEGORIES = 'categories';

    /**
     * @var string
     */
    public const PACKAGES = 'packages';

    /**
     * @var string
     */
    public const NONE = 'none';
}
```

Note: the `@enum` docblock is a marker for PHP 8.1 Rector rule that will be able to refactor it to native `enum`.

<br>

In the end, a single detected enum helped us **to remove nullable on ~30 places** with a single refactoring.

```diff
-public function sort(array $changes, ?string $priority): array
+public function sort(array $changes, string $priority): array
 {
-    if ($priority === null) {
+    if ($priority === PackageCategoryPriority::NONE) {
     }
     // ...
 }
```

Now we can repeat a similar process for the other nullables. If you get stuck, you can find inspiration in [Symplify PR, where we cleaned all 130 cases](https://github.com/symplify/symplify/pull/2977/files).

<br>

Happy coding!
