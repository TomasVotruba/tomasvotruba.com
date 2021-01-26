---
id: 297
title: "PHPStan Abstract Parent Generics for Dummies"
perex: |
    I'm trying to write code that is independent of abstract classes. Type-juggling can create n-matrix complexity in both directions and remind me of stringy static code where everything is one type - `mixed`.
    <br><br>
    But we cannot always avoid it. Do you use repositories with one abstract repository? Projects [I upgrade](https://getrector.org/) do.
    <br><br>
    So I tried to use [PHPStan generics](https://phpstan.org/blog/generics-in-php-using-phpdocs) and failed hard.

tweet: "New Post on #php 🐘 blog: #phpstan Abstract Parent Generics for Dummies"
---

<blockquote class="blockquote text-center">
    "If you can't explain it to a six-year-old,<br>
    you don't understand it yourself."
    <footer class="blockquote-footer">Albert Einstein</footer>
</blockquote>

The documentation [barely scratches this topic](https://phpstan.org/blog/generics-in-php-using-phpdocs) and examples are not very clear. Neither were responses on GitHub issues.

<br>

What is the use case? We have two classes - an abstract repository:

```php
abstract class AbstractRepository
{
    public function get(int $id): object
    {
        // ...
    }
}
```

and children classes that extend it:

```php
final class ProductRepository extends AbstractRepository
{
}
```

<br>

In the controller, we use `ProductRepository` to display product detail:

```php
final class ProductDetailController
{
    public function __construct(
        private ProductRepository $productRepository
    ) {
    }

    public function __invoke(int $id)
    {
        $product = $this->productRepository->get($id);
    }
}
```

Pretty straightforward, right?

Now we'll try to solve the hard question...

## What Type is `$product`?

```php
$product = $this->productRepository->get($id);
```

The class method `AbstractRepository->get()` returns and `object`, so for PHP, PHPStan, Rector and IDE it's an `object`. Nothing less, nothing more.

But what **do you see**? You probably assume it's a specific `object` with a specific type - `Product`. How can we get this knowledge to the code?

<br>

1. We could add a method to `ProductRepository`

```diff
 final class ProductRepository extends AbstractRepository
 {
+    public function get(int $id): Product
+    {
+        return parent::get($id);
+    }
 }
```

2. We could add magic `@method` docblock

```diff
+/**
+ * @method Product get(int $id)
+ */
 final class ProductRepository extends AbstractRepository
 {
 }
```

3. We could add `@var` annotation to every `$product` variable

```diff
+/** @var Product $product */
 $product = $this->productRepository->get($id);
```

4. We could add `assert()` to `$product` variables

```diff
 $product = $this->productRepository->get($id);
+assert($product instanceof Product);
```

5. We could add `instanceof` check

```diff
 $product = $this->productRepository->get($id);
+if (! $product instanceof Product) {
+    throw new ShouldNotHappenException('$product is not a ' . Product:class);
+}
```

We can see these solutions out in the wild. All of them are valid. They verify an assumption - is object a `Product` type?

But **validation is not designed for type specification**. It's a layer to verify external input from QA when they walk in a bar order beer, `-5`, `$^@'ł`, an array of `T_INF`, and a bottomless glass of invisible water.

Here **we know** the `$product` is always the `Product` type unless some very nasty bug will get into Doctrine.
How can we teach our PHP code this knowledge?

## Generics!

Adding generics to 2 classes is 2 step process. Surprise, right?

### Open Parent

**First**, we need to tell the abstract class to be opened to type override from children. By convention, the generics are not defined with the `@generics` keyword but by `@template`. It has nothing to do with rendering templates.

With `@template` we define a keyword and a type. Same as `@param int $age` does.

```diff
+/**
+ * @template TEntity as object
+ */
 abstract class AbstractRepository
 {
 }
```

Now we use this `TEntity` keyword in places where we know the specific type will be used:

```diff
 abstract class AbstractRepository
 {
+    /**
+     * @return TEntity
+     */
     public function get(int $id): object
     {
         // ...
     }
 }
```

Good job, we're halfway through.

### Specify Child

So how do we tell `ProductRepository` to treat every object as `Product`?

```diff
+/**
+ * @template TEntity as Product
+ */
 final class ProductRepository extends AbstractRepository
 {
 }
```

That should be it, right? Let's run our controller and try it:

```php
$product = $this->productRepository->get($id);
// "object"
```

Damn, what's going on?

I stuck on this part and could not go on. We defined `TEntity` in both of our classes. We defined `TEntity` as `Product`. What is wrong?

## How to promote the `TEntity` type to Parent Class?

The problem is, PHPStan sees **generic types only in the classes they're defined** by default.
While `@return int` gets promoted to child methods, `@template` does not.

- So `TEntity` is `Product` only in class we defined it in - `ProductRepository`
- And `TEntity` is only an `object` in class we defined it in - `AbstractRepository`

So we need to tell `AbstractRepository` to use `TEntity` as `Product`, without modifying it. That's a headache task, right?

<br>

In the documentation, there is mentioned `@extends` annotation. It is not related to extending any class, **but instead promoting types to parent class**. Probably another generics convention, confusing to a newbie like me.

Let's get back to our repository:

```php
 /**
  * @template TEntity as Product
  */
 final class ProductRepository extends AbstractRepository
 {
 }
```

The `@extends` annotation takes an argument of 1-n item names. These items will be pushed parent repository.

How do we tell the parent class to use `TEntity` as `Product`?

```diff
 /**
  * @template TEntity as Product
+ * @extends AbstractRepository<TEntity>
  */
 final class ProductRepository extends AbstractRepository
 {
 }
```


Now the `TEntity` in `AbstractRepository` will be overridden by type defined here. So `TEntity` will be treated as `Product` <em class="fas fa-fw fa-lg fa-check text-success"></em>

Beware! Even though it looks like any `array<shape>`, it has nothing to do with arrays.

<br>

It can take multiple arguments, separated by comma:

```diff
/**
 * @template TEntity as Product
+* @template TQuery as ProductQuery
-* @extends AbstractRepository<TEntity>
+* @extends AbstractRepository<TEntity, TQuery>
 */
 final class ProductRepository extends AbstractRepository
 {
 }
```

And that's how you use generics with parent abstract class with PHPStan.

<br>

It's my first generic time, so I bet this setup is not the best. Do you have a tip to improve? Share it. I'd love to learn a better way.

<br>

Happy coding!
