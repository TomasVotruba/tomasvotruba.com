---
id: 263
title: "Why Software Articles Must be CI&nbsp;Tested"

perex: |
    I know many great articles that go right to the point. I use their code examples and they work. But when I recommend these articles to people I mentor, I realize the articles are already 2 years old and their code samples probably do not work any more.

    <br>
    From hero to zero. Today I will show you how to **keep content alive lot longer with a minimal effort.**

tweet: "New Post on my Blog: Why Softare Articles Must be CI tested          #sustainability"
tweet_image: "/assets/images/posts/2019/tested-article/covered.png"

deprecated_since: "May 2021"
deprecated_message: "In the end, testing articles required so much work to be useful. I will let testing for book content, so updates can be released more easily. And the book of life will be prolonged :)."
---

Do you know [Awesome lists](https://github.com/sindresorhus/awesome)? If not, go to check them. They collect best sources about a certain topic. When I try to learn something new, I usually start on Github looking for "awesome <technology>". I recommend at least briefly to check them.

The idea behind Awesome list is to have resources that:

- **are up-to-date with modern technologies**
- **are the best in the field**
- **are easy to learn by beginners**


## How "Awesome Doctrine" was Born

When I was working with Nette, I met **Doctrine ORM** thanks to [Filip Procházka](https://filip-prochazka.com) and his great [Kdyby](https://github.com/Kdyby) open-source boom.

One day I decided to learn more about Doctrine. Documentation looked like a manual for experts rather than something I could learn from. I was also curious **how people use Doctrine in real applications, how to overcome performance issues, some cool features and pro tips**.

I was already familiar with [Awesome PHP](https://github.com/ziadoz/awesome-php) by [ziadoz](https://github.com/ziadoz), so I looked for "Awesome Doctrine".

0 results. Really? Why nobody made this? It's so obvious this would be useful.

Ah, it's my job then. And the joyful hell started.


### Many Sources on Many Versions

I was lucky to find many articles about Doctrine. One about Filters, others about Events or Criteria. But when I tried to use the code, it often didn't work. After digging I found out there was version 1.0, which was completely different.

> Tip: When you write a post about software, mention the version you're referring to – even if it only has one at the moment.

So I liked the concept in article and wanted to use it, but I didn't know what is different in version 2.4. I closed it.

I also read [Czech series on Zdroják](https://www.zdrojak.cz/clanky/doctrine-2-uvod-do-systemu) written by [Jan Tichy](https://www.jantichy.cz). It could give me great insights, but it was about Doctrine 2-beta. I closed it.


### What to put in the list?

I've decided to focus on sources released in that year. When articles and Doctrine version are the same - Doctrine 2.4 - it will be great source to learn from.

Idea was good, [List](https://github.com/biberlabs/awesome-doctrine) was done. I was happy until...

### ...Doctrine 2.5 was out!

So now each of the 20 sources on the list got a bit deprecated.

Oh, so that's why nobody made it in the first place :).

Now I also understand why many programmers hate new versions of software and want to stick with version they already know. It makes sense in such conditions.


## "Awesome Symfony"

Before I realized it makes no sense to make a list of sources, because next year I could drop most of them, I make [Awesome list for Symfony](https://github.com/Pehapkari/awesome-symfony-education).

New Symfony version is released every single year, so the list is even more outdated than Doctrine.

**So what this leads to?**


## Running in Circles

If I get back to the **useful source** idea from the beginning.

- **are up-to-date with modern technology**
- **are the best in the field**
- **are easy to learn by beginners**


To make this happen, I would have to create "Awesome * List" every year.
To make that happen, each article would have to be checked for compatibility with each new version and updated if needed.

That would mean around 50 articles on Doctrine every year. **And all this work just to keep status quo**. In big communities like Symfony and Laravel, this happens, but I still consider it too much wasted work (constructive ideas coming bellow).

So sources are useful upon their release but become more and more outdated every year. Writing article in such environment would be as useless as writing 100% test coverage for Christmas campaign microsite.

Thus, motivation to write software article is getting low, even when software is being released. - I call this *Know How Sharing Lag*.


## This Sounds like Legacy Code

Let's say we have application with legacy code. It brings me money and I want to keep it alive and growing as long as possible.

...

Mm, I should write tests and start refactoring?

Could this be possible to integrate into a blog or website?

### Dream Big

It would have to be:

- **integrated in blog**, because another external source would deprecate - thanks Statie
- **composer supported** - thanks Github Pages and Travis
- **open-source hosted**, so the author won't burn out on yearly fixes - thanks Github Pages
- **CI supported** - thanks Travis
- **tested daily** - thanks Travis Cron Jobs (Beta)


This idea was created in late 2015 with no solution ahead. I want to thank [Jáchym Toušek](https://twitter.com/enumag) for consulting this idea and making [the first prototype](https://github.com/enumag/enumag.cz/commit/3efc82717b9965bb19a2609e4caddc0c5467552d).

And that's why and how *tested articles* were born.

<br>

**How does tested article look like?** [See `HashPasswordCommandTest.php`](https://github.com/TomasVotruba/tomasvotruba.com/blob/8c68d0fcb64a73fb71f157452288711219501763/packages/blog/tests/Posts/Year2019/SymfonyConsole/HashPasswordCommandTest.php). It will last for years, will work on Symfony 4, 5, 6... 42.

**Feel free to send one.** We'll make sure it will make it into 2018 and beyond.

<br>

Happy coding!
