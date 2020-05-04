---
id: 25
title: "How Monolithic Repository in Open Source saved my Laziness"
perex: |
    I've started creating open-source about 6 years ago. Now I'm maintaining over 20 repositories. I used classic standalone repositories, but with each new package I wanted to add, I realized, how much work it needs to keep everything up-to-date and consistent. So I didn't and got stuck.
    <br><br>
    Fortunately, I noticed <a href="https://www.youtube.com/watch?v=4w3-f6Xhvu8">talk from Fabien</a> about <a href="http://danluu.com/monorepo/">monorepo</a> and Symfony. I said to myself: "I don't know a thing about it. Let's try it out. I can always return if it sucks."
    <br><br>
    I never did. Today I will show you <strong>why I see monorepo approach in open-source so awesome</strong>.
tweet: "How #symfony and #laravel can manage so many packages with ease? #composerPHP #monolith #php"
---

If you don't have 50 minutes to watch the talk (my case), here are **[40 slides](https://speakerdeck.com/fabpot/a-monorepo-vs-manyrepos)** from it.

Fabien introduces a tool, that helps you with splits - [splitsh](https://github.com/splitsh/lite). Do you understand it? Me neither. Splitsh is fast yet complex tool to maintain Symfony and Blackfire ecosystem. **All we need is one git command**.

But we'll get to that later. First things first.


### What is Monorepo?

**Monorepo** (for monolithic repository) is single repository, which contains code for group of packages (framework, application...).

Not only Symfony, but also:

- [Laravel](https://github.com/laravel/framework)
- [Sylius](https://github.com/Sylius/Sylius)
- [Elcodi](https://github.com/elcodi/elcodi/) - which is dead now

Opposed to this is **manyrepo** approach (for many repositories), meaning every package is in his own repository.

It is used by:

- [Nette](https://github.com/nette/)
- [Doctrine](https://github.com/doctrine)
- [PHPLeague](https://github.com/thephpleague)

and almost everybody else.


So how does this monorepo work?


## From One Heart to Many Arteries

Imagine flow of a oxygenated blood from heart to arteries. All blood that was in heart, will drift to all arteries.

<img src="/assets/images/posts/2017/monorepo/blood-vein.png" alt="Blod vein" class="img-thumbnail">

From **1 heart** to **many arteries** in one direction. Same relation is between **1 monolithic repository** and **many repositories**.


This is related to commits and tagging:

- **Every commit** that does changes is Symfony\Console code in monorepo, is also in Symfony\Console manyrepo.
- **Every tag** in monorepo, is also in all manyrepos.


## What Are top 4 Advantages of Monorepo?

### 1. It scales

You have one repo to maintain.

- New package? Not a problem.
- Changing name of method that is used by 10 other packages? Easier than ever.
- Starting a framework? Ideal.

That's the way Nette, Symfony and Laravel started and grew so much, even if only one person was behind majority of commits.


### 2. Upgrade Fast, Upgrade Safe

It's easy to make changes that affect all packages - bump to PHP 7.1, Symfony 3.2 or Nette 3.0 is a matter of minutes.
And I know it works on all packages. Before monorepo, I had to upgrade every package manually, which resulted in dissonance:
one package used Symfony\Console 3.2, but other only 2.8 and it got messy for no reason.


### 3. Test Both on Monorepo and Manyrepo Level With Ease

Another thing I love is testing both monorepo (all packages together) and manyrepo level. Once I spend 5 hours fixing a bug in Symfony\Process. It was difficult to find, because all tests were passing. Even for testing just the Symfony\Process directory made it pass. But when I copied only the Symfony\Process code to the standalone directory, it failed. Few hours later I found out, it's somehow related to having Symfony\Stopwatch package. Yea, WTF.

**That could be caught testing on monorepo level.**

On the other hand, monorepo testing is also important. When Nette [was split from monorepo to manyrepo only](https://phpfashion.com/prave-jsem-smazal-nette-framework), all tests were passing packages were standalone. But in combination some of them didn't.

This is not issue of the code itself, but of the testing architecture.


### 4. The Burnout is Much More Harder

When maintaining 15 own packages, [ApiGen](https://github.com/Apigen) and co-maintaining few more repositories, I spent a lot of time by package management and not coding. It wasn't fun and I contributed less and less.

Many packages like [Doctrine](https://github.com/doctrine) or [Kdyby](https://github.com/Kdyby) are slowing down in evolution because of this.

Again, it's a matter of project architecture rather than the code or maintainers.


These are the best for me, but there are many more described in [those slides](https://speakerdeck.com/fabpot/a-monorepo-vs-manyrepos) by Fabien.


## Example: How it's done in Symplify

### Monorepo

[symplify/symplify](https://github.com/symplify/symplify)

```bash
/packages
    /Symplify
        /DefaultAutowire
            /src
            /tests
            composer.json
```

### Run Git Command

Inspired by [Laravel](https://github.com/laravel/framework/tree/17ee3fd536d1db54dd4ae117c5665b6d03761337/build), all we really need is one git command. To split:

```bash
git subsplit init git@github.com:symplify/symplify.git
git subsplit publish --heads="master" packages/DefaultAutowire:git@github.com:Symplify/DefaultAutowire.git
rm -rf .subsplit/
```

It says: take code from `packages/DefaultAutowire` directory and put it to `git@github.com:Symplify/DefaultAutowire.git` repository.

### Manyrepo

[Symplify/DefaultAutowire](https://github.com/Symplify/DefaultAutowire)

As a result, we have this:

```bash
/src
/tests
composer.json
```

That's it! Could it be simpler?


### Further reading - Local Packages

I recommend to read [Monolithic Repositories with PHP and Composer](http://www.whitewashing.de/2015/04/11/monolithic_repositories_with_php_and_composer.html) by Benjamin Eberlei (Doctrine Core Maintainer). It's surpassed by composer *local packages*, but points remain the same. Don't worry, I will write about *local packages* in the near future.

If you like monorepo approach, but prefer own tagging per package, Martin Zlámal recently wrote about [git submodule approach](http://zlml.cz/vy-jeste-nemate-svuj-superprojekt) (in Czech).


### How do you like this?

This is my point of view.

Do you maintain lot of repositories? How do you make it fun to code and care for all of them?

Let me know in the comments. Thank you.
