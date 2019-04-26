---
id: 205
title: "Hidden Gems of PHP Packages: Static Analysis Results Baseliner"
perex: |
    Have you used PHPStan, Psalm or Easy Coding Standard on a very old project and got 10 000+ errors?
    <br>
    **Do you wish to skip fixing these 10 000 errors and check new code only?**
    <br>
    <br>
    Yes and yes? You'll love SARB.
tweet: "New Post on the #php 🐘 blog: Hidden Gems of PHP Packages: Static Analysis Results Baseliner by @daveliddament"
---

I found this package by random clicking on meetups on [Friends of PHP](https://friendsofphp.org/) and reading program of the meetup. One of them was Dave with his SARB - *Static Analysis Results Baseliner* tool. What an interesting name. In the Czech, I read *SRAB*, which means *coward*... what does such tool do?

## What Does SARB Help You With?

"SARB is used to create a baseline of these results. As work on the project progresses SARB can take the latest static analysis results, removes those issues in the baseline and report the issues raised since the baseline. SARB does this, in conjunction with it, by tracking lines of code between commits."

Such a simple idea with huge application in practice - amazing! What does it mean? Let me show you on a project:

- Let's say you add PHPStan to your project on 1. 4. 2019.
- It shows 10 000 errors, yay!
- You add SARB, get the baseline errors.
- Then you add new 10 new PHP classes on 15. 4 2019.
- Instead of getting 10 000 errors + 20 more on 10 PHP classes, **you get only those 20 new errors on new files since 1. 4. 2019**.

No stress with the legacy you didn't even write, but full focus on the present moment.

## How can You use it?

1. Install it

```bash
composer require dave-liddament/sarb --dev
```

2. Create a baseline file

```bash
# for psalm
vendor/bin/psalm --report reports/psalm-result.json
vendor/bin/sarb create-baseline reports/psalm-result.json reports/sarb_baseline.json psalm-json

# for phpstan
vendor/bin/phpstan analyse --error-format json > reports/phpstan-result.json
vendor/bin/sarb create-baseline reports/phpstan-result.json reports/sarb_baseline.json phpstan-json
```

3. Commit the code

4. Rerun the static analysis on the latest code

```bash
# for psalm
vendor/bin/psalm --report reports/psalm-result.json

# for phpstan
vendor/bin/phpstan analyse --error-format json > reports/phpstan-result.json
```

5. Use SARB to remove baseline results

```bash
# psalm
vendor/bin/sarb remove-baseline-results reports/phpstan-result.json reports/sarb_baseline.json reports/issues_since_baseline.json

# phpstan
vendor/bin/sarb remove-baseline-results reports/psalm-result.json reports/sarb_baseline.json reports/issues_since_baseline.json
```

That's it!

You can use this also on EasyCodingStandard:

```bash
vendor/bin/easy-coding-standard check src --output-format reports/ecs-result.json
vendor/bin/sarb remove-baseline-results reports/ecs-result.json reports/sarb_baseline.json reports/issues_since_baseline.json
```

The sky is the limit :)

<br>

Read more about the tool in [Introduction post](https://www.daveliddament.co.uk/sarb/introduction/) and in [README](https://github.com/DaveLiddament/sarb) of the project on Github.

Great job David!

<br>

Happy coding!
