---
id: 87
title: "New in Coding Standard 4: Long Line Breaks Automated and 3 Legacy Prevention Fixers"
perex: |
    Legacy code prevention, lines automated and clear naming of classes in huge projects.
    That all is coming to Coding Standard 4 (still in alpha).
    <br><br>
    Are you curious what work will now these 4 news fixers handle for you? Look inside.
tweet: "New in Coding Standard 4: Long Line Breaks Automated and 3 Legacy Prevention Fixers"
tweet_image: "/assets/images/posts/2018/symplify-4-cs/tweet.png"
related_items: [86, 70, 68, 51]
---

## 1. Let Coding Standard handle Line Length for You

<a href="https://github.com/Symplify/Symplify/pull/749" class="btn btn-dark btn-sm mt-2 mb-3">
    <em class="fab fa-github"></em>
    &nbsp;
    Check the PR #749
</a>

*I'm so happy to announce this fixer, because it saved my so many times and also motivates me to use decoupling to smaller, SRP classes.*

If you use `LineLengthSniff`, you know it's painful to fix every error report it makes.

```yaml
# ecs.yml
services:
    PHP_CodeSniffer\Standards\Generic\Sniffs\Files\LineLengthSniff:
        absoluteLineLimit: 120
```

The most typical use case is constructor dependencies. You code start small:

```php
public function __construct(OneLittleDependency $oneLittleDependency)
{
}
```

Then it grows...

```php
public function __construct(OneLittleDependency $oneLittleDependency, AnotherLittleDependency $anotherLittleDependency)
{
}
```

...and grows...

```php
public function __construct(OneLittleDependency $oneLittleDependency, AnotherLittleDependency $anotherLittleDependency, $someParameter)
{
}
```

...sniff screams, so you inline it...

```php
public function __construct(
    OneLittleDependency $oneLittleDependency,
    AnotherLittleDependency $anotherLittleDependency,
    $someParameter
) {
}
```

...then you refactor and merge 2 services to 1...

```php
public function __construct(
    OneLittleDependency $oneLittleDependency,
    $someParameter
) {
}
```

...and that is inconsistent and has no reason to be inlined, so you inline it...

```php
public function __construct(OneLittleDependency $oneLittleDependency, $someParameter) {
}
```

...and that is one of many reasons people don't like to decouple classes and keep them small.

<br>

There are other cases, where parameters, arguments or array items can change up and down:

```diff
-$someObject = new SomeClass(
-    $shortArg
-);
+$someObject = new SomeClass($shortArg);
```

```diff
-$someArray = ['superlooongArgumentsover120chars', 'superlooongArgumentsover120chars', 'superlooongArgumentsover120chars'];
+$someArray = [
+    'superlooongArgumentsover120chars',
+    'superlooongArgumentsover120chars',
+    'superlooongArgumentsover120chars'
+];
```

What if I told you that you'll have to never deal with this manually. Ever!

Welcome `LineLengthFixer`.

### How to Register It?

```yaml
# ecs.yml
services:
    Symplify\CodingStandard\Fixer\LineLength\LineLengthFixer: ~
```

As you guessed, this fixer works with 120 chars as maximum line-size... by default ↓

<br>

## 2. Choose Line Length to Match Your Display

<a href="https://github.com/Symplify/Symplify/pull/747" class="btn btn-dark btn-sm mt-2 mb-3">
    <em class="fab fa-github"></em>
    &nbsp;
    Check the PR #751
</a>

Do you prefer shorter or longer lines?

Do you want use breaks only and not inline short code?

Just configure it:

```yaml
# ecs.yml
services:
    Symplify\CodingStandard\Fixer\LineLength\LineLengthFixer:
        max_line_length: 100 # default: 120
        break_long_lines: true # default: true
        inline_short_lines: false # default: true
```

<br>

## 3. Keep Legacy Far Away with New `ForbiddenStaticFunctionSniff`

