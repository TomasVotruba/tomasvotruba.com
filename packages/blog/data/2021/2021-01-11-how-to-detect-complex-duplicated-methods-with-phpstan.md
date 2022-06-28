---
id: 296
title: "How to detect Complex Duplicated Methods With PHPStan"
perex: |
    Duplicated code is a code smell that hides the potential of better design.
    How can we find it? Is it 100 % identical code token by token?
    Are methods `getName()` and `getName()` on 2 entities duplicated?
    <br>
    <br>
    Today we look at PHPStan and how to use it to find duplicated class methods.

tweet: "New Post on #php 🐘 blog: How to detect Complex Duplicated Methods With #phpstan"
---

## Why even Care about Duplicated Code?

What is the practical approach to finding a duplicated code? You probably know about [phpcpd](https://github.com/sebastianbergmann/phpcpd) tool. It goes through your code and dumps a list of duplicated lines:

```bash
- .../sodium_compat/src/Core32/Curve25519.php:879-889 (10 lines)
  .../sodium_compat/src/Core32/Curve25519.php:1072-1082

1.82% duplicated lines out of 446676 total lines of code
```

Is this number high? Is it low? How low should it be? The output is so generic. There is no clear answer to what to do with it. That's because [phpcpd is using tokens](/blog/2018/10/22/brief-history-of-tools-watching-and-changing-your-php-code/).

PHPStan changes an approach to duplicated code. It runs on php-parser and uses an abstract syntax tree. So why should we care?

### PHPStan gives Human Output, so we Know What to Do

Compare reports of 2 tools:

- phpcpd: "10 lines of here and 10 lines of here are identical"
- PHPStan: "Class methods `ProductSorter::sortResults()` and `PictureSorter::sortResults()` are identical

In the first case, we'd have to look into the files, check if it's part of the method or couple of properties, how to extract it from both, and combine it in a class or method?

With PHPStan, we know it's class methods, so we can only **extract them to external service**.

### Improve Architecture Flaws without Effort

Where in your code is duplicated code? We don't know. It's a tough question for the human to spot.

Do you look for all possible duplication during each code review? I don't think so. We go through added/modified code, and that's it.

PHPStan **does this hard work on each commit** and tells us precisely the duplicated code. 0 effort.

When we know where duplications are, we can do something about it. Our project code will be more consistent, logical, and each piece of code will fit into another.

<br>

Let's look at a practical example from the Rector code. We applied the PHPStan duplication rule a month ago, and this is what we found:

```php
private function unwrapExpression(Node $node): Node
{
    if ($node instanceof Expression) {
        return $node->expr;
    }

    return $node;
}
```

```php
private function unwrapExpression(Node $node): Node
{
    return $node instanceof Expression ? $node->expr : $node;
}
```

```php
/**
 * @param Node|Expression $node
 */
private function unwrapExpression(Node $node): Node
{
    if ($node instanceof Expression) {
        return $node->expr;
    }

    return $node;
}
```

Each method has a different count of nodes. There is short ternary in one and if condition in other 2. All three have the same logic. **We extracted this method to shared service** and used it in 3 places.

Now the method is always re-used because PHPStan reports all the future cases of duplication.

What are other benefits?

- 1 place to fix it ✅
- 1 place to maintain it ✅
- Code is much more robust and easier to change ✅
- The service is doing exactly 1 thing ✅
- Other services are not cluttered with "util code" ✅


## What is Not Duplicated Method?

These 2 methods are 100 % identical, including spaces. Would you consider them duplicated?

```php
public function getName(): string
{
    return $this->name;
}
```

```php
public function getName(): string
{
    return $this->name;
}
```

No. These are only getters. Imagine **one parent object for all entities with all the getters** that used at least twice. That's nonsense.

That's why the PHPStan rule skips:

- value objects,
- entities
- and methods with only 1 line of code.

## How can we spot the Duplicated Method with X-Rays?

Let's get back to our example above. What if we use different variables names, method names, and omit return type?

```php
public function unwrapExpression(Node $node)
{
    if ($node instanceof Expression) {
        return $node->expr;
    }

    return $node;
}
```


```php
private function unwrap($stmt)
{
    if($stmt instanceof Expression){
        return $stmt->expr;
    }
    return $stmt;
}
```

Is this method still duplicated? Yes, because **the semantics remain the same**. Method and variable names are subjective and can be anything without changing logic.

Here I borrow an example from [Eliminating Visual Debt](https://ocramius.github.io/blog/eliminating-visual-debt/) by Ocramius. If you haven't read it, it's so much fun you're missing out.

Let's replace these irrelevant names with foo/bar:

```php
function foo($bar)
{
    if ($bar instanceof Expression) {
        return $bar->expr;
    }
    return $bar;
}
```

When we look at both methods with this approach, we see the logic is identical → let's extract it.

## How to Teach it PHPStan?

Samsonasik contributed this [rule to Symplify](https://github.com/symplify/symplify/pull/2666) about a month ago. We took a month to try it out, fixed repeated methods from trait, and class using it.

We tried various angles that we tested on 4 different projects.

In the end, the final rule works like this:

- look for a class method
- normalized variable names
- ignore parameters, method name, and return types
- print method to string
- compare it to previous methods

You can see [final rule here](https://github.com/symplify/symplify/blob/master/packages/phpstan-rules/src/Rules/PreventDuplicateClassMethodRule.php).

This rule has been running on our code base and already reported over 50 duplicated methods. It's a bizarre feeling if you see that you put in the effort to write 20 lines long method in 2 different Rector rules. But **the feeling after refactoring to cleaner design is priceless**.

## 3 Steps to Get rid of Duplications in Your Code Today

Do you want to try it out? I must warn you. It will dig out the darkest secrets of your project that you never thought they exist. Be ready for critics. Critics that will help your code to be more fun work with.

### 1. Get [symplify/phpstan-rules](https://github.com/symplify/phpstan-rules):

```bash
composer require symplify/phpstan-rules --dev
```

### 2. Add it to `phpstan.neon`

```yaml
includes:
    # set services that are needed by PHPStan rules
    - vendor/symplify/phpstan-rules/config/services/services.neon

rules:
    -  Symplify\PHPStanRules\Rules\PreventDuplicateClassMethodRule
```

### 3. Run PHPStan

```bash
vendor/bin/phpstan analyse
```

Now you won't miss any single duplicated method in your code.

<br>
<br>

Happy coding!
