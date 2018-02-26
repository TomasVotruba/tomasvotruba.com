---
id: 79
title: "New in Symplify 3: 4 Improvements in EasyCodingStandard"
perex: '''
    Nice diffs for fixable sniffs, smart excluding and support for sniff warnings.
    Checkout these news in Easy Coding Standard 3.  
'''
tweet: "New post on my blog: New in Symplify 3: 4 Improvements in EasyCodingStandard #codingstandard php"
---

### 1. Exclude Files or Dirs via `exclude_files`

<a href="https://github.com/Symplify/Symplify/pull/583" class="btn btn-dark btn-sm mb-3 mt-2">
    <em class="fa fa-github"></em>
    &nbsp;
    Check the pull-request #583
</a>

<a href="https://github.com/Symplify/Symplify/pull/584" class="btn btn-dark btn-sm mb-3 mt-2">
    <em class="fa fa-github"></em>
    &nbsp;
    Check the pull-request #584
</a>
 
Do you have `src/Migrations` that you need to skip from your `vendor/bin/ecs check src` command?

```yml
# easy-coding-standard.neon
parameters:
    exclude_files:
        - src/Migrations/LastMigration.php
        # or better 
        - *src/Migrations/*.php
```

With favorite [`fnmatch()` function](http://php.net/manual/en/function.fnmatch.php) on board. 

### 2. Warnings are not Silenced any more for Specific Sniffs

<a href="https://github.com/Symplify/Symplify/pull/481" class="btn btn-dark btn-sm mb-3 mt-2">
    <em class="fa fa-github"></em>
    &nbsp;
    Check the pull-request #481
</a>

Sniff warnings are skipped by default, because it doesn't make sense to differentiate errors vs warnings. Yet some official Sniffs only produce warning and could not be used for ECS. Like `PHP_CodeSniffer\Standards\Squiz\Sniffs\PHP\CommentedOutCodeSniff`.

New property [`$reportWarningsSniffs` in `Symplify\EasyCodingStandard\SniffRunner\File\File`](https://github.com/Symplify/Symplify/blob/3d058becb57efefe2307c88ee94acbfbd15ebd1c/packages/EasyCodingStandard/packages/SniffRunner/src/File/File.php#L52) now allows this.

Do you miss useful Sniff that reports only warnings? Send PR to add it!  

### 3. Nice and Clear Diff over Table Report

<a href="https://github.com/Symplify/Symplify/pull/474" class="btn btn-dark btn-sm mb-3 mt-2">
    <em class="fa fa-github"></em>
    &nbsp;
    Check the pull-request #474
</a>

Inspired by [PHP CS Fixer](https://github.com/friendsofphp/php-cs-fixer) we've decided to use files diffs everywhere where it makes sense.

When a fixable sniff found an error, ECS reported it like this: 

```bash
 ------ -------------------------------------------------------------------------------------------- 
  Line   src/Posts/Year2017/Ast/SomeClass.php                                                        
 ------ -------------------------------------------------------------------------------------------- 
  10     Property $someProperty should use doc block instead of one liner                                               
         (SomeSniff)   
```

But why bother with such detailed text information, if the ECS will fix it to better form anyway?

From now on, **it is reported the PHP CS Fixer-way like all the fixers**: 

```bash
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

### 4. Skip Sniff Codes 

<a href="https://github.com/Symplify/Symplify/pull/388" class="btn btn-dark btn-sm mb-3 mt-2">
    <em class="fa fa-github"></em>
    &nbsp;
    Check the pull-request #388
</a>

You can now **skip specific sniff codes**, e.g. PHP 7 typehints for [Slevomat\CodingStandard](https://github.com/slevomat/coding-standard):

```yaml
parameters:
    skip_codes:
        # code to skip for all files
        - SlevomatCodingStandard\Sniffs\TypeHints\TypeHintDeclarationSniff.UselessDocComment
```

<a href="https://github.com/Symplify/Symplify/pull/406" class="btn btn-dark btn-sm mb-3 mt-2">
    <em class="fa fa-github"></em>
    &nbsp;
    Check the pull-request #406
</a>

To take it even further, you can even **skip the codes by list of files or [`fnmatch`](php.net/manual/en/function.fnmatch.php)** - thanks to [@ostrolucky](https://github.com/ostrolucky):

```yaml
parameters:
    skip_codes:
        # code to skip for specific files/patterns
        SlevomatCodingStandard\Sniffs\TypeHints\TypeHintDeclarationSniff.MissingTraversableParameterTypeHintSpecification:
            -  *src/Form/Type/*Type.php
```


Hope you like the changes and thanks the people who push these tools further by every single PR or issue report!


<br>

Happy code fixing!
