---
id: 166
title: "The Rocket Science Behind Migration of Docblock Types to PHP Typehints"
perex: |
    What if you could add scalar typehints `int`, `bool`, `string`, `null` to all parameter type and return type by running a CLI command? But also all classes, `parent`, `self` and `$this`?
    <br>
    <br>
    Do you think it's an easy task to move `@param int $number` to `(int $number)`?
tweet: "New post on my #php blog: The Rocket Science Behind Migration of Docblock Types to PHP Typehints   #instantupgrade"
tweet_image: "/assets/images/posts/2018/rocket-typehints/example.gif"

updated_since: "August 2020"
updated_message: |
    Updated Rector YAML to PHP configuration, as current standard.
---

Sneak peak what this post will be about:

<img src="/assets/images/posts/2018/rocket-typehints/example.gif" class="img-thumbnail">

<br>

There are tools that convert `@param` and `@return` doc to types today - like coding standards:

```diff
 /**
  * @param int $number
  * @param string|null $name
  * @return bool
  */
-public function isBigEnough($number, $name)
+public function isBigEnough(int $number, ?string $name): bool
 {
 }
```

But its **breaks your code** because it **only works with tokens of the current file**. It's like robot seeing the text by *e a c h c h a r* instead of understanding a sentence in a paragraph context.

<a href="https://github.com/FriendsOfPHP/PHP-CS-Fixer/pull/4056#issuecomment-442264393" class="text-center">
    <img src="/assets/images/posts/2018/rocket-typehints/break.png" class="img-thumbnail">
</a>

You probably assume coding standard would not break your code, but then you spend 2 days fixing invalid typehints.

"How did the example above break your code?", you might ask. That one would pass. But what if your implements interface from `/vendor`?

```php
<?php

interface Sniff
{
    /**
     * @param int $position
     */
    function process(File $file, $position);
}
```

Your code updated by coding standards:

```diff
 <?php

 final class SuperCoolSniff implements Sniff
 {
     /**
      * @param int $position
      */
-    public function process(File $file, $position)
+    public function process(File $file, int $position)
     {
         // ...
     }
 }
```

<p class="text-danger">
    <strong>PHP Fatal error</strong>:
    Declaration of <code>SuperCoolSniff::process(File $file, <strong>int $position</storng>)</code>
    <br>
    must be compatible with <code>Sniff::process(File $file, <strong>$position</strong>)</code> ...
</p>

<br>

"Just fix these cases manually". Yes, you could do that. But **why would you test your code manually** after each commit if you can cover them with tests in a matter of minutes?

<br>

I wonder what Albert Einstein would say seeing you do that work manually:

<blockquote class="blockquote text-center">
    If you can't ~~explain~~ <em>automate</em> it simply,
    <br>
    you don't understand it well enough.
</blockquote>

<br>

## Doc != Type

The problematic itself is not as simple as moving `@return int` to `int`.

If there is `@param boolean`, can the typehint be`boolean`?

```diff
/**
 * @param integer $value
 * @return boolean|NULL $value
 */
-function some($value)
+function some(int $value): ?bool
 {
 }
```

