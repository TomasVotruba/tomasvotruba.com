---
id: 416
title: "PHP is the Best Choice for Long&#8209;Term Business"
perex: |
    Recently, I listened to Lex Friedman's [podcast with Pieter Levels](https://open.spotify.com/episode/6KBpL2XfR9VdojbKNpE7cX). Pieter talked about his technology stack for building startups: vanilla PHP, jQuery, and SQLite.

    Hype is exciting, but there is no better technology proof than long-term usage by sustainable business.

    The podcast inspired me to write my views about why PHP is the best choice for long-term.
---

Pieter talked about his hyper-focused approach to learning new skills. Yet he never had the need to learn Angular, React, Vue, or Next. Hype comes and goes.

<br>

Do you know this joke?

<blockquote class="blockquote text-center">
"A new JS framework is released every day."
</blockquote>

But it's no joke for a business that runs on it.

## Hype-driven development?

In 2016, a company contacted me to help upgrade their internal CRM. They used Angular 1 and wanted to go to Angular 2. It would require a complete rewrite.

Another path was to migrate to React, the "Facebook framework".

In 2018, Vue became the new competition. Another rewrite would be required to attract more developers.

Next, Alpine... there are dozens of new tutorials on YouTube on *the brand new JS framework*. They have 90 % feature overlap, just slightly different syntax sugar.

**This is a great fun for developers to learn and try new shiny things,<br>but a big problem for business costs**.

Imagine having a house where you have to replace all windows every winter. There are new standards, new dimensions and a style that doesn't fit the whole building.

## PHP has Long-term Stability you can Rely on

Now, let's examine the same situation from the side bank of the fence.

**What frameworks are used by majority of PHP developers?**
<br>
Symfony and Laravel.
<br>
Symfony 1 was released in 2007, and Laravel 1 was released in 2011.

Both are time-tested, community-driven, and, most importantly, still being used by businesses. At least based on the dozens projects we've helped to upgrade.

**We have 1 new PHP version released every year**, with a clear release path ahead:

* 4 minor versions (e.g. 8.1, 8.2, 8.3, 8.4)
* followed by a new major release with BC breaks (e.g. 9.0)

## PHP has Pro-Growth Competition

* If a market **has one big player**, it's a monopoly that degrades into stagnation. There is no space for new players or evolution. Until Apple and Linux came along, Microsoft was the only player in the game, and it dominated with terrible user experience and extreme license prices.

* On the other hand, if the market **has too many players**, it's a battlefield where customers are lost in the noise. In such an environment, you can hardly find a stable solution as it will soon be replaced by a new, well-funded competitor.

PHP is unique.

Did you know it's **the only programming language with 2 strong framework players**? **I'm profoundly grateful that we have such a market**. This competition keeps both Symfony and Laravel working hard to improve and innovate. It also gives us customers a choice based on personal feelings or preferences.

Ultimately, this gives companies a solid foundation they can build on for a decade.

## Healthy Ecosystem

This healthy competition has positive side effects in the community. Both organize a couple of conferences a year, where you can [meet great friends](/blog/why-I-migrated-this-website-from-symfony-to-laravel), find a job, or learn new skills.

If you're working in vanilla PHP like Pieter, there are [dozens of conferences](https://www.php.net/conferences/index.php) a year across Europe, America, Asia, and Australia.

The PHP ecosystem didn't get stuck on bare websites in a browser. You can build APIs, [desktop apps](https://nativephp.com/), [machine learning](https://php-ml.readthedocs.io/en/latest/), static analysis, and tools that improve the code for you. It's full circle.

## Self-reflecting Technology

Last but not least, other languages lack advanced technology.

[Nikita Popov](https://www.npopov.com/) came to PHP core in 2014 and [brought AST](https://wiki.php.net/rfc/abstract_syntax_tree) to PHP 7.0 core. It's not a PHP new feature we can use, but it made PHP core codebase (written in C) much simpler to work with. It's a similar jump to moving from HDD to SSD drive or from a button phone to a smartphone.

The problem is that PHP itself is written in C. Even the best PHP developer cannot code in C at a world language level. **If PHP stopped here, it would still be as good as its best C developer**.

But! In parallel, Nikita also created another tool. It opened up new possibilities to PHP developers—the [php-parser](https://github.com/nikic/php-parser).

<br>

<blockquote class="blockquote text-center mt-5 mb-5">
Imagine an AI that can be as smart to improve itself<br>
or robots as advanced to build better versions of themselves.
</blockquote>

<br>

I call this self-reflection technology. **It's an evolutionary level when a programming language can improve itself**. PHP is unique in this matter.

Javascript has a few such tools but are inconsistent because of many language dialects.

In the PHP community, **we have PHPStan, a tool that can find bugs in your code without running it**. Is there a new bug in your project that we missed? Add a new PHPStan rule, and it will never happen again. Never. It's a read-only tool, like the GPTs we have now. It doesn't work for us, but it's helpful at any moment we ask for help.

We also **have Rector, a tool that can improve and upgrade our code**. Most importantly, it can use the best current knowledge. Has someone in India come up with a better way to write a controller in PHP 9? They can create a Rector rule that anyone in the world can use without any knowledge about controllers or PHP 9.

<br>

**All other languages I know of are still stuck in the read-only phase**—the new features of languages are spread via articles, videos, or conference talks.

There is no tool you could run from CI to convert your Python 2 project to Python 3. However, there is a tool you can run to convert your PHP 5.2 project to PHP 8.4. **In the case of vanilla PHP, this can be done in a single day**, fully automated.

<br>

**That's why PHP is the best choice for long-term business costs.**

## Open-source Adaptation to the Future

I'm not saying PHP has the best syntax, generics support or CPU-level speed. It's about the whole package that business builds upon.

All of the **PHP tools mentioned are open-sourced** and not under commercial or benevolent dictatorship. If something changes&ndash;and it will&ndash;, we can adapt.

That's why I agree with Pieter and his vanilla PHP stack. It's not about the language, but the ecosystem around it, and PHP is the best for long-term business costs.

<br>

Happy coding!
