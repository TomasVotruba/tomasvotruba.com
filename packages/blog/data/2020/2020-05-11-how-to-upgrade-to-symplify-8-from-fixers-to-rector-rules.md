---
id: 251
title: "How to Upgrade to Symplify 8 - From Fixers to Rector Rules"
perex: |
    Symplify 8 is going to be released in the 2nd half of May. But as in Symfony, you can get ready for future version today.
    <br><br>
    [In the previous post we upgraded Coding Standard from Sniffs to PHPStan](/blog/2020/05/04/how-to-upgrade-to-symplify-8-from-sniffs-to-phpstan-rules/). Today we finish with 2nd half - **from Fixers to Rector rules**.

tweet: "New Post on #php 🐘 blog: How to Upgrade to #symplify 8 - From Fixer to @rectorphp Rules"

updated_since: "August 2020"
updated_message: |
    Updated Rector/ECS YAML to PHP configuration, as current standard.
---

When you run [ECS](https://github.com/symplify/easy-coding-standard) with version 7.3+:

```bash
vendor/bin/ecs check
```

You might see such notices right before your code gets checked:

```bash
PHP Notice:  Fixer "..." is deprecated. Use "..." instead
```

## Why were These Fixers Dropped?

You'll find answer to this question [in previous post](/blog/2020/05/04/how-to-upgrade-to-symplify-8-from-sniffs-to-phpstan-rules/). To extend answer specifically for this post: Fixer and Rector do the same job - **they change code based on specific recipe**.

### What is the Difference?

- Fixer works with [tokens](https://www.php.net/manual/en/function.token-get-all.php) → which is **great for spaces and `{}()` positions** etc.,
- Rector works with abstract syntax tree → **great for refactoring, method/property position changes**, rename across the code base, etc.

Now we know *why*. Let's look *how* to deal with that.

[link_rector_book]

## What to do With These Deprecations?

So what does it mean? Remove all the rules from `ecs.php` and let go?

No, **all you need to do is switch to Rector rules**. It's better working and more reliable since it works with context and not token positions. So at first, you might see new changes in your code.

## How to Handle Upgrade in 30 minutes?

There are dozen deprecated fixers in total. Let's take it one by one.

First - if you don't have Rector, install it:

```bash
composer require rector/rector --dev
```

## 1. No Empty Doc Block

The `RemoveEmptyDocBlockFixer` rule basically copied behavior of native `NoEmptyPhpdocFixer`, so just it instead:

```diff
 <?php

 // ecs.php

 declare(strict_types=1);

 use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

 return static function (ContainerConfigurator $containerConfigurator): void {
     $services = $containerConfigurator->services();
-    $services->set(Symplify\CodingStandard\Fixer\Commenting\RemoveEmptyDocBlockFixer::class);
+    $services->set(PhpCsFixer\Fixer\Phpdoc\NoEmptyPhpdocFixer::class);
 };
```

## 2. Preg Delimiter Character

The `PregDelimiterFixer` was checking consistent preg delimiter, in this case `#`:

```diff
 class SomeClass
 {
     public function run()
     {
-        preg_match('~value~', $value);
+        preg_match('#value#', $value);
     }
 }
```

Instead of ~~`PhpCsFixer\Fixer\ControlStructure\PregDelimiterFixer`~~ fixer:

↓

use Rector rule:

```php
<?php

// rector.php

declare(strict_types=1);

use Rector\CodingStyle\Rector\FuncCall\ConsistentPregDelimiterRector;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

return function (ContainerConfigurator $containerConfigurator): void {
    $services = $containerConfigurator->services();
    $services->set(ConsistentPregDelimiterRector::class)
        // default
        ->arg('delimiter', '#');
};
```

## 3. Required Must be followed by Absolute Path

```diff
 class SomeClass
 {
     public function run()
     {
-        require 'autoload.php';
+        require __DIR__ . '/autoload.php';
     }
 }
```

Instead of ~~`PhpCsFixer\Fixer\ControlStructure\RequireFollowedByAbsolutePathFixer`~~ fixer,

↓

use Rector rule:

```php
<?php

// rector.php

declare(strict_types=1);

use Rector\CodingStyle\Rector\Include_\FollowRequireByDirRector;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

return function (ContainerConfigurator $containerConfigurator): void {
    $services = $containerConfigurator->services();
    $services->set(FollowRequireByDirRector::class);
};
```

## 4. Match Exception variable name to its Type

```diff
 class SomeClass
 {
     public function run()
     {
         try {
             // ...
-        } catch (SomeException $typoException) {
-            $typoException->getMessage();
+        } catch (SomeException $someException) {
+            $someException->getMessage();
         }
     }
 }
```

Instead of ~~`PhpCsFixer\Fixer\Naming\CatchExceptionNameMatchingTypeFixer`~~ Fixer:

↓

use Rector rule:

```php
<?php

// rector.php

declare(strict_types=1);

use Rector\CodingStyle\Rector\Catch_\CatchExceptionNameMatchingTypeRector;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

return function (ContainerConfigurator $containerConfigurator): void {
    $services = $containerConfigurator->services();
    $services->set(CatchExceptionNameMatchingTypeRector::class);
};
```

## 5. Match Property and Variable to its Type

```diff
 class SomeClass
 {
     /**
      * @var EntityManager
      */
-    private $eventManager;
+    private $entityManager;
-    public function __construct(EntityManager $eventManager)
+    public function __construct(EntityManager $entityManager)
     {
-        $this->eventManager = $eventManager;
+        $this->entityManager = $entityManager;
     }
 }
```

Instead of ~~`Symplify\CodingStandard\Fixer\Naming\PropertyNameMatchingTypeFixer`~~ Fixer

↓

use Rector rule:

```php
<?php

// rector.php

declare(strict_types=1);

use Rector\Naming\Rector\Class_\RenamePropertyToMatchTypeRector;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

return function (ContainerConfigurator $containerConfigurator): void {
    $services = $containerConfigurator->services();
    $services->set(RenamePropertyToMatchTypeRector::class);
};
```

## 6. Set Default Values to bool and array Type to Prevent Undefined Value

```diff
 class SomeClass
 {
     /**
      * @var bool
      */
-    private $isDisabled;
+    private $isDisabled = false;

     /**
      * @var int[]
      */
-    private $values;
+    private $values = [];

     public function isEmpty()
     {
         return $this->values === [];
     }
 }
```

Instead of:

- ~~`Symplify\CodingStandard\Fixer\Property\BoolPropertyDefaultValueFixer`~~
- ~~`Symplify\CodingStandard\Fixer\Property\ArrayPropertyDefaultValueFixer`~~

↓

use Rector rules:

```php
<?php

// rector.php

declare(strict_types=1);

use Rector\CodingStyle\Rector\Class_\AddArrayDefaultToArrayPropertyRector;
use Rector\SOLID\Rector\Property\AddFalseDefaultToBoolPropertyRector;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

return function (ContainerConfigurator $containerConfigurator): void {
    $services = $containerConfigurator->services();
    $services->set(AddFalseDefaultToBoolPropertyRector::class);
    $services->set(AddArrayDefaultToArrayPropertyRector::class);
};
```

## 7. Use `::class` over Strings Names

This feature is here since PHP 5.5, and it's a massive help for static analysis and instant migrations.

```diff
 class AnotherClass
 {
 }

 class SomeClass
 {
     public function run()
     {
-        return 'AnotherClass';
+        return \AnotherClass::class;
     }
 }
```

Instead of ~~`Symplify\CodingStandard\Fixer\Php\ClassStringToClassConstantFixer`~~ Fixer:

↓

use Rector rule:

```php
<?php

// rector.php

declare(strict_types=1);

use Rector\Php55\Rector\String_\StringClassNameToClassConstantRector;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

return function (ContainerConfigurator $containerConfigurator): void {
    $services = $containerConfigurator->services();
    $services->set(StringClassNameToClassConstantRector::class);
};
```

## 8. Order Property by Complexity, Private Methods by Use

How do you order your methods? Random?

**Be sure to read [How to Teach Your Team Private Method Sorting in 3 mins](/blog/2018/11/01/how-teach-your-team-private-method-sorting-in-3-mins/).**

Instead of:

- ~~`Symplify\CodingStandard\Fixer\Order\PrivateMethodOrderByUseFixer`~~
- ~~`Symplify\CodingStandard\Fixer\Order\PropertyOrderByComplexityFixer`~~

↓

use Rector rules:

```php
<?php

// rector.php

declare(strict_types=1);

use Rector\Order\Rector\Class_\OrderPrivateMethodsByUseRector;
use Rector\Order\Rector\Class_\OrderPropertyByComplexityRector;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

return function (ContainerConfigurator $containerConfigurator): void {
    $services = $containerConfigurator->services();
    $services->set(OrderPrivateMethodsByUseRector::class);
    $services->set(OrderPropertyByComplexityRector::class);
};
```

## 9. Specific Order By Parent Contract

Do you implement one interface over and over? Do you have dozens of such classes and want their public methods to have a specific order?

```diff
 final class SomeFixer implements FixerInterface
 {
-    public function isCandidate()
+    public function getName()
     {
     }

-    public function getName()
+    public function isCandidate()
     {
         // ...
     }
 }
```

Instead of ~~`Symplify\CodingStandard\Fixer\Order\MethodOrderByTypeFixer`~~ Fixer:

↓

use Rector rule:

```php
<?php

// rector.php

declare(strict_types=1);

use Rector\Order\Rector\Class_\OrderPublicInterfaceMethodRector;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

return function (ContainerConfigurator $containerConfigurator): void {
    $services = $containerConfigurator->services();
    $services->set(OrderPublicInterfaceMethodRector::class)
        ->call('configure', [[
            OrderPublicInterfaceMethodRector::METHOD_ORDER_BY_INTERFACES => [
                'FixerInterface' => [
                    'getName',
                    'isCandidate',
                ]
            ]
        ]]);
};
```

## 10. Make Classes `final`, if You Can

This will be the biggest added value, as tokens have no idea if your class is extended by another class.

Rector knows that, so be ready for more solid code after you run it.

Instead of ~~`Symplify\CodingStandard\Fixer\Solid\FinalInterfaceFixer`~~ Fixer:

↓

use Rector rule:

```php
<?php

// rector.php

declare(strict_types=1);

use Rector\SOLID\Rector\Class_\FinalizeClassesWithoutChildrenRector;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

return function (ContainerConfigurator $containerConfigurator): void {
    $services = $containerConfigurator->services();
    $services->set(FinalizeClassesWithoutChildrenRector::class);
};
```

<br>

And that's it. Now you're ready!

<br>

Happy coding!