*Since PHP 7.0 [is dead now](http://php.net/supported-versions.php), we'll work with PHP 7.1 with `void` and nullables on board.*

I did some research on [existing tools](https://github.com/nikic/PHP-Parser/issues), [their issues](https://github.com/dunglas/phpdoc-to-typehint/issues) and [Symfony code](https://github.com/symfony/symfony/compare/master...TomasVotruba:typehint-test?expand=1) and this is what I found:

```diff
/**
 * @param false|true|null $value
 */
-function some($value)
+function some(?bool $value)
 {
 }
```

```diff
/**
 * @param $this $value
 */
-function some($value)
+function some(self $value)
 {
 }
```

```diff
/**
 * @param array|Item[]|Item[][]|null $value
 */
-function some($value)
+function some(?array $value)
 {
 }
```

```diff
/**
 * @param \Traversable|array $value
 */
-function some($value)
+function some(iterable $value)
 {
 }
```

Docs are quite easy, just parse few strings and change them to types that PHP accepts. [phpdoc-parser](https://github.com/phpstan/phpdoc-parser) by *Jan&nbsp;Tvrdík* helps it lot, together with [format-preserving printer](https://github.com/Symplify/BetterPhpDocParser).

Let's get harder...

## Interface, Children, Traits all Together

What happens when your interface is changed?

```diff
 interface WorkerInterface
 {
      /**
       * @param string $version
       */
-     public function work($version);
+     public function work(string $version);
 }
```

You need to update all its children:

```diff
 final class StrongWorker implements WorkerInterface
 {
      /**
       * @param string $version
       */
-     public function work($version)
+     public function work(string $version)
      {
      }
 }
```

```diff
 final class SmartWorker implements WorkerInterface
 {
      /**
       * @param string $version
       */
-     public function work($version)
+     public function work(string $version)
      {
      }
 }
```

Don't forget the interface too:

```diff
 interface CacheableWorkerIntreface extends WorkerInterface
 {
      /**
       * @param string $version
       */
-     public function work($version);
+     public function work(string $version);
 }
```

And finally, one of my favorite cases I found in Symfony:

```php
 <?php

 final class SmartWorker implements WorkerInterface
 {
      use BasicWorkerTrait;
 }
```

Oh no, we almost forgot to upgrade the trait that implements the interface indirectly:

```diff
 <?php

 trait BasicWorkerTrait
 {
-    public function work($version)
+    public function work(string $version)
     {
     }
 }
```

Trait has no doc block, no interface, no class, no other trait in it. She has no idea she should be updated.

## `self` & `parent`

`self` and `parent` are unique in each classes.

```diff
 <?php

 class P
 {
 }

 class A extends P
 {
     /**
      * @return self
      */
-    public function foo()
+    public function foo(): self
     {
     }

     /**
      * @return parent
      */
-    public function bar()
+    public function bar(): parent
     {
     }
 }

 class B extends A
 {
-    public function foo()
+    public function foo(): A
     {
     }

-    public function bar()
+    public function bar(): P
     {
     }
 }
```

## Respect The Namespace

Last but not least, different namespaces can cause another error:

```diff
 <?php

 namespace SomeNamespace;

 class A
 {
     /**
      * @return B ← "SomeNamespace\B"
      */
-    public function foo()
+    public function foo(): B
     {
     }
 }

 namespace AnotherNamespace;

 class C extends A
 {
-    public function foo()
+    public function foo(): B // missing class "AnotherNamespace\B"
+    public function foo(): \SomeNamespace\B // correct!
     {
     }
 }
```

**Do you want more *wild code cases*?** You'll find the full test battery of [60 snippets here in Github test](https://github.com/rectorphp/rector/tree/master/packages/Php/tests/Rector/FunctionLike/).

<br>

This where [good old AST](/blog/2018/10/25/why-ast-fixes-your-coding-standard-better-than-tokens/) comes the rescue. It knows all the nodes in your scope = not in `/vendor`, all children, all their implementations and used traits. It can traverse up and down this tree and see if the typehint would break something.

## Give Your Code a Typehint Facelift

[PHP 7.3 is out and PHP 7.0](http://php.net/supported-versions.php) is in <span class="text-danger">end of life</span> for 6 days. This is the best time to go PHP 7.1.


### 1. Install

```bash
composer require rector/rector --dev
```

*For those of you who have Rector already installed, use at least `0.3.24` version to get these features.*

### 2. Create Config

```php
<?php

declare(strict_types=1);

use Rector\TypeDeclaration\Rector\FunctionLike\ParamTypeDeclarationRector;
use Rector\TypeDeclaration\Rector\FunctionLike\ReturnTypeDeclarationRector;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

return function (ContainerConfigurator $containerConfigurator): void {
    $services = $containerConfigurator->services();
    $services->set(ParamTypeDeclarationRector::class);
    $services->set(ReturnTypeDeclarationRector::class);
};
```

### 3. Run

```bash
vendor/bin/rector process src --dry-run

# all good? instantly upgrade your code ↓
vendor/bin/rector process src
```

As there are many ways class-like elements can be connected - like the one with the trait that was accidentally part of interface -, there might be some more cases. **[Report everything](https://github.com/rectorphp/rector/issues) you found**, so one day this will be able to refactor all PHP Github code without breaking anything.

<br>

And when you're done, you can [get your docblocks cleaned](/blog/2017/12/17/new-in-symplify-3-doc-block-cleaner-fixer/) :)
