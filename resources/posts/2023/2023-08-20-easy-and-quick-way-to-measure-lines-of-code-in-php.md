---
id: 391
title: "Easy and Quick way to Measure&nbsp;lines&nbsp;of&nbsp;Code in&nbsp;PHP"

perex: |
    The famous [phploc](https://github.com/sebastianbergmann/phploc) package to measure project size was archived by Sebastian on Jan 10, 2023. I used this package to get feedback on [CLI apps vendor shrink](/blog/unleash-the-power-of-simplicity-php-cli-app-with-minimal-dependencies) and for [fast estimation of project size in Rector upgrades](https://getrector.com/hire-team).

    **That's why I needed a replacement**. Fast!
---

There are a few forks *kind of* working, but they don't provide enum support, rely on PHP tokens, and conflict with installation.

You might also suggest generic Linux tools like `cloc`, but it requires specific operation system, thus different installation etc.  Also it doesn't provide **PHP-specific** metrics that will give [you better idea about project code quality](https://matthiasnoback.nl/2019/09/using-phploc-for-quick-code-quality-estimation-part-1/) :

* number of classes
* number of global functions
* number of public methods
* number of static methods
* interface and trait count etc.

<br>

I told myself, "Maybe we can use more reliable tooling to handle this, like php-parser," and I didn't stop there. I shared my idea on Twitter, and the feedback gave me the energy to think more deeply about this.

## What do we Need?

When we look at "why" such a package is used and what people need from it, we come to a few key points:

* there must be a **JSON output format**, for easy piping to next tool
* it must be **easy to install on PHP 7.2+**
* it must use standard dependencies like symfony/console, to make contributions effortless
* it must have scoped `/vendor`, so **anyone can install it on any project with `composer require`**
* it should have a short and pretty output so that we can use it in posts

<br>

A few days later, the prototype package was born:

<blockquote class="twitter-tweet"><p lang="en" dir="ltr">Using lines the first time for writing a post with real data:<br><br>What is a quick size of the vendor?<br><br>* Too long? Make it short 😉<br><br>* Too verbose? Make it json 😉 <a href="https://t.co/FRsqsNXUJE">pic.twitter.com/FRsqsNXUJE</a></p>&mdash; Tomas Votruba (@VotrubaT) <a href="https://twitter.com/VotrubaT/status/1686671043677523968?ref_src=twsrc%5Etfw">August 2, 2023</a></blockquote>

<br>

## 3. Steps to Measure Lines of your PHP project


1. Install [the lines package](https://github.com/tomasVotruba/lines)

```bash
composer require tomasvotruba/lines --dev
```

<br>

2. Run bin with paths to measure

```bash
vendor/bin/lines measure src
```

<br>

3. Adjust the output to fit your needs

```bash
vendor/bin/lines measure src --json --short
```

To get ↓

```json
{
    "filesystem": {
        "directories": 174,
        "files": 753
    },
    "lines_of_code": {
        "code": 42627,
        "code_relative": 65.4,
        "comments": 22545,
        "comments_relative": 34.6,
        "total": 65172
    }
}
```

*This command is perfect for blog posts, as it gives you a **idea about the size** without the clutter.*

<br>


That's it!


## Termwind on Board

Before I even managed to launch the package, Francisco jumped in and gave the CLI output a fresh and sexy look ↓

<br>

<blockquote class="twitter-tweet"><p lang="en" dir="ltr">Just did a PR to add 🍃 Termwind styling to the lines package made by <a href="https://twitter.com/VotrubaT?ref_src=twsrc%5Etfw">@VotrubaT</a> <br><br>Let me know what do you think! 👊<a href="https://t.co/fFRVYbpzj5">https://t.co/fFRVYbpzj5</a> <a href="https://t.co/Og9LrDUHSP">pic.twitter.com/Og9LrDUHSP</a></p>&mdash; Francisco Madeira (@xiCO2k) <a href="https://twitter.com/xiCO2k/status/1689052931125854208?ref_src=twsrc%5Etfw">August 8, 2023</a></blockquote> <script async src="https://platform.twitter.com/widgets.js" charset="utf-8"></script>

<br>

Give the *lines* a try, and if you want to improve the package, [just go for it](https://github.com/tomasVotruba/lines)!

<br>

Happy coding!
