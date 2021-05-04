---
id: 314
title: "PHPStan Generics for Dummies - With&nbsp;2&nbsp;Parents"
perex: |
    In previous post we talked about how to [promote generics to parent class](/blog/2021/01/04/phpstan-abstract-parent-generics-dummies/).
    There we learned how to tell parent class what generic type we use (typically from `ProductRepository` and `AbstractRepository`).
    <br>
    <br>
    Today we take the same use case and **add one more parent class**.
    <br>
    <br>
    That must be easy, right?

tweet: "New Post on #php 🐘 blog: #phpstan Generics for Dummies - With 2 Parents"
---

Let's start where we ended last time:

```php
/**
 * @extends AbstractRepository<Product>
 */
final class ProductRepository extends AbstractRepository
{
}
```

With an abstract parent:

```php
/**
 * @template TEntity as object
 */
abstract class AbstractRepository
{
    /**
     * @return TEntity
     */
    public function get($id)
    {
        // ...
    }
}
```

<em class="fas fa-fw fa-2x fa-check text-success mb-3"></em>

PHPStan now knows exact returned type of all method annotated with `TEntity`:

```php
/** @var ProductRepository $productRepository */
$product = $productRepository->get(1);
```

Here we always have a `Product` type.

<br>

## Constraints Have Changed

Everything works well, our project grows, and more and more users are visiting our website. Life is good.

Just the response time is lagging more and more. How could we improve it? One way is to **cache the most visited entities**.

Alright! We'll add a new repository class with cache:

```php
abstract class AbstractCachedRepository extends AbstractRepository
{
    // cache everywhere
}
```

And one parent switch:

```diff
 /**
  * @extends AbstractRepository<Product>
  */
-final class ProductRepository extends AbstractRepository
+final class ProductRepository extends AbstractCachedRepository
 {
     // ...
 }
```

Our class structure changes like this:

```diff
- ProductRepository -> AbstractRepository
+ ProductRepository -> AbstractCachedRepository -> AbstractRepository
```

The cache is working, and lagging is gone... we run PHPStan, and... it crashes. **Why?** Because the class in `@extends class` is no longer there:

```php
/**
 * @extends AbstractRepository<Product>
 */
final class ProductRepository extends AbstractCachedRepository
{
}
```

The `ProductRepository` class does not see its grand-parent class `AbstractRepository`, only its direct parent.
**We'll fix that:**

```diff
 /**
- * @extends AbstractRepository<Product>
+ * @extends AbstractCachedRepository<Product>
  */
 final class ProductRepository extends AbstractCachedRepository
 {
 }
```

Well done! Now we rerun PHPStan to see the result... and it crashes. **Why?**

## 2 Steps to Man in the Middle Class

`AbstractCachedRepository` is a middle man. **Its job is to take type from the child class and send it to the parent class.** But it's just a bare class with no annotations.

<br>

It has no idea what to do. We have to tell it to...

**1. pick the type from child class**

```diff
+ /**
+  * @template TEntity of object
+  */
  abstract class AbstractCachedRepository extends AbstractRepository
  {
  }
```

**2. send it to parent class via `@template-extends`**

```diff
  /**
   * @template TEntity of object
+  * @template-extends AbstractRepository<TEntity>
   */
  abstract class AbstractCachedRepository extends AbstractRepository
  {
  }
```

We run PHPStan, and... it works!

<em class="fas fa-fw fa-2x fa-check text-success mb-3"></em>

<br>

## Let PHPStan Watch Your Back

Is your PHPStan passing **even without generic definition in child class**? You need to enable it first.

There are 2 ways. One of them is level 6+:

```yaml
parameters:
    level: 6
```

Are you stuck on a lower level? There is **single parameter** you can enable instead:

```yaml
parameters:
    level: 4
    checkGenericClassInNonGenericObjectType: true
```

Then run PHPStan and **complete generic types with confidence**.

<br>

Happy coding!
