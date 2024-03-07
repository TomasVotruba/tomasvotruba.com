---
id: 405
title: "Finalize Classes - Automated and Safe"
perex: |
    Final classes have [many](https://ocramius.github.io/blog/when-to-declare-classes-final/) [great](https://tomasvotruba.com/blog/2019/01/24/how-to-kill-parents) [benefits](https://matthiasnoback.nl/2018/09/final-classes-by-default-why/) for future human readers of your code.

    They have even more benefits for static analysis and Rector rules.

    But what if we have a **project with 1000+ classes, 10 minutes of time** and want to automate the finalize process in safe way?
---

Why are `final` classes so valuable for automated tools? Let's see this code:

```php
class Conference
{
    protected $name;

    public function __construct(string $name)
    {
        $this->name = $name;
    }

    // ...
}
```

Static analysis and [Rector](https://getrector.com/) could do so much here... but they're not sure:

* What if this class is extended?
* What if child classes use the `$name` property?
* What if child class overrides the constructor and changes the type?

<br>

Let's see what Rector can do, if we add a `final` keyword:

```diff
-final class Conference
+final readonly class Conference
 {
-    protected $name;
-
-     public function __construct(string $name)
+     public function __construct(private string $name)
-     {
-        $this->name = $name;
-    }

     // ...
 }
```

We've just shifted from PHP 7.0-like code to PHP 8.2-like.

<br>

## Need for Automation

In case of huge project with many classes, we would not get much work done manually. That's why we need automation to handle this for us.

Rector to the rescue? There was once a rule in Rector called `FinalizeClassesWithoutChildrenRector`. It was quite helpful, but also did [many false positive changes](https://github.com/rectorphp/rector/issues/8439), so I deprecated it.

<br>

What now?

## First Principles

We removed a buggy Rector rule, but that doesn't help new projects to implement `final` fast and safe. What exactly are the minimal goal we want to achieve?

<br>

We need a tool that can:

### 1. Find classes that are Parents

```php
class SomeClass extends ParentClassThatCannotBeFinal
{
}
```

### 2. Find all Doctrine Entities

Defined in attribute:

```php
use Doctrine\ORM\Mapping\Entity;

#[Entity]
class ThisIsEntity
{
}
```

...docblock:

```php
use Doctrine\ORM\Mapping\Entity;

/**
 * @Entity
 */
class ThisIsAlsoEntity
{
}
```

...but also in YAML mapping configs:

```php
class ThisIsAlsoEntityWithMappingInYAML
{
}
```

### 3. Find classes that Mocked

Those are extended by mocking framework, so they have to skipped.

```php
namespace PHPUnit\Framework\TestCase;

final class SomeTest extends TestCase
{
    public function test()
    {
        $someMock = $this->createMock(SomeRepository::class);
    }
}
```

This is optional, as we [separate tests and source code](/blog/2019/03/28/how-to-mock-final-classes-in-phpunit).

### 4. Handle this in Static Way

Imagine having this amazing tool with all features mentioned above, but when you run `composer require` it will conflict on Symfony 7 vs Symfony 5 in your project.

Last but not least, we want to make the tool available the most PHP devs. We have to:

* be open about **PHP versions - PHP 7.2+**
* and have **no required dependencies**.

<br>

## Building The Solution

What needs to be done? When we check the first principals, it seems quit easy:

* take finder component,
* find PHP and YAML files in the project,
* do abstract syntax tree traversing with nikic/php-parser
* excluded bunch classes and add `final` to the rest

*Note: If you're new to AST, check this super fun and practical talk by Marcel Pociot about [Parsing PHP for fun and profit](https://www.youtube.com/watch?v=3gqPJvY8d30).*

<br>

## Introducing Swiss Knife toolkit

It took about 3 days to build first prototype. Then 2 more months of internal testing on real projects, and improve with feedback. Today I'm proud to share it with the public.

We use this technique in every project [we help upgrade](https://getrector.com/hire-team), so we called it accordingly - a *swiss knife*.


### 1. Install package

```bash
composer require rector/swiss-knife --dev
```

### 2. Run Command

```bash
vendor/bin/swiss-knife finalize-classes app tests --dry-run
```

On first run, use `--dry-run` just to be safe.

Does all seems good to you? Let's roll:

```bash
vendor/bin/swiss-knife finalize-classes app tests
```

### 3. Skip Mocked classes

Its better to separate tests leaking requirements to your source code and use [bypass final](/blog/2019/03/28/how-to-mock-final-classes-in-phpunit). But maybe you don't want to deal with it right now.

That's why we have `--skip-mocked` options, that keeps all mocked classes without `final`:

```bash
vendor/bin/swiss-knife finalize-classes app tests --skip-mocked
```

That's it!

<br>

*Protip: add the `--dry-run` to your CI to spot these classes early and delegate work to your CI.*

<br>

Is there a spot that was finalized incorrectly? Let us [know in the issues](https://github.com/rectorphp/swiss-knife/issues), we'll cover it.

<br>


Happy coding!
