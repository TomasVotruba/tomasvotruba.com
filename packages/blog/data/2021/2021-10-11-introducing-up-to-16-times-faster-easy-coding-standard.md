---
id: 332
title: "Introducing up-to 16&nbsp;Times&nbsp;Faster Easy&nbsp;Coding&nbsp;Standard"
perex: |
    Do you use [Easy Coding Standard](https://github.com/symplify/easy-coding-standard)? Do you find **extremely useful and easy to use, but a little slow on a huge code base**?


    If you [follow me carefully on Twitter](https://twitter.com/votrubaT), you already know that the ECS got a new parallel run feature. I wanted to test it in a circle of early adopters first, so we make the run as smooth as possible for you.


    Today, we're sharing the ECS Parallel run with the public, so you **can cut down ECS run times from minutes to seconds**.

tweet: "New Post on the 🐘 blog: Introducing up-to 16-Times Faster Easy Coding Standard"
tweet_image: "/assets/images/posts/2021/ecs_before_now.png"
---

<blockquote class="twitter-tweet"><p lang="en" dir="ltr">Have you already updated <a href="https://twitter.com/hashtag/ecs?src=hash&amp;ref_src=twsrc%5Etfw">#ecs</a> to parallel run? ☺️<br><br>This is what you&#39;re missing out ↓ <a href="https://t.co/fuBKMpl8ID">pic.twitter.com/fuBKMpl8ID</a></p>&mdash; Tomas Votruba (@VotrubaT) <a href="https://twitter.com/VotrubaT/status/1446065404976279558?ref_src=twsrc%5Etfw">October 7, 2021</a></blockquote>

---

## Bottle Neck?

[Coding Standard tools](/blog/2018/10/22/brief-history-of-tools-watching-and-changing-your-php-code/) are **very fast in processing single file**. They parse PHP code to tokens, analyze tokens in a foreach loop, and report found issues. The performance hit is in the number of processed files. If we have 10 files, the 1st file is processed. The 2nd file cannot start until the 1st file is finished.

Fortunately, this architecture has **huge potential in performance improvement** once we add a parallel run. That's what I've been working for July and August this summer. Huge thanks to Ondrej Mirtes for inspiration in [PHPStan parallel run](https://phpstan.org/blog/from-minutes-to-seconds-massive-performance-gains-in-phpstan).

## ECS is now X-times Faster!

...where X = number of your CPU *threads*. What does it mean?

* Let's say your CPU has 4 cores. The ECS was running for 60 seconds on your project. Now it runs **15 seconds**. But there is more.

* Some CPUs have 4 cores, but 8 *threads*. That means an improvement to **7,5 seconds**.

<br>

You might think: "such CPUs must be expensive and available only for high-end laptops". You'll be surprised.

* My middle-end laptop uses <a href="https://en.wikipedia.org/wiki/Ryzen#Mobile_3">Ryzen 7 made in 2020</a>. It has 8 cores with 16 threads. Now we have **16-times faster runs**, from 60 seconds to **3,75 seconds**.

## Don't Take My Word for It

We've got few reports from our early adopters. How does ECS handle their projects?

<blockquote class="twitter-tweet"><p lang="en" dir="ltr">Thanks, <a href="https://twitter.com/VotrubaT?ref_src=twsrc%5Etfw">@VotrubaT</a> for adding parallel run to ECS 🥰. Running on Shopware 6 needs now 24s instead of 229s. <a href="https://t.co/cuxKy2ubfE">pic.twitter.com/cuxKy2ubfE</a></p>&mdash; Shyim (@Shyim97) <a href="https://twitter.com/Shyim97/status/1447588634203508739?ref_src=twsrc%5Etfw">October 11, 2021</a></blockquote>

<script async src="https://platform.twitter.com/widgets.js" charset="utf-8"></script>

## Enjoy Parallel ECS in Your Project Today

How to add this feature to your project?

Be sure to use at least ECS 9.4.70:

```bash
composer require symplify/easy-coding-standard:^9.4.70
```

And enable a new parameter in your `ecs.php` config file:

```diff
 use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
 use Symplify\EasyCodingStandard\ValueObject\Option;

 return function (ContainerConfigurator $containerConfigurator): void {
     $parameters = $containerConfigurator->parameters();
+    $parameters->set(Option::PARALLEL, true);
 };
```

That's it!

<br>

The parallel run might become enabled by default in ECS 10. We have enough time to test it on real projects and make it reliable till then. So far, it has been tested on Linux and Windows. If you're lucky enough to find the edge case, <a href="https://github.com/symplify/symplify/issues/new">please let us know in issues</a>, so we can cover it.

<br>

Happy coding!
