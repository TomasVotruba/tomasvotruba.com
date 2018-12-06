---
id: 64
title: "United PHP 7.1 Adoption 6 Months Later"
perex: |
    A year since it's release and 6 months since GoPHP71 initiative.
    PHP 7.1 is **the fastest adopted** minor version of PHP already beating PHP 7.0.
    <br><br>
    How is adoption going in open-source and why it should continue from bleeding-edge projects?
tweet: "Is #PHP 7.1 the fastest version to be adopted by PHP community? And what projects are already requiring it as minimum version #symfony #doctrineORM #laravel #nettefw"
tweet_image: "/assets/images/posts/2017/go-php-71-later/unity.jpg"
---

<br>

<blockquote class="blockquote text-center">
    "United we stand, divided we fall."
    <footer class="blockquote-footer">ancient Greek storyteller Aesop</footer>
</blockquote>

<br>


<img src="/assets/images/posts/2017/go-php-71-later/unity.jpg">


## How is GoPHP71.org Doing?


I've created a page [GoPHP71.org](https://gophp71.org/) half year ago, inspired by [GoPHP7.org](http://gophp7.org/) and by [Go PHP 5](https://www.garfieldtech.com/blog/go-php-5-go) - read this one if you care about background values of this movement.


In [release post](/blog/2017/06/05/go-php-71/) I've explained **why right to PHP 7.1** and not PHP 7.0, how important is **united community** in this and how this can **bring positive energy to open-source** and as well host providers upgrades.

<br>

From 2 projects in *June 2015*:

<img src="/assets/images/posts/2017/go-php-71/first-version.png">

Now there are **11 projects**, including *big 3* - Symfony, Doctrine and Laravel.

<img src="/assets/images/posts/2017/go-php-71-later/current-version.png">



Is your project missing? [Go and it!](https://github.com/TomasVotruba/gophp71.org/edit/master/_data/projects.yaml)



### Prove beats Promise - Packagist Stats


To support "PHP 7.1 is the fastest adopted minor version of PHP" statement, [Jordi](https://seld.be/) recently released [PHP Versions Stats - 2017.2 Edition](https://seld.be/notes/php-versions-stats-2017-2-edition) with very nice result from packagist stats:

<img src="/assets/images/posts/2017/go-php-71-later/composer-bump.png" class="img-thumbnail">



## Great Job, PHP Community!


It makes me very happy, that **people from PHP community are able to [synchronize](/blog/2017/10/30/what-can-you-learn-from-menstruation-and-symfony-releases/)** despite their different opinions on things.
<br>


### Special Thanks to Doctrine Project

I really loved this [Doctrine bump PHP 7.1 announcement](http://www.doctrine-project.org/2017/07/25/php-7.1-requirement-and-composer.html). I completely agree with "Why dropping PHP support in a minor version is not a BC break" part. If you think PHP version bump is BC break, you should read it.

I admit [I wasn't nice](/blog/2017/03/27/why-is-doctrine-dying/) to Doctrine Project this Spring and **I'm sincerely sorry about that**. I'm trying to [influence this better way](/blog/2017/10/16/how-to-use-repository-with-doctrine-as-service-in-symfony/).

Ever since I **see Doctrine community are doing great** - from [removing YAML references](https://github.com/doctrine/doctrine2/pull/5932), to [cleaner Symfony support](https://github.com/doctrine/DoctrineBundle/pull/727).



## But I want to go PHP 7.0

Still not convinced about reasons? Check [this issue](https://github.com/php-ai/php-ml/issues/148) on `php-ai/php-ml` library.

[@dmonllao poses question or rather idea](https://github.com/php-ai/php-ml/issues/148#issuecomment-346790142) there: *I want to take is slowly <or another reason> and go only to PHP 7.0*.

Let me explain how that could influence PHP ecosystem and slow down productivity of many projects:

- Imagine that in 6 months all of those 11 projects on gophp71.org will **require PHP 7.1 in their LTS versions**.
- *Moodle* (could be any other package, it's just example) decides to go with **PHP 7.0**.
- If you work with PHP, there is quite big chance you'll be using at least one of those 11 packages.
- Let's say you want to use newest features + LTS, so you **bump your local nad server to PHP 7.1**.

All good for now, but then:

- You need to use *Moodle* in your project. **Its code contains only PHP 7.0 features**.
- Your code naturally **extends or implements 3rd party classes**. You can use PHP 7.1 on most of them - e.g. `void` and nullable typehints of interfaces.
- But then your need to extends *Moodle*'s code and **you have to be careful and use only PHP 7.0 features**. Features like `void` or `nullable` would break it.


### Result? Double Measures & Dichotomic Coding

- You have to have **2 different coding standards** - one for PHP 7.0 and one for PHP 7.1 with various paths to scan.
- If you use static analysis like [PHPStan](/blog/2017/01/28/why-I-switched-scrutinizer-for-phpstan-and-you-should-too/), you have to have 2 configs again to validate code properly.
- 2 testing approaches etc.


And that's **only 1 package with different PHP version**. Imagine there would another package that requires PHP 7.2... or if you combine PHP 7.0 and PHP 7.1 interface in single class.



## But I don't want to Drop Support for PHP 7.0

<img src="/assets/images/posts/2017/go-php-71-later/old-releases.png" class="img-thumbnail">

I've borrowed this amazing picture from [Jordi](https://seld.be/notes/php-versions-stats-2016-2-edition).

You **can keep support for older PHP version** even if you bump minimal requirement to PHP 7.1, just won't add new features to them.



### Spread the Word

At the moment only 4 projects on are tagged and it will take some timer before this becomes mainstream. Yet, we can see obvious trend moving to PHP 7.1 as minimal requirement. **Thanks to community and people that are bold enough to ask the question** or [even sending a PR](https://github.com/laravel/framework/pull/21995).


<br>


**If you see some next project bumping to PHP 7.0, think about possible consequences of that decision.**

<br><br>

Happy bumping!
