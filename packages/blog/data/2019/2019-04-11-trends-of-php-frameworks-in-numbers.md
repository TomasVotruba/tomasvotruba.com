---
id: 202
title: "Is Zend Dead? Is Laravel Losing Breath? Trends of PHP Frameworks in Numbers"
perex: |
    We often hear "Zend is dead", "Laravel is the most favorite", "X is trending on Google", "F is Dead, Migrate!" etc. But are these statements supported by any research or numbers? No.
    <br>
    <br>
     **I was curious, how all PHP frameworks are doing, so I've looked at downloads and trends of each PHP framework**. And here are the results.

tweet: "New Post on #php 🐘 blog: Is #zendframework Dead? Is #laravel Losing Breath? Trends of PHP Frameworks in Numbers        #cakephp #nettefw #symfony #yii #php #mvc"
tweet_image: "/assets/images/posts/2019/trends/trends.png"

updated_since: "March 2021"
updated_message: |
    Updated with 1000 → **500 downloads**/day and 24 months to **12 months** minimal age. Link and screen to brand new website [phpfwtrends.org](https://phpfwtrends.org/) added.
---

There are plenty *What is the Best PHP framework(s) in 20X* posts all over the Internet. Usually written by someone, who uses one of them and prefer them. It's pretty easy to put out many arguments, why is your favorite framework "the best framework". These posts mislead the reader because only someone using all PHP frameworks out there in equal time and skill could evaluate it objectively.

## Numbers vs. Vague Statements

I wanted to separate feelings and opinions of influencers - mostly framework leads or people paid for working in the framework (myself including) - from numbers and facts.

The active community, **with long duration and rising trend** will provide a much better idea, how the framework is really successful. You can use marketing and made up stories, but if the community isn't happy with the framework in the long term, the numbers will show.

## Methodology

I've downloaded a few numbers from Packagist API for every package in the vendor name. E.g. for Symfony framework, all `symfony/*` packages are included.

Then I took the **sum of package downloads in the last 6 months** and the **trend in last 6 months**. From those, I made an average for the whole framework.

Small packages with less than 500 downloads/day or younger than 12 months are excluded as outliers.

## There is More...

- What packages are active in those frameworks?
- What frameworks are dinosaurs - with big long-tail effect in total downloads, but losing in trends?
- What frameworks have [hidden](/blog/2018/07/30/hidden-gems-of-php-packages-nette-utils/) [cool](/blog/2018/08/13/hidden-gems-of-php-packages-symfony-finder-and-spl-file-info/) utils packages?

<br>

There is a **detailed table** where you can find these answers:

<a href="https://phpfwtrends.org" class="btn btn-warning btn-2x mt-4 mb-5">See full PHP Framework Trends table</a>

<a href="https://phpfwtrends.org">
    <img src="https://user-images.githubusercontent.com/924196/110786128-0c566700-826c-11eb-912a-a8f79e177665.png" class="img-thumbnail">
</a>

<br>

Next time you'll read <em>"X is the Best PHP Framework..."</em>, ask for numbers behind the statement and share this table.

<br>

Happy coding!
