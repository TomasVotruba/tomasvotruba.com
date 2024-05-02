---
id: 407
title: "How adding Type Declarations makes Your Code Dangerous"
perex: |
    ...and how to avoid it.

    [Type coverage](/blog/how-to-measure-your-type-coverage) is a way to gradually add type declarations to your PHP project—step by step, one by one. It's a PHPStan package that helps you maintain a specific minimal level from 0 % to 100 %.
    <br><br>
    Once we reach high coverage of 80-90 %, we feel safer. But our code can actually be in worse, even dangerous, shape.
---

Imagine we have the following PHP 8.2 code with 100 % type coverage:

```php
<?php

final readonly class Price
{
    public function __construct(
        private int $price,
        private int $decimalCount
    ) {
        // ...
    }
}
```

It looks like very modern PHP, right?

But what if we pass values via controller route params or external service API?

```php
$price = new Price('100.82', '2');
```

What will be the price value? We'd love to see `100.82` float, right?

This is what our code really does ([see 3v4l.org](https://3v4l.org/lKgfG)):

```php
$price = new Price((int) '100.82', (int) '2');
```

## Non-strict? Typecasting!

Even if we have 100 % type coverage, all we've done is add typecasting to our codebase:

```php
return (int) $value + (string) $anotherValue + (float) $thirdValue;
```

Seeing it like this is an obvious red flag, but without using `declare(strict_types=1)`, PHP will silently continue. If we're lucky and we have enabled deprecation warning to trigger errors, we might see:

```bash
Deprecated: Implicit conversion from float-string "100.82" to int loses precision on line 5
```

But this is usually lost because we must run our tests with enabled deprecation warnings.

## Step by step...

We already used a step-by-step approach to reach 100 % type coverage with the help of the [PHPStan package](https://github.com/TomasVotruba/type-coverage). The configuration is in the `phpstan.neon`, and we know what level of type coverage we have.

```yaml
parameters:
    type_coverage:
        return: 90
        param: 90
        property: 90
```

Those numbers were once `0`s. We've gradually increased them to `90` with the kaizen approach, gradual work.

<br>

What if we do a similar measurement in CI for strict types?

```diff
 parameters:
     type_coverage:
         return: 90
         param: 90
         property: 90
+        declare: 5
```

The new `declare` parameter will ensure that at least 5 % of your files use `declare(strict_types=1)`. If it's below 5 %, PHPStan will tell us and fail in CI. This has been available since version 0.2.7.

<br>

## Copy-paste? Automate!

Similar to increasing the type coverage of property, return, and param, the declare feature is **done best in small steps**:

```diff
 parameters:
     type_coverage:
         return: 90
         param: 90
         property: 90
-        declare: 5
+        declare: 7
```

PHPStan says we should increase our `declare(strict_types=1)` usage from 5 % to 7 %. Let's add it to a bunch of files:

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

If your project has 1000 files, this work requires to:

* go through at least 20 files,
* find only those that miss the declare,
* and edit them one by one.

That might be quite a lot of tedious work, right?

<br>

We apply this technique during PHP project upgrades. After a few manual rounds, we felt frustrated and made a [Rector rule](https://github.com/rectorphp/rector-src/pull/5849) work for us. This rule is now being tested on the dev branch and will be released sometime next week.

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

It will find 10 files that lack the `declare(strict_types=1)` and add it for you only in the places where it is missing.
Then push, create pull-request, see what fails on CI, fix type errors, and merge.

<br>

With this PHPStan and Rector combo, you'll quickly increase your type coverage and safety against typecasting.

<br>

Happy coding!
