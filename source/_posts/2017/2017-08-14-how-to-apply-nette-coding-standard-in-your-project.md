---
id: 50
title: "How to Apply Nette Coding Standard in Your Project"
perex: '''
    <strong>Pull-requests are more fun</strong> thanks to automated coding standard. You don't have explain where to put space or bracket. You <strong>can talk about architecture or meaning of the code</strong> instead. Moreover in open-source. <strong>I wanted to make this possible in Nette</strong>, but Coding Standards could be found only in <a href="https://nette.org/en/coding-standard">documentation</a>.
    <br><br>
    This year I started to work on a Nette Coding Standard (<em>NCS</em>) that you can put to CLI. And you'll <strong>be able set it up in in your project</strong> yourself today.
'''
related_posts: [49]
tweet: "How to setup #nettefw Coding Standard in your project? Local #ci or #travisci"
tweet_image: "assets/images/posts/2017/nette-coding-standard/travis-check.png"
---

[Nette\CodingStandard](https://github.com/nette/coding-standard/) version [0.5](https://github.com/nette/coding-standard/releases/tag/v0.5.0) with important bug-fixes was released a week ago. This version **is ready to use, includes all important checkers and is used on all `Nette\*` packages in Travis**.

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

If you still don't know why should you **join [Symfony, Nette, Doctrine, Zend or Sylius](https://gophp71.org/)**, you can read [this post](/blog/2017/06/05/go-php-71/#why-go-right-to-php-7-1) or wait a bit longer. It's up to you.


## Setup Your Project

You have 2 options how to use NCS in your project.


### 1. As a Composer Project

Nette packages require this approach, because NCS depends on many Nette packages. **NCS should be installed to standalone directory**, so changing the Nette code by NCS doesn't break NCS.

The easiest way is to setup `.travis.yml`:

```bash
install:
    - composer create-project nette/coding-standard temp/nette-coding-standard

script:
    - temp/nette-coding-standard/ecs check src tests --config temp/nette-coding-standard/coding-standard-php71.neon
```

And you are ready to go!


### 2. As a Composer Dev Dependency

I prefer this in projects, where I **want to check coding standards locally and have dependencies up-to date**. It's easier than composer project, where you need to remember to update dependencies manually in the directory.

```bash
composer require nette/coding-standard --dev
```

Check the code:

```bash
vendor/bin/ecs check src tests --config vendor/nette/coding-standard/coding-standard-php71.neon
```

Fix the code:

```bash
vendor/bin/ecs check src tests --config vendor/nette/coding-standard/coding-standard-php71.neon --fix
```



## 3 Configs based on PHP version

Do you need to **check code that is not PHP 7.0 ready**? You can.


At the moment there are 3 configs with set of checkers (click to see their content):

- [`coding-standard-php56.neon`](https://github.com/nette/coding-standard/blob/2f935070b82fbe4b1da8e564a8dc6dcb9bbeca25/coding-standard-php56.neon)
- [`coding-standard-php70.neon`](https://github.com/nette/coding-standard/blob/2f935070b82fbe4b1da8e564a8dc6dcb9bbeca25/coding-standard-php70.neon)
- [`coding-standard-php71.neon`](https://github.com/nette/coding-standard/blob/2f935070b82fbe4b1da8e564a8dc6dcb9bbeca25/coding-standard-php71.neon)

**Config with higher PHP version includes all lower versions**, so with `coding-standard-php71.neon` you cover the other 2 configs as well.


For PHP 5.6 it would like this:

```bash
vendor/bin/ecs check src tests --config vendor/nette/coding-standard/coding-standard-php56.neon
```


Happy coding!