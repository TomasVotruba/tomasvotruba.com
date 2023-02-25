---
id: 209
title: "End of goPhp71.org"
perex: |
    I [launched goPhp71.org](/blog/2017/06/05/go-php-71/) in June 2017, just 6 months after release of PHP 7.1. In those times nobody was sure what version to require - 7.1? 7.0? Or wait for 7.2?

    Future is now. There is no need for the initiative now and it's time to [let it go](https://zenhabits.net/letting-go).
    <br>
    **How much did it cost? What was the effect? Was it worth it?** I'll share answers to these question so you know what to expect when you start a similar project. Let's get numberz!
---

When I bought the `gophp71.org` domain, I had no idea what it will become. You know, try to tell the whole PHP open-source community what min version should they use in `composer.json`, right? Explain why it's a good idea to work as a community to people you've never met. **But when I saw [Go PHP 5](https://www.garfieldtech.com/blog/go-php-5-go) I felt it's my duty to give back to PHP community and help with PHP 7.1.**

Today it's gonna about numbers, how well PHP community works together and few of mine fuck-ups :)

## How Much Did it Cost?

- Domain `gophp71.org`? - 15 $ per year, 2 years = **30 $** in total
- Hosting? **0 $** Thanks to [Github Pages hosting](https://github.com/tomasVotruba/gophp71.org) and [static site generator Statie](https://www.statie.org)
- And the work? I didn't Toggle it, but with all the design iterations, posts and commenting all over the internet **I'd say ~25-30 hours**. As you can guess, I'm very bad at tuning CSS for mobiles. Use your own hourly rate to have the idea in $.

## What was The Impact?

I took a while. Thanks to Jordi (thank you!), we have numbers on min. required PHP version on Packagist:

- [May 2017](https://seld.be/notes/php-versions-stats-2017-1-edition) - 1,7 %
- [November 2017](https://seld.be/notes/php-versions-stats-2017-2-edition) - 5,3 % (+ 218 % compared to previous 6 months)
- [May 2018](https://seld.be/notes/php-versions-stats-2018-1-edition) - 11,8 % (+ 121 %)
- [November 2018](https://blog.packagist.com/php-versions-stats-2018-2-edition) - 19,4 % (+ 64 %)

Is 19,4 % to small for you?

**What about 38 528?** That's the absolute number of packages that required PHP 7.1 in [November 2018](https://packagist.org/statistics).

<br>

I look forward to May 2019 stats update coming anytime soon.

## Correlation !== Causality

I don't think one website changed the PHP world. We made it together as one community. I tried to convince *David* from PHP 7.0 to PHP 7.1 over lunch. I thought I lost him not having enough arguments and *Nette* will go with PHP 7.0. **Later that month Nette went 7.1** as one of the first PHP frameworks - it was not stable, but still had a big effect on Czech open-source maintainers 👍

There was a sparkle of hope this might work.

<br>

**Fabien from Symfony helped** [by opening a pool on Twitter](https://twitter.com/fabpot/status/851558576770252800):

<img src="/assets/images/posts/2019/go-php-die/symfony_pool.png">

Over 1000 people helped this in that pool! To sum it up - 1003 specific people so far 👍

<br>

**Doctrine team also helped** with [PHP 7.1 announcement](https://www.doctrine-project.org/2017/07/25/php-7.1-requirement-and-composer.html) for all their packages. They also explained very nicely *Why dropping PHP support in a minor version is not a BC break* 👍

<blockquote class="blockquote mb-5 mt-5">
"A BC break happens when there is an incompatible change that your package manager can't handle. For example, changing a method signature in a minor version is a no-go, since the composer version constraints mentioned above assume any minor upgrade can safely be used."
</blockquote>

Remember this quote, we'll need it when we'll go PHP 8.1 (maybe?) later.

<br>

[**Over 19 PHP projects**](https://github.com/TomasVotruba/gophp71.org/graphs/contributors) have added their go PHP 7.1 statement to the website in the 1st year. Thank you' all! 👍

<img src="/assets/images/posts/2019/go-php-die/most_active.png" class="img-thumbnail">

Last but not the least, **[the_alias_of_andrea](https://www.reddit.com/r/PHP/comments/6xqa23/go_php_71) helped** spread the news on Reddit 👍

<img src="/assets/images/posts/2019/go-php-die/reddit.png">

<br>

"Sure Tom, good job... you're the best, but we're really reading just for your fuck-ups."

All right...

## What I Fucked Up?

### Too Complicated Wording

I wrote "add your project too" instructions on the website, so all projects could join. **I learned that people don't understand my complicated thoughts**.

Instead of keeping it as simple as:

- "Add your project if you require PHP 7.1 in the stable release"

I created a mess like this:

- "Add your project if you have PHP 7.1 in the `composer.json` file. It doesn't matter it's not stable yet. If it's not stable yet, add *released: no* to the configuration file. But remember to update this website, when you finally decide to use a stable tag. Also, add some link to the statement, so we can read about it a bit more. I hope it's not too much to ask you to follow these very few simple steps. I really tried to make them very easy, as you can see. Thank you"

That led to hanged PRs, manual *yes/no* corrections that didn't have much-added value and confused comments from contributors (I'm sorry about that).

### Too Late

**I fucked up timing.** PHP 7.1 was released in December 2016, yet `gophp71.org` was not launched until 7 months later.
I had the idea before, but **I was afraid how will you react**. I had thoughts like "Who the hell is this Votruba and why does he think he is that he tells us what PHP version should we use?" or "Do you want us to go PHP 7.1? Well, we'll go PHP 7.0 on one project and PHP 7.2 on the other, a-ha!" or "What is he selling us? That's some version conspiracy!"

Now I see that was stupid. I should start earlier, try it, give a go and adapt upon the feedback.

<br>

That's all folks, hope you enjoyed it! I sure did start almost 24 months ago with just this:

<img src="/assets/images/posts/2017/go-php-71/first-version.png">

## What's Going to be Next?

Will we "Go PHP 8"? Someone from Arizona, US [already bought the `gophp8.org` domain](https://gophp8.org).
Or 8.1, when 8 becomes more mature?

Who knows. All I know **I'm very happy to be part of our PHP ~~community~~ family ❤️️, where we support each other despite our differences and work together when we need to**.

<br>

Happy coding!
