---
id: 277
title: "Introducing Object Calisthenics Rules for PHPStan"
perex: |
    For the last 2 years, I've maintained [Object Calisthenics Rules for PHP_CodeSniffer](https://github.com/object-calisthenics/phpcs-calisthenics-rules). In 2019 and 2020, there was a huge boom of custom PHPStan rulesets that make everyday development [easier and stronger](/blog/2018/08/27/why-and-how-to-avoid-the-memory-lock/) at the same time.
    <br><br>
    **Have you been waiting to put these rules into your `phpstan.neon`?**
    <br>
    <br>
    Today you can!

tweet: "New Post on #php 🐘 blog: Introducing Object Calisthenics Rules for @PHPStan"
tweet_image: "/assets/images/posts/object_calisthenics_phpstan.jpg"
---

Object Calisthenics is about SOLID rules and code architecture more than spaces and bracket positions, maybe [PHPStan and AST](/blog/2018/10/25/why-ast-fixes-your-coding-standard-better-than-tokens/) are better to handle it?

<br>

## What is Object Calis...?

Oh wait, it's the first time you hear *Object Calisthenics*?

<img src="/assets/images/posts/object_calisthenics_phpstan.jpg" class="img-thumbnail">

Don't worry. It's not a physical sport that is required to become a better developer. You read about all 9 rules or listen to 12-min audio in [Object Calisthenics](https://williamdurand.fr/2013/06/03/object-calisthenics) post by William Durand or check [colorful slides](https://www.slideshare.net/guilhermeblanco/object-calisthenics-applied-to-php) by Guilherme Blanco, the former maintainer PHP_CodeSniffer set package.

Some of the rules are rather theoretical to entertain the mind, but some can be measured. And **what can be measured, can be automated**. What rules can you check in your CI?

- Rule 1: Only X Level of Indentation per Method
- Rule 2: No `else` And `elseif`
- **Rule 5: No Chain Method Call**
- **Rule 6: No Names Shorter than 3 Chars**
- Rule 7: Keep Your Classes Small
- Rule 9: No Setter Methods

## Introducing PHPStan Rules

You can add all of the rules above as [a PHPStan ruleset](https://github.com/symplify/phpstan-rules/blob/master/packages/object-calisthenics/config/object-calisthenics-rules.neon). I've ported these rules to `symplify/coding-standard` in the last 2 days.

```bash
composer require symplify/coding-standard --dev
```

And update `phpstan.neon`:

```yaml
# phsptan.neon
includes:
    - vendor/symplify/coding-standard/packages/object-calisthenics/config/object-calisthenics-rules.neon
```

As you can see, their rules are pretty strict, and in practice, that might be impossible to put on a real project. It's better to start slowly with low hanging fruit rules. Like these 2:

### Rule 5: No Chain Method Call

```yaml
# phpstan.neon
services:
    -
        class: Symplify\CodingStandard\ObjectCalisthenics\Rules\NoChainMethodCallRule
        tags: [phpstan.rules.rule]
```

### Rule 6: No Names Shorter than 3 Chars

```yaml
# phpstan.neon
services:
    -
        class: Symplify\CodingStandard\ObjectCalisthenics\Rules\NoShortNameRule
        tags: [phpstan.rules.rule]
        arguments:
            minNameLength: 3
            allowedShortNames: ['id', 'to', 'up']
```

And you're ready to go!

## How to Switch from PHP_CodeSniffer to PHPStan rules?

Most likely, you're not using all the rules at once, so we look at migrating particular rules.

- Look at the [list of PHPStan rules](https://github.com/symplify/phpstan-rules/blob/master/docs/rules_overview.md)
- Look at the full set - [`object-calisthenics-rules.neon`](https://github.com/symplify/phpstan-rules/blob/master/packages/object-calisthenics/config/object-calisthenics-rules.neon)
- Pick the rules you need and copy-paste them to your `phpstan.neon`
- You can use parameters to configure them, or explicit values (like in 2 cases above)
- Add only **1 rule at a time** and then try to run PHPStan (`vendor/bin/phpstan analyse`) to make sure, it works

Good luck and have fun.

<br>

Happy coding!
