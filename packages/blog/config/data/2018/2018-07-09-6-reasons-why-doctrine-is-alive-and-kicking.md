---
id: 121
title: "6 Reasons Why Doctrine is Alive and Kicking"
perex: |
    Almost 1,5 year ago I wrote [Why is Doctrine Dying](/blog/2017/03/27/why-is-doctrine-dying/). I didn't use *dead*, because it's is just state of time being. Open-source projects - like people - tend to find themselves on the top, being stuck or struggling with the right path from time to time. It's a completely normal process of evolution.
    <br><br>
    I don't know if that post helped it, but since then many **things changed for better in Doctrine project**. Saying that this post deprecates my old view and celebrate changes.
    <br><br>
    **May this be an inspiration for open-source projects that find themselves stuck and the maintainers that find themselves unhappy**.
tweet: "New Post on my Blog: 6 Reasons Why is #Doctrine Alive and Kicking #orm #open-source #github #packagemanagement"
tweet_image: "/assets/images/posts/2018/doctrine-alive/contrib.png"
---

What's new in Doctrine and what might help you and your project to restart?

## 1. Bump PHP 7.1 Without Waiting for Major Release

Doctrine bumped **all packages** to min PHP 7.1. This change reduces a lot of features that have to be maintained. But that's not the best benefits in my opinion. The best thing is **the maintainer doesn't have to keep in mind all the clutter know-how** he or she probably uses only for this project, like arrays should be written in `array()`, there is no `void` yet or `yield` yet etc. This makes development much faster and enjoyable.

