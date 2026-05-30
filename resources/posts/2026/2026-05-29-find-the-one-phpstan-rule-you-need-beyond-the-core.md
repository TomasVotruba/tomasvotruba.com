---
id: 437
title: "Find the One PHPStan Rule You Need, Beyond the Core"
perex: |
    PHPStan core gets you to level 10 with solid type checks. But some gems, the rules that catch *your* anti-patterns, live scattered across a dozen community packages most people never hear about.

    I got tired of hunting through GitHub READMEs to find "is there a rule for this?", so I built a single searchable page that indexes 11 custom PHPStan rules packages.
---

You spot the same code smell in pull request after pull request - a `static` method here, a forbidden `dd()` in production there - and you think:

<blockquote class="blockquote text-center">
"Surely someone already wrote a PHPStan rule for this?"
</blockquote>

They probably did. The problem is finding it.

<div class="note-box">
    <p>We had the same problem with <a href="http://github.com/rectorphp/rector">Rector</a> rules - so many that people kept re-implementing ones that already existed. I built the <a href="https://getrector.com/find-rule">"Find rule" page</a> to fix it.</p>
</div>

<br>

These project-specific rules live in community packages: `symplify/phpstan-rules`, `shipmonk/phpstan-rules`, `ergebnis/phpstan-rules`, `spaze/phpstan-disallowed-calls`, and a long tail of smaller ones.

Each ships its own README and naming, often with just a generic package description. To answer "is there a rule for X?", you'd open a dozen tabs - nobody does that. **So great rules sit unused, only their authors know about them, and we keep eyeballing the same problems by hand in code review**.

## One page, Every rule

So I made a single page that indexes them all: **[PHPStan Rules Beyond Core](/phpstan-rules-beyond-core)**

<br>

A searchable index of **260+ rules** from **11 popular community packages**. Each entry shows:

* the rule class you drop into `phpstan.neon`
* a plain-English description of what it does
* the message it reports when it fires
* and - where documented - a wrong/correct code example side by side

<br>

## Search the way you think

Discovery is the point, so type what's on your mind - no exact class name needed:

* search "forbidCast" and casting rules rank first
* mistype "noreturn" as one word and it still finds "No" + "return"


<br>

...or ["final abstract"](https://tomasvotruba.com/phpstan-rules-beyond-core?q=final+abstract) - it marks all non-abstract methods of `abstract` classes as `final`. Surprisingly useful; fixed 2 cases on my own site.

<br>

## From "I wish" to installed in one click

Found one? Each package block has a ready-to-copy install line:

```bash
composer require --dev symplify/phpstan-rules
```

<br>

Then register the rule in your `phpstan.neon`:

```yaml
rules:
    - Ergebnis\PHPStan\Rules\Methods\FinalInAbstractClassRule
```

...and run PHPStan:

```bash
vendor/bin/phpstan
```

Keep this one or explore the full package. That's how PHPStan rules should work - **solve one specific issue and catch it in CI before we even tell our agent**.

<br>

Missing a package? Open [an issue](https://github.com/TomasVotruba/tomasvotruba.com/issues) or [send a PR](https://github.com/TomasVotruba/tomasvotruba.com/pull/1540) (this site is 100 % open source) - I'd love to improve the index.

<br>

Happy coding!
