---
id: 223
title: "All You Always Wanted to Know About Monorepo But Were Afraid To Ask"
perex: |
    Do you want to know what is monorepo and why/when you should use it?
    Do you look for brief source that will answer your questions in simple and understandable manner?
    <br>
    This is it.
tweet: "New Post on #php 🐘 blog: All You Always Wanted to Know About #Monorepo But Were Afraid To Ask"
---

## What is Monorepo?

A repository **that contains multiple packages** or projects. Those projects can, but **don't have to be related**. Most famous monorepo pioneers are Google, Facebook and Twitter.

The most famous monorepos in PHP are Symfony, Laravel, Symplify, Sylius, Yii2 or Shopsys.

<hr>

## Monorepo vs. Multirepo

### Single-repo or split-repo?

Monorepo is split into many single-repos, e.g. [Symfony/Symfony](https://github.com/symfony/symfony) is split into [Symfony/Console](https://github.com/symfony/console), [Symfony/Validator](https://github.com/symfony/validator) etc. Each single-repo repository is read-only. You can change its code via pull-request to the monorepo.

### Many-repo

The other approach to manage multiple repositories. 1 package = 1 own repository. Each package has it's own development, tagging and even maintainers. E.g. [Doctrine 2](https://github.com/doctrine) or [Nette 2](https://github.com/nette).

### Monolith

Monolith ≠ monorepo. Monolith is huge amount of coupled code of 1 application that is hell to maintain.

<hr>

## Why is Monorepo so Awesome?

* Simplified organization
* Easy to coordinate changes across modules.
* Simplified dependencies
* Single lint, build, test and release process

* Tooling
* Single place to report issues
* Cross-project changes
* Tests across modules are run together → finds bugs that touch multiple modules easier

*These are cherry-picked reasons from legendary [Advantages of Monolithic Version Control](https://danluu.com/monorepo). Read it to get deeper insight.*

---

## Tools that make using Monorepo Easy

* [Symplify/MonorepoBuilder](https://github.com/symplify/monorepobuilder) - simpler, written in PHP
    * init, setup and auto-split monorepo in minutes - great for start from scratch

* [shopsys/monorepo-tools](https://github.com/shopsys/monorepo-tools) - advanced, shell scripts
    * init, setup and auto-split monorepo in minutes - great for start from scratch
    * merges history of multiple repos to one and more - great for start for code with many repositories with long history

## What to Read Next?

* [Monorepo: From Zero to Hero (2018)](/clusters/#monorepo-from-zero-to-hero)
* [Why Google stores billions of lines of code in a single repository (2016)](https://dl.acm.org/citation.cfm?id=2854146)
* [Monorepos in Git (2015)](https://developer.atlassian.com/blog/2015/10/monorepos-in-git)
* [korfuri/awesome-monorepo](https://github.com/korfuri/awesome-monorepo)
