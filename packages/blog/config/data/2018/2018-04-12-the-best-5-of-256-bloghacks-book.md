---
id: 91
title: "The Best 5 of 256 Bloghacks Book"
perex: |
    Have you heard of [*256 Bloghacks* book](http://www.yegor256.com/256-bloghacks.html) by Yegor? Do you think about reading it, but just don't have the time and money?
    <br><br>
    This post is *the best of* selection just for you and if you feel you like it, you can buy it and read as a whole.
tweet: "New Post on My Blog: The Best 5 of 256 Bloghacks Book"
tweet_image: "/assets/images/posts/2018/bloghacks/book.png"
---

I came across this book [in the review by Vojta Růžička](https://www.vojtechruzicka.com/book-review-256-bloghacks) on [devblogy.cz](http://devblogy.tk) (the best place to follow Czech it bloggers by [kaja47](https://twitter.com/kaja47) whom I'm very thankful for it). You might know [Yegor](http://www.yegor256.com) from Java world or from
**[Software Quality Award](http://www.yegor256.com/award.html) he organizes for open-source projects** every year. [A friend of mine](https://arkadiuszkondas.com) won the award with *the* machine learning package in PHP - [php-ai/php-ml](https://github.com/php-ai/php-ml#awards) - last year.




Vojta was so kind to lend me the book over the lunch. This post is an answer to my first question I asked: **what are the top 5 tips I should take from this book if no other?**

256 items is a lot. No matter if it's a tip, a story, a class or a food item in your home. The most of them are common, some are better and only **a few are golden**. I look for the gold in books, so here is what I found.

## 1. Blog Once a Week for 2 Years

When I posted [my first post in late 2015](/blog/2015/11/02/ovladni-doctrine-migrace-v-nette/) and it had 24 comments and I was very happy my knowledge is worth talking about. When I posted the next one and had no responses. I was frustrated and felt hopeless and as a just not good enough writer. So I stopped blogging for a few months.

I see now that it was a mistake, so the first tip is **to blog once a week**. Life is trying to tell me over and over again, that [Consistency Beats Talent, Luck, Good Intentions, and Even Quality](https://medium.com/@anthony_moore/consistency-beats-talent-luck-good-intentions-and-even-quality-66ba255aa4f7).

But not more often. I tried to blog 2x a week, right from the start and got burnout lesson (what a surprise). After a year of weekly blogging, I felt I want to blog more often and add a Thursday post. Very carefully, just once a month. Now after a couple of months I feel confident enough to write about it.

<div class="card mb-5">
    <div class="card-body">
        <strong>Tip summary</strong>: start blogging slowly, once a month, once a week, be persistent and wait 1-2 years before getting the popularity.
    </div>
</div>

## 2. Be Active on Reddit

[Reddit](https://www.reddit.com/r/PHP) is like StackOverflow for personal opinions or like Devel.cz, but international and for everyone. It's a place, where people share ideas, comment it, up-vote it or down-vote it.

Vojta shared with me his first experience with Reddit: he posted 2-3 his posts and he got autoban. That **kind of ban, when you don't know you're banned** - everything works, there're up-votes on your posts, but they're all in grey. Also, Yegor wrote about the similar experience. My experience was a little better: I could post only once 10 minutes. Then I learned I could post more often when I got more profile points (up-votes of my ideas and posts).

### Get what you Give

That's the way of Reddit to tell you: **You've got to give to get.** I tried this tip, I'm voting and engage in discussions. I must admit, it was not out of altruistic unconditional love to the world. It was to get my posts be seen. But thanks to this, **I started to be open to others' opinions, learned about few weaknesses in my communication and often learned something new.**

And this could be at any other community, whether you like Twitter, Facebook, Slack, Github or Stackoverflow.

<div class="card mb-5">
    <div class="card-body">
        <strong>Tip summary</strong>: pick an online community and start to learn how it works. It might take time, weeks or months, but you'll get there. Become a member. What you give, will get back to you.
    </div>
</div>

## 3. Use Static Website Generator

This blog runs on [Statie](http://statie.org) and [is fully open-sourced on Github](https://github.com/tomasvotruba/tomasvotruba.com) (found a *tyop* here? just edit this file and send a PR - there is a link right at the top right of this post).

I love open-source and did this right from the start, but it feels so important to me that I mention this tip again. Also, I had a meeting with Vojta and he told me [how he migrated from Wordpress to GatsbyJS](https://www.vojtechruzicka.com/gatsby-migration) (ReactJS-based static generator) and how he loves it.

Static websites are fast, simple, easy for your fan/critics-base to work with and most importantly - open. No secrets, copy anything you like.

<div class="card mb-5">
    <div class="card-body">
        <strong>Tip summary</strong>: use any <a href="https://www.staticgen.com/">static generator</a> you feel is the right choice for you, put it on Github and put a link to every page, so people can edit it. Who didn't have a typo fix as a first proud contribution to the open-source?
    </div>
</div>

## 4. Put a Search on Website

This was one of the biggest fails on my blog. Imagine you've read about "symfony controller service something", you liked it, but you don't remember the exact title of the post. My blog has all posts on the main page, with no paging, so you can just use *Ctrl + F* to find... **well that just sucks, right?**

Thanks to Yegor and Vojta I finally [added Google Search](https://github.com/TomasVotruba/tomasvotruba.com/pull/286) on the homepage, so you can type "symfony controller service", hit *Enter* and [get results you need](https://www.google.cz/search?sitesearch=tomasvotruba.com&q=%22symfony+controller+service).

I'm sorry to all that felt frustrated when looking for any valuable content post by post, manually.

In case you'd like to try Algolia and [simple DocSearch](https://community.algolia.com/docsearch) that Roman [added to Statie](https://github.com/crazko/statie-web/commit/6c218b5d06666a098341960129617441c7cf8acb) and works very well for Statie-based website on Markdown... I tried it for you and was rejected because "blog is not a documentation".

<div class="card mb-5">
    <div class="card-body">
        <strong>Tip summary</strong>: working solution is better than a perfect one. Add simple stupid Google Search right from the start. I do it, Yegor does it, Google does it. Don't you know how? Just copy <a href="https://github.com/TomasVotruba/tomasvotruba.com/pull/286">these few lines</a>.
    </div>
</div>

## 5. Turn your Best StackOverflow Answers to Posts

Did you provide a great answer on StackOverflow? Is that answer to duplicated and repeated questions? Do you think it might be useful to more people than StackOverflow users reading your post?

**Turn it into a blog post**. I was not sure about this myself, so I tried it:

- Question: [Symfony 3 - Outsourcing Controller Code into Service Layer
](https://stackoverflow.com/questions/38346281/symfony-3-outsourcing-controller-code-into-service-layer/38349271#38349271)
- Post: [How to use Repository with Doctrine as Service in Symfony](/blog/2017/10/16/how-to-use-repository-with-doctrine-as-service-in-symfony/)

- Question: [How do I read from parameters.yml in a controller in symfony2?
](https://stackoverflow.com/questions/13901256/how-do-i-read-from-parameters-yml-in-a-controller-in-symfony2/48371606#48371606)
- Post: [How to Get Parameter in Symfony Controller the Clean Way](/blog/2018/01/22/how-to-get-parameter-in-symfony-controller-the-clean-way/)

And it works great! Now all I do is to **provide a specific answer on the StackOverflow and link the post where I explain all possible pitfalls**. So the question is answered and there is a follow-up, when they need to know more.

<div class="card mb-5">
    <div class="card-body">
        <strong>Tip summary</strong>: Aggregate answers from StackOverflow and polish them on your blog as much as possible. Your readers will thank you and you'll make use of the energy you've already put to StackOverflow single answer. Win-win.
    </div>
</div>

### Are 5 Tips not Enough for You?

Well, if you're from Prague, reach out [Vojta](https://www.vojtechruzicka.com) and ask him about the book. He's very open-minded person and eats lunch every day.

<br>

Carpe postum!
