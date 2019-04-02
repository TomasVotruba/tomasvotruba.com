---
id: 193
title: "Why we Migrated from Nette to Symfony in 3 Weeks - Part 3 - Brain Drain Dead Packages-Lock"
perex: |
    Do you want to **migrate your project from Nette to Symfony**? In [the part 2](/blog/2019/03/07/how-we-migrated-from-nette-to-symfony-in-3-weeks-part-2/) we looked at **escaping semver hell**.
    <br><br>
    Today we'll look on **package vendor-locks** caused by brain drain.
tweet: "New Post on #php 🐘 blog: Why we Migrated from #nettefw to #symfony in 3 Weeks - Part 3"
tweet_image: "/assets/images/posts/2019/nette-to-symfony3/nette-object-your-code.png"
---

*This post will be a bit harder for me because I'm part of this problem. I'm a former Nette activist and open-source hyper. I created many open-source packages Nette developers use, but those are not actively maintained nor developed for the last couple of years. I'm sorry about that.*

<br>

**If you love Nette, [keep using it](/blog/2018/05/31/symfony-vs-laravel-vs-nette-which-php-framework-you-should-choose/). This post is for people, whose companies are hurt by being locked to Nette ecosystem and who want to solve that but don't know how or if that is even possible.**

## February 2017...

When we talked with [Honza](https://janmikes.cz/) about framework A → framework B migration first related to [Entry.do](https://entry.do/), it was on the first PHP Mountains 2017. Rector was still 5 months before being born, so manual work was the only way.

First, we looked into `composer.json` and tried to get rid of some packages. What packages we won't need on Symfony?

- `kdyby/events`
- `kdyby/console`
- `kdyby/doctrine`
- `kdyby/rabbitmq`
- `kdyby/redis`
- `kdyby/translation`
- `zenify/doctrine-behaviors`
- `zenify/doctrine-migrations`
- `zenify/doctrine-filters`
- `zenify/modular-latte-filters`
- `zenify/doctrine-fixtures`
- `zenify/doctrine-extensions-tree`

**We could drop all these**, because:

- `kdyby/*` is basically integration of Symfony packages with `nette/di`,
- and `zenify/*` is basically `doctrine/*` integration with `nette/di`

Of course, you can't delete them right away. Yet, [Rector covers most of this migration now](/blog/2019/02/21/how-we-migrated-from-nette-to-symfony-in-3-weeks-part-1/#3-automated-migration-gt-manual-changes), so this part is fine.

## Upgrade Lagging

We decided to remove Symfony/Doctrine *glue packages* first, so we could work with fewer dependencies and be more flexible. And upgrade PHP first, so we can use right the newest Symfony packages.

We tried to remove one `zenify/*` package (because it's small → possibly easy to replace) and use the package we have in control.

### `object`

Thing is, PHP 7.2 introduced `object ` keyword:

<img src="/assets/images/posts/2019/nette-to-symfony3/nette-object-easy.png" class="img-thumbnail">

Nette had class `Nette\Object` that was actively promoted as the parent of all classes in your code. Now it had **to be removed from all these classes and replaced by trait*:

```diff
 <?php

-class SomeClass extends Nette\Object
+class SomeClass
 {
+    use Nette\SmartObject;
 }
```

*If you still have this problem, [use Rector that handles this case](https://github.com/rectorphp/rector/blob/master/docs/AllRectorsOverview.md#parentclasstotraitsrector).*

<br>

In that time, we had to use PHPStorm old-school *find & replace* with regex pattern:

<img src="/assets/images/posts/2019/nette-to-symfony3/nette-object-your-code.png">

The upgrade path is like a road with holes. It's getting crappy, but there is still at least some road 👍

**But what if there are many packages**, that nobody actively takes care of?

<img src="/assets/images/posts/2019/nette-to-symfony3/nette-object-in-3rd-party.png">

**Until the maintainer fixes that, the upgrade path is closed**. We'd have to fork every Nette package that is not maintained, fix it manually in the code and add them into `composer.json`.

We don't talk about small package with few classes that is easy to rewrite:

<img src="/assets/images/posts/2019/nette-to-symfony3/downloads.png" class="img-thumbnail">

Most of Kdyby packages still have [500-900 daily downloads](https://packagist.org/packages/kdyby/doctrine/stats). Even if we take CI servers into account, that still **might be 120-150 PHP applications** locked to legacy with packages that no-one maintains.

<blockquote class="blockquote text-center mt-5 mb-5">
    "When you stop growing, you start dying."
</blockquote>

## Nette Brain Drain?

With a healthy active community as in Laravel or Symfony, there would be PR and we never came across this problem. What's different with Nette?

David Grudl, the author of Nette [tweeted at the end of 2013](https://twitter.com/geekovo/status/417869320677367808) that **he ends with Nette**. "One tweet", you might think, but there was more of similar news on Nette forum and popular Czech IT blogs.

Many years later, when I become a Symfony consultant, I asked companies why did they choose Symfony over Nette. After all, there were Nette meetups in our the Czech Republic every month and maybe 2 non-Nette PHP meetups about other frameworks. The answer was almost unanimous: when they discussed what PHP framework to use, they saw David's tweet. **They needed something stable they could for the next 5 years.**

<br>

In following years, without anyone noticing, **slow brain drain from Nette to Symfony, Java or Javascript** started:

- [Filip Procházka](https://prochazka.su/), the author of Kdyby → is now Java programmer
- [Patrik Votoček](https://patrik.votocek.cz/), one of first Nette evangelist and author of Nella →  switched to Symfony, then to chaos monkey,
- [Martin Zlámal](https://github.com/mrtnzlml), very active Nette evangelist who held many Nette/PHP talks on university → now works with Javascript at Kiwi.com
- [Jáchym Toušek](http://enumag.cz/), active Symfony to Nette integrator → switched to Symfony
- I, author of Zenify and Symnedi → switched to Symfony
- ...

**Many of open-source packages for Nette slowly become unmaintained.** So this error is new status-quo for these packages:

<img src="/assets/images/posts/2019/nette-to-symfony3/nette-object-in-3rd-party.png">

<br>

When we realized with Honza that night, that to upgrade project means "fork every unmaintained dependency and I hope there will be better times", we stopped. But **the motivation remained and 2 years later**, with better skills and Rector, we managed to migrate the application from Nette to Symfony in less than a month.

<br>

## Come to Meetup and Tell Us About Your Problem

Are you stuck with Nette at home and thinking about in your wet dreams Symfony? This is your lucky week! :)

Honza will talk about Nette to Symfony migration on **[PHP meetup in Prague this Thursday - 14. 3.](https://www.meetup.com/friends-of-php-prague/events/259627000/)**

Entrance free, language is English and I'll be there too!

<br>

Happy coding!