<a href="https://github.com/Symplify/Symplify/pull/722" class="btn btn-dark btn-sm mt-2 mb-3">
    <em class="fab fa-github"></em>
    &nbsp;
    Check the PR #722
</a>

It started as one simple static method. A helper method. They say: it's ok to use static methods, when you know when to use them.
And that's how cancer started to spread slowly and legally through Symplify code.

One day you wake up and from 1 static method is 60 static factories all over your code - Dependency Injection for very poor. And that not the worst. When all code works and is easy to maintain, 1 static method can't hurt it, right?

**Well until you need to replace one of nested dependencies that requires few more classes. And then you realized your work is to basically manually maintain dump of dependency injection container** and that you're not coding anymore.

It took weeks to get from this position back to clear dependency injection and I don't want to do it ever again. That's why this fixer was born.

### How to Register It?

```yaml
services:
    Symplify\CodingStandard\Sniffs\CleanCode\ForbiddenStaticFunctionSniff: ~
```

<br>

## 4. Prevent & references with `ForbiddenStaticFunctionSniff`

<a href="https://github.com/Symplify/Symplify/pull/692" class="btn btn-dark btn-sm mt-2 mb-3">
    <em class="fab fa-github"></em>
    &nbsp;
    Check the PR #692
</a>

We all already know that `&$references` are bad practise, since they increase cyclomatic complexity and hide dependency logic.

```php
function someFunction(&$var)
{
    $var + 1;
}
```

And that we should prefer explicit syntax:

```php
function someFunction($var)
{
    return $var + 1;
}
```

I though I would never meet them again, but they somehow pop-up in PRs. So I made a Fixer for it!

### How to Register It?

```yaml
# ecs.yml
services:
    Symplify\CodingStandard\Sniffs\CleanCode\ForbiddenReferenceSniff: ~
```

<br>

## 5. Clear Child Class Naming Once and For All with `ClassNameSuffixByParentFixer`

<a href="https://github.com/Symplify/Symplify/pull/633" class="btn btn-dark btn-sm mt-2 mb-3">
    <em class="fab fa-github"></em>
    &nbsp;
    Check the PR #633
</a>

Often in the code of private companies there are classes like:

- `ProductSorter`
- `ProductSorter`
- `ProductSorter`

If you use PhpStorm and *open file* shortcut, you know where I aim.

Now, imagine you want to update the Command that sorts Products by price to Redis database. Inside, it looks like this:

```php
final class ProductSorter extends Command
{
    // ...
}
```

### Which one do you open?

I could also ask, which one is the interface and which is its implementation, but [there is already checker for that](https://github.com/Symplify/CodingStandard#class-should-have-suffix-by-parent-classinterface).

Probably each of them manually until you find the right one, which really sucks. That why not only methods names, **but also class names should be as descriptive and as deterministic as possible**. Like this:

```diff
-final class ProductSorter extends Command
+final class ProductSorterCommand extends Command
 {
     // ...
 }
```

And then you have clear class names, that you're able to distinguish without their content:

- `ProductSorterCommand`
- `ProductSorterRepository`
- `ProductSorterController`

And that's exactly what `ClassNameSuffixByParentFixer` helps you to do.

### How to Register It?

```yaml
# ecs.yml
services:
    Symplify\CodingStandard\Fixer\Naming\ClassNameSuffixByParentFixer: ~
```

And it handles all these cases for you:

- `*Command`
- `*Controller`
- `*Repository`
- `*Presenter`
- `*Request`
- `*Response`
- `*EventSubscriber`
- `*FixerInterface`
- `*Sniff`
- `*Exception`
- `*Handler`

Note: since PHP Coding Standard tools don't modify your filesystem, after the fixer run don't forget to change file names as well.

### Your type is missing?

The fixer is configurable to comfort your needs, so just add it:

```yaml
# ecs.yml
services:
    Symplify\CodingStandard\Fixer\Naming\ClassNameSuffixByParentFixer:
        parent_types_to_suffixes:
            '*Control': Control
```

<br><br>

Happy sniffing and fixing!