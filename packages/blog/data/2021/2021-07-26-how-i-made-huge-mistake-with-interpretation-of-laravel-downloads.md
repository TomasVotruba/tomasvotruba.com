---
id: 330
title: "How I made Huge Mistake with Interpretation of Laravel Downloads"
perex: |
    The [PHP FW Trends](/blog/sunsetting-php-framework-trends) project was introduced 2 years ago to compare download statistics over "what they say on the internet". The methodology is based on Packagist data, which worked but had some flaws.


    Also, monorepo and split packages downloads lead to a problem when one monorepo download has hidden 10-30 package downloads. **There is no way to detect what packages are downloaded or used exactly**, so [this project should be stopped to avoid showing miss-leading](/blog/2020/03/09/art-of-letting-go/) data far from reality.

tweet: "New Post on the 🐘 blog: How I made Huge Mistake with Interpretation of Laravel Downloads"
---

This project had some controversial feedback, mainly because the numbers put some frameworks into a lower position than framework communities expected.

Let's look at 3 main problems.

## Composer vs. Not Composer Downloads

Most frameworks are accessible via packagist nowadays. If you need it, you use `composer require x/y`, and the package is there. But not all frameworks that are used in the PHP community have this install model.

One of them is [phalcon/cphalcon](https://packagist.org/packages/phalcon/cphalcon), which is also distributed on packagist but not exclusively. This means unknown X downloads from `wget`, `git clone` from the internal repository or zip files. I could leave this package with minimal downloads from packagist, suggesting that nobody uses this framework, or remove it from stats altogether, suggesting similar status.

Both wrong solutions had to be explained in methodology that very few people read based on feedback on Reddit.

## How Laravel adds to Symfony?

Next problem was that Laravel depends on Symfony with [most of the packages](https://packagist.org/packages/laravel/framework). E.g. `illuminate/console` depends on `symfony/console` (see [packagist](https://packagist.org/packages/illuminate/console)).

10 millions download of the Laravel package probably produces 10 million downloads of Symfony packages. In some cases, even more, because one Laravel package can depend on multiple Symfony packages.

This has to be reflected in negated downloads per each Laravel package to Symfony. But was it good enough?

## How is Laravel Itself Downloaded?

Let's get to the

I only had experience from ~10 Laravel projects, so what might be obvious was some was hidden for me. It was not easy to find out why the numbers are so skewed against it, but now I know. I'm sorry about that.

Let's look where I made the mistake in the interpretation:

<img src="https://user-images.githubusercontent.com/924196/127477591-8b1550a8-f2f9-41ad-8492-0b16496663f8.png" class="img-thumbnail">

<br>

In the previous paragraph, I linked `illuminate/console`. How can you download it?

```bash
composer require illuminate/console
```

but also

```bash
composer require illuminate/framework
```

The first package has 2.x million downloads, and the second has 24.x million downloads. Yet both will get us `illuminate/console` content.

<br>

## Can You See the Problem?

* `illuminate/framework` will give us all Laravel packages but will mark only one download
* `illuminate/console` will give us one Laravel package and will mark only 1 download too

I assumed that `illuminate/framework` is not used for downloading packages anymore (data are for the last 12 months), like Symfony, Nette, Doctrine, and Zend developers don't use monorepo for downloads. In ~10 Laravel projects, the dependencies were always used separately, one package by another. But that was not a rule but rather an exception.

**Saying that there is no way to find out real per-package downloads numbers for Laravel.** We can only guess, and guess is not good enough to make final assumptions.

Until the most used PHP frameworks use the per-package download approach and use packagist exclusively, these stats cannot be considered representative.

## It's not about Numbers, It's about Emotions

Last but not least, I've realized using your favorite tool is not about number of other people using it or not using it. The most important is to have fun - so if you're having fun, use whatever you need to keep the fun on.

<br>

For these reasons, I'm sunsetting the [phpfwtrends.org](https://phpfwtrends.org/) project.

<br><br>

Thank you for constructive feedback and patience till I figure it out.

<br>

Happy coding!
