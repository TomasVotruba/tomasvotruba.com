---
id: 101
title: "Programming = Climbing a Huge Mountain"
perex: |
    Let's take a break after 2 long code-posts from last week and enjoy bit of philosophy. I apply *the mountain climber* in programming for last 2 years and it really helps me to overcome difficult spots.
    <br><br>
    Today we'll climb together.
tweet: "New Post on #lazyprogrammer Blog: Program Like Your Climb a Huge Mountain #craftsmanship"
tweet_image: "/assets/images/posts/2018/climb/climb-7.jpg"
---

Just a year ago I was deep-train traveling southern Europe and I worked on ApiGen (here [is a short story about that work](/blog/2017/09/04/how-apigen-survived-its-own-death/)). I worked on a migration of the most coupled dependency, which was unmaintained for 3 years, to new one. You can imagine it like migration your application from Nette to Symfony or from Doctrine to Eloquent (or vice versa).

## Rushing Up is Rushing to Fuck-Up

First I approached this problem in very... how do they say it... agile way. Let's start committing and see what happens. I was sure I'll be over after a week. Well, I wasn't and I was angry at myself. The faster I tried to finish it the slower I went.

And this rush got me to a situation I had to revert the whole PR and start over again, after a week of work.

## Know Your Path

Then I stopped for a while and thought: Okay, I'm here under the mountain, there is the peak and want to get there. I don't see the peak or the way, I just know I want to climb this mountain.

<img src="/assets/images/posts/2018/climb/climb-2.jpg" class="img-thumbnail">

I had one package and I wanted to switch to the other. What are the minimal steps?

## Make Safe Spots

When you climb a mountain for the first time, you have rope, a buddy and clinch along the whole way.

<img src="/assets/images/posts/2018/climb/climb-1.jpg" class="img-thumbnail">

**These are all the safe spots and it's ok to use them.** Well, unless you'd like to die climbing.

In programming I apply the same principles: I have tests, static analysis, coding standard fixers and CI. Without them, I'd be lost.

And are there no safe spots? **I take a time before climbing and prepare these spots.** I'll increase the code coverage for code part I'd like to work with - and that already gives me some hints, what the path looks like.

The same way you prepare for the climbing - you ask other climbers how it went, what are the hacks, where are the places to rest and where you should be careful.

## Be Safe like a Pro

<img src="/assets/images/posts/2018/climb/climb-3.jpg" class="img-thumbnail">

As you can see, it's no shame to use the spots, ~~even~~ mainly professional climbers do that.

**Because the safer you feel, the better your brain operates and the faster you climb** - either a mountain or a code you write.

## Use Safe Spots

If you already have such safe spots, be sure to use them. I've seen many applications that had over 30 tests but didn't actually use them - no continuous integration, no [simple scripts in composer](https://blog.martinhujer.cz/have-you-tried-composer-scripts/) that could run them locally with `composer run-tests`.

<img src="/assets/images/posts/2018/climb/climb-4.jpg" class="img-thumbnail">

It doesn't matter that other programmer made it, that they don't cover 100 % of the code or that they use the other test framework I don't prefer. **I'm grateful there is something that will help me to climb faster and that another climber made it for me, even though he didn't have to.**

## Staying in the Present Moment - One Move at a Time

Zen, Kaizen, Ikigai, Present moment, <a href="/blog/2017/09/25/3-non-it-books-that-help-you-to-become-better-programmer/#deep-work-by-cal-newport">Deep Work</a>, Flow. Whatever you call it, it matters.

When I program, I don't know what will happen in next 15 minutes. Maybe it will be over, or maybe I'll find a bug that I'll investigate for 2 hours in a row and then [use this workaround](https://github.com/TomasVotruba/tomasvotruba.cz/commit/a890d5100e2226d4958504a50efa282fd1b2c4a1).

<img src="/assets/images/posts/2018/climb/climb-5.jpg" class="img-thumbnail">

I don't see the end, only the next step. Same is for climbing, I don't see the top of the mountain. I barely see 5 meters ahead of me. But even if I see the top of the mountain it doesn't matter. I can only move my hands or legs just a few feet ahead of me.

<blockquote class="blockquote">
    The present moment contains past and future.
    <br>
    The secret of transformation is in the way we handle this very moment.
    <footer class="blockquote-footer">Thích Nhất Hạnh</footer>
</blockquote>

One [Peace Step](https://www.amazon.com/Peace-Every-Step-Mindfulness-Everyday/dp/0553351397) at a Time  (great book, just reading it). One commit at a time. One merged pull-request at a time. Not 2, just 1.

## Take a Break

When you're tired, frustrated, angry or sad, will you rush to climb more and more steps? No, you'd take a break. Just hang on the rope for a while (not by the neck, it's not healthy!)... well, for [17 minutes](https://lifehacker.com/52-minute-work-17-minute-break-is-the-ideal-productivi-1616541102) as I learned in one of the amazing [Pinkcasts](https://www.danpink.com/pinkcast/).

It might be a coffee, it might be a transfer to another train, it might be toilet visit.

<img src="/assets/images/posts/2018/climb/climb-6.jpg" class="img-thumbnail">

You probably won't believe it, but most breakthroughs come to me in the toilet room (intellectual, not physical damaging any part of the toilet).

Why? Because when the brain enters *the serendipity-mode*, it starts to think subconsciously and connect thoughts more effectively than with active thinking. For example, a few paragraphs up I made a workaround because key in the array didn't match the `PostFile` `id` in one spot. It worked in 3 other places in the application, but 1 just missed it. Then in my *toilet time*, it came to me that this can be solved by using a collection. One iterable immutable object everywhere.

Well, now it's like standing on the top of the hill and seeing the elevator that was on the left side all along. But when I was under the mountain, I didn't see it. I needed to take a break to see.
## The Mountain Climber Way

So this is my climbing approach to code (I'm not a climber, to be clear).

<img src="/assets/images/posts/2018/climb/climb-7.jpg" class="img-thumbnail">

The more you climb, the better you know the terrain and the more you can improvise. [Like this guy, who climbed a 3000-feet tall mountain in 4 hours](https://www.nationalgeographic.com/adventure/features/athletes/alex-honnold/most-dangerous-free-solo-climb-yosemite-national-park-el-capitan/). **Without a rope.**

<br>

**And how do you approach your coding?**

<br><br>

Happy climbing!

