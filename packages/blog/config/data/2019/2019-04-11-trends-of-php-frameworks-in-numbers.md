---
id: 202
title: "Is Zend Dead? Is Laravel Losing Breath? Trends of PHP Frameworks in Numbers"
perex: |
    I often hear "Zend is dead", "Laravel is the most favorite", "X is trending on Google", "F is Dead, Migrate!" etc. But are these statements supported by any research or numbers? No.
    <br>
    <br>
     **I was curious, how all PHP frameworks are doing, so I've looked at downloads and trends of each PHP framework**. And here are the results.

tweet: "New Post on #php 🐘 blog: Is #zendframework Dead? Is #laravel Losing Breath? Trends of PHP Frameworks in Numbers        #cakephp #nettefw #symfony #yii #php #mvc"
tweet_image: "/assets/images/posts/2019/trends/trends.png"
---

There are plenty *What is the Best PHP framework(s) in 20X* posts all over the Internet. Usually written by someone, who uses one of them and prefer them. It's pretty easy to put out many arguments, why is your favorite framework "the best framework". These posts mislead the reader because only someone using all PHP frameworks out there in equal time and skill could evaluate it objectively.

## Numbers vs. Vague Statements

I wanted to separate feelings and opinions of influencers - mostly framework leads or people paid for working in the framework (myself including) - from numbers and facts. The active community, **with long duration and rising trend** will provide a much better idea, how the framework is really successful. You can use marketing and made up stories, but if the community isn't happy with the framework in the long term, the numbers will show.

## Methodology

I've downloaded a few numbers from Packagist API for every package in the vendor name. E.g. for Symfony framework, all `symfony/*` packages are included.

Then I took the **sum of package downloads in the last 12 months** and the **trend in last 12 months**. From those, I made an average for the whole framework.

Some packages were out only 6 months, but rising in downloads with + 8 000 % trend, because they were a new split of monorepo. Imagine "X is Rising in + 8 000 % downloads in a Year!" - That's marketing nonsense. That's why **small packages with less than 1000 downloads a day or less than 12 months old are excluded as outliers**.

Instead of copy-pasting conditions here, check the full process in [this PR](https://github.com/TomasVotruba/tomasvotruba.com/pull/717).

## Results

Cut the small-talk, these are the numbers (to the day of publishing this post).

<br>

<div class="alert alert-sm alert-danger mt-3" role="alert" markdown=1>
This table is out of date as the days go by. **See [updated results](/php-framework-trends)**.
</div>

<table class="table table-bordered table-responsive table-striped">
    <thead class="thead-inverse">
        <tr>
            <th class="text-center">
                Framework
            </th>
            <th class="text-center">Monthly Average</th>
            <th class="text-center">Yearly Total</th>
            <th class="text-center">Year Trend</th>
        </tr>
    </thead>

    <tr>
        <th>
            CakePHP
        </th>
        <td class="text-right">
            41 826
        </td>
        <td class="text-right">
            11 643 390
        </td>
        <td class="text-right">
            <strong>
                <span class="text-success">+ 127 %</span>
            </strong>
        </td>
    </tr>
    <tr>
        <th>
            Symfony
        </th>
        <td class="text-right">
            3 011 473
        </td>
        <td class="text-right">
            881 984 370
        </td>
        <td class="text-right">
            <strong>
                <span class="text-success">+ 43 %</span>
            </strong>
        </td>
    </tr>
    <tr>
        <th>
            Laravel
        </th>
        <td class="text-right">
            187 429
        </td>
        <td class="text-right">
            56 268 780
        </td>
        <td class="text-right">
            <strong>
                <span class="text-success">+ 14 %</span>
            </strong>
        </td>
    </tr>
    <tr>
        <th>
            Zend
        </th>
        <td class="text-right">
            550 187

        </td>
        <td class="text-right">
            165 652 860
        </td>
        <td class="text-right">
            <strong>
                <span class="text-success">+ 12 %</span>
            </strong>

        </td>
    </tr>
    <tr>
        <th>
            Nette
        </th>
        <td class="text-right">
            26 233
        </td>
        <td class="text-right">
            8 319 180

        </td>
        <td class="text-right">
            <strong>
                <span class="text-success">+ 8 %</span>
            </strong>
        </td>
    </tr>
    <tr>
        <th>
            Yii
        </th>
        <td class="text-right">
            52 151
        </td>
        <td class="text-right">
            16 470 660
        </td>
        <td class="text-right">
            <strong>
                <span class="text-success">+ 6 %</span>
            </strong>
        </td>
    </tr>
</table>

It seems that the most active community is now CakePHP. I've been following their very dynamic upgrade path with [Rector sets](https://github.com/rectorphp/rector/tree/master/config/level/cakephp), so it makes sense.

## There is More...

- What packages are active in those frameworks?
- What frameworks are dinosaurs - with big long-tail effect in total downloads, but losing in trends?
- What frameworks have [hidden](/blog/2018/07/30/hidden-gems-of-php-packages-nette-utils/) [cool](/blog/2018/08/13/hidden-gems-of-php-packages-symfony-finder-and-spl-file-info/) utils packages?

<br>

There is a **detailed table** where you can find these answers:

<a href="/php-framework-trends/" class="btn btn-success">See full PHP Framework Trends table</a>

<br>

I dare you to **find any flaws in these numbers**. Kick me in the nuts in the comments ↓

<br>

So next time you'll read "X is the Best PHP Framework...", ask for numbers behind the statement and share this table.

<br>

Happy coding!
