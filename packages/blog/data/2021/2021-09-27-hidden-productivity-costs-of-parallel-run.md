---
id: 334
title: "Hidden Productivity Costs of Parallel Run"
perex: |
    Have you enabled parallel run in PHPStan and ECS? You know the speed gain is brutal. On the other hand, there are **few hidden costs in developer experience** I haven't faced before.


    What are they, and how to mitigate their impact?

tweet_image: "/assets/images/posts/2021/cores.jpg"
---

## All CPUs are Fully Used

PHPStan parallel implementation uses your CPU cores to process multiple files at once. When I run it on my PC, it looks like this:

<img src="/assets/images/posts/2021/hidden_cost_cpu_history.png" class="img-thumbnail mt-3" style="max-width: 38em">

That's great! 16 threads, all fully used. It means the static analysis process of any project ends up in 15 seconds. That's fast, and then I'll be able to continue.

The problem is, for these 15 seconds, the laptop does not have any spare computation power. It only focuses on tasks given by PHPStan, but **everything else is slowed down**.

[Instant feedback is essential](/blog/2020/01/13/why-is-first-instant-feedback-crucial-to-developers/) to stay in the flow and focused on the project. Any lag can destroy [state of beautiful deep work](/blog/2018/09/13/your-brain-is-your-garden/).

## Short but Complete Lag?

Imagine you're writing a post or a code, and then you have to pause for 15 seconds... or even 10 or 5 seconds. Your brain has to stop thinking, start caching the thoughts and code you want to write.

<blockquote class="blockquote text-center mt-3 mb-5">
    Would you use PHPStorm<br>
    if it has 5 seconds lag everytime you create a new class?
</blockquote>

What if you want to run PHPStan and one more tool, e.g., PHPUnit or ECS? The **latter will suffer from low computation power** and take much longer than parallel of PHPStan.

<br>

## Keep 1 CPU Always Free

There is a workaround for this. We can tell PHPStan to use fewer threads than is our maximum. That way, there will always be one free:

```yaml
# phpstan.neon
parameters:
    parallel:
        # for Tomas: 16 threads - 1
        maximumNumberOfProcesses: 15
```

## Max Full !== Min Free

Well, there will be one always free *on my pc*. The problem is, all the developers share the main `phpstan.neon` config. But what is the chance the developers use computers with the same amount of CPU threads?

<br>

It would be much better to have the option to see the number of accessible CPUs. Something like:

```yaml
# phpstan.neon
parameters:
    parallel:
        minimumNumberOfFreeThreads: 1
```

P.S.: If you know of such a feature, let me know.

<br>

## All at Once of One by One?

Imagine you have to compute 5 algebraic formulas. You can choose one from 2 ways to receive them:

* A) you'll get one every 10 minutes
* B) you'll get all formulas at once

<br>

**Which will cost you more time and which more energy?**

## Parallel Drains your Battery Much Faster

Before parallel run was added to PHPStan, my laptop worked for only 4-5 hours of coding. I could go for coffee in the morning, see a client, do little mentoring, and it was still running in the afternoon.

<br>

As only 1 core was working, **there was lot of energy left.**

<img src="/assets/images/posts/2021/cores.jpg" class="img-thumbnail mt-2 mb-4" style="max-width: 27em">

That changed since parallel. When you develop PHPStan rules or work on various projects, PHPStan often needs to analyze the whole project from scratch. That's fast in time but costly in energy.

<br>

**Suddenly, my laptop battery duration dropped from 5 hours to 2,5 hours**. It turned off, charger at home and I was free to meditate.

The battery duration estimates were jumping like crazy from 6 hours to 30 minutes and back. First, I assumed it's some bug in my Ubuntu. Then I found out it depended if PHPStan was **running or not**.

## Charger is Your Friend

This is not a life-ending situation, but it's something you should be aware of. If you're a cable type and stay in one place always plugged in, you don't mind.

<img src="/assets/images/posts/2021/parallel_charger.jpg" class="img-thumbnail mt-4 mb-3" style="max-width: 25em">

But **if you love to travel, work, or write posts on the train** like me, keep it in mind.

<br>

Happy coding!
