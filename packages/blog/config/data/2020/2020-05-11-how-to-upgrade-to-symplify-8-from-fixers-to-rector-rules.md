---
id: 251
title: "How to Upgrade to Symplify 8 - From Fixers to Rector Rules"
perex: |
    Symplify 8 is going to be released in the 2nd half of May. But as in Symfony, you can get ready for future version today.
    <br><br>
    [In the previous post we upgraded Coding Standard from Sniffs to PHPStan](/blog/2020/05/04/how-to-upgrade-to-symplify-8-from-sniffs-to-phpstan-rules). Today we finish with 2nd half - **from Fixers to Rector rules**.
tweet: "New Post on #php 🐘 blog: How to Upgrade to #symplify 8 - From Fixer to @rectorphp Rules"
---

When you run [ECS](https://github.com/symplify/easycodingstandard) with version 7.3+:

```bash
vendor/bin/ecs check
```

You might see such notices right before your code gets checked:

```bash
PHP Notice:  Fixer "..." is deprecated. Use "..." instead
```

## Why were These Fixer Dropped?

You'll find answer to this question [in previous post](/blog/2020/05/04/how-to-upgrade-to-symplify-8-from-sniffs-to-phpstan-rules/). To extend answer specifically for this post: Fixer and Rector do the same job - **they change code based on specific recipe**.

### What is the Difference?

- Fixer works with [tokens](https://www.php.net/manual/en/function.token-get-all.php) → which is **great for spaces and `{}()` positions** etc.,
- Rector works with abstract syntax tree → **great for refactoring, method/property position changes**, rename across the code base, etc.

Now we know *why*. Let's look *how* to deal with that.

## What to do With These Deprecations?

So what does it mean? Remove all the rules from `ecs.yaml` and let go?

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
 # ecs.yaml
 services:
-    Symplify\CodingStandard\Fixer\Commenting\RemoveEmptyDocBlockFixer: null
+    PhpCsFixer\Fixer\Phpdoc\NoEmptyPhpdocFixer: null
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

Instead of fixer:

```diff
 # ecs.yaml
 services:
-    PhpCsFixer\Fixer\ControlStructure\PregDelimiterFixer: null
```

↓

use Rector rule:

```yaml
# rector.yaml
services:
    Rector\CodingStyle\Rector\FuncCall\ConsistentPregDelimiterRector:
         $delimiter: '#' # default
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

Instead of fixer:

```diff
 # ecs.yaml
 services:
-    PhpCsFixer\Fixer\ControlStructure\RequireFollowedByAbsolutePathFixer: null
```

↓

use Rector rule:

```yaml
# rector.yaml
services:
    Rector\CodingStyle\Rector\Include_\FollowRequireByDirRector: null
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

Instead of fixer:

```diff
 # ecs.yaml
 services:
-    PhpCsFixer\Fixer\Naming\CatchExceptionNameMatchingTypeFixer: null
```

↓

use Rector rule:

```yaml
# rector.yaml
services:
    Rector\CodingStyle\Rector\Catch_\CatchExceptionNameMatchingTypeRector: null
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

Instead of Fixer:

```diff
 # ecs.yaml
 services:
-    Symplify\CodingStandard\Fixer\Naming\PropertyNameMatchingTypeFixer: null
```

↓

use Rector rule:

```yaml
# rector.yaml
services:
    Rector\Naming\Rector\Class_\RenamePropertyToMatchTypeRector: null
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

Instead of Fixers:

```diff
 # ecs.yaml
 services:
-    Symplify\CodingStandard\Fixer\Property\BoolPropertyDefaultValueFixer: null
-    Symplify\CodingStandard\Fixer\Property\ArrayPropertyDefaultValueFixer: null
```

↓

use Rector rules:

```yaml
# rector.yaml
services:
    Rector\SOLID\Rector\Property\AddFalseDefaultToBoolPropertyRector: null
    Rector\CodingStyle\Rector\Class_\AddArrayDefaultToArrayPropertyRector: null
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

Instead of Fixer:

```diff
 # ecs.yaml
 services:
-    Symplify\CodingStandard\Fixer\Php\ClassStringToClassConstantFixer: null
```

↓

use Rector rule:

```yaml
# rector.yaml
services:
    Rector\Php55\Rector\String_\StringClassNameToClassConstantRector: null
```

## 8. Order Property by Complexity, Private Methods by Use

How do you order your methods? Random?

**Be sure to read [How to Teach Your Team Private Method Sorting in 3 mins](/blog/2018/11/01/how-teach-your-team-private-method-sorting-in-3-mins/).**

Instead of Fixers:

```diff
 # ecs.yaml
 services:
-    Symplify\CodingStandard\Fixer\Order\PrivateMethodOrderByUseFixer: null
-    Symplify\CodingStandard\Fixer\Order\PropertyOrderByComplexityFixer: null
```

↓

use Rector rules:

```yaml
# rector.yaml
services:
    Rector\Order\Rector\Class_\OrderPrivateMethodsByUseRector: null
    Rector\Order\Rector\Class_\OrderPropertyByComplexityRector: null
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

Instead of Fixer:

```diff
 # ecs.yaml
 services:
-    Symplify\CodingStandard\Fixer\Order\MethodOrderByTypeFixer:
-        ...
```

↓

use Rector rule:

```yaml
# rector.yaml
services:
    Rector\Order\Rector\Class_\OrderPublicInterfaceMethodRector:
        $methodOrderByInterfaces:
            FixerInterface:
                - 'getName'
                - 'isCandidate'
```

## 10. Make Classes `final`, if You Can

This will be the biggest added value, as tokens have no idea if your class is extended by another class.

Rector knows that, so be ready for more solid code after you run it.

Instead of Fixer:

```diff
 # ecs.yaml
 services:
-    Symplify\CodingStandard\Fixer\Solid\FinalInterfaceFixer: null
```

↓

use Rector rule:

```yaml
# rector.yaml
services:
    Rector\SOLID\Rector\Class_\FinalizeClassesWithoutChildrenRector: null
```

<br>

And that's it. Now you're ready!

<br>

Happy coding!
