---
id: 79
title: "New in Symplify&nbsp;3: 4&nbsp;Improvements in EasyCodingStandard"
perex: |
    What is new in Easy Coding Standard 3?
    Nice diffs for fixable sniffs, smart excluding, support for sniff warnings and one more...
tweet_image: "/assets/images/posts/2018/symplify-3-ecs/exclude-files.png"

updated_since: "August 2020"
updated_message: |
    Updated with **ECS 5**, Neon to YAML migration and new simplified `skip` parameter syntax.

    Updated ECS YAML to PHP configuration since **ECS 8**.
---

## 1. Exclude Files or Dirs

Do you have `src/Migrations` that you need to skip from your `vendor/bin/ecs check src` command?

```php
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Symplify\EasyCodingStandard\ValueObject\Option;

return function (ContainerConfigurator $containerConfigurator): void {
    $parameters = $containerConfigurator->parameters();

    $parameters->set(Option::EXCLUDE_PATHS, [
        __DIR__ . '/src/Migrations/*Migration.php',
    ]);
};

```

With favorite [`fnmatch()` function](http://php.net/manual/en/function.fnmatch.php) on board.

## 2. Warnings are Reported for Specific Sniffs

<a href="https://github.com/symplify/symplify/pull/481" class="btn btn-dark btn-sm mb-3 mt-2">
    Check the PR #481
</a>

Sniff warnings are skipped by default, because it doesn't make sense to differentiate errors vs warnings. Yet some official Sniffs only produce warnings and that made them useless. Like `PHP_CodeSniffer\Standards\Squiz\Sniffs\PHP\CommentedOutCodeSniff`.

That changed. New property [`$reportWarningsSniffs` in `Symplify\EasyCodingStandard\SniffRunner\File\File`](https://github.com/symplify/symplify/blob/3d058becb57efefe2307c88ee94acbfbd15ebd1c/packages/EasyCodingStandard/packages/SniffRunner/src/File/File.php#L52) now lists all sniffs, that report warnings in ECS as well.

**Do you miss useful Sniff that reports only warnings?** Send PR to add it.

## 3. Nice and Clear Diff over Boring Table Report

<a href="https://github.com/symplify/symplify/pull/474" class="btn btn-dark btn-sm mb-3 mt-2">
    Check the PR #474
</a>

Inspired by [PHP CS Fixer](https://github.com/friendsofphp/php-cs-fixer) we've decided to **use files diffs everywhere wherever it saves user daunting reading**.

When a **fixable sniff found an error**, ECS reported it like this:

```bash
 ------ --------------------------------------------------------------------------------------------
  Line   src/Posts/Year2017/Ast/SomeClass.php
 ------ --------------------------------------------------------------------------------------------
  10     Property $someProperty should use doc block instead of one liner
         (SomeSniff)
```

But why bother with such detailed text information, if the ECS will fix it to better form anyway?

From now on, **it is reported the PHP CS Fixer-way like all the fixers**:

```diff
@@ -1,14 +1,13 @@
 final class SomeClass
 {
+    /**
+     * @var SomeType
+     */
-    /** @var SomeType */
     private $someProperty;
 }

    ----------- end diff -----------

Applied checkers:

 - SomeSniff
```

Which one do you prefer?

## 4. Skip Sniff Codes instead of Whole Sniffs

<a href="https://github.com/symplify/symplify/pull/388" class="btn btn-dark btn-sm mb-3 mt-2">
    Check the PR #388
</a>

If you wanted to skip specific part of sniff, you had to **exclude whole sniff** via "skip" option.
But what if you liked all the other codes? **Now you can:**

```php
use PHP_CodeSniffer\Standards\Generic\Sniffs\PHP\LowerCaseTypeSniff;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Symplify\EasyCodingStandard\ValueObject\Option;

return function (ContainerConfigurator $containerConfigurator): void {
    $parameters = $containerConfigurator->parameters();

    $parameters->set(Option::SKIP, [
        // old in Symplify 2
        LowerCaseTypeSniff::class => null,

        // new in Symplify 3
        LowerCaseTypeSniff::class . '.SpecificCode' => null,

        // new in Symplify 3 - only some files
        LowerCaseTypeSniff::class . '.SpecificCode' => [
            __DIR__ . '/src/Command/*'
        ]
    ]);
};
```

Thanks [@ostrolucky](https://github.com/ostrolucky) for taking adding fnmatch() skip of files.

Enjoy the news and thanks to the people who push these tools further by every single PR or issue report!

<br>

Happy coding!
