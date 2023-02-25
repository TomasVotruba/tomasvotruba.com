---
id: 293
title: "New in Symplify 9: More than 110 PHPStan Rules"
perex: |
    In Symplify 8, a bunch of PHP rules was part of the symplify/coding-standard package.
    It was a mix of too many tools, so we decided to **decouple a new package - symplify/phpstan-rules**.


    During summer 2020, our Rector team grew from 1 member to 4. To keep onboarding smooth, we started to use PHPStan **to help with code-reviews in Rector**. We got obsessed with [moving human code-reviews to CI](/blog/2019/11/18/how-to-delegate-code-reviews-to-ci/).


    Was it worth it? Hell yea!

---

That's how we grew from 20 rules to 110 PHPStan Rules in ~3 months. I'd say we are barely **scratching the surface of what CI can handle** for us in code-reviews, but 110 rules is a solid base to start from.

<br>

Since Symplify 9, released on December 9th, you can use them in your code. Give it a try:

```bash
composer require symplify/phpstan-rules --dev
```

This is not a typical PHPStan package you're used to. That you just install, add to `phpstan.neon` and forget.

symplify/phpstan-rules is more **like a bucket of lego bricks**. It has prepared parts that you can use, but it's up to you to configure them.

<br>

symplify/phpstan-rules has **3 main areas**:

- static rules
- prepared sets
- configurable rules

We'll take them one by one, from the simplest to most powerful.
<br>

## 1. Static Rules

These rules are not configurable. You either use them or not. Dare to try them all and see what happens?

```yaml
includes:
    - vendor/symplify/phpstan-rules/config/static-rules.neon
```

Or do you want to **cherry-pick rule by rule**? Go through the [rules overview with PHP code snippets](https://github.com/symplify/phpstan-rules/blob/master/docs/rules_overview.md) and copy-paste those you like:

```yaml
rules:
    - Symplify\PHPStanRules\Rules\CheckRequiredInterfaceInContractNamespaceRule
```

<br>

Some rules require extra services. To avoid service duplications, they're in the separate config that you can easily include:

```yaml
includes:
    - vendor/symplify/phpstan-rules/config/services/services.neon
```

That's it!

## 2. Prepared Sets

Special group of static rules are [prepared sets](https://github.com/symplify/phpstan-rules/tree/master/config/symplify-rules.neon). Similar to ECS sets, each has one area it focuses on:

```yaml
includes:
    - vendor/symplify/phpstan-rules/config/array-rules.neon
    - vendor/symplify/phpstan-rules/config/code-complexity-rules.neon
    - vendor/symplify/phpstan-rules/config/doctrine-rules.neon
    - vendor/symplify/phpstan-rules/config/naming-rules.neon
    - vendor/symplify/phpstan-rules/config/regex-rules.neon
    - vendor/symplify/phpstan-rules/config/services-rules.neon
    - vendor/symplify/phpstan-rules/config/size-rules.neon
    - vendor/symplify/phpstan-rules/config/forbid-static-rules.neon
    - vendor/symplify/phpstan-rules/config/string-to-constant-rules.neon
    - vendor/symplify/phpstan-rules/config/symfony-rules.neon
    - vendor/symplify/phpstan-rules/config/test-rules.neon
```

Pick what you like and drop the rest.

## 3. Configurable Rules

This is the powerful part that PHPStan advanced users will appreciate.

**You can tune configurable rules to your project context**. Do you need more strict [cognitive complexity](/blog/2018/05/21/is-your-code-readable-by-humans-cognitive-complexity-tells-you/)? No problem. Do you want to allow fewer dependencies in the constructor? Just set it.

Configurable rules are configured for Symplify by defaults. You can find them in:

```yaml
includes:
    - vendor/symplify/phpstan-rules/config/configurable-rules.neon
```


Symplify standards might not fit your standard, so it's better to use rules separately.

### Example: Configuring `ForbiddenNodeRule`

Let's look at [`ForbiddenNodeRule`](https://github.com/symplify/phpstan-rules/blob/master/docs/rules_overview.md#forbiddennoderule). In this rule, you can say what [nodes](https://github.com/rectorphp/php-parser-nodes-docs) are forbidden to use.

We don't like `switch()`, `empty()` and `@`, so we forbid them:

```yaml
services:
    -
        class: Symplify\PHPStanRules\Rules\ForbiddenNodeRule
        tags: [phpstan.rules.rule]
        arguments:
            forbiddenNodes:
                - PhpParser\Node\Expr\Empty_
                - PhpParser\Node\Stmt\Switch_
                - PhpParser\Node\Expr\ErrorSuppress
```

Now on each CI run, PHPStan makes sure our code is clean and with high standard :)


<br>

That's it!
