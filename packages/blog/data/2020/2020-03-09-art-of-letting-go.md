---
id: 242
title: "Art of Letting Go"
perex: |
    Recently I come to a dead end with one of my projects. I felt it doesn't bring value to me, to people that use it, and to neither to the open-source community. I felt sour about it for the last 3 months, confused about what to do next.
---

## It Works... For a Time

When I created this project in 2016, it worked well. It worked well, and it brought me value. I enjoyed it, and it was exactly what I needed.
I was also frustrated by the previous tool I used - [Sculpin](https://github.com/sculpin/sculpin) - that didn't work for PHP 7. [Statie](https://github.com/symplify/statie) was a pet project for me, to **code website in Markdown and PHP and publish it on the Internet** via GitHub Pages for free.

The 0 $ was partial motivation, but **I was astonished much more by the miracle of deploying the PHP app into HTML and CSS**. In times of MVC frameworks build on Request and Response, it took me many days to shift my mind.

I had my first talk about Statie abroad, in Berlin I think, and I enjoyed it. Why? It was my first talk in English, and [I was so scared](/blog/2018/07/23/5-signs-should-never-have-a-talk-abroad/) about the feedback and people will understand my poor English (at least what I thought about it).

I wrote a few posts about it and also used it for our community website [Pehapkari.cz](https://pehapkari.cz) - proudly of a piece of art at that time. I mean, **anyone could edit the website on GitHub**, and it was **deployed to production after merging the pull-request** in a matter of 5 minutes. How long does it take to projects, you know?

Also, it didn't got much popularity:

<a href="https://packagist.org/packages/symplify/statie/stats">
    <img src="/assets/images/posts/2020/letting_go_bad_stats.png" class="img-thumbnail">
</a>

## Change is Knocking on The Door One Step at a Time

The first signs are very subtle. Few of my PHP friends used Jekyll or Sculpin around 2017, then switched to projects not written in PHP - like [Hugo](https://gohugo.io) or [Gatsby](https://www.gatsbyjs.org). I didn't think much of it.

<br>

Statie generated code from PHP. When you change the code, **it runs full PHP command and regenerates the whole website**, even if you change only one post. To make this fast, we used Javascript. Write code in PHP and use Javascript to run PHP to generated HTML and CSS... it started like scratching behind the ear with middle toes of both of your legs.

<br>

Our Pehapkari.cz community grew further than we first imagined. In Spring 2019, we started to need more than just a static website. We needed forms, cron jobs, and emails.

How can we make it happen? Symfony, DigitalOcean, and Docker came as a way to go. We [switched from Statie to Symfony](https://github.com/pehapkari/pehapkari.cz/pull/47). **I was surprised how easy it was**. It might seem right, but it was a bad sign for Statie.

<img src="/assets/images/posts/2020/letting_go_pehapkari_switch.png" class="img-thumbnail">

<br>

The next sign came in the Autumn of 2019. [Statie dropped Latte support](https://github.com/symplify/symplify/pull/1641), as it was buggy and didn't work well with JavaScript rebuild. What was Statie? A Symfony Kernel? Static Site Generator?

**A few months later, I felt like I'm running in circles**. Something was wrong, but I didn't know what it was.

<blockquote class="blockquote text-center mb-5 mt-5">
    What you know you can't explain, but you feel it. You've felt it your entire life,<br>
    that there's something wrong with the world.

    You don't know what it is, but it's there,<br>
    like a splinter in your mind, driving you mad
</blockquote>

## Time for Self-Reflection

What now? One of the few things that help me in complicated situations with coding is Occam's razor.

<img src="/assets/images/posts/2020/letting_go_occams_razor.jpg" class="img-thumbnail">

I paused and asked myself a few questions.

<br>

What main purpose of Statie?
- *To generate HTML and CSS web from Twig and some PHP.*

<br>

How do I use it?
- *In a command line.*

<br>

Why was it so easy to switch to Symfony?
- *Because they both run on Symfony Kernel, configs, Twig, DependencyInjection, and other Symfony components*

<br>

What is it similar too?
- *Symfony application*

<br>

How is it unique to anything else on the market?
- ...

<br>

### Unique Selling Point

A *unique selling point* is a term from a business. If investors want to get an idea about startup value, they ask CEOs what their product exceptional compared to the competition is. **It can be convenient in coding as well**. e.g., how does your private MVC differ from Symfony MVC? In only 5 %? Then it's obvious it would save you huge costs to switch to Symfony.
Occam's razor in practice.

<br>

Saying that the last question bugged me the most: **How is it unique to anything else on the market?**

*Well, it's like Symfony, but it generates static website. So I made a Symfony application that generates a static website? Isn't that what Symfony does anyway? When we visit a controller, it shows HTML and CSS after all, right? It's just not cached... but is it though?*

I tried a simple demo a week ago. When I render Symfony controller to string... I got a bitter-sweet surprise: **it's HTML**.

<blockquote class="blockquote text-center">
    Then I realized a simple truth: I wrote a Symfony clone all along.
</blockquote>

<img src="/assets/images/posts/2020/letting_go_double.jpg" class="img-thumbnail">

## Why Let Go?

You would say, "why change something that works"?

Sometimes, a **new opportunity is waiting behind the corner**. But to allow an opportunity to come, **we have to make space for it first**. If we'd stick with old phones or GSM, we block ourselves from using smartphones with an instant internet connection.

<br>

Remember not to have high expectations. The next thing doesn't have to be as impressive as it often seems. It's like with relationships with man or woman, a new friend, a new job or a new country you move in. If we let go of something that doesn't work for us, we can't expect the next thing to solve all our problems.

<blockquote class="blockquote text-center">
    There are no solutions. There are only trade-offs.
</blockquote>

It will be only better and **that's good enough because that how we learn and grow** &ndash; 1 % at a time.

<img src="/assets/images/posts/2020/letting_go_better.png" class="img-thumbnail">

## Spread The Code you Want to See in The World

I realized I made the project that worked before, **but doesn't anymore**. And that's the lesson for open-source I want to see in the world.
**I don't want to spread bad habits amongst programmers who read my code** when I already know it's wrong. I feel it's my personal responsibility to correct mistakes of past, moreover when [the code teaches programmers](/blog/2020/02/24/how-many-days-of-technical-debt-has-your-php-project/).

<blockquote class="blockquote text-center">
    If something doesn't work and we feel there might be a better way,<br>
    we should pursue it, even though we don't know the outcome yet.
</blockquote>

**What works today doesn't have to work in the future**. And probably won't. New PHP framework may appear in 2020, and it will be so good that we'll all migrate to it in 2022. It would not be the first time.

## Verify Your Feelings with Experiment

Before all this really happened and I made sense to it, we have to **test the assumption**:

<blockquote class="twitter-tweet"><p lang="en" dir="ltr">Talking about Occam&#39;s razor... this is how Statie 20-lines implementation looks like in normal Symfony app 😱<br><br>Time to let go? <a href="https://t.co/1Fxk31wWaf">pic.twitter.com/1Fxk31wWaf</a></p>&mdash; Tomas Votruba (@VotrubaT) <a href="https://twitter.com/VotrubaT/status/1237437559254417408?ref_src=twsrc%5Etfw">March 10, 2020</a></blockquote>
<script async src="https://platform.twitter.com/widgets.js" charset="utf-8"></script>

<br>

**If you don't know, test it, try it.** Your feelings might be right, your feelings might be wrong, but only real test in real life will tell.

- [PR on friendsofphp.org](https://github.com/TomasVotruba/friendsofphp.org/pull/169/files)

In a few hours, I got a working generator and deploy on [friendsofphp.org](https://friendsofphp.org). It worked!

<br>

So I made a slightly bigger experiment on my blog website with over 230 posts.

- [PR on tomasvotruba.com](https://github.com/TomasVotruba/tomasvotruba.com/pull/940)

I got stuck with a route with an argument - a blog post detail - but after a few fixes, it worked too!

## The Letting Go in Practise

Since that moment, I knew, Statie needs to be deprecated. To spread the miss-information, I had to [deprecate 8 posts](https://github.com/TomasVotruba/tomasvotruba.com/pull/944) about Statie on my blog.

Also, before we burn bridges, **there should be a path to follow**. And that is [Symfony Static Dumper](https://github.com/symplify/symfony-static-dumper) with only 550 lines of code (compared to Statie with over 4500 lines). A package that you plugin in Symfony app and works the same way Statie does.
I'll write about in the next post, which will focus only on migration.

<a href="https://www.youtube.com/watch?v=moSFlvxnbgk">
    <img src="/assets/images/posts/2020/letting_go_frozen.jpg" class="img-thumbnail">
</a>

If you haven't heard, the song is a blast!

<br>

Happy coding and stay healthy!
