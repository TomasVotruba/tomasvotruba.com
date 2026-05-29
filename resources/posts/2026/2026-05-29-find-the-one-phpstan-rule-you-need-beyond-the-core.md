---
id: 437
title: "Find the One PHPStan Rule You Need, Beyond the Core"
perex: |
    PHPStan core gets you to level 10 with solid type checks. But some gems, the rules that catch *your* anti-patterns, live scattered across a dozen community packages most people never hear about.

    I got tired of hunting through GitHub READMEs to find "is there a rule for this?", so I built a single searchable page that indexes 11 custom PHPStan rules packages.
---

You know the feeling. You spot the same code smell in pull request after pull request - a `static` method here, a forbidden `dd()` left in production code there - and you think:

<blockquote class="blockquote text-center">
"Surely someone already wrote a PHPStan rule for this?"
</blockquote>

They probably did. The problem is finding it.

We had similar problem with [Rector](http://github.com/rectorphp/rector) rules.
There is so many that some people requested or even made a copy of a rule that already exists. I created ["Find rule" page](https://getrector.com/find-rule) to fix that.

<br>

The rules that catch project-specific patterns live in community packages: `symplify/phpstan-rules`, `shipmonk/phpstan-rules`, `ergebnis/phpstan-rules`, `spaze/phpstan-disallowed-calls`, and a long tail of smaller ones.

Each ships its own README, its own naming, configuration, or only generic descsription of what the whole package does. To answer "is there a rule for X?", you'd have to open a dozen tabs and skim each one. Nobody does that. **So great rules sit unused, only their authors know about them, and we keep eyeballing the same problems by hand in code-review comments**.

## One page, Every rule

So I made a single page that indexes them all: **[PHPStan Rules Beyond Core](/phpstan-rules-beyond-core)**

<br>

It's a searchable index of **260+ individual rules** scraped from **11 of the most popular community packages**. Each entry shows you:

* the rule class you drop into `phpstan.neon`
* a plain-English description of what it does
* the message it reports when it fires
* and - where the package documents it - a wrong/correct code example side by side

<br>

## Search the way you think

The whole point is discovery, so the search is built for it. Type what's on your mind, no need for exact class name:

* search "forbidCast" and it ranks the casting rules first
* mistype "noreturn" as one word and it still finds "No" + "return"


<br>

...or ["final abstract"](https://tomasvotruba.com/phpstan-rules-beyond-core?q=final+abstract) - I found this rule thanks to this mini project and it's surprisingly useful: marking all `abstract` classes' non-abstract methods with `final`. Never though of that, but that's how I want to use `abstract` classes. Fixed 2 cases on my website.

<br>

## From "I wish" to installed in one click

Found a rule you want? Each package block has a ready-to-copy install line:

```bash
composer require --dev symplify/phpstan-rules
```

Copy the line and install the package.

<br>

Then register the rule to your `phpstan.neon`:

```yaml
rules:
    - Ergebnis\PHPStan\Rules\Methods\FinalInAbstractClassRule
```

...and run PHPStan:

```bash
vendor/bin/phpstan
```

If this rule got you interested, explore the full package the rule comes from. Or just stay with this one and get back to work. That's how PHPStan rules should work - **to solve single specific issue and catch it in CI sooner than we tell our agent**.

<br>

Missing a package you love? Do you see and improvement? Open [an issue](https://github.com/TomasVotruba/tomasvotruba.com/issues) or [send a PR](https://github.com/TomasVotruba/tomasvotruba.com/pull/1540) (this website is 100 % open source) - I'd love to make the index better.

<br>

Happy coding!
