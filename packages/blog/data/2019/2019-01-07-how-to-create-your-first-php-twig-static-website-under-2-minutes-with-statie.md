---
id: 174
title: "How to Create Your First PHP Twig Static Website under 2 Minutes with Statie"
perex: |
    Do you like to write or create micro-sites? This post is for you.
    Statie is now [the most downloaded](https://packagist.org/packages/symplify/statie/stats) PHP Twig static site generators, even surpassing 7-years old [Sculpin](https://packagist.org/packages/sculpin/sculpin/stats) by 200 downloads a month. On the other hand, Sculpin is about to release [version 3](https://github.com/sculpin/sculpin/releases) creating healthy competition.
    <br><br>
    As you can see, **Static websites are on the rise in PHP** and they were never used more than now. It's time to make creating a new static website simple for everyone.
tweet: "New Post on #php 🐘 blog: How to Create Your First PHP #Twig Static Website under 2 Minutes with #Statie"

deprecated_since: "2020-03"
deprecated_message: |
    Statie was deprecated with last version 7, because 99 % of features are covered in Symfony application.<br>
    <br>
    To create static content, migrate to Symfony app and [SymfonyStaticDumper](https://github.com/symplify/symfony-static-dumper).
---

Until today, to create and understand static website basics, you had to learn:

- [Statie - part 1: How to run it Locally](/blog/2017/02/20/statie-how-to-run-it-locally/)
- [Statie - part 2: How to add Contact Page With Data
](/blog/2017/03/06/statie-2-how-to-add-contact-page-with-data)
- [Statie - part 3: How to Add Reusable Parts of Code](/blog/2017/03/09/statie-3-how-to-add-reusable-parts-of-code/)
- [Statie - part 4: How to Create The Simplest Blog](/blog/2017/03/13/statie-4-how-to-create-the-simplest-blog/)

**Don't read it! It's an utter waste of time.**

## Documentation is Dead, Long Live Automatization

In recent years, there is a trend towards **embodied knowledge**:

- Do you understand semver and compare package dependencies? - No, you use Composer.
- Do you test your code against every PHP type? - No, you use PHPStan.
- Do you type manually method names or class names? - No, you use PHPStorm (or any other IDE).
- Do you upgrade your code manually? - No, you use Rector.

In other words, **the knowledge is shifting from human brain storage to tool's abilities**.

Not the *smart knowledge* that helps us create algorithms, but the dummy knowledge like "what is the 3rd most largest river in the USA" that I can Google in 2-3 seconds (depending on internet connection in the train). We [don't become retarded](/blog/2017/12/04/life30-what-will-you-do-when-ai-takes-over-the-world/), we become more powerful to use our brains for what they're best at.

## Your First Statie in 2 minutes

<a href="https://github.com/symplify/symplify/pull/1285" class="btn btn-dark btn-sm">
    See pull-request #1285
</a>

```bash
composer require symplify/statie
```

Statie 5.3 brings 1 new command:

```bash
vendor/bin/statie init
```

See how it works:

<script id="asciicast-220449" src="https://asciinema.org/a/220449.js" async></script>

Do you prefer Latte over Twig?

```bash
vendor/bin/statie init --templating latte
```

Explore the files to find hints on how to use templates, even if you never used Twig or Latte ever before ( ← embodied knowledge).

<br>

Great job! You're up and running locally.

Do you want to **finish the Travis → Github Pages deployment**? Just follow [the official documentation](https://www.statie.org/docs/github-pages).

<br>

Happy coding!
