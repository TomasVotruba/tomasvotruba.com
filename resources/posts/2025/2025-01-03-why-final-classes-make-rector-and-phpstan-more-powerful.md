---
id: 422
title: "Why Final Classes make Rector and PHPStan more powerful"
perex: |
    Final classes bring much more value than `extends` it lacks. It teaches composition over inheritance, makes upgrades easier, and [even mocking](/blog/2019/03/28/how-to-mock-final-classes-in-phpunit) is fine.

    If you're lazy like me, you can [automate the `final` keyword addition](/blog/finalize-classes-automated-and-safe) to your code - quickly, safely, and check it the CI!
---

If you wonder "why final" from a technical and architecture point of view, check [this amazing writeup](https://matthiasnoback.nl/2018/09/final-classes-by-default-why/) by Matthias Noback.

If you care about the real value in the code right now and use Rector and PHPStan, I'll share a little secret with you - `final` takes Rector and PHPStan instantly to the next level. How?

<br>

We'll look at **3 examples** and how these tools see the code:

```php
class GptModel
{
    protected function getName()
    {
        return '5o';
    }

    public function getContextWindow()
    {
        return 500_000;
    }
}
```


What are **Rector and PHPStan** thinking?

"It's a class that may have children"

<br>

"There is a protected `getName()` method, it seems unused, but maybe the child class uses it"

If we remove this method, we **might break** child class that depends on it or override it. There is no parent class, so that direction is safe.

<br>

"There is public `getContextWindow()` method, and it returns a scalar `int` value, **but maybe** child overrides it"

**We could** add an `int` return type declaration, **but that would break** child classes—simply because they have to be compatible with parent type declarations.


<br>

## Uncertainty

"could, would, but" - the general mood is anxious. We could improve the code, but we're not sure if we won't break something. Better not touch it, right?

<blockquote class="blockquote mt-5 mb-5 text-center">
"The road to legacy codebase<br>
is paved with good intentions"
</blockquote>

This approach is typical for all legacy codebases. **Something could be improved and it would instantly bring value, but we're afraid to take the leap**. In 5 years, someone else will deal with the mess - it will only take 10x more time and money.

<br>


## Proof over Promise

Now, let's see what happens if we add a single keyword:

```diff
-class GptModel
+final readonly class GptModel
 {
-    protected function getName()
-    {
-        return '5o';
-    }

-    public function getContextWindow()
+    public function getContextWindow(): int
     {
         return 500_000;
     }
 }
```

What are **Rector and PHPStan** thinking now?


"Look, this class is what you see is the way you get - we are sure nobody changes it"

<br>

"The `getName()` method is not used anywhere"

PHPStan spots the `getName()`, which is actually `private` in the context of `final` classes. Rector will turn it into `private`... and then, they can see it's never used. **Rector will remove it safely**.

<br>

"The `getContextWindow()` method returns `int`"

This method is never changed by the child class, so we can add **type declaration `int` safely**. Rector will do that for us.

<br>

As a bonus, we see that **values are never changed - this class is `readonly`**.

<br>

## What does the `final` keyword bring to your project?

* fake `protected` are turned into `private`
* unused `private` methods are removed
* add known return type declarations
* add `readonly`
* add known param type declarations on `protected`/`private` called methods
* spots [unused classes](https://github.com/tomasVotruba/class-leak) easily
* and more...

<br>

## How does your codebase react to `final`

Adding `final` doesn't have to be pain and BC breaks all over the codebase.

We at Rector made a [simple tool](https://github.com/rectorphp/swiss-knife/#4-finalize-classes-without-children) that adds `final` to classes:

* without children,
* without entities
* and without mocked classes.

<br>

Try an experiment to see for yourself:

1. make your classes final - store to separate commit
2. run PHPStan and see report
3. run Rector and see the changes

<br>

Include PHPStan fixes and Rector changes and you're done - pure value.

You can revert step 1 to keep the codebase as it was.

<br>

## `Final` by default to unlock productivity

We add `final` to all classes in private projects and the value only compounds.

**Developer teams get more self-confident, more productive, and more relaxed**. It's safe to make huge refactoring and last but not least, Rector and PHPStan have our back.

<br>

Happy coding!
