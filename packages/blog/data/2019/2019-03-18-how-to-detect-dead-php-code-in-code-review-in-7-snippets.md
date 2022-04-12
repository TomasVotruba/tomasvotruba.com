---
id: 195
title: "How to Detect Dead PHP Code in Code Review in 7&nbsp;Snippets"
perex: |
    After few long Nette to Symfony migration series, it's time for relax.
    <br>
    Let's look at 7 snippets of PHP code, that [happily takes your attention](/blog/2018/05/21/is-your-code-readable-by-humans-cognitive-complexity-tells-you/) but **is never run**.
tweet: "New Post on #php 🐘 blog: How to Detect Dead PHP Code in Code Review in 7 Snippets"
tweet_image: "/assets/images/posts/2019/dead-code/fine.jpg"

updated_since: "November 2020"
updated_message: |
    Switch from deprecated `--set` option to `rector.php` config.
---

Imagine you're doing a code review of "various improvements" pull-request with **150 changed files**...

<img src="/assets/images/posts/2019/dead-code/fine.jpg" class="img-thumbnail">

Ok, not that one. Let's go **only for 7 changed files**. There will be 7 code snippets, each possibly with extra code that looks useful, but it doesn't do anything really.

Then click on the button to see if you were right :)

## 1. Duplicated Array Key

```php
<?php

$items = [
    1 => 'A',
    1 => 'B',
    1 => 'C'
];
```

What is `1`?

```php
<?php

var_dump($items[1]);
```

"A" or "C"? Or Array with all fo them?

<a class="btn btn-success mt-2 mb-2" href="https://3v4l.org/cjNWO">See Result</a>

## 2. Key with Key-Hole

```php
<?php

$items = [];
$result = [];
foreach ($items as $key => $value) {
    $result[] = $value;
}
```

What value is extra here?

<a class="btn btn-success mt-2 mb-2" href="#example_2">See Result</a>

<br>
<br>
<br>

<a name="example_2"></a>

```diff
-foreach ($items as $key => $value) {
+foreach ($items as $value) {
     $result[] = $value;
 }
```

## 3. Call for Nothing

```php
<?php

class ParentButNoMethod extends ParentMethod
{
    public function one()
    {
        parent::one();
    }

    public function two()
    {
        parent::two();
    }
}

class ParentMethod
{
    public function one()
    {
    }
}
```

<a class="btn btn-success mt-2 mb-2" href="#example_3">See Result</a>

<br>
<br>
<br>

<a name="example_3"></a>

```diff
<?php

class ParentButNoMethod extends ParentMethod
{
    public function one()
    {
        parent::one();
    }

    public function two()
    {
-       parent::two();
    }
}

class ParentMethod
{
    public function one()
    {
    }
}
```

## 4. Reborn?

```php
<?php

class ProductController
{
    public function actionDiscount(Product $product)
    {
        $discount = $this->getDiscount();
        $productCategory = $this->categoryRepository->findCategoriesByProduct(
            $product->getCategory()
        );
        $discount = $this->getDiscount();

        return $this->render('product/discount.twig', [
            'discount' => $discount,
            'product' => $product,
            'productCategory' => $productCategory,
        ]);
    }
}
```

<a class="btn btn-success mt-2 mb-2" href="#example_4">See Result</a>

<br>
<br>
<br>

<a name="example_4"></a>

```diff
-$discount = $this->getDiscount();
 $productCategory = $this->categoryRepository->findCategoriesByProduct(
     $product->getCategory()
 );
 $discount = $this->getDiscount();
```

## 5. Behind the Mirror

```php
<?php

final class TimeMachine
{
    public function mirrorFunction(Quiz $quiz)
    {
        $timeLimit = $this->resolveTimeLimitForThisTest();
        if ($timeLimit >= 20) {
            return false;
        }

        $timeLimit = $timeLimit;
        if ($this->isQuizFinished($quiz)) {
            $correctQuestions = 1;
            $correctQuestions = $correctQuestions;
            $incorrectQuestions = $correctQuestions - 3;
        }
    }
}
```

<a class="btn btn-success mt-2 mb-2" href="#example_5">See Result</a>

<br>
<br>
<br>

<a name="example_5"></a>

