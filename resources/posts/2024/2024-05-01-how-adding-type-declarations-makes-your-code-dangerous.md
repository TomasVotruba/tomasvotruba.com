---
id: 407
title: "From Type Coverage to Strict Types"
perex: |
    [Type coverage](/blog/how-to-measure-your-type-coverage) is a way to add type declarations to your PHP project gradually. Step by step, one by one. It's PHPStan package that helps you hold specific minimal level from 0 % to 100 %.
    <br><br>
    Once we reach high coverage of 80-90 %, we might feel safer. But our code can be actually in a worse, even dangerous shape.
---

Imagine we have following PHP 8.2 code with 100 % type coverage:

```php
<?php

final readonly class Price
{
    public function __construct(private int $price, private int $decimalCount)
    {
        // ...
    }
}
```

Looks very modern, right? But what if we pass some values:

```php
$price = new Price('100.82', '2');
```

What will be the price first value? We'd love to see `100.82` float, right?

This is what our code really does ([see 3v4l.org](https://3v4l.org/lKgfG)):

```php
$price = new Price((int) '100.82', (int) '2');
```

## Non-strict? Typecasting!

Even if we have 100 % type coverage, all we've done is to add typecasting to our codebase:

```php
return (int) $value + (string) $anotherValue + (float) $thirdValue;
```

Seeing it like this is obvious red flag, but without using `declare(strict_types=1)` PHP will silently continue. If we're lucky and we have enabled deprecation warning to trigger errors, we might see:

```bash
Deprecated: Implicit conversion from float-string "100.82" to int loses precision in /in/lKgfG on line 5
```

But this is usually lost, because we're not running our tests with deprecation warnings enabled.

## Step by step...

We already used step-by-step approach to reach 100 % type coverage with help of [PHPStan package](https://github.com/TomasVotruba/type-coverage). The configuration is in the `phpstan.neon` and we know what level of type coverage we have.

```yaml
parameters:
    type_coverage:
        return: 90
        param: 90
        property: 90
```

Those numbers were once `0`s. We've gradually increased them to `90` with kaizen approach, gradual work.

<br>

What if we do the same measurement in CI for strict types?

```yaml
parameters:
    type_coverage:
        return: 90
        param: 90
        property: 90
        declare: 5
```

The new `declare` parameter will make sure at least 5 % of your files use `declare(strict_types=1)`. If it's bellow, PHPStan will tell you. This available since version 0.2.7.

<br>

## Copy-paste? Automate!

Again, like the type coverage on property, return and param, the declare feature is best used with small steps:

```yaml
 parameters:
     type_coverage:
         return: 90
         param: 90
         property: 90
-        declare: 5
+        declare: 7
```

PHPStan tell us, we should increase our `declare(strict_types=1)` usage to 7 %. Let's add it to bunch of files:

```diff
 <?php

+declare(strict_types=1);

 final readonly class Price
 {
     public function __construct(private int $price, private int $decimalCount)
     {
         // ...
     }
 }
```

If your project has 1000 files, that's:

* 20 files to go through manually,
* find only those that miss the declare,
* and add it one by one.

That might be quite a lot of boring work, right?

<br>

We apply these technique on PHP project upgrades, so after few manual rounds, we felt the frustration... and made a [Rector rule](https://github.com/rectorphp/rector-src/pull/5849) to work for us. This rule is now being tested on the dev, and will be released sometimes next week.

```php
use Rector\TypeDeclaration\Rector\StmtsAwareInterface\IncreaseDeclareStrictTypesRector;

return RectorConfig::configure()
    ->withConfiguredRule(IncreaseDeclareStrictTypesRector::class, [
        'limit' => 10,
    ]);
```

Once you run Rector:

```bash
vendor/bin/rector
```

It will find 10 files that miss the `declare(strict_types=1)` and add it for you only in places its missing.
Then push, create pull-request, see what fails on CI, fix type errors and merge.

<br>

With this PHPStan and Rector combo you'll increase not only your type coverage, but also safety againts type-casting in no time.

<br>

Happy coding!
