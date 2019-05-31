---
id: 211
title: "Hidden Gems of PHP Packages: Psalm Fixing Your Code"
perex: |
    Psalm is a static analyzer of PHP code originated at Vimeo and developed by [Muglug](http://github.com/muglug). It can analyze your code for incorrect type declarations or unused code.
    <br><br>
    But did you know it can automatically fix these issues?
tweet: "New Post on #php 🐘 blog: Hidden Gems of PHP Packages: @psalmphp Fixing Your Code"
---

I did not and I was surprised. Psalm added this feature long time ago in March 2018 (as [reported on Reddit](https://www.reddit.com/r/PHP/comments/84xrgy/fixing_code_that_aint_broken_vimeo_engineering/))! I was also surprised because static analyzer is read-only tools - "here is error → fix it yourself manually"

## Static Analyzers Evolving to Code Fixers

**But it's not an unexpected development.** As I wrote in *[Brief History of Tools Watching and Changing Your PHP Code](/blog/2018/10/22/brief-history-of-tools-watching-and-changing-your-php-code/)*, coding standard tools were *read-only* too and you had to change all the spaces manually. That was so annoying with large code-bases and many programmers didn't adopt it because it added the extra work on a daily basis, instead of saving time.

In response to this pressure, they **had to become more useful by working for the programmer**. PHP CS Fixer fixed code from the very first commit and PHP_CodeSniffer added this feature in response.

We can assume PHPStan and Phan will follow Psalm path since human-fixing is hard to scale and will become annoying in time compared to automated instant upgrades.

<br>

So what Psalm can do for you?

## 1. Missing Type Declaration?

Do you know that feeling when PHPStan reports "Method X is returning an int, but should be a string" in 10 000 places? Yes, you can use [Baseliner](/blog/2019/04/22/hidden-gems-of-php-packages-srab/) to ignore them and check only new code, **but that only postpones the problem**. One day there still will be 3-4 days of full-time boring work ahead of you.

```php
/**
 * @return int
 */
function foo()
{
  return 'hello';
}
```

That's what Psalm fixes for you and even adds the return type declaration:

```diff
-/**
- * @return int
- */
-function foo()
+function foo(): string
 {
   return 'hello';
 }
```

### How to Run it?

Just use binary with `--alter` (that says "fix" this) + the `--issues` option:

```bash
vendor/bin/psalm src --alter --issues=MissingReturnType
vendor/bin/psalm src --alter --issues=MissingClosureReturnType
vendor/bin/psalm src --alter --issues=InvalidReturnType
vendor/bin/psalm src --alter --issues=InvalidNullableReturnType
```

The docs doesn't say if they stack together, but I'd assume so by the plural in "issues":

```bash
vendor/bin/psalm src --alter --issues=MissingReturnType,MissingClosureReturnType,InvalidReturnType,InvalidNullableReturnType
```

## 2. Falseable Strings?

Though the same kind of *detect type → complete it* logic, this example is really nice:

```php
function foo(): string {
  return rand(0, 1) ? 'hello' : false;
}
```

↓

```
/**
 * @return string|false
 */
function foo() {
  return rand(0, 1) ? 'hello' : false;
}
```

## 3. Unused Property or Method?

I wish this would be run before each code-review. Imagine you decouple a method during refactoring and stop using one of the existing methods in the same class. A dead method is born. **A dead method that you need to maintain, test and upgrade to new version of PHP or your framework.**

Not anymore.

```diff
 class A {
-     private function foo() : void {}
-     protected function bar() : void {}
-     public function baz() : void {}
 }

 new A();
```

Same goes for properties:

```diff
 class A {
-    /** @var string */
-    public $foo;

-    /** @var string */
-    protected $bar;
 }

 new A();
```

### How to Run it?

```bash
vendor/bin/psalm src --alter --issues=UnusedMethod
vendor/bin/psalm src --alter --issues=PossiblyUnusedMethod
vendor/bin/psalm src --alter --issues=UnusedProperty
vendor/bin/psalm src --alter --issues=PossiblyUnusedProperty
```

## 4. Undefined Variable?

How do you like this code?

```php
if (rand(0, 1)) {
  $a = 5;
}
echo $a;
```

Ups, `$a` is not defined (sometimes).

PHPStorm would tell you what's wrong with this code if you'd be writing this code. But again, it adds you extra manual work and doesn't ~~check~~ fix the rest of your huge code base from your CI.

Psalm can:

```diff
+$a = null;
 if (rand(0, 1)) {
   $a = 5;
 }
 echo $a;
```

If you're into static analysis, you know it's very hard to examine this flow of control and moreover add the variable not just the beginning of file or method, but to the right place where you or I would add it.

Cudos to [Mathew](https://github.com/muglug)! 👍

### How to Run it?

```bash
vendor/bin/psalm src --alter --issues=PossiblyUndefinedVariable
```

## Check all 13 of them

At the time of writing this post, there is 13 issue alters now. I believe we can expect up to 100 more in next year or two.

**Read about them and about extra options like `--php-version`, `--dry-run` or `--safe-types` in [very beautiful and short documentation](https://psalm.dev/docs/fixing_code/)**.

## Try it, Even if you use PHPStan

Personally, I use PHPStan because I'm not good with XML. But even if I should **install Psalm just to complete type-declarations and remove dead code**, it's worth the 10 minutes time to set it up. Give a try, it's huge time saver the bigger code base you have.

You don't have to take my word for it:

<blockquote class="twitter-tweet mt-5 mb-5" data-lang="en"><p lang="en" dir="ltr">Damn, <a href="https://twitter.com/psalmphp?ref_src=twsrc%5Etfw">@psalmphp</a> type declarations are saving me *TONS* of time and testing.</p>&mdash; null (@Ocramius) <a href="https://twitter.com/Ocramius/status/1120797947921350656?ref_src=twsrc%5Etfw">April 23, 2019</a></blockquote>
<script async src="https://platform.twitter.com/widgets.js" charset="utf-8"></script>

<br>

This is the future = PHP tools working for us - enjoy it :)

<br><br>

Happy coding!
