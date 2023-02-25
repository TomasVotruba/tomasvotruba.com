---
id: 290
title: "New in Symplify 9: Documentation Generator for PHP CS Fixer, Code Sniffer, PHPStan and Rector Rules"
perex: |
    Rector is providing rules, so is PHPStan and PHP CS Fixer, and Code Sniffer. If you use only 5-10 rules and want to share them with the world, you create a README and describe them.

    In Rector, we now have over 640 rules, in Symplify 110 rules for PHPStan and 15 rules for PHP CS Fixer. **How can we handle documentation for this amount of rules without going crazy?**

tweet_image: "/assets/images/posts/2020/rules-nesting-full.png"
---

This post is for rules-based package maintainers.

- If we add one new rule, how do we document it?
- What is some rule is configurable? It must be mentioned in the documentation.
- If somebody sends a pull-request, we should tell them to do this.
- [But what if we forget?](/blog/2018/08/27/why-and-how-to-avoid-the-memory-lock/) What file should the documentation be placed?
- What if the rule is configurable, but the example doesn't show how?

But the critical question is:

- **How can users quickly see what the rule does?**


## Status Quo: Manual Documentation

Today, we create documentation manually. Add a new rule, new option, update the name or describe an example. Here are 4 examples from such documentations:

<div class="row">
    <div class="col-3">
        <img src="/assets/images/posts/2020/rules-docs-second.png" class="img-thumbnail">
    </div>
    <div class="col-3">
        <img src="/assets/images/posts/2020/rules-docs-first.png" class="img-thumbnail">
    </div>
    <div class="col-3">
        <img src="/assets/images/posts/2020/rules-docs-third.png" class="img-thumbnail">
    </div>
    <div class="col-3">
        <img src="/assets/images/posts/2020/rules-docs-fourth.png" class="img-thumbnail">
    </div>
</div>

There are roughly **1500-2000 rules in the whole rules-ecosystem** and growing every day. We don't have time to read about each rule. Nor deduct from the text how it will confront our code.

How do we adapt? **We scan**. We go quickly through README and look for something we like. [When we scan](https://www.amazon.com/Thinking-Fast-Slow-Daniel-Kahneman/dp/0374533555), we look for colors, **bold**, patterns or pictures.

<br>

Have a guess, what does this rule do?

<img src="/assets/images/posts/2020/rules-example.png" class="img-thumbnail">

You're right. It makes line-length fit `x`.

<br>

## Easy for Maintainers and Sexy for Users?

The diff example above is beautiful, but it requires extra work with diff formatting, precise code indent, and don't even start about maintenance in case of change...

<blockquote class="blockquote text-center">
    "Give me the tools to solve my problem, and I'll consider it.<br>
    Give me extra work, and I'll pass."
</blockquote>

...but wait. There might be a way to less work.

<br>

In Rector, we have over 640 rules. That would be hell to maintain, so eventually, we came up with a documentation generator. One command line hit, and a whole file with examples, rule names is generated:

```bash
bin/rector generate-rule-docs
```

We already maintained [Symplify\CodingStandard](https://github.com/symplify/coding-standard)
documentation with 40 rules **manually**. There was some itching on every new rule, but we always learned to ignore it. That was about to change.

During summer 2020 a new packaged started to grow - [Symplify\PHPStanRules](https://github.com/symplify/phpstan-rules). Just during summer, 50 rules were added, now growing over 110. Constant reminders in pull-request "Could you add it to documentation?" were **the last nail in the coffin**.

We had to automate it.

## Every Rule is the Documentation

The first step to automation is to merge rule documentation and rule into a single place. There is no markdown file, just a PHP code rule.

We got inspired in [php-cs-fixer](https://github.com/FriendsOfPHP/PHP-CS-Fixer/blob/ae6fceca37615fcd08183e9c3cfb8f296d2de8c2/src/Fixer/Phpdoc/PhpdocTypesFixer.php#L87) where `RuleDefinition` is part of every rule.

<br>

Add an `DocumentedRuleInterface` interface and implement `getRuleDefinition()`:

```php
namespace App\CodingStandard\Fixer;

use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\CodeSample;
use Symplify\RuleDocGenerator\Contract\DocumentedRuleInterface;

final class RemoveCommentedCodeFixer implements DocumentedRuleInterface
{
    public function getRuleDefinition(): RuleDefinition
    {
        return new RuleDefinition('Remove commented code', [
            new CodeSample(
                <<<'CODE_SAMPLE'
// $one = 1;
// $two = 2;
// $three = 3;
CODE_SAMPLE
                ,
                <<<'CODE_SAMPLE'
CODE_SAMPLE
            ),
        ]);
    }
}
```

That's it!

## 3 steps to Generated Documentation

The [Symplify\RuleDocGenerator](https://github.com/symplify/rule-doc-generator) packages take care of full documentation. There is just one interface to implement for Rector, PHP CS Fixer, CodeSniffer, and PHPStan rules.

1. Install Package

```bash
composer require symplify/rule-doc-generator
```

2. Implement `DocumentedRuleInterface` interface

You can pick from more code sample classes:

- `CodeSample`
- `ConfiguredCodeSample`

For read-only rules, e.g. Sniff or PHPStan, the second argument of `CodeSample` is for correct code:

```php
use Symplify\RuleDocGenerator\ValueObject\CodeSample\CodeSample;

// ...
return new CodeSample('bad code', 'good code');
```

3. Generate Documentation in CLI

```bash
vendor/bin/rule-doc-generator generate <directories-with-rules>
```

There is also implicit `--output-file docs/rules_overview.md` option.

```bash
vendor/bin/rule-doc-generator generate src/Rules
```

↓

You've just created smart documentation with **nice and clear design**:

<img src="/assets/images/posts/2020/rules-nesting-full.png" class="img-thumbnail mt-4 mb-5">


**Tired of screenshots? See the Real Documentation on GitHub**

See our 3 documentations re-generated everyday with GitHub Actions cron:

- [Rector with 640 rules](https://github.com/rectorphp/rector/blob/master/docs/rector_rules_overview.md)
- [Symplify\PHPStanRules with 110 rules](https://github.com/symplify/phpstan-rules/blob/master/docs/rules_overview.md)
- [Symplify\CodingStandard with 15 rules](https://github.com/symplify/coding-standard/blob/master/docs/rules_overview.md)

<br>

That's it!

<br>

Happy coding!

