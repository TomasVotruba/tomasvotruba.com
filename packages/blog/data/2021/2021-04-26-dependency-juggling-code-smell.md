---
id: 313
title: "Dependency Juggling Code Smell"
perex: |
    The best way to pass dependencies is via constructor injection. Only in services, we need the dependency in. I've noticed that **sometimes the dependency is passed way too early**, just to be passed to another service as a method argument.
    <br><br>
    Why is it a code smell, and how can we avoid it?

tweet: "New Post on #php 🐘 blog: Dependency Juggling Code Smell"
---

Because constructor injection has good karma, we automatically assume that everything in `__construct(...)` is correct. That's why we can easily miss it.

Imagine this code in your project:

```php
final class TypeResolver
{
    public function __construct(
        // using PHP 8 property promotion here
        private StringTypeResolver $stringTypeResolver,
        private ConstantStringTypeResolver $constantStringTypeResolver,
    ) {
    }

    public function resolve(Expr $expr): Type
    {
        return $this->stringTypeResolver->resolveFromExpr(
            $expr,
            $this->constantStringTypeResolver,
        );
    }
}
```

Can you see the smell in here? We have 2 services passed as a dependency in the constructor:

- `StringTypeResolver`
- `ConstantStringTypeResolver`

Constructor states what dependencies this class needs to work.

<br>

How many of them the **class directly uses**? Just the `StringTypeResolver` one.

The other one `ConstantStringTypeResolver` is juggled to `StringTypeResolver` as side dependency.

## 2 Pair of Keys

<img src="https://user-images.githubusercontent.com/924196/116310231-03810c80-a7aa-11eb-8053-508de8ba8149.jpg" class="img-thumbnail">

It's like your own keys from your home with one extra pair. You take them both with you everywhere. You use the first pair to unlock doors from your home. The extra pair you use exclusively when you open your door for your wife.

Why would you have these extra keys for your own? Instead, **give them to your wife and let her handle it**.

<blockquote class="blockquote pt-3 pb-3 text-center">
Each service should take care only of its own dependencies.
<br>
not take responsibility for others.
</blockquote>

If we apply this approach to our code, we can move the `ConstantStringTypeResolver` where it belongs:

```diff
 final class TypeResolver
 {
     public function __construct(
        private StringTypeResolver $stringTypeResolver,
-       private ConstantStringTypeResolver $constantStringTypeResolver,
     ) {
     }

     public function resolve(Expr $expr): Type
     {
         return $this->stringTypeResolver->resolveFromExpr(
             $expr,
-            $this->constantStringTypeResolver,
         );
     }
 }
```

The `StringTypeResolver` now handles its dependencies on its own:

```diff
 final class StringTypeResolver
 {
+    public function __construct()
+    {
+        private ConstantStringTypeResolver $constantStringTypeResolver,
+    }

     public function resolveFromExpr(
        Expr $expr,
-       ConstantStringTypeResolver $constantStringTypeResolver,
     ) {
-        $constantStringTypeResolver->resolve(...);
+        $this->constantStringTypeResolver->resolve(...);
         // ...
     }
 }
```

<em class="fas fa-2x fa-thumbs-up text-warning"></em>

<br>

## PHPStan Got Your Back

The tricky part is to watch for these cases not to get into your code. It's easy to take 2 key sets instead of 1 when you're in a rush to catch a tram or taxi.

[Symplify PHPStan Rules](https://github.com/symplify/phpstan-rules) got you covered. Just install it:

```bash
composer require symplify/phpstan-rules --dev
```

Then add the rule to your `phpstan.neon` config:

```yaml
services:
     -
        class: Symplify\PHPStanRules\Rules\NoDependencyJugglingRule
        tags: [phpstan.rules.rule]
```

The rule looks for constructor dependencies that are juggled to method calls. Don't worry about it, and let your CI handle it.
That's all for today.

<br>

Happy coding!
