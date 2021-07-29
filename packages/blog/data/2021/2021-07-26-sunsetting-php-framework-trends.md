---
id: 330
title: "Sunsetting PHP Framework Trends"
perex: |
    The [PHP FW Trends](/blog/sunsetting-php-framework-trends) project was introduced 2 years ago with a goal to compare download statistics over "what they say on the internet". The methodology was based on Packagist data, which worked but had some flaws.
    <br><br>
    Also, monorepo and split packages downloads lead to a problem, when 1 monorepo download has hidden 10-30 package downloads. **There is no way to detect what packages are downloaded or used exactly**, so [this project should be stopped to avoid showing miss-leading](/blog/2020/03/09/art-of-letting-go/) data far from reality.

tweet: "New Post on the 🐘 blog: Sunsetting PHP Framework Trends"
---

This project had some controversial feedback, mostly because the numbers very putting some frameworks into lower position than framework communities expected.

Let's look at 3 main problems.

## Composer vs Not Composer Downloads

Most framework are accessible via packagist nowadays. If you need it, you use `composer require x/y` and the package is there. But not all frameworks that are used in PHP community have this install model.

One of them is [phalcon/cphalcon](https://packagist.org/packages/phalcon/cphalcon) that is also distributed on packagist, but not exclusively. This means there is unknown X downloads from `wget`, `git clone` from internal repository or from zip files. I could leave this package with minimal downloads from packagist, suggesting that nobody uses this framework, or remove it from stats completely suggesting similar status.

Both bad solutions that had to be explained in methodology, that very few people
actually read based on feedback on Reddit.

## How Laravel adds to Symfony?

Next problem was that Laravel depends on Symfony with [most of the packages](https://packagist.org/packages/laravel/framework). E.g. `illuminate/console` depends on `symfony/console` (see [packagist](https://packagist.org/packages/illuminate/console)).

10 millions download of Laravel package probably produces 10 millions downloads of Symfony packages. In some case even more, because one Laravel package can depends on multiple Symfony packages.

This had to be reflected in negated downloads per each Laravel package to Symfony. But was it good enough?

## How is Laravel Itself Downloaded?

Let's get to the

I only had experience from ~10 Laravel projects, so what might be obvious was some, was hidden for me. It was not easy to find out why the numbers are so skewed against it, but now I know. I'm sorry about that.

Let's look where I made the mistake in the interpretation:

<img src="https://user-images.githubusercontent.com/924196/127477591-8b1550a8-f2f9-41ad-8492-0b16496663f8.png" class="img-thumbnail">

<br>

In previous paragraph I linked `illuminate/console`. How can you download it?

```bash
composer require illuminate/console
```

but also

```bash
composer require illuminate/framework
```

First package has 2.x million downloads, and second has 24.x million downloads. Yet both will get us `illuminate/console` content.

<br>

Can you see the problem?

* `illuminate/framework` will give us all Laravel packages, but will mark only 1 download
* `illuminate/console` will give us one Laravel package, and will mark only 1 download too

My assumption was, that `illuminate/framework` is not used for downloading packages anymore (data are for last 12 months), as Symfony, Nette, Doctrine and Zend developers don't use monorepo for downloads. In ~10 Laravel projects, the dependencies were always used separately, one package by another. But that was not rule, but rather exception.

**Saying that, there is no way to find out real per-package downloads numbers for Laravel.** We can only guess and guess is not good enough to make a final assumptions.

Until the most used PHP frameworks use per-package download approach and use packagist exclusively, these stats cannot be considered representative.

<br>

For these reasons, I'm sunsetting [phpfwtrends.org](https://phpfwtrends.org/) project.

<br><br>

Thank you for constructive feedback and patience till I figure it out.

<br>

Happy coding!










...
