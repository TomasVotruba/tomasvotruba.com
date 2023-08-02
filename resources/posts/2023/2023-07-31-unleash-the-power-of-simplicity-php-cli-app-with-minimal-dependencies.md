---
id: 388
title: "Unleash the Power of Simplicity: PHP CLI App with Minimal Dependencies"

perex: |
    I have a couple of open-source CLI apps like Rector, ECS, Class Leak, Config Transformer, Monorepo Builder and Lines, and private ones like Cleaning Checklist, Fixai, Private Rector and Entropy. All of them run in the command line, and some of them [are downgraded to PHP 7.2](/blog/how-to-release-php-81-and-72-package-in-the-same-repository).

    In every project, there is the rule, the fewer dependencies you have, the less work to maintain them. This applies twice to CLI apps distributed with scoped and downgraded /vendor included.

    How to achieve this goal? It's not what I thought.
---

A typical CLI PHP application needs 2 principal packages:

* **console** to make work with command arguments and options easy,
* and **dependency injection container** to avoid creating services manually

<br>

E.g., [ECS](https://github.com/symplify/easy-coding-standard/) required these dependencies:

```json
{
    "require": {
        "symfony/console": "*",
        "symfony/dependency-injection": "*",
        "symfony/http-kernel": "*",
    }
}
```

*The http-kernel is not needed perse, but it [contains container factory](https://www.reddit.com/r/PHP/comments/159lc7j/comment/jtihjh7/?utm_source=share&utm_medium=web2x&context=3) that is handy to build cached container.*

## Where is the Devil?

The more packages we have, the more time we have to gradually invest to maintain, downgrade, and scope them. Downgrading and scoping are challenging, so **every package we can omit** is like taking off a person standing on our shoulders.

<br>

Let's get some actual data. First, we install packages:

```bash
composer require symfony/console symfony/dependency-injection symfony/http-kernel
```

<br>

Then we measure the project using [the lines package](https://github.com/tomasVotruba/lines):

```bash
vendor/bin/lines measure vendor --short --json
```

Here are results:

```json
{
    "filesystem": {
    "directories": 123,
    "files": 773
},
"lines_of_code": {
    "code": 85586,
    "code_relative": 78.7,
    "comments": 23144,
    "comments_relative": 21.3,
    "total": 108730
}
```

We have **85 586 lines of code to maintain in bare `/vendor`**. To compare, the Laravel 10.17 has 121&nbsp;656&nbsp;lines.

<br>

*Note: if we drop http-kernel package, copy factory/caching to our code, we'd have 54 048 lines.*

<br>

## A/B Testing over Short Emotions

As you already know, lately, I've been working with Laravel [to fetch my dependencies](/blog/what-i-prefer-about-laravel-dependency-injection-over-symfony). My goal is to be super productive with the least amount of code. That's what I find fascinating in handyman craftsmanship. Fix problem quickly with few tools and powerful skill.

<br>

Let's make a simple experiment: "What would a minimal CLI project look like in Laravel?"

```json
{
    "require": {
        "illuminate/console": "*",
        "illuminate/container": "*"
    }
}
```

<br>

Let's install those dependencies:

```bash
composer require illuminate/container illuminate/console
```

And measure lines of `/vendor` again:

```json
{
    "filesystem": {
        "directories": 177,
        "files": 1935
    },
    "lines_of_code": {
        "code": 113755,
        "code_relative": 65.1,
        "comments": 60913,
        "comments_relative": 34.9,
        "total": 174668
    }
}
```

Now we have **113 755 lines of code**, about 30 000 more.

<br>

That's even worse than before. But let's not jump to conclusions yet.

<br>

<blockquote class="blockquote text-center">
"Perfection is achieved not when there is nothing more to add<br>
but when there is nothing left to take away."
</blockquote>

<br>

## The Right tool for the Right Job

The older I am, I find inspiration in real material world. **Concrete** is a robust material known for its exceptional durability against force and capacity to withstand heavy explosions.

On the other hand, **iron** stands out due to its flexibility in shaping, allowing for a wide range of applications and designs.

By combining these two - by reinforcing concrete with iron, we can **capitalize on the strengths of both materials**. Composite is essential in many construction and structural engineering projects.

<br>

The Laravel-only project size got me thinking and circled back to our original question. Now let's step aside from personal likeness preferences and emotions and focus on our objective goal:

<br>

**What is required for the CLI app?**

* a console package
* a container package

<br>

The Laravel container package having 6 files in total, and barely 2 containing some logic, make a great candidate for a slim container.

* a console package
* a container package ✅

<br>

What about the console one?

Let's look closer at the composer output on installing the `illuminate/console` package:

<img src="https://github.com/TomasVotruba/tomasvotruba.com/assets/924196/82fb9333-ccf4-428f-ab45-62999c562926" class="img-thumbnail">

**That's 28 packages in total, or 27 in transitional dependency to a single console one.**

<br>

Hm, how about the `symfony/console` one?

<img src="https://github.com/TomasVotruba/tomasvotruba.com/assets/924196/0073e775-62a4-4f17-97b7-5e30b99f5c19" class="img-thumbnail">

Barely 8 packages. That's 20 packages less than Laravel one. But we want actual data, not emotions. How big is our vendor now?

```bash
composer remove illuminate/console
composer require symfony/console
```

And the results?

```json
{
    "filesystem": {
        "directories": 79,
        "files": 315
    },
    "lines_of_code": {
        "code": 34043,
        "code_relative": 77,
        "comments": 10180,
        "comments_relative": 23,
        "total": 44223
    }
}

```

<br>

Not bad - 34 043 lines.

The Symfony and Laravel console packages differs in syntax sugar, so we can use one or another to build any command class.

<br>


## Final Results

Let's sum up and compare the metrics we've done today:

* Symfony-only: 85 586
* Symfony-only (without http-kernel): 54 048
* Laravel-only: 113 755
* **Composite: 34 043**

<br>

This post emphasizes the importance of carefully selecting the most effective and efficient packages when building a CLI package.

Keep your CLI package slim - **you will reduce maintenance, speed up feature development, and contribute to a more sustainable and energy-efficient digital environment**.

Before adding a new package, ask yourself: "What are the other competitive alternatives? How can we compare them?".

<br>

In the next post, I'll share a few tips and hacks on how to make the CLI apps even slimmer, be more polite on the green plane, and make them faster to download.

<br>

Happy coding!