In [the official post](https://www.doctrine-project.org/2017/07/25/php-7.1-requirement-and-composer.html) they explain why this not a BC (back-compatibility) break and I must say I really like it and often refer to it:

<blockquote class="blockquote mt-5 mb-5">
    One question we frequently hear is, "isn't dropping support for a PHP version a BC break"? In a nutshell, no. <strong>A BC break happens when there is an incompatible change that your package manager can't handle</strong>. For example, changing a method signature in a minor version is a no-go, since the composer version constraints mentioned above assume any minor upgrade can safely be used.
    <br>
    <br>
However, when we drop support for an older version of PHP, <strong>composer will not consider the new version if the PHP version requirement is no longer fulfilled</strong>. Thus, you won't end up with a fatal error due to a wrong method signature, you just won't get the new version.
</blockquote>

Until we agree on regular PHP minor version bumping as a community, I think it's the best to jump to *the unicorn* versions. Those with the biggest impact, the most stable and helpful - and [go PHP 7.1](https://gophp71.org) as one.

## 2. Use Coding Standard

Although the coding standard is standard nowadays, it's not very easy to setup them to existing projects. Moreover more complex ones than a few basic rules. I'm very happy to see that Doctrine made this happen. The [`ruleset.xml`](https://github.com/doctrine/coding-standard/blob/master/lib/Doctrine/ruleset.xml) is quite rich.

Coding Standard makes contributing much more fearless since **you don't have to worry I'll get smashed in code-review by "extra space here" comment**.

Since it uses only PHP_CodeSniffer and not PHP CS Fixer, these still is a lot of manual work and space for huge cost-effective improvement. I tried to help with EasyCodingStandard implementation many months ago, but in that time ECS required PHP 7.1 and Doctrine not and used Neon of YAML to configure. Not anymore! [YAML is now default since ECS 4](/blog/2018/03/26/new-in-easy-coding-standard-4-clean-symfony-standard-with-yaml-and-services/), so the path is open from the technical point of view.

## 3. Cut the Weight to Save Yourself

Let's stay with YAML for a few more moments. There was PR to Doctrine to [remove all YAML references](https://github.com/doctrine/doctrine2/pull/5932) (mainly Entity mapping) in the time of writing my former post, but it was not clear when that will happen.

<img src="/assets/images/posts/2018/doctrine-alive/yaml-drop.png" class="img-thumbnail">

Now it's clear the Doctrine 3.0 will include this drop. This is very similar to PHP 7.1 change. The most healthy benefit is that maintainers don't have to constantly think about PHP, XML, YAML and Annotation support in everything they do - just in case it might be related to it. Instead, **the focus is now more narrow, clear and as a side effect - development is more enjoyable**.

<div class="text-center">
    <img src="/assets/images/posts/2018/doctrine-alive/balloon.jpg" class="img-thumbnail">
    <br>
    <em>
        It's like a hot air balloon.<br>
        If you have too many sandbags, you won't fly as high you want no matter how hard you try.
        <br>
        Drop just a few of them and you'll see how life becomes much lighter.
    </em>
</div>

### Get rid of Over-Support as part of Psychohygiene

*Over-support* is very common in open source. It happened to me in ECS, [I saw it ApiGen](/blog/2017/09/04/how-apigen-survived-its-own-death//) and almost burned out while getting rid of it (it also took me many months to even realize it and step out of it). **People request features, your project is popular, you gave people these features and that makes it more popular, so more people request features... it's challenging to keep on track when you're on celebrity power-trip**.

It's very healthy to be [selfish in open-source](/blog/2018/06/21/open-source-is-selfish//), not just for you for for the project to live and prosper.

## 4. Give People Vision to Follow

How can people orientate in the product, the package, the ideas, if you have no information about it? People need to know, what will happen when - at least approximately, but moreover in software that changes so fast. There were times when there was one post for a whole year [on Doctrine's blog](https://www.doctrine-project.org/blog). That changed!

The blog was refreshed, [fully-open sourced](https://github.com/doctrine/doctrine-website) and now runs on Sculpin, a project that I used before and get a lot of inspiration for [Statie](https://www.statie.org).

There is news about Doctrine ORM 3.0 about PHP 7.1 bump etc. Even these small notes give a great feeling of trust, of *something is going on* feeling, that creates a relationship.

## 5. From Talks and Post Evangelization to Code Improvements

I didn't really measure myself this so [I can't fake it](https://www.goodreads.com/quotes/300097-i-only-believe-in-statistics-that-i-doctored-myself), but I have a feeling that there are fewer talks and posts about Doctrine than years before. And that's a good thing. Why? Because this energy is now directed to the code.

<img src="/assets/images/posts/2018/doctrine-alive/contrib.png" class="img-thumbnail">

Don't take me wrong, both development and popularization are important, but if the project is not moving ahead, the popularization only vendor-lock know-ledge to slowly deprecating code.

<div class="text-center">
    <img src="/assets/images/posts/2018/doctrine-alive/hide.jpg" class="img-thumbnail">
    <em>
        So don't forget to hide from time to time from the public and deep-work on your project.<br>
        The world will wait in excitement for your news.
    </em>
</div>

## 6. New Release as a Baseline

Last but not least, take a time and [declutter your desk](http://how-to-stay-organized.blogspot.com/2011/08/declutter-desk.html) once a few months.

<img src="https://4.bp.blogspot.com/-uISp2pDSrTs/Tjr1Rr_07NI/AAAAAAAAAls/F3cq72YywMw/s320/computer-desk-before.JPG" width="400px" class="img-thumbnail">

↓

<img src="https://4.bp.blogspot.com/-W7YoCGWIlP4/Tjr1y2qZaxI/AAAAAAAAAlw/RWqZI7ECmfQ/s320/computer-desk-after.JPG" width="400px" class="img-thumbnail">

I think you know what a great feeling is to work with a clean desk. How does that relate to an open-source project?

Packages **tagging** is like [publishing a book](https://www.doctrine-project.org/2017/07/25/php-7.1-requirement-and-composer.html). You summarize all you know, all you did up-to-the one point of time. It's a big step, you celebrate it and... then you continue working on your next book.

<img src="/assets/images/posts/2018/doctrine-alive/packages.png" class="img-thumbnail">

**Tag from time to time just to put your work out, to get feedback, to share it with the world** so anyone can [steal it](/blog/2017/09/25/3-non-it-books-that-help-you-to-become-better-programmer/#steal-like-and-artist-by-austing-kleon).

<br><br>

And that's all folks. **I'm happy Doctrine is moving forward to the version 3.0 and I really look forward to it** - it will run on PHP 7.2, one more sandbag dropped to make code better.

How is your project doing? What do you do when you feel stuck for a while? Let me know in the comments.

