---
id: 299
title: "4 PHPStan Rules that Bring Order to Nette Injects"
perex: |
    Do you have at least one `@inject` property or `inject*()` method in your Nette project? If no, stop reading and have fun with another post.

    If you do, you probably have some internal rules where what and how to use them and where to avoid them. But how do you keep order?

tweet: "New Post on #php 🐘 blog: 4 PHPStan Rules that Bring Order to #nettefw Injects"
---

If you talk about your project's standards that everyone *should* follow, remember the old lazy programmer's saying:

<blockquote class="blockquote text-center">
    "In CI,<br>
    or won't happen."
</blockquote>


## Injection Everywhere?

Using injection makes sense in some cases, like avoiding circular dependencies. Another use case is an abstract class with dozens of child classes, but mostly it's [a killer for a healthy dependency tree](/blog/2020/06/01/inject-or-required-will-get-you-any-service-fast/).

In a one [Nette](https://nette.org/) project I worked on within the last five years, they had a very "cool" DI extension. A DI extension that enables `@inject` everywhere:

```php
final class SomeClass
{
    /**
     * @var EventDispatcher
     * @inject
     */
    public $eventDispatcher;
}
```

Why needs `__construct()`. right? Get PHP 8 promoted properties today.

<blockquote class="blockquote text-center">
    "If it's not forbidden,<br>
    it's allowed."
</blockquote>

Luckily, nowadays, we have a [Symplify PHPStan rules](https://github.com/symplify/phpstan-rules) to help us.

## 1. Do Not Combine Nette Inject and `__construct()`

If there is a constructor already, there an exact working way to get dependencies:

```php
class SomeClass
{
    private $someType;

    private $anotherType;

    public function __construct(AnotherType $anotherType)
    {
        $this->anotherType = $anotherType;
        // ...
    }

    public function injectSomeType(SomeType $someType)
    {
        $this->someType = $someType;
    }
}
```

❌

<br>


It's better to use far cleaner `__construct()` inection:

```php
class SomeClass
{
    private $someType;

    private $anotherType;

    public function __construct(AnotherType $anotherType, SomeType $someType)
    {
        $this->anotherType = $anotherType;
        $this->someType = $someType;
    }
}
```

✅

Covered by `Symplify\PHPStanRules\Rules\NoNetteInjectAndConstructorRule` rule.


<br>

## 2. Use Single Nette `inject*()` Method

Using the `inject*()` method is an equal alternative to `@inject` property that does not require a property to be public.
It like an extra `__construct()` that is caller by the framework to set other dependencies.

Single `__construct()` has its meaning. Imagine this use case:

```php
class SomeClass
{
    private $type;

    private $anotherType;

    public function injectOne(SameType $type)
    {
        $this->type = $type;
    }

    public function injectTwo(SameType $anotherType)
    {
        $this->anotherType = $anotherType;
    }
}
```

❌

Having multiple `inject*()` methods allow us to **accidentally put two dependencies** in the same class. This has performance hit, maintenance hit, and duplicated parasitic code that needs our attention for no gain.

<br>

We can solve all this with a single `inject*()` method per class:

```php
class SomeClass
{
    private $someType;

    public function injectSomeClass(SomeType $someType)
    {
        $this->someType = $someType;
    }
}
```

✅

Covered by `Symplify\PHPStanRules\Rules\SingleNetteInjectMethodRule` rule.

<br>

## 3. Validate `@inject` Format

I'm proud to admit I made this bug last week. With IDE autocomplete, class annotations, and PHP 8 attributes, I'm removing my skill to type precisely the word:

```php
class SomeClass
{
    /**
     * @injects
     * @var SomeDependency
     */
    public  $someDependency;
}
```

...and the property was `null`.

❌

<br>

Let's not do that ever again and validate **letter by letter** of `@inject` annotation:

```diff
 class SomeClass
 {
     /**
-     * @injects
+     * @inject
      * @var SomeDependency
      */
     public $someDependency;
}
```

✅

Covered by `Symplify\PHPStanRules\Rules\ValidNetteInjectRule
` rule.


## 4. No `@inject` on `final` Class

Last but not least, this rule finally put an order to our codebase.

A specific need for injects is an abstract class with a couple of children:

```php
abstract class AbstractPresenter
{
}

final class ProductPresenter extends AbstractPresenter
{
}
```

**Where would you allow injects?**

- in both classes
- in children classes only
- in abstract class only

<br>

What would be the consequences:

- **In both classes?** It's a mess that leads to injection hell. Would you get vaccinated against covid every week? No, so don't force your code to suffer either.
- **In children classes only?** Putting responsibility on every single child can be quite a challenge.
- **In `abstract` class only?** Well, a single parent with five children is quite a responsibility, but it's clear and one place to go in case of problems.

The ideal options is 3). Using inject only abstract classes make children cleaner and less coupled to the framework. There are more children classes than abstract, so code base quality will be much higher if children classes are strict and clean.

```php
final class ProductPresenter extends AbstractPresenter
{
    /**
     * @inject
     * @var AnotherDependency
     */
    public $anotherDependency;
}
```

✅

Covered by `Symplify\PHPStanRules\Rules\NoInjectOnFinalRule` rule.

<br>

That's it for today. Try the rule one by one and run PHPStan to see how it helps your project:

```bash
composer require symplify/phpstan-rules --dev
```


<br>


Happy coding!
