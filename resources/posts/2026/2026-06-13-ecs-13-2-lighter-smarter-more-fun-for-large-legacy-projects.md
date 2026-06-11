---
id: 438
title: "ECS 13.2: Lighter, Smarter and More Fun for Large/Legacy Projects"
perex: |
    Easy Coding Standard focuses on easy setup, easy run, and easy use. Last year we [simplified the config](/blog/zen-config-in-ecs) to a single fluent line.

    Today we ship **ECS 13.2** - a release that makes ECS lighter to install, smarter about docblocks, and more fun to integreate to a large project, step by step.

    Let's look at what's new.
---

## ECS & CS is Now a Single Package

For years, the custom Symplify rules lived in a separate `symplify/coding-standard` package. To use them, you had to require both packages and keep their versions in sync:

```bash
composer require symplify/easy-coding-standard --dev
composer require symplify/coding-standard --dev
```

That's one dependency too many. In ECS 13.2, **`symplify/coding-standard` is merged directly into ECS**. All 26 custom fixers now ship with ECS out of the box.

<br>

The class names stay exactly the same, so your config keeps working without a single change:

```php
use Symplify\CodingStandard\Fixer\LineLength\LineLengthFixer;
use Symplify\EasyCodingStandard\Config\ECSConfig;

return ECSConfig::configure()
    ->withPaths([__DIR__ . '/src', __DIR__ . '/tests'])
    ->withRules([
        LineLengthFixer::class,
    ]);
```

If you already require `symplify/coding-standard`, you can drop it:

```bash
composer remove symplify/coding-standard --dev
composer require symplify/easy-coding-standard:^13.2 --dev
```

One package, one version to track, all the rules included.

## Lighter Install

ECS needs a dependency-injection container to wire up its checkers. Two years ago [I replaced Symfony DI with the Laravel container](/blog/experiment-how-i-replaced-symfony-di-with-laravel-container-in-ecs) to make the setup lighter.

In 13.2 we go one step further and replace `illuminate/container` with the tiny `entropy/entropy` container. The behavior and the checker registration order stay the same, but the install footprint shrinks.

## Smarter Docblock Level

ECS uses **levels** for gradual adoption. Instead of enabling 24 rules at once and drowning in errors, you start at level `0` and bump the number once your code is clean:

```php
use Symplify\EasyCodingStandard\Config\ECSConfig;

return ECSConfig::configure()
    ->withPaths([__DIR__ . '/src', __DIR__ . '/tests'])
    ->withDocblockLevel(0);
```

Each level enables the next safe rule from a curated list, ordered from the safest to the most invasive. You move at your own pace - bump to `1`, fix what comes up, commit, bump to `2` etc.

<br>

### Levels anyone can Handle

Docblocks are just one of four curated lists. ECS ships a level method for each area, so you can adopt them independently:

```php
use Symplify\EasyCodingStandard\Config\ECSConfig;

return ECSConfig::configure()
    ->withPaths([__DIR__ . '/src', __DIR__ . '/tests'])
    ->withSpacesLevel(0)
    ->withArrayLevel(0)
    ->withControlStructuresLevel(0)
    ->withDocblockLevel(0);
```

<br>

- `withSpacesLevel()` - spacing around operators, casts, and concatenation
- `withArrayLevel()` - array syntax, trailing commas, and indentation
- `withControlStructuresLevel()` - `if`/`else`, `switch`, and early returns
- `withDocblockLevel()` - docblock noise covered below

<br>

In 13.2 the `withDocblockLevel()` got **11 new rules - 24 in total**. They clean up the most common docblock noise:

```diff
 /**
- * @param string $name
- * @return string
+ * @param non-empty-string $name
  */
 public function process(string $name): string
 {
     // ...
 }
```

A `@param` name that no longer matches the argument gets corrected:

```diff
 /**
- * @param string $value
+ * @param string $name
  */
 public function process(string $name): string
 {
     // ...
 }
```

A `@param` for an argument that no longer exists is dead weight and gets removed:

```diff
 /**
  * @param string $name
- * @param int $age
  */
 public function process(string $name): string
 {
     // ...
 }
```

And inline `@var` annotations get normalized to a single consistent format:

```diff
-/** @var $items array<int> */
+/** @var array<int> $items */
 foreach ($items as $item) {
     // ...
 }
```

Inline `@var` normalization, `@param` name corrections, dead `@param` removal, and superfluous return and variable annotations - all behind one growing level. Perfect for onboarding a legacy codebase one level at a time, without a wall of red on day one.

## How to get the Most out of ECS

If you're starting on a fresh project, or not on all sets, enable the levels and let them grow with you:

```php
use Symplify\EasyCodingStandard\Config\ECSConfig;

return ECSConfig::configure()
    ->withPaths([__DIR__ . '/src', __DIR__ . '/tests'])
    ->withRootFiles()
    ->withDocblockLevel(0)
    ->withArrayLevel(0)
    ->withControlStructuresLevel(0)
    ->withDocblockLevel(0);
```

When the level is green, bump the number, commit, repeat. Your code get cleaner with every step, and you stay in control the whole time.

<br>

Once you reach the highest level, you can switch to the full set of rules:

```php
use Symplify\EasyCodingStandard\Config\ECSConfig;

return ECSConfig::configure()
    ->withPaths([__DIR__ . '/src', __DIR__ . '/tests'])
    ->withRootFiles()
    ->withPreparedSets(psr12: true, common: true);
```

<br>

That's it for today. Update to ECS 13.2 and give the new levels a try.

<br>

Happy coding!