```diff
 <?php

 final class TimeMachine
 {
     public function mirrorFunction(Quiz $quiz)
     {
         $timeLimit = $this->resolveTimeLimitForThisTest();
         if ($timeLimit >= 20) {
             return false;
         }

-        $timeLimit = $timeLimit;
         if ($this->isQuizFinished($quiz)) {
             $correctQuestions = 1;
-            $correctQuestions = $correctQuestions;
             $incorrectQuestions = $correctQuestions - 3;
         }
     }
 }
```

## 6. Rinse & Repeat

```php
<?php

final class WhateverMethodCall
{
    public function run()
    {
        $directories = 1;
        $anotherDirectories = 1;
        $directories = 2;
        $this->store($directories);
        $anotherDirectories = 2;
        $directories = 3;
        $anotherDirectories = 3;
        $directories = 4;
        $directories = 5;
        return $directories + $anotherDirectories;
    }
    public function store(int $directories)
    {
    }
}
```

<a class="btn btn-success mt-2 mb-2" href="#example_6">See Result</a>

<br>
<br>
<br>

<a name="example_6"></a>

```diff
<?php

final class WhateverMethodCall
{
    public function run()
    {
-       $directories = 1;
-       $anotherDirectories = 1;
        $directories = 2;
        $this->store($directories);
-       $anotherDirectories = 2;
-       $directories = 3;
        $anotherDirectories = 3;
-       $directories = 4;
        $directories = 5;
        return $directories + $anotherDirectories;
    }
}
```

## 7. Privates that No-One See

```php
<?php

final class SomeController
{
    private const MAX_LIMIT = 5;

    private const LIMIT = 5;

    private $cachedValues = [];

    private $cachedItems = [];

    public function run()
    {
        $values = $this->repeat();
        $values[] = 5;

        return $values + $this->cachedItems;
    }

    private function repeat()
    {
        $items = [];
        while ($this->fetch() && $this->fetch() < self::LIMIT) {
            $items[] = $this->fetch();
            $this->cachedItems[] = $this->fetch();
        }

        return $items;
    }

    private function fetch()
    {
        return mt_rand(1, 15);
    }

    private function clear()
    {
        $this->cachedItems = [];
    }
}
```

<a class="btn btn-success mt-2 mb-2" href="#example_7">See Result</a>

<br>
<br>
<br>

<a name="example_7"></a>

```diff
<?php

final class SomeController
{
-    private const MAX_LIMIT = 5;

     private const LIMIT = 5;

-    private $cachedValues = [];

     private $cachedItems = [];

     public function run()
     {
         $values = $this->repeat();
         $values[] = 5;

         return $values + $this->cachedItems;
     }

     private function repeat()
     {
         $items = [];
         while ($this->fetch() && $this->fetch() < self::LIMIT) {
             $items[] = $this->fetch();
             $this->cachedItems[] = $this->fetch();
         }

         return $items;
     }

     private function fetch()
     {
         return mt_rand(1, 15);
     }

-    private function clear()
-    {
-        $this->cachedItems = [];
-    }
}
```


## You're in the Finish!

- How many dead codes did you find?
- Did you just scan right here, because the task was too hard and it didn't make sense to you?
- Would you do it again tomorrow or rather code?

No wonder **people don't do code-review** (right), there is no time and it's often super boring.

<br>

What if there would be a way to **automate all that checks above** + 10 more with a CI tool. Something that:

- would **do code-review for you** making your team much smarter forever
- would **not require your check** on every new piece of code
- you could **extend** as you like

## Have you tried Rector?

Rector doesn't only refactor applications from one framework to another, upgrade your codebase and get you out of legacy. It can be also **part of your CI**:

1. Install Rector

```bash
composer require rector/rector --dev
```

2. Update `rector.php` with dead code set

```php
use Rector\Set\ValueObject\SetList;
use Rector\Config\RectorConfig;

return function (RectorConfig $rectorConfig): void {
    $rectorConfig->import(SetList::DEAD_CODE);
};
```

3. Run Rector

```bash
vendor/bin/rector process src --dry-run
```

**If Rector detects any dead code, CI will fail**. You can, of course, run it without `--dry-run` after to actually remove the code.

See [Dead Code set](https://github.com/rectorphp/rector/blob/master/docs/rector_rules_overview.md#deadcode) for more features.
