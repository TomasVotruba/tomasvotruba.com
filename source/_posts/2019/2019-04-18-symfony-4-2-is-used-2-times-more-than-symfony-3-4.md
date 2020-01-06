---
id: 204
title: "Symfony 4.2 is used Twice More than Symfony 3.4"
perex: |
    PHP itself is very quickly adopted. Last Packagist stats from 2018/11 [report 32,6 %](https://blog.packagist.com/php-versions-stats-2018-2-edition/) people are using PHP 7.2. That's a very nice number, great job y' all!
    <br><br>
    But most of our code is not just plain PHP. **It's framework-locked PHP**. How is framework adoption?

tweet: "New Post on #php 🐘 blog: Symfony 4.2 is used 2 times More than #symfony 3.4 ... Includes also #laravel #zend #yii #nettefw and #cakephp download stats by version"
---

<div class="alert alert-sm alert-success mt-3">
This post was inspired by very active reactions to <a href="/blog/2019/04/11/trends-of-php-frameworks-in-numbers/">Trends of PHP Frameworks in Numbers</a>
</div>

If you look at the [PHP Framework Trends table](/php-framework-trends/) and see Symfony with 595 mils. downloads last year, what will it tell us? What if 90 % of that is just legacy Symfony 2.8?

When I talked with my friends Marek and Honza about PHP downloads trends last week, they came with an idea that stats should include **downloads numbers for each version** and **the release date** of that version. That would help us separate long-tail dinosaurs from actively adopted packages.

So I closed myself in a closet for 3 days and put together framework downloads grouped by version ([see PR](https://github.com/TomasVotruba/tomasvotruba.com/pull/738/files)). I won't lie, few numbers really surprised me.

## Symfony on Bleeding Edge

They say it's best practice to use LTS version - now Symfony 3.4. I personally prefer living on the edge with the 4.x version, but after feedback from the community, I lowered requirements to Symfony 3.4 as well.

Let's look at a base stone for Symfony applications - [symfony/http-kernel](/package-downloads-by-version/#symfony-http-kernel).

- v4.2 - **1 838 593** downloads monthly - **54 %** of all downloads
- v4.1 - 230 975
- v4.0 - 45 539
- v3.4 - **891 778**

It's that Symfony community doesn't wait on another LTS. It grabs the new features as soon as they're out. Amazing job!

## Laravel wide Spread and Stable

I don't follow Laravel releases much. There is no clear release plan like PHP or Symfony has and they seemed somewhat random to me.
So when I looked at stats of [laravel/framework](/package-downloads-by-version/#laravel-framework), I was surprised there are **basically 2 release/year every ~6 months**.

It's also interesting, that people stick with various versions:

- v5.8 - 623 534 - **30 % adoption**
- v5.7 - 532 232
- v5.6 - 240 762
- v5.5 - 317 489
- v5.4 - 146 119

Other versions have less than 70 k downloads.

It's also notable that **89,5 %** downloads are for Laravel 5.x.

## Zend Injection along with Adoption

This week was [Matthew announced moving Zend to Laminas project](https://mwop.net/blog/2019-04-17-from-zend-to-laminas.html). For users, it technically means just change of `Zend` namespace to `Laminas`, but potentially growth of Zend features thanks Linux Foundation funding. Great news!

How is the [Zend adoption doing now](/package-downloads-by-version/#zend)?

- 27 of 91 packages has an adoption rate of 80 %+
- 51 of 91 packages has an adoption rate of 60 %+

The Zend community is clearly interested in new features, far from "Zend is Dead".

## CakePHP with Peak

- The most **downloaded package is `cakephp/chronos`** - with 290 045 downloads/month. Next packages have only 60-70 000 downloads.

## Nette slowly Adopting

- The backbone for applications - [`nette/application`](/package-downloads-by-version/#nette-application) - **has only 4 % adoption**. No surprise there, since Nette 3.0 was released only on April 2nd, 2019. Keep updating!


Here are [data to this day](https://github.com/TomasVotruba/tomasvotruba.com/blob/6c9df3aa834a213ea1a94d619f4cbc1564ff727e/source/_data/generated/vendor_packages_by_version.yaml), I wonder how they change in 6 months.

<br>

This is just the tip of the iceberg that caught my eyes.

**[Check the full table](/package-downloads-by-version/)** and discover more interesting details about your favorite framework.

<br>

Happy coding!
