---
id: 50
title: "How to Apply Nette Coding Standard in Your Project"
perex: |
    <strong>Pull-requests are more fun</strong> thanks to automated coding standard. You don't have explain where to put space or bracket. You <strong>can talk about architecture or meaning of the code</strong> instead. Moreover in open-source. <strong>I wanted to make this possible in Nette</strong>, but Coding Standards could be found only in <a href="https://nette.org/en/coding-standard">documentation</a>.
    <br><br>
    This year I started to work on a Nette Coding Standard (<em>NCS</em>) that you can put to CLI. And you'll <strong>be able set it up in in your project</strong> yourself today.
tweet: "How to setup #nettefw Coding Standard in your project? Local #ci or #travisci"
tweet_image: "/assets/images/posts/2017/nette-coding-standard/travis-check.png"

updated_since: "December 2018"
updated_message: |
    Updated to Nette CodingStandard 2.0 and <strong>EasyCodingStandard 5</strong>.
---

[Nette\CodingStandard](https://github.com/nette/coding-standard) 2.0 was released 2 months. This version **is ready to use, includes all important checkers and is used on all `Nette\*` packages in Travis**.

**NCS checks every pull-request you make to Nette**:

<div>
    <a href="https://travis-ci.org/nette/application/jobs/261987910#L349">
        <img src="/assets/images/posts/2017/nette-coding-standard/travis-check.png" class="img-thumbnail">
    </a>
</div>


All that need is to [define stage in `travis.yml`](https://github.com/nette/application/blob/2f545e64fc4bfc941d7e48a95e3faca7c468ac35/.travis.yml#L31-L41):

<div>
    <img src="/assets/images/posts/2017/nette-coding-standard/travis-setup.png" class="img-thumbnail">
</div>

That's it! Just 2 commands and it checks any project you have.

But first...


## PHP 7.1+

This packages requires PHP 7.1 to run as the rest of the Nette (mostly current `master` or `3.0`).

If you still don't know why should you **join [Symfony, Nette, Doctrine, Zend or Sylius](https://gophp71.org)**, you can read [this post](/blog/2017/06/05/go-php-71/#why-go-right-to-php-7-1) or wait a bit longer. It's up to you.

## Setup Your Project

Install the package to your dev dependencies:

```bash
composer require nette/coding-standard --dev
```

Good! Now just pick the prepared set.

At the moment there are **3 configs with set of checkers**:

- [`coding-standard-php71.yml`](https://github.com/nette/coding-standard/blob/master/coding-standard-php71.yml)
- [`coding-standard-php70.yml`](https://github.com/nette/coding-standard/blob/master/coding-standard-php70.yml)
- [`coding-standard-php56.yml`](https://github.com/nette/coding-standard/blob/master/coding-standard-php56.yml)

Just pick the one that suits you:

```bash
vendor/bin/ecs check src tests --config vendor/nette/coding-standard/coding-standard-php71.yml
```

Then, fix the code:

```bash
vendor/bin/ecs check src tests --config vendor/nette/coding-standard/coding-standard-php71.yml --fix
```

Config with higher PHP version includes all lower versions, so with `coding-standard-php71.yml` you cover the other 2 configs as well.

Happy coding!
