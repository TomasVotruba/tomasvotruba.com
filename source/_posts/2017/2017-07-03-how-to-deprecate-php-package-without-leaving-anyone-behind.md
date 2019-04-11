---
id: 45
title: "How to Deprecate PHP Package Without Leaving Anyone Behind"
perex: |
    You create PHP open-source packages because you personally use them in your projects. <strong>And you take care of them.</strong>
    In time you change a job or switch a programming language and you don't have time to take care of them properly. Number of issues and PRs grows and <strong>package is getting obsolete</strong>.
    <br><br>
    You can do 2 things in this situation: nothing like most people do or <strong>take responsibility, deprecate package and inform your users about better alternative</strong>.
tweet: "Deprecating #github #php package? This is safe and kind way to do it"
---

## Why Care About Deprecation as a Maintainer

I created over 10 packages for [Nette](https://nette.org) in the past under *Zenify* namespace. I used them in my projects extensively and they were growing in downloads. Then I switched to [Symfony](https://symfony.com) and I worked mostly with Symfony projects.

If I tell you this in person, you'd know that future of *Zenify* is not based on daily usage of it's author and you'd consider switching to concurrent packages.

**The thing is: 95 % of users of your package don't know about your personal development and life path. They use `composer require vendor/package-name` and that's it.** Of course, few people follow issues and PRs around your package and they might notice 6-months gap in activity.

### Open-Source Holidays

But in open-source these *pauses* are normal: you need to finish your work project, focus on boost of another package, you have some personal or family events (wedding, newborn child, break-up, moving to another country etc.), so you take a break.

**6-months pause gap and 6-months end-of-development pause look the same. But they are way different.**

This is the second case.

As [No more Mr. Nice Guy](https://www.amazon.com/No-More-Mr-Nice-Guy/dp/0762415339) would say:

## Don't do Anything in Secret

### A. If you DON'T inform people, they might:

- build new open-source or application that depends **on non-supported package**
- integrate your package deeply in their architecture
- **promote bad practice** that you don't support anymore but don't have time to put them in the package
- **wait in darkness** thinking "Author is on a vacation"

### B. If you DO inform people, they will:

- **be informed** - either notified by composer or on your blog (I will your show how later)
- **know** what part of their application won't be upgraded anymore
- **be able to plan** next upgrade much better

<br>

<img src="/assets/images/posts/2017/deprecate/trust.jpg" class="img-thumbnail">

## Packages like Relationships Stand on Trust

You have a meeting with a friend on Saturday on his birthday party. During the week you got close with a girl you like and you'd like to spend a weekend with her, because it's great opportunity to get to know her better. But what about your friend?

You can either:

- A. Wait it out and don't tell him anything. He probably wouldn't even notice.
- B. Call him, explain the situation and let him know, you'll come next week as alternative.

*What would you choose if you were your friend?*

Actually, **B builds foundations of great and strong relationship**, because people know they can trust you if anything difficult ever happens between you.


## Deprecate Package !== Delete Package

I thought when I deprecate package, application who use it as a dependency stops working. This could be **caused by deleting a package**, but not by deprecating it. Imagine **dropping from level 50 to 20**.

**Deprecating a package** is like **having level 50 and staying there** forever. It will never be worse, but it won't be better either. Deprecation won't break anything and won't improve anything.

<a href="https://seld.be/notes/php-versions-stats-2016-2-edition">
<img src="https://seld.be/images/update-reqs.png" class="img-thumbnail">
</a>

It works the same for releases. When you release version 2.0 that [requires PHP 7.1](/blog/2017/06/05/go-php-71/), it doesn't mean your package won't work on PHP 5.6. Version 1.0 still does.


## 3 Steps To Perform Safe Deprecation

### 1. Explain Why

"This package is deprecated" isn't satisfying, is it?

It's common psychological effect that people accept change with [**an explanation behind it**](https://startwithwhy.com/) more than without it.

### 2. Suggest Replacement

Software develops all the time. New and better packages are born everyday.
It so much helpful if you suggest a way to go. It doesn't have to share 100 % features of your package. **A package that you'd use if your package won't exist is fine**.

You can combine both [like this](/blog/2016/03/10/autowired-controllers-as-services-for-lazy-people/):

> Since Symfony 3.3 you can use PSR4-based service discovery and registration. It does pretty much the
same thing - registers autowired controllers (and more) - and it has native support in Symfony.
>
> I recommend using it instead!

### 3. Inform People on All Possible Places They can Meet Your Package

How many places are there to get in contact with your package? Programmer A added package to his composer, programmer B read about it on Github, programmer C saw a blog post that your wrote about it.

To make sure there are no deprecation leaks, put a sign on all sources:

<br>

**Packagist**

- Go to your package on Packagist, in my case [symplify/symfony-event-dispatcher](https://packagist.org/packages/symplify/symfony-event-dispatcher)
- Hit "Abandon" button

    <img src="/assets/images/posts/2017/deprecate/packagist-abandon.png" class="img-thumbnail">

- Pick a replacement

    <img src="/assets/images/posts/2017/deprecate/packagist-replacement.png" class="img-thumbnail">

- And confirm

    <img src="/assets/images/posts/2017/deprecate/packagist-abandoned.png" class="img-thumbnail">

Now you hope that everybody is going to packagist to check if any of packages they're using are abandoned and seek their replacement... No it's not so painful.

Composer will tell you for every `composer require/update` that includes this package from now on:

<img src="/assets/images/posts/2017/deprecate/composer-info.png" class="img-thumbnail">

<br>

**Github Repository**

The best way to make people know the package is deprecated on Github **is not by a note in `README`**. Nobody reads readme if he doesn't use the package first time. Note in a description is also small to spot.

Much more effective **by changing an organization**.

(And if you still need an access to the package, let me know. I'll add it for you.)

- Go to your package on Github, in my case [Symplify/SymfonyEventDispatcher](https://github.com/Symplify/SymfonyEventDispatcher)
- Go to *Settings*
- Scroll down to *Danger Zone* and Pick *Transfer*

    <img src="/assets/images/posts/2017/deprecate/github-danger-zone.png" class="img-thumbnail">

- Fill in the package name and ["DeprecatedPackages" organization](https://github.com/DeprecatedPackages)

    <img src="/assets/images/posts/2017/deprecate/github-transfer.png" class="img-thumbnail">

- And you're done!

This is the most obvious way to let people know on Github.

If this process is too difficult for you, you can [add a "deprecated" note to README title](https://github.com/DeprecatedPackages/ControllerAutowire#controller-autowire---deprecated-in-core-of-symfony-33) as well.


**The League of Deprecated Packages**

<a href="https://github.com/DeprecatedPackages">
    <img src="https://avatars0.githubusercontent.com/u/22506867?v=3&s=200" class="img-thumbnail">
</a>

*Thanks to [Milan Šulc](https://f3l1x.io/) for making this beautiful logo that tells all the story.*

I've come with this organization a year ago, when me and my friends needed to deprecate more package together. As some of you already figured out, it's a place to put all deprecated packages into.

<br>

**Blog Post(s) about Package**

One the most underestimated places is a blog post. I consider it the most important place for people who don't know the package yet, but might start using it. I'd feel bad if people would start using package **after I deprecated them**, instead of reaching out for the replacement right away.

I've [created a simple warning system](https://github.com/TomasVotruba/tomasvotruba.cz/pull/88) on my blog:

- Open post about the package.
- Add warning above the perex with reasoning and suggested replacement:

    <img src="/assets/images/posts/2017/deprecate/blog-deprecate-note.png" class="img-thumbnail">

- Profit! Thousands of programmer pain-hours saved.


## What's in it For You as Maintainer?

This all you do is for package users - what a great and altruistic person you are!
**But it has one great upside for you as well.** When I've [deprecated 5 of Symplify packages](/blog/2017/05/29/symplify-packages-deprecations-brought-by-symfony-33/) I was sad to lose a legacy, my baby, my work... **But it's worth it!**

In following weeks I found:

- I have **more energy** to work on the rest of packages,
- I have **much less responsibility** so I can breathe more lightly,
- and I can put more work into less projects

Thanks to that, I've [added new features to EasyCodingStandard](/blog/2017/05/03/combine-power-of-php-code-sniffer-and-php-cs-fixer-in-3-lines/), released [Object Calisthenics Coding Standard 3.0](/blog/2017/06/26/php-object-calisthenics-rules-made-simple-version-3-0-is-out-now/) and [released ApiGen 5.0-RC2](https://github.com/ApiGen/ApiGen/releases/tag/v5.0.0-RC2). The last one is secret in progress, so don't tell anybody.
