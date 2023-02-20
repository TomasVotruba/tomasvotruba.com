---
id: 246
title: "How to Find Dead Symfony Routes"
perex: |
    Almost half a year ago, I spotted a post called [Route Usage Package for Laravel](https://laravel-news.com/route-usage-package-for-laravel). It's nice to have to see what routes are used and how often.

    But when dealing with legacy code, knowing **dead routes will save you dozens of hours in refactoring**.
tweet: "New Post on #php 🐘 blog: How to Find Dead #Symfony Routes"
tweet_image: "/assets/images/posts/2019/spaceflow_10_points/13.png"

deprecated_since: "November 2020"
deprecated_message: "This package didn't shown as very useful, so we deprected it. Use classic logging services instead."
---

## Why are Dead Routes Important for Your Code?

If you know that 20 % is never used, you can **drop it and ease your maintenance**. Also, it's a must-have pre-step [to code migrations](/blog/2019/12/16/8-steps-you-can-make-before-huge-upgrade-to-make-it-faster-cheaper-and-more-stable/).

**Static analysis** can help you with [dead code that is never used](/blog/2019/03/18/how-to-detect-dead-php-code-in-code-review-in-7-snippets/) and with [public methods that are never called](/blog/2019/03/14/remove-dead-public-methdos-from-your-code/), e.g.:

```diff
-$discount = $this->getDiscount();
 $productCategory = $this->categoryRepository->findCategoriesByProduct(
     $product->getCategory()
 );
 $discount = $this->getDiscount();
```

<br>

<blockquote class="blockquote text-center">
    10-20 % of code in most PHP code bases<br>
    is dead and can be deleted.
</blockquote>

In this [case study from Spaceflow](/blog/2019/12/09/how-to-get-rid-of-technical-debt-or-what-we-would-have-done-differently-2-years-ago/) project we worked in summer 2019, **we removed ~20 % of the code... and nobody noticed**.

<img src="/assets/images/posts/2019/spaceflow_10_points/13.png" class="img-thumbnail">

## Static Analysis of Dead Code is Not Enough
It's quite easy to find dead calls, but when it comes to controller and API endpoints, it's a different game. Controller methods are public and are called by a framework.

**Static analysis won't tell us, which controller is used and which not.** Also, the controller might call 2-3 other services... and they might call other services... and those could be dead too... **welcome fractal of dead code we have to maintain.** What now?

We'll have to use similar approach we used for [From 0 Doc Types to Full Type Declaration](/blog/2019/11/11/from-0-doc-types-to-full-type-declaration-with-dynamic-analysis/).


<img src="/assets/images/posts/2020/dead_routes_branch.jpg" class="img-thumbnail mt-4 mb-2">
<em>Such branch is in your code... somewhere. Would you still water it for next couple years?</em>

## Coffee Shop - Static vs. Dynamic Analysis

Imagine you're a coffee franchise owner. Not just 1, but 30 coffee houses. Suddenly, there comes corona, and shops have to be locked down.
 Luckily, you have an emergency fund to keep them running... well, just 15 of them. **Which one you choose to close?**

Coffee shop with a property of specific size, location, number of chairs, and toilets - that's static analysis. It won't help us here. What if the shop with the smallest area is making more money than the biggest one?

Let's use **dynamic analysis** here - you **measure data in time** and decide based on it. E.g., income for last year from all of them compared to expenses to run them.

## 2 Steps to add Route Usage to Your Symfony App

Inspired by the Laravel package, I've made a [Symfony Route Usage](https://github.com/symplify/symfony-route-usage).

### 1. Install it

```bash
composer require symplify/symfony-route-usage
```

### 2. Enable Bundle

```php
// config/bundles.php
return [
    Symplify\SymfonyRouteUsage\SymfonyRouteUsageBundle::class => ['all' => true],
];
```

<br>

Collect data for a couple of weeks (depends on the size of your site) and see for yourself, **what routes have been used**:

```bash
bin/console show-route-usage
```

↓

<img src="/assets/images/posts/2020/dead_routes_used_routes.png" class="img-thumbnail mt-4 mb-2">

<br>

## What Routes Have Never Been Used?

Just run:

```bash
bin/console show-dead-routes
```

↓

<img src="/assets/images/posts/2020/dead_routes_dead_routes.png" class="img-thumbnail mt-4 mb-2">

<br>

**What about the performance?** Pehapkari website takes 72 ms to load, of which 10 ms (13 %) is Symfony Route Visit. If that would be too much for your website, add partial logging by overloading [the subscriber](https://github.com/symplify/symfony-route-usage/blob/master/src/EventSubscriber/LogRouteUsageEventSubscriber.php) - e.g., every 100th or 1000th request.

<br>

This package is freshly baked. Do you have some ideas how to make it better? Let me know ↓

<br>

Happy coding!
