---
id: 90
title: "Try PSR-12 on Your Code Today"
perex: |
    The standard is still behind the door, but feedback, before it gets accepted, is very important. After accepting it will be written down and it will be difficult to change anything.
    <br><br>
    Try PSR-12 today and see, how it works for your code.
tweet: "New Post on My Blog: Try PSR-12 on Your Code Today"
tweet_image: "/assets/images/posts/2018/psr-12/preview.png"
---

## tl;dr;

```bash
composer require symplify/easy-coding-standard --dev
vendor/bin/ecs check /src --level psr12
```

And to fix the code:

```bash
vendor/bin/ecs check /src --level psr12 --fix
```

Now in more detailed way.

## PSR-12 meets ECS

Someone on [Reddit referred a PSR Google Group](https://www.reddit.com/r/PHP/comments/84vafc/phpfig_psr_status_update/), where they **asked for real-life PSR-12 ruleset implementation in a coding standard tool**. Korvin Szanto already prepared 1st implementation for PHP CS Fixer, at the moment [only as a commit in](https://github.com/KorvinSzanto/PHP-CS-Fixer/commit/c0b642c186d8f666a64937c2d37442dc77f6f393) the fork. I put the ruleset to `psr12.yml` level in ECS and it looks like this in time of being:

```yaml
imports:
    - { resource: 'php_cs_fixer/psr2.yml' }

services:
    PhpCsFixer\Fixer\CastNotation\LowercaseCastFixer: ~
    PhpCsFixer\Fixer\CastNotation\ShortScalarCastFixer: ~
    PhpCsFixer\Fixer\PhpTag\BlankLineAfterOpeningTagFixer: ~
    PhpCsFixer\Fixer\Import\NoLeadingImportSlashFixer: ~
    PhpCsFixer\Fixer\Import\OrderedImportsFixer:
        importsOrder:
            - 'class'
            - 'const'
            - 'function'
    PhpCsFixer\Fixer\LanguageConstruct\DeclareEqualNormalizeFixer:
        space: 'none'
    PhpCsFixer\Fixer\Operator\NewWithBracesFixer: ~
    PhpCsFixer\Fixer\Basic\BracesFixer:
        'allow_single_line_closure': false
        'position_after_functions_and_oop_constructs': 'next'
        'position_after_control_structures': 'same'
        'position_after_anonymous_constructs': 'same'

    PhpCsFixer\Fixer\ClassNotation\NoBlankLinesAfterClassOpeningFixer: ~
    PhpCsFixer\Fixer\ClassNotation\OrderedClassElementsFixer:
        order:
            - 'use_trait'
    PhpCsFixer\Fixer\ClassNotation\VisibilityRequiredFixer:
        elements:
            - 'const'
            - 'method'
            - 'property'
    PhpCsFixer\Fixer\Operator\BinaryOperatorSpacesFixer: ~
    PhpCsFixer\Fixer\Operator\TernaryOperatorSpacesFixer: ~
    PhpCsFixer\Fixer\Operator\UnaryOperatorSpacesFixer: ~
    PhpCsFixer\Fixer\FunctionNotation\ReturnTypeDeclarationFixer: ~
    PhpCsFixer\Fixer\Whitespace\NoTrailingWhitespaceFixer: ~

    PhpCsFixer\Fixer\Operator\ConcatSpaceFixer:
        spacing: 'one'

    PhpCsFixer\Fixer\Semicolon\NoSinglelineWhitespaceBeforeSemicolonsFixer: ~
    PhpCsFixer\Fixer\ArrayNotation\NoWhitespaceBeforeCommaInArrayFixer:
    PhpCsFixer\Fixer\ArrayNotation\WhitespaceAfterCommaInArrayFixer:

parameters:
    exclude_checkers:
        - 'PhpCsFixer\Fixer\Import\SingleImportPerStatementFixer'
        - 'PhpCsFixer\Fixer\Whitespace\NoExtraBlankLinesFixer'
```



## Do You Agree or Disagree with PSR12?

There are still many [missed cases to be integrated into the standard](https://github.com/KorvinSzanto/PHP-CS-Fixer/milestones), but there is never to soon to get feedback from the community.

<div class="text-center">
    <img src="/assets/images/posts/2018/psr-12/php-cs-fixer-thing.png" alt="PR in PHP CS Fixer?" class="img-thumbnail">
</div>

It will be *a thing*: PSR-12 set is definitely coming to PHP CS Fixer and [PHP_CodeSniffer has also an active issue](https://github.com/squizlabs/PHP_CodeSniffer/issues/750) as well. Both of these tools are more stable, more popular and thus more rigid than ECS. So it will take time before there will be a pull-request and then stable release with PSR-12 set.

**That's an advantage of smaller packages like ECS, they can evolve faster and live in the present.** Only that way ECS 4 already has PSR-12 set on board and ready to use.

### What do I Like?

I like that PSR-12 puts to standard rules that I consider standard for years and most of them are already integrated with ECS [`common` sets](https://github.com/Symplify/Symplify/tree/master/packages/EasyCodingStandard/config/common):

- it applies PHP 7.1 features, like constant visibility
- concat ` . ` spacing
- mostly spacing
- and letter casing

### What don't I Like?

Symplify code is already checked by PSR-12 ([see pull-request](https://github.com/Symplify/Symplify/pull/773)):

<div class="text-center">
    <img src="/assets/images/posts/2018/psr-12/symplify-implementation.png" alt="Integration to project with ECS" class="img-thumbnail">
</div>

It was easy to setup and works with 0 changes in the code. But as you can see, there 2 rules I don't fully agree with.

#### 1. `PhpCsFixer\Fixer\PhpTag\BlankLineAfterOpeningTagFixer`

This rule creates this code:

```php
<?php

declare(strict_types=1);
```

Namespace changes, file doc changes, `use`, `class`, `interface`... that all changes in every file, so it should be on a standalone line, **that will force you to notice it and orientate**. But not `declare(strict_types=1);`, that is the same in every file.

I think our attention deserves to ignore anything that is the same in every file so inline its line:

```php
<?php declare(strict_types=1);
```

<br>

#### 2. `PhpCsFixer\Fixer\Operator\UnaryOperatorSpacesFixex`

It takes care of spacing around `!`

```php
if (!$isNotTrue) {
}

if ($isNotTrue) {
}
```

Here we can apply the same approach we did in 1. An important code should be visually clear, an unimportant code should not bother us. Personally, **I prefer seeing the negation clearly**, so I know it's a negation:

```php
if (! $isNotTrue) {
}
```

## Try It Yourself Today

Communicate, spread the ideas and find your way. This is only PSR - PS **Recommendation**. It's better to keep things standard for others, [so they can drink water if they're thirsty and not start a research on bottle colors instead](/blog/2018/03/12/neon-vs-yaml-and-how-to-migrate-between-them/#why-are-standards-so-important). But not a rigid rule that cannot be improved.

So just [try it](#tl-dr). Maybe your code is already PSR-12 ready.

<br>

Happy coding!
