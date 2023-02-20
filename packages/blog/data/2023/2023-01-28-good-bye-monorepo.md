---
id: 375
title: "Good Bye, Monorepo"
perex: |
    I've been using monorepo for almost a decade, and I was thrilled with that. Yet, in recent years I've noticed a few glitches in the workflow. The more popular the package became, the more visible frictions were.


    How do I see the monorepo approach in 2023, and what do I want to do differently?
---

## For Frameworks? Yes

For frameworks like Symfony, Laravel, or Laminas, the monorepo is the best choice. The packages like http, dependency injection, forms, or validator are very close to each other. Their cohesion is high = if we change, e.g., validator, we'll update the forms package too.

Monorepo makes it easy to release new major releases like the upcoming Laravel 10. Users of the framework appreciate that the whole upgrade is done in a single run.

## For Projects? No

In one project I've been working on, we were considered monorepo for applications. Imagine you have an e-commerce for T-shirts, another for selling cars, and another for rentals. You are a developer company and you maintain all of them. Why not put them in a single repository? That would save you time for upgrades, CI setup and make changes cheaper, right?

Yes, that's true. On the other hand, 3 domains are now leaking across the repository, the pull request to various projects clashes, and any significant change may leak to other projects. E.g., an upgrade to PHP 8.1 has to be done in sync.

Saying that, I'd definitely not recommend using a monorepo for projects.

## For Tools? No

Now we get to the only monorepo I maintain - [the Symplify monorepo](https://github.com/symplify/symplify/). You may know it from hits like [Easy Coding Standard](/blog/introducing-up-to-16-times-faster-easy-coding-standard), [Config Transformer](/blog/2020/07/27/how-to-switch-from-yaml-xml-configs-to-php-today-with-migrify), [Monorepo Builder](/blog/2018/10/08/new-in-symplify-5-create-merge-and-split-monorepo-with-1-command) or [Vendor Patches](/blog/2020/07/02/how-to-patch-package-in-vendor-yet-allow-its-updates).

It contains about 30 packages and about 5 tools.

<br>

The monorepo is very convenient in situations we deal with code quality:

* applying coding standard
* PHP upgrades with Rector
* fixing PHPStan hundreds of type errors with Rector
* generating tests with [TestGen AI](https://testgenai.com/)
* releasing a new version with a single command
* downgrading for all packages happens at once, and splitting to another repository is relatively easy to setup

One commit and single pull request improve 30 packages at once. That's pretty smooth. At least for me as a maintainer.

<br>

## What about package users and Monorepo?

But for you as a contributor or developer who uses `compose require symplify/*`, it gets a little bit dicey:

* When a new release comes, there is 10 other package upgrade you don't use as well
* When you want to contribute to ECS, it's crazy - you have to navigate to `packages/easy-coding-standard` - there are `/src` and more nested `/packages` directory
* It's unclear what is the relationship between packages, which one you should change, and where to add tests
* When you opened a pull request to `symplify/easy-coding-standard`, it was closed instantly because that package was read-only
* Are you sharing a link to the tool? It's not `symplify/symplify`, it's somewhere else

<br>

These problems have been here for at least the past 4 years, and I knew we must solve them. It helped me look at other packages - apart from frameworks, the packages are doing fine in their repositories.

<blockquote class="blockquote text-center">
"If you don't think you can run a marathon,<br>
go to the end of your street."
</blockquote>

After having monorepo for years, my thinking was very narrow-minded, and I was scared. I needed more information and genuine experience. I've decided to make a little experiment.

<br>

In November 2022, I started to separate Symplify packages into their repository. That way, I could see how it works and how the release and maintenance work.

This way, we've separated tools from the Symplify monorepo to their repositories:

* [easy-coding-standard/easy-coding-standard](https://github.com/easy-coding-standard/easy-coding-standard)
* [config-transformer](https://github.com/symplify/config-transformer)
* [monorepo-builder](https://github.com/symplify/monorepo-builder)
* [phpstan-rules](https://github.com/symplify/phpstan-rules)
* [vendor-patches](https://github.com/symplify/vendor-patches/)
* [coding-standard](https://github.com/symplify/coding-standard)

<br>

I waited to announce this change for two months because I was curious how the community would react. There were no complaints. Quite the contrary - more people started to contribute fixes!

...and how is that for me?

## I Love it!

Before, I had to load 30 packages in an IDE, and my mind had to be careful about the changes. Now **I open the single package with a relaxing mind**.

I've cleaned up the packages from useless mutual dependencies, made them smaller and more to the point.

<br>

**I was taking the courage to write this post** because I wrote about 5 posts that made monorepo like a silver bullet. Now it's time to share the experience and past mistakes - I know I've learned much from it, and I hope you have too.

<br>

Happy coding!
