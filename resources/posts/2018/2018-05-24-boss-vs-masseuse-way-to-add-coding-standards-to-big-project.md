---
id: 108
title: "The Boss vs. The Masseuse Way to Add Coding Standards to a Big Project"
perex: |
    Do you prefer a **boss who's watching you** how you sit at the desk telling how to sit right
    or a **masseuse who's taking care of your hands** tired from programming with her gentle hands?


    When it comes to coding standards, the love and fun is the best experience with it. Let's look how such "masseuse" can be added to your big project.


updated_since: "February 2021"
updated_message: |
    Use new Symplify 9 syntax for PHPStan rule registration.
---

## "The Boss" Way to Add Coding Standard

It's very rare that projects have coding standards right from the first line. That applies to CI, tests, coverage, and docs. Why? They come with experience and with a need. **The biggest added value of coding standards is to bring more fun to your team, as it works for you**.

Saying that **the most projects need and then add coding standards when they grow up to a large code base**.

The most [popularized way](https://akrabat.com/checking-your-code-for-psr-2) to do this is:

```bash
composer require squizlabs/php_codesniffer --dev
vendor/bin/phpcs --standard=PSR2 /app /src
```

I bet you're able to run these command even if you see it for the first time.

But what will happen next?

<img src="/assets/images/posts/2018/cs-masseuce/unknown-error.png">

**You'll get ~ *X* hundreds of errors you don't understand**. It can feel embarrassing like having the boss' eyes on you all the time.

This is often the reason coding standard is not part of many great PHP projects, which makes me very sad.

## "The Masseuse" Way to Add Coding Standard

How to make this first experience better? Start slowly, one touch at a time, like a masseuse with your hands.

### 1. Install Your Favoring Coding Standard Tool

For me, it's obviously [ECS](https://github.com/symplify/easy-coding-standard):

```bash
composer require symplify/easy-coding-standard --dev
```

### 2. Use One Sniff/Fixer that Helps You The Most

This is the most important step. This checker should be

- easy to understand
- helpful for you as a programmer (not a `{` or `"` position)
- helpful to your project
- and easy to fix code for you (like `array()` → `[]`)

### 3. Make it Pass Without Any Code Change

Last week a [Cognitive Complexity Rule](/blog/2018/05/21/is-your-code-readable-by-humans-cognitive-complexity-tells-you/) was published there was [very positive feedback](https://github.com/symplify/symplify/issues/834) on it.

**If your coding standard should have only 1 rule - this is the one.**

```yaml
# phpstan.neon
includes:
    - vendor/symplify/phpstan-rules/packages/cognitive-complexity/config/cognitive-complexity-services.neon

services:
    -
        class: Symplify\PHPStanRules\CognitiveComplexity\Rules\FunctionLikeCognitiveComplexityRule
        tags: [phpstan.rules.rule]
        arguments:
            maxMethodCognitiveComplexity: 8
```

But when you run your tool (`vendor/bin/phpstan analyse /src`), it will probably drop dozens of errors. And we don't want to go to the boss approach.

<br>

Saying that, **we make the rule so free, that your code passes it**:

```diff
 services:
     -
         class: Symplify\PHPStanRules\CognitiveComplexity\Rules\FunctionLikeCognitiveComplexityRule
         tags: [phpstan.rules.rule]
         arguments:
-            maxMethodCognitiveComplexity: 8
+            maxMethodCognitiveComplexity: 50
```

**0 errors!**

You can now add this to your GitHub Actions and make the PR. Merge it and take a 2 weeks break.

<br>

Then decrease the criteria for 10 %:

```diff
 services:
     -
         class: Symplify\PHPStanRules\CognitiveComplexity\Rules\FunctionLikeCognitiveComplexityRule
         tags: [phpstan.rules.rule]
         arguments:
-            maxMethodCognitiveComplexity: 8
+            maxMethodCognitiveComplexity: 45
```

- Fix couple the cases that will appear.
- One at a time.
- Then repeat previous workflow: PR, merge and take a week break.

When **you feel ready**, you can add 1 more checker, make a rule more strict... you get the idea to enjoy your massage :).

## Proven Practice

This way I was able to add coding standards to quite a big codebase in [Lekarna.cz](https://github.com/lekarna) a few years ago with not many troubles, and learn how they work along the way.

<br>

**I wish you the same experience in your huge project.**

<br><br>

Happy coding!
