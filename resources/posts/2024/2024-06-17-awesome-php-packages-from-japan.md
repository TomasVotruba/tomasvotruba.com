---
id: 414
title: "Awesome PHP Packages from Japan"
perex: |
    Last month, I spent in Japan to travel and absorb the local culture. I've also pinged a few friends I knew from Rector and AST projects in the past. I was surprised by how productive the PHP community is around here, with tools I'd personally use and ideas that inspire me.
---

One of them finished migration [from FuelPHP to Laravel](https://getrector.com/blog/success-story-of-automated-framework-migration-from-fuelphp-to-laravel-of-400k-lines-application) on 400 000+ lines project under a year. By himself without any of our help. That's astonishing and worth remembering. **Spectacular job, [rayjan](https://github.com/rajyan)!**

I was thinking, how many European devs do I know who work with AST at such an expert level? How many Japanese PHP devs do I know? It seems there is some hidden value in the Japanese PHP community that is not clear to me. I visited [PHP Asia 2018](https://2018.phpconf.asia/) with my very first Rector talk, and the local community understood it very quickly. So, I've decided to investigate more about this topic.

## How many Japan PHP packages do you know?

It's a surprise, how many applicable PHP packages and frameworks are developed in Japan. I bet you've used at least one of them before Composer 2.0 was out.

Why are they not so knowledgeable? I was surprised by how few local people speak English. Old or young, we met barely three people we could speak with fluently over the past 40 days. Why is that?

## What is the history behind the Language barrier?

We'd need a historian to explain the complicated reasoning behind the English barrier. So this is my amateur version: Japan cut of the world with **a strict lockdown for 250 years**. From 1620 to 1868, during the Meiji period, Japan developed its own culture and language internally.

In the same way that legacy projects build on the shoulders of its history, Japan works with its past historical isolation. I think that's why there is no desire to learn English to explore the outer world. I could not find a single article by Japan author written in English.

<br>

Do you like a **visual way of learning and want to dive deeply into historical context**? I recommend watching [Shogun TV series](https://www.imdb.com/title/tt2788316/), then [Silence by Martin Scorsese](https://www.imdb.com/title/tt0490215/) and at last, [The Last Samurai](https://www.imdb.com/title/tt0325710/). In this very order, to respect chronological order.

<br>

These packages had or have practical value, so I've decided to credit them in this post and share them with you. They might come in handy!

## hirak/prestissimo

* https://github.com/hirak/prestissimo

Yes, this is the package most well-known in the PHP community. It made `composer install` faster with parallel runs and was part of every CI build I've worked with. Composer 2.0 included this feature, so it is no longer needed. But yes, this package comes from Japan.

## koriym/spaceman

* [koriym/spaceman](https://github.com/koriym/spaceman)

We upgraded a project in 2024, where this tool would save dozens of hours. Some projects still don't have namespaces, or they emulate them. This tool can help you to migrate to real namespaces in a few minutes. This package is from Japan.

## BEAR.Sunday

* [bearsunday.github.io](https://bearsunday.github.io/)

Aki "Koriym" is the most well-known Japanese PHP developer. He's visited Europe many times.
He made the package above and the whole PHP framework, focusing on REST, dependency injection, and aspect-oriented programming.

## komtaki/visibility-recommender

* [komtaki/visibility-recommender](https://github.com/komtaki/visibility-recommender)

I've discovered this package in the best time possible. Right after I've [published a package with my own solution](/blog/how-to-add-visbility-to-338-class-constants-in-25-seconds). It was interesting to explore a different approach to the same problem, but both using an abstract syntax tree.

**What does the "visibility-recommender" package do?** It finds all class constants and their usage and recommends `private`, `protected`, or `public` visibility. Decision boundaries are clearly described in README, and it feels like poetry to read it.

This package is 4 years old, and I've discovered it only in 2024.

<br>

## Birds of a Feather Flock Together

In Japanese culture, I've felt like my mindset is at home. There is a high focus on precise, simple, reliable, and rigid work in every area.

From taxi drivers bowing and driving with gloves and commercial pilot-like uniforms over woodworkers building simple yet robust structures exclusively from wood, through huge displays in front of building sites that show the maximum allowed and current noise in DB that reconstruction produces.

<br>

## More than Meets the Eye

Japan seems like **an ideal pond for the birth of technical talents**. That's why Shinkansen trains that go 280-300 km/h on average have never had a fatal crash since their launch in the 1960s. To give this a perspective, in my country, two trains crashed with fatalities just last month.

Tokyo is a highly effective PHP think tank that **generates robust solutions to hard problems**. It has yet to be discovered.


<blockquote class="blockquote text-center">
"Life moves pretty fast.<br>
If you don't stop and look around once in a while, you could miss it."
</blockquote>

If you should take away one idea from this post, it would be this:<br>**Not every useful tool is popular.**

Not every great developer writes articles and speaks at conferences. There is so much to discover to save work, learn from each other and grow together.

<br>

I hope to see more PHP conferences in English in Japan! I would love to come and learn more.

<br>

Happy coding!
