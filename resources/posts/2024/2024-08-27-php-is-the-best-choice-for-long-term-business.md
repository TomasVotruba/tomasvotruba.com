---
id: 416
title: "PHP is the Best Choice for Long-Term Business"
perex: |
    Recently I've listened Lex Friedman [podcast with Peter Levels](https://open.spotify.com/episode/6KBpL2XfR9VdojbKNpE7cX). Peter talked about his technology stack to build startups - vanilla PHP, jQuery and SQLite.

    Hype is hype, but there is no better technology proof than a longterm usage. This inspired me to write my view about why PHP is the best choice for long-term business costs.
---

Peter talked about his hyper-focused way of learning new skills. Yet he never had the need to learn Angular, React, Vue or Next. It comes and goes. Do you know this joke...

<blockquote class="blockquote">
A new JS framework is released every day.
</blockquote>

But it's no joke for a business that run on it.

## There is no Hype

In 2016, I was contacted by a company to help with an upgrade their internal CRM. They used Angular 1 and wanted to go Angular 2.  It would require complete rewrite, so they passed. Another path was to migrate to React, the Facebook framework for frontend rendering. Then it was 2018 and Vue become the new competition. Yet, another rewrite would be required to attract more developers.

Next, Alpine... fresh stuff and new tutorials on Youtube on the same topic, just slightly different syntax sugar. This is a big problem for a business costs. Imagine having a house, where you have to replace windows before every winter. New standard, new dimensions, new style that doesn't fit the whole building and so on.

## PHP has Long-term Stability

Now let's look at the same situation from the other shore. What frameworks are used in PHP?
Symfony and Laravel. Far away from hype train, Symfony 1 was released 2007 and Laravel 1 in 2011.

Both are time-tested, community driven and most importantly - still being used by businesses. I can tell, as we work with dozens of those every year to upgrade.

We have a new PHP version released every-year, with clear release path ahead:

* 4 patch versions,
* than a new major release with BC breaks

## PHP has Pro-growth Competition

If a market has one big player, it's a monopoly that degrades into stagnation. There is no space for new player and for evolution. Until Apple and Linux came, Microsoft was the only player in the game and it dominated with terrible user experience and extreme license prices.

On the other hand, if market has too many players, it's a battlefield where customers would be lost in the noise. In such environment, you can hardly find a stable solution as it will be soon replaced by new, well-funded competitor.

PHP is very market in this matter. Did you know it's the only programming language that has 2 strong framework players? **I'm profoundly grateful that we have such a market**. This competition that keeps both Symfony and Laravel work hard to improve and innovate. It also gives us customer to have a choice, based on personal feeling or preferences.

In the end, this gives business solid foundation that can be built on for a decade to come.

## Healthy Ecosystem

This healthy competition has positive side effect in the community. Both organize couple conferences a year, where you can [meet great friends](/blog/why-I-migrated-this-website-from-symfony-to-laravel), find a job or learn new skills.

If you're working in vanilla PHP like Peter, there are [dozens of conference](https://www.php.net/conferences/index.php) a year across Europe, America, Asia and Australia.

The PHP ecosystem didn't get stuck original websites. You can build APIs, [desktop apps](https://nativephp.com/), [machine learning](https://php-ml.readthedocs.io/en/latest/), static analysis and tools that improve the code for you. It's full circle.

## Self-reflecting Technology

Last but least, what other languages don't have is advanced technology. [Nikita Popov](https://www.npopov.com/) came to PHP core in 2014 and [brought AST](https://wiki.php.net/rfc/abstract_syntax_tree) to PHP 7.0. It's not a new feature we can use in PHP, but it made PHP core code much straightforward to work with. It's similiar jump like moving from HDD to SSD drives, from CRT to LCD displays, from button phone to smart phones.

The problem is, PHP itself is written in C. Even the best PHP developer cannot code in C in a world language-level. If PHP would stop here, it would still be as good as its best C developer.

In parallel, Nikita contributes another tool that accidentally solved this problem and opened up a new possibilities to PHP developers - the [php-parser](https://github.com/nikic/php-parser).

<blockquote class="blockquote">
Imagine AI that can be as smart to improve itself,
or robots as advanced and skilled to build better version of themselves.
</blockquote>

I call this **self-reflection technology**. It's a state when a programming language reaches such a level it can improve itself. As far as I know, PHP is unique in this matter. Javascript has few such tools, but they are not consistent, because of so many language dialects.

In PHP community we have PHPStan, a tool that can find bugs in your code without running it. Is there a new bug in your project? You add a new PHPStan rule and till never happen again. Never. That's a readonly tool, like GPTs we have now, it doesn't work for us but its helpful at any moment we ask for help.

We have also Rector, a tool that can improve code for us and **upgrade code for us**. Most importantly, it can use the best world available knowledge to the moment. Has someone in India came up with a better way to write controller in PHP 9? They can create a Rector rule that anyone in the world can use, without any knowledge about controller or PHP 9.

**All other language I know of are still stuck in read-only phase** - the new features of language are spread via articles, videos or conference talks. There is no tool you could run from CI and get your Python 2 project to Python 3. There is a tool that you can run and get your PHP 5.2 project to PHP 8.4. In case of vanilla PHP, this can be done in single day, fully automated.

**That's why PHP is the best choice for long-term business costs.**

## Open-source adaptation to the Future

I'm not saying PHP has the best syntax, best collection and generics support or best speed. None of the language has. But all of the PHP technology/tools mentioned above are open-sources and not under commercial nor benevolent-dictator ownership. If something will change &nbdash; and it will &nbdash;, we can adapt.

That's why I agree with Peter and his vanilla PHP stack. It's not about the language, but about the ecosystem around it. And PHP has the best one for long-term business costs.

<br>

Happy coding!
