---
id: 113
title: "How to Turn Mocks from Nightmare to Solid Kiss Tests"
perex: |
    [Martin Hlaváč](http://mhlavac.net) had a very nice talk about testing in [Berlin PHP Meetup](http://www.bephpug.de/2018/06/05/june.html) last week (while I hosted with [Rector](https://github.com/rectorphp/rector)), and one of the topic was mocking.
    <br><br>
    I often see developers fighting with this, in places they don't have to, just because this topic is so widespread all over the internet and unit tools.
    <br><br>
    Did you know there is easier and more clear way to do "mocking"?
tweet: "New Post on my Blog: How to Turn Mocks to Readable Tests #phpunit #mocking"

updated_since: "August 2020"
updated_message: |
    Updated with shift from sniffs to Rector rules, that handle these cases much better.
---

At the time being, there is only 1 post about [anonymous classes in tests](https://mnapoli.fr/anonymous-classes-in-tests/) (thanks to Matthieu!). Compared to that, there are many PHP tool made just for mocking: Prophecy, Mockery, PHPUnit native mocks, Mockista and so on. If you're a developer who uses one of them, knows that he needs to add proper annotations to make autocomplete work, has the PHPStom plugin that fixes bugs in this autocomplete and it works well for you, just stop reading.

This post is for developers who struggle with mocking and have a feeling, that they're doing something wrong.

**You're not. It's the mocking part**. Mocks are often the bottleneck of understanding in tests. They're so easy to make, that they can overpopulate your tests... the same way units test can test every getter and setter of all your entities in 20 minutes *(hint: not a way to go)*.

## Was it `willReturn()`, `willReturnAny()` or `willReturnExact()`?

Let's get to code. [Real open source code](https://github.com/shopsys/shopsys/commit/b73fc8da82f7d2679f05c8aedd29f010fd5d0630#diff-f1a8f90cb34e69e324153cce909467a2R92) from one of my code-reviews that inspired me to make this post:

```php
namespace PHPUnit\Framework\TestCase;

final class SomeTest extends TestCase
{
    public function test()
    {
        $heurekaCategoryFacade = $this->createHeurekaCategoryFacadeMock();

        // ...
    }

    /**
     * @return \PHPUnit\Framework\MockObject\MockObject|\Shopsys\ProductFeed\HeurekaBundle\Model\HeurekaCategory\HeurekaCategoryFacade
     */
    private function createHeurekaCategoryFacadeMock()
    {
        $returnCallback = function ($categoryId) {
            if ($categoryId === self::CATEGORY_ID_FIRST) {
                return $this->heurekaCategory;
            }
            return null;
        };

        /** @var HeurekaCategoryFacade|\PHPUnit\Framework\MockObject\MockObject $heurekaCategoryFacadeMock */
        $heurekaCategoryFacadeMock = $this->createMock(HeurekaCategoryFacade::class);

        $heurekaCategoryFacadeMock
            ->method('findByCategoryId')
            ->willReturnCallback($returnCallback);

        return $heurekaCategoryFacadeMock;
    }
}
```

The code is intentionally more complex, so we have real-life example, instead of made-up code with `Car` class and `open()` method that no-one can relate to.

**Now answer me in 5 seconds:**

- What does mock do?
- How would you extends it to work with number 7?

Now try to implement your idea. If you made it under another 60 seconds and your tests pass, you master mocking well and there is nothing for you to learn from this post.

### Documentation, Google and Stackoverflow Juggling

What happened to use in reality? **We got stuck for at least 30 minutes on modification of methods like that**. [Studying PHPUnit manual](https://phpunit.readthedocs.io/en/7.1/test-doubles.html?highlight=mocking) and looking to StackOverflow with my favorite [PHPUnit mock method multiple calls with different arguments](https://stackoverflow.com/questions/5988616/phpunit-mock-method-multiple-calls-with-different-arguments).

That's not what tool should do for you. **Tools should work for you, not you for them.**

## Pure PHP Code

Let me show an alternative approach that has the same result.

~ 95 % developers can read this code, even if they see PHPUnit for the first time:

```php
namespace PHPUnit\Framework\TestCase;

final class SomeTest extends TestCase
{
    public function test()
    {
        $heurekaCategoryFacade = $this->createHeurekaCategoryFacade();

        // ...
    }

    private function createHeurekaCategoryFacadeMock()
    {
        // anonymous class mock
        return new class extends HeurekaCategoryFacade
        {
            public function findByCategoryId($categoryId)
            {
                if ($categoryId === self::CATEGORY_ID_FIRST) {
                    return $this->heurekaCategory;
                }

                return null;
            }
        };
    }
}
```

We don't need no PHPStorm plugin, memorized methods from mock framework nor duplicated|annotations.

I believe now we all made it under 5 seconds with both answers:

```diff
 namespace PHPUnit\Framework\TestCase;

 final class SomeTest extends TestCase
 {
     public function test()
     {
         $heurekaCategoryFacade = $this->createHeurekaCategoryFacade();

         // ...
     }

     private function createHeurekaCategoryFacade()
     {
         // anonymous class mock
         return new class extends HeurekaCategoryFacade
         {
             public function findByCategoryId($categoryId)
             {
-                if ($categoryId === self::CATEGORY_ID_FIRST) {
+                if ($categoryId === self::CATEGORY_ID_FIRST || $categoryId === 7) {
                     return $this->heurekaCategory;
                 }

                 return null;
             }
         };
     }
 }
```

## Your Code Guides You, Just Be Open to Listening

The code already tells us what to do next.

Some people mock because they follow good practice and **[make every class abstract or final](https://ocramius.github.io/blog/when-to-declare-classes-final)**. They don't want to deal with constructors, that would often lead to more mocking. It's great practice and super easy to put make classes final with Rector CI:

```bash
composer require rector/rector --dev
```

```php
<?php

// rector.php

declare(strict_types=1);

use Rector\SOLID\Rector\Class_\FinalizeClassesWithoutChildrenRector;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

return static function (ContainerConfigurator $containerConfigurator): void {
    $services = $containerConfigurator->services();
    $services->set(FinalizeClassesWithoutChildrenRector::class);
};
```

Yes, it's that simple, you just saved your project from most of its legacy code. But `final` classes should **not be the reason to choose to mocks**. Well, you can also [hack the `final` and use mocking right away](https://phpfashion.com/how-to-mock-final-classes), or you can go with the code flow. Dance with it!

## SOLID Code as a Side Effect

You don't need to go on a mocking spree. The *constructor issue* naturally lead us to **abstract an interface** refactoring.

We create a new interface:

```php
interface CategoryFacadeInterface
{
    public function findByCategoryId($categoryId);
}
```

And use it in anonymous class:

```diff
 private function createHeurekaCategoryFacade()
 {
     // anonymous class mock
-    return new class extends HeurekaCategoryFacade
+    return new class implements CategoryFacadeInterface
     {
         public function findByCategoryId($categoryId)
         {
            // ...
         }
     };
 }
```

And now you respect SOLID principles - your code is:

- extendable (*O*pen-closed principle - open to extension, closed to modification)
- and replaceable (*D*ependency inversion principle).

Also, your application can now use abstraction (= interface) instead of specific implementation (= class). That leads to autowiring benefits, decoupling from monolith and better service replace-ability.

## 1000x Code

They say **your code is 10x more read than written** on average. I believe it's at least 1000x in open-source. Knowing that we want our code, not to be just clear and readable, but to be

- super-readable,
- deterministic = with only one clear way one can understand it,
- with [cognitive complexity](/blog/2018/05/21/is-your-code-readable-by-humans-cognitive-complexity-tells-you/) < 8,
- easy to fix,
- easy to extend,
- easy to test.

<br>

<img src="https://upload.wikimedia.org/wikipedia/commons/thumb/a/ab/William_of_Ockham_-_Logica_1341.jpg/220px-William_of_Ockham_-_Logica_1341.jpg">

Let's close this with [Occam's razor](https://en.wikipedia.org/wiki/Occam%27s_razor):

<blockquote class="blockquote text-center mt-5 mb-5">
    One should select the answer that makes the fewest assumptions
</blockquote>

Pick a solution that is understandable to the most people. No tool, posts or studying tutorials or reading books is needed. **People will thank you and your code will attract more people because they'll feel confident to manage the code**. Then naturally, your code will get more contributions from happy developers. Win win :)

<br><br>

Happy anonymocking!
