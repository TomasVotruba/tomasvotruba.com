---
id: 70
title: "New in Symplify 3: 4 Time-saving Coding Standard Checkers"
perex: |
    Coding Standard in Symplify 3 brings **checkers build from practical use in PHPStorm**. It can do lot of work for you, like add getters, remove trailing spaces, but has still some room for automation and improvement.
    <br>
    I already wrote about [doc block cleaner fixer](/blog/2017/12/17/new-in-symplify-3-doc-block-cleaner-fixer/) and here 4 more checkers, **that saves you from monkey-typing and let you focus on algorithms**.
tweet: "Absolutize require/include, empty line after strict_types() definition, import all the names and the best - unused public methods. Welcome and use new checkers in Symplify 3 Coding Standard #codingstandar #phpcsfixer #phpcodesniffer #php"
tweet_image: "/assets/images/posts/2018/symplify-3-checkers/import-fixer.png"

updated_since: "December 2018"
updated_message: |
    Updated with <strong>EasyCodingStandard 5</strong>, Neon to YAML migration and <code>checkers</code> to <code>services</code> migration.
    <br>
    <code>ImportNamespacedNameFixer</code> [was removed](https://github.com/Symplify/Symplify/pull/1110) in favor of <code>ReferenceUsedNamesOnlySniff</code> from <a href="https://github.com/slevomat/coding-standard">slevomat/coding-standard</a>
---

Starting with the simple checkers and moving to those, which save you even hours of manual work.

## 1. Absolutely Require and Include

<a href="https://github.com/Symplify/Symplify/pull/385" class="btn btn-dark btn-sm mb-3 mt-2">
    <em class="fab fa-github"></em>
    &nbsp;
    Check the pull-request
</a>


You probably recognize this:

```php
require 'vendor/autoload.php';
```

**Why is this bad?** It promotes relative paths by default, supports magic path resolving and can cause errors, because we expects existing file by default. You can easily end-up in ambiguous file paths like:

```php
var_dump($relativeFile);
"/var/path/fileName.php"
# or
"/var//path/fileName.php"
# or
"/varpath/fileName.php"
```


Of course there are cases when using absolute paths is not suitable, like templates in Symfony application, but **when we know the absolute path we should prefer it**:

```diff
-require 'vendor/autoload.php';
+require __DIR__.'/vendor/autoload.php';
```

And that's what this checker does for you:

```yaml
# ecs.yml
services:
    Symplify\CodingStandard\Fixer\ControlStructure\RequireFollowedByAbsolutePathFixer: ~
```

<br>

## 2. Empty Line after `declare(strict_types=1)`

<a href="https://github.com/Symplify/Symplify/pull/443" class="btn btn-dark btn-sm mb-3 mt-2">
    <em class="fab fa-github"></em>
    &nbsp;
    Check the pull-request
</a>

The next one started as issue [PHP CS Fixer in January 2016 (if not earlier)](https://github.com/FriendsOfPHP/PHP-CS-Fixer/issues/1793). The story [continues in next issue](https://github.com/FriendsOfPHP/PHP-CS-Fixer/issues/2062), but final fixer is not in near sight.

<img src="/assets/images/posts/2018/symplify-3-checkers/long-time.png" class="img-thumbnail">

Why all these issues? Official fixer modifies code like this:

```diff
-<?php
+<?php declare(strict_types=1);
-
 namespace Abc;
```

Which is not what we want.

`BlankLineAfterStrictTypesFixer` fixer was needed so **EasyCodingStandard could refactor open-source packages without any manual work**:

- see [cpliakas/git-wrapper PHP 7 pull-request](https://github.com/cpliakas/git-wrapper/pull/137/files)
- or [phpDocumentor/ReflectionDocBlock PHP 7  pull-request](https://github.com/phpDocumentor/ReflectionDocBlock/pull/137/files)

When the official fixer is finished, I'd be happy to use it and recommend it. But **right now you can use**:

```yaml
# ecs.yml
services:
    Symplify\CodingStandard\Fixer\Strict\BlankLineAfterStrictTypesFixer: ~
```

Which helps official fixer to keep the space:

```diff
-<?php
+<?php declare(strict_types=1);

 namespace Abc;
```

## 3. One Way To Use Namespace Imports

What do you think about this?

<img src="/assets/images/posts/2018/symplify-3-checkers/import-fixer.png" class="img-thumbnail">

*Import class* is great PHPStorm feature. It sometimes does only partial imports, sometimes is unable to resolve conflict of 2 `SameClass` names and it still requires your time and attention to work.

If you don't care about this, your code can look like this:

```php
<?php

namespace SomeNamespace;

final class SomeClass extends \SubNamespace\PartialNamespace\AnotherClass
{
    public function getResult(): \ExternalNamespace\Result
    {
        $someOtherClass = new \SomeNamespace\PartialNamespace\SomeOtherClass;
        // ...
    }
}
```

**If you do care** - which is probably why you're reading this post - you'd prefer code like this:

```diff
 <?php

 namespace SomeNamespace;

+use ExternalNamespace\Result;
+use SubNamespace\PartialNamespace\AnotherClass
+use SubNamespace\PartialNamespace\SomeOtherClass;

-final class SomeClass extends \SubNamespace\PartialNamespace\AnotherClass
+final class SomeClass extends AnotherClass
 {
-    public function getResult(): \ExternalNamespace\Result
+    public function getResult(): Result
     {
-        $someOtherClass = new \SomeNamespace\PartialNamespace\SomeOtherClass;
+        $someOtherClass = new SomeOtherClass;
         // ...
     }
 }
```

Original `ImportNamespacedNameFixer` doing this job [was removed in EasyCodingStandard 5](https://github.com/Symplify/Symplify/pull/1110) in favor of `ReferenceUsedNamesOnlySniff` from [slevomat/coding-standard](https://github.com/slevomat/coding-standard).

It's not able to import partial namespace or resolve conflicts, but it has extra features, like functions imports etc.

How does it Work?```yaml
# ecs.yml
services:
    SlevomatCodingStandard\Sniffs\Namespaces\ReferenceUsedNamesOnlySniff: ~
```

## 4. Possible Unused Public Method

<a href="https://github.com/Symplify/Symplify/pull/466" class="btn btn-dark btn-sm mb-3 mt-2">
    <em class="fab fa-github"></em>
    &nbsp;
    Check the pull-request
</a>


If you create method **to be used in your code only and available as service** - no matter if open-source or closed-source - you might end-up with many public methods. Your application or packages grows, there are some refactoring going on, even few deprecations.

Same happened with [Symplify](https://github.com/Symplify/Symplify). Eventually, I came across **unused public methods** that contained lot of unused code. A code I hade to test and maintain. There is already [Sniff from Slevomat](https://github.com/slevomat/coding-standard#slevomatcodingstandardclassesunusedprivateelements-) for unused private elements (great job guys!) - which inspired me to question:

### Could a Sniff Spot Unused Public Methods?

Consider this checker **as adviser, who helps to you to spot weak points** and makes rest of your code more valuable and consistent, since it contains only what it needs.

This checkers **requires EasyCodingStandard** to run - it uses its "double run" feature:

- on first run checker finds all public method names and all method calls
- on second run it reports those public method names, that were not called

It helps you to spot spots like [this](https://github.com/Symplify/Symplify/pull/466/commits/3f08ed1fb2f22dd6c4a7b46d680adf4ab5a0907d), [this](https://github.com/Symplify/Symplify/pull/466/commits/fa21855694d933716117b2a2db13acac55b86d69) or [this](https://github.com/Symplify/Symplify/pull/552/commits/2df9cbab657a701acb2163f7321216fb782fcf35).

### Run it Occasionally to Save Dozens of Hours of Dead Code Maintenance

I recommend it running from time to time in standalone thread, since it takes lot of performance and reports all unused public method, even those destined for public use:

```yaml
# ecs.yml
services:
    Symplify\CodingStandard\Sniffs\DeadCode\UnusedPublicMethodSniff: ~
```

To make the first run collection effective, a `--clear-cache` option must be added:

```bash
vendor/bin/ecs check src --clear-cache
```

<br>

### Do You Want More?

There are **over 30 standalone checkers** in Symplify\CodingStandard 3.0 with more added every release.

See [visual examples in `README`](https://github.com/Symplify/CodingStandard#rules-overview) and decide for yourself, which you like and which you don't.

Thanks [@carusogabriel](https://twitter.com/carusogabriel) for the `diff` idea in `README`. It's brilliant!

<br><br>

Happy fixing and sniffing!
