---
id: 414
title: "Awesome PHP Packages from Japan"
perex: |
    Last months I've spent in Japan to work-travel and absorb local culture. Apart that, I've also pinged few friends I knew from Rector and AST projects in the past. I was surprised how productive the PHP community is around here, with tools I'd personally and ideas that inspire me.
---

One of them finished migration [from FuelPHP to Laravel](https://getrector.com/blog/success-story-of-automated-framework-migration-from-fuelphp-to-laravel-of-400k-lines-application) on 400 000+ lines project under a year. By himself without any our help. That's astonishing and worth remembering. Spectacular job, [Komtaki](https://github.com/komtaki)!

I was thinking, how many European devs I know who work with AST in such an expert a level? How many Japan PHP devs I know? It seem there is some hidden value in Japan PHP community, that is not clear to me. I've visited [PHP Asia 2018](https://2018.phpconf.asia/) with my very first Rector talk, and local community understood it very quickly. So I've decided to investigate more about this topic.

<br>

It's a surprise, how many useful PHP packages and frameworks are developed in Japan. I bet you've used at least one of them before Composer 2.0 was out.

Why they're now so know? I was surprised how very **few local people speak English**. Old or young, we met barely 3 people we could speak with fluently over past 40 days. Why is that?

## What is history behind Language barier?

I'm no historian to explain complicated reasoning behind the English barreer.
I came with my amateur version: Japan put the whole world into strict-lockdown for 250 yeras. From 1620 till 1868 and Meiji period, Japan has developed its own culture and language internally. The same way legacy projects build on shoulder of its history, the Japan works with its past histrical isolation. I think that's why there is no desire to learn English to explore the outer world.

Do you like visual way of learning and want to dive deeply into historical context? I recommend watching [Shogun TV series](https://www.imdb.com/title/tt2788316/), then [Silence by Martin Scorsese](https://www.imdb.com/title/tt0490215/) and [The Last Samurai](https://www.imdb.com/title/tt0325710/). In this very order, to respect chronological order.

<br>

These packages had or have practical value, so I've decided to give them a credit in this post and share them with you. They might come handy!

## hirak/prestissimo

* https://github.com/hirak/prestissimo

Yes, this is the package most well known in PHP community. It made `composer install` faster with parallel runs and was part of every CI build I've worked with. Composer 2.0 included this feature, so is no longer needed. But yes, this package comes from Japan.

## koriym/spaceman

* https://github.com/koriym/spaceman

We actually upgraded a project in 2024, where this tool would save dozens of hours. Some projects still don't have namespaces, or they emulate them. This tool can help you to migrate to real namespaces in a few minutes. This package is from Japan.

## BEAR.Sunday

* https://bearsunday.github.io/

Aki "Koriym" is the most well-known Japanese PHP developer. He's been to Europe and US many times.
He made the package above, but also whole PHP framework. With focus on REST, dependency injection and aspect-oriented programming.

## komtaki/visibility-recommender

* https://github.com/komtaki/visibility-recommender

I've discovered this package in the best time possible. Right after I've [published a package with my own solution](/blog/how-to-add-visbility-to-338-class-constants-in-25-seconds). It was interesting to explore different approach to the same problem, but both using abstract syntax tree.

What does "visibility-recommender" package do? It finds all class constants, their usage and recommends `private`, `protected` or `public` visibility. Decision boundaries are clearly described in README and it feels like a poetry to read it.

<br>

## Birds of a Feather Flock Together

In the Japenese culture, I've felt like my mindset is at its own home. There is high focus on precise, simple, reliable and rigid work in every area. From taxi drivers bowing and driving with gloves and pilot-like uniforms, over wood workers building simple yet robuts structures exlusively from wood, through huge displays in front of building sites, that shows maximum allowed and current noise in DB that reconstruction produces.

It looks like ideal pond to give birth technical talents. I guess that's why Shinkanses trains that go 280-300 km/h on average never had a single fatal crash since its launch in 1960ties. To give this a perspective, in my country 2 trains crashes with fatals just past month.

**Tokyo is highly effective PHP think-tank that generates robust solutions to hard problems**. It's yet to be discovered.

<br>

I hope to see more PHP conferences in English there. Would love to come and learn more.

<br>

Happy coding!
