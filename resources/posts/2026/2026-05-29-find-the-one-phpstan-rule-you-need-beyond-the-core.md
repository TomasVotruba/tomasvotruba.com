---
id: 437
title: "Find the One PHPStan Rule You Need, Beyond the Core"
perex: |
    PHPStan core gets you to level 10 with solid type checks. But the real gems - the rules that catch *your* anti-patterns - live scattered across a dozen community packages most people never hear about.

    I got tired of hunting through GitHub READMEs to find "is there a rule for *this*?", so I built a single searchable page that indexes them all.
---

You know the feeling. You spot the same smell in pull request after pull request - a `static` method here, a forbidden `dd()` left in production code there, a `@param` that lies about its type - and you think:

*"Surely someone already wrote a PHPStan rule for this?"*

They probably did. The problem is finding it.

<br>

PHPStan core is great at types. But the rules that catch project-specific patterns - the ones that keep your codebase future-proof - live in community packages: `symplify/phpstan-rules`, `shipmonk/phpstan-rules`, `ergebnis/phpstan-rules`, `spaze/phpstan-disallowed-calls`, and a long tail of smaller ones.

Each ships its own README, its own naming, its own way of describing what it does. To answer "is there a rule for X?", you'd have to open a dozen tabs and skim each one. Nobody does that. So great rules sit unused, and we keep eyeballing the same problems by hand.

## One page, every rule

So I made a single page that indexes them all:

* **[PHPStan Rules Beyond Core](/phpstan-rules-beyond-core)**

It's a searchable index of **266 individual rules** scraped from **13 of the most popular community packages**. Each entry shows you:

* the rule class you drop into `phpstan.neon`
* a plain-English description of what it does
* the message it reports when it fires
* and - where the package documents it - a wrong/correct code example side by side

<br>

No tags, no node-type badges, no internal identifiers. Just the rule, what it catches, and how to install the package it lives in.

## Search the way you think

The whole point is discovery, so the search is built for it. Type what's on your mind, not the exact class name:

* search `forbidCast` and it ranks the casting rules first
* search `mixed` and you get every rule that fights the `mixed` type
* mistype `noreturn` as one word and it still finds `No` + `return`

<br>

It tokenizes camelCase, does prefix and fuzzy matching, and ranks results so the rule you meant floats to the top. When you're not searching, the rules stay grouped by package so you can browse a whole package at once.

## From "I wish" to installed in one click

Found a rule you want? Each package block has a ready-to-copy install line:

```bash
composer require --dev symplify/phpstan-rules
```

Copy it, add the rule class to your `phpstan.neon`, and run PHPStan:

```bash
vendor/bin/phpstan
```

<br>

That's the loop I wanted: *spot a smell → search → install → enforce it in CI forever.* The same philosophy as [adding custom rules one at a time](/blog/custom-phpstan-rules-to-improve-every-symfony-project) - except now you don't have to know the rule exists beforehand.

## Why bother?

Because [good code quality should survive us leaving the project](/blog/custom-phpstan-rules-to-improve-every-symfony-project). A rule enforced in CI is a guideline that never gets forgotten, never gets tired, and never skips a review.

The community has already written hundreds of them. They just needed a place where you can actually find the one you need.

<br>

Go have a look: **[PHPStan Rules Beyond Core](/phpstan-rules-beyond-core)**

<br>

Missing a package you love? Spot a rule that's described poorly? Open an issue - I'd love to make the index better.

<br>

Happy coding!
</content>
</invoke>
