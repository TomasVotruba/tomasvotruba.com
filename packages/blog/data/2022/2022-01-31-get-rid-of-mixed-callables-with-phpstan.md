---
id: 350
title: "Get Rid of Mixed Callables with PHPStan"
perex: |
    Working with PHPStan on level 8 is a luxury, at least for a project where you've just introduced it. To give you a practical non-open-source perspective: **we're at level 5 after 2 years of hard work**.
    <br><br>
    In this level, we've noticed that PHPStan skips expression on variables that it resolved as `mixed`. Level 5 already provides valuable checks that we wanted for all **method calls and property fetches**. So [we added custom rule to report those](/blog/not-all-mixed-types-are-equally-useless).
    <br><br>
    After we cut the low-hanging fruit, we discovered the same way are skipped all *callable* types. How to deal with those?

tweet: "New Post on the 🐘 blog: Get rid of Mixed Callables with PHPStan"
tweet_image: "/assets/images/posts/2022/mixed_callable_rule.png"
---

<div class="card border-warning mt-4 mb-4">
    <div class="card-header text-black bg-warning shadow">
        <strong>Proof over theory?</strong>
        Thanks to this PHPStan rule, the Symplify <a href="https://github.com/symplify/symplify/commit/61e8bf67d52f0759d2efb688728a4ebd2c72b64b">improved 8 callables</a> from <code>mixed</code> to param and return typed.
    </div>
</div>

## `mixed` !== `mixed`

Dealing with mixed is so hard. That's why it's included in the highest level of PHPStan - level 9.

Let's look at quite a typical code snippet. How would you this detect type?

```php
final class TripController
{
    public function showFlights($destination)
    {
        // what exactly is $destination?
        echo $destination;
    }
}
```

This can be tricky with scalar variables and entry points like controller or API calls.
To be 100 % sure, we have to run the code [collect production types with dynamic analysis](/blog/2019/11/11/from-0-doc-types-to-full-type-declaration-with-dynamic-analysis), verify the user input, etc.

<br>

We won't deal with those now. Instead, we take the low hanging fruit, like method calls or property fetches:

```php
public function fetchFlights($destination): array
{
    $destination->getCovidRestrictions();
}
```

For the human eye, it's obvious how to type this parameter. But how to detect these with PHPStan? [See the previous post for unlocking the secret](/blog/not-all-mixed-types-are-equally-useless).

But today, we'll look at something different.

## From Typed Method to a Callable

Try to follow this analogy with class structure. We have a class with 1 method, with clearly defined types:

```php
final class CovidRestrictionResolver implements RestrictionResolverInterface
{
    public function resolve(Destination $destination): Restrictions
    {
        // ...
    }
}
```

<br>

Now we go one level lower, from class method to a function. We still have types and know what comes there:

```php
function resolve(Destination $destination): Restrictions
{
    // ...
}
```

<br>

Let's say we need to use callable for JSON input/output, parallel run, or because we love functional programming.

```php
$restrictionResolver = function (Destination $destination): Restrictions
{
    // ...
};

echo $restrictionResolver(new Destination('Portugal'));
```

<br>

Do you see what happened with `$restrictionResolver`? From the typed class method, nested in beautiful final class with the interface we got to... `mixed`.

We can use any of the following lines, and static analysis would silently pass:

```php
echo $restrictionResolver('Portugal');
echo $restrictionResolver(new Destination('Portugal'));
echo $restrictionResolver(776);
```

## How to Type a Callable?

What if we love defensive programming? We promote the closure back to the class method with type declarations and use the `RestrictionResolverInterface` interface to type it in place of use:

```php
/** @var RestrictionResolverInterface $restrictionResolver */
$restrictionResolver->resolve(...);
```

Here the PHPStan knows everything and has our back <p class="text-success pt-3 pb-3">✅</p>

**But how do we get the same luxury with callables?** We can use docblock to define the types explicitly. We just move class method declaration from the very start to a `@var` format:

```php
/** @var callable(Destination $destination): Restrictions $restrictionResolver */
$restrictionResolver(...);
```

We have defined parameter types and a return type.

<br>

You can read more [in PHPStan docs](https://phpstan.org/writing-php-code/phpdoc-types#callables):

<img src="/assets/images/posts/2022/mixed_callable_rule.png" class="img-thumbnail" style="max-width: 35em">

It's almost identical to PHP type declaration syntax, except union type has to be wrapped in brackets `()`:

<br>

Now we know how to type callables in our code. Just image it's a class method and write the types:

```php
/** @var callable(<paramNamesWithTypes>): <returnType> $<variableName> */
```

In our case:

```php
/** @var callable(Destination $destination): Restrictions $restrictionResolver */
```

<br>

But how do we detect this in CI before merging them in our code base?

## Detect Mixed Callables in 2 Steps

1. Add new rule from [`symplify/phpstan-rules`](https://github.com/symplify/phpstan-rules):

```yaml
# phpstan.neon
services:
    -
        class: Symplify\PHPStanRules\Rules\Explicit\NoMixedCallableRule
        tags: [phpstan.rules.rule]
```

Update: Ondra shared with me, that you can also [use parameter](https://twitter.com/OndrejMirtes/status/1495064465339039751) for similar check outside levels:

```yaml
parameters:
    checkMissingCallableSignature: true
```

2. Run PHPStan:

```bash
vendor/bin/phpstan
```

That's it!

<br>

To make the type as strict as possible, we always have to type all the places with callable:

* the assigned property with `@var`,
* the parameter with `@param`,
* the return value with `@return`,
* and the inlined variables with `@var ... $variableName`.

Then your PHPStan will see much more than before, making your code **even safer to work with**!

<br>

Happy coding!

