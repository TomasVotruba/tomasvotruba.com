---
id: 415
title: "Scope and Downgrade your PHP Tools for&nbsp;Everyone&nbsp; to Use"
perex: |
    Yesterday, I came across a cool PHP tool. I wanted to try it, but the installation instructions were a bit tricky. The tool required a specific PHP version and a specific version of each dependency. It required Symfony 5.4+, but our project has Symfony 3.3. I was unable to use it.

    Many PHP tools suffer from the same issue, so I thought I'd share a way to do it better.
---

This is a tiny part of `composer.json` that shows the point:

```json
{
    "require": {
        "php": "^7.4|^8.0",
        "laravel/container": "^8.0|^9.0|^10.0|^11.0",
        "symfony/console": "^5.4|^6.4|^7.0",
        "symfony/finder": "^5.4|^6.0|^7.0"
    }
}
```

The tool has to **allow every version of every package** so that people can install it. It doesn't bring any value to the package. You might suggest "use a PHAR" - that won't work, as it has the same PHP and version requirements. Just in a single "ZIP file".

<br>

What happens in the next 2 years?

```diff
 {
     "require": {
-        "php": "^7.4|^8.0",
+        "php": "^7.4|^8.0|^9.0",
-        "laravel/container": "^8.0|^9.0|^10.0|^11.0",
+        "laravel/container": "^8.0|^9.0|^10.0|^11.0|^12.0|^13.0",
-        "symfony/console": "^5.4|^6.4|^7.0",
+        "symfony/console": "^5.4|^6.4|^7.0|^8.0|^9.0",
-        "symfony/finder": "^5.4|^6.0|^7.0"
+        "symfony/finder": "^5.4|^6.0|^7.0|^8.0|^9.0"
     }
```

<blockquote class="blockquote text-center">
When we sweep it under the rug, it doesn't work.
<br>
The rug will eventually start to rise.
</blockquote>

## Limit for Tool Maintainer

If you decide to maintain your package this way, you put a lock on your utils code:

* You can't use promoted properties from PHP 8.0 because code has to work on PHP 7.4.
* You can use union types, new `str_*()` functions and so on
* Your code becomes more and more obsolete with every new PHP version.

The same goes for packages you use. Once you use Symfony 5.4, 6, and 7 at the same time, you have to look for features that are available in Symfony 5.4 but also in Symfony 7.0. You cannot use new features, you cannot use deprecated and removed features either.

Soon, you **support everything for everyone**.

<img src="/assets/images/posts/2024/interconnected.jpg" class="img-thumbnail" style="max-width: 30em">

They joy of fresh coding is slowly going away.

<blockquote class="blockquote text-center">
It's like driving a Tesla car. But instead of an electricity-powered engine,
<br>you'd have to use your legs and bike pedals.
</blockquote>

## Result? Abandoned Packages

Frustrations from struggling with legacy code are among the reasons developers leave their paid jobs. It's no surprise that these packages—once cool and useful tools—are now slowly abandoned and forgotten. Even merging PRs with diffs like above takes months. It takes a few more months to tag and release a new version.

It's a pity because these PHP tools are often handy and well-written. They help you improve code, monitor code quality, or make your code style consistent.

<br>

E.g. check this `composer.json` snippet from [php-cs-fixer](https://github.com/PHP-CS-Fixer/PHP-CS-Fixer/blob/930dc93a1b90eb991d13e2766a340aa6922d4a2c/composer.json#L22-L47):

```json
{
    "require": {
        // ...
        "symfony/console": "^5.4 || ^6.0 || ^7.0",
        "symfony/event-dispatcher": "^5.4 || ^6.0 || ^7.0",
        "symfony/filesystem": "^5.4 || ^6.0 || ^7.0",
        "symfony/finder": "^5.4 || ^6.0 || ^7.0",
        "symfony/options-resolver": "^5.4 || ^6.0 || ^7.0",
        // ...
    }
}
```

<br>

## New major dependency version? = Unable to install

The php-cs-fixer uses roughly 20 dependencies. The moment a major version of any of those 20 dependencies is out, and your projects start to use it, you're unable to install the tool. That's 20 possible BC breaks your tools users can experience.

<br>

## Where do these tools bring the most impact?

The irony is that the older the project, the more helpful these tools are. It's quite easy to have high-quality code on a PHP 8.2+ project. Running a standard coding tool is nice, but it does not elevate the codebase to another level.

On the other hand, if your project uses PHP 5.4, using a tool just to fix all spaces and indents will move it light-years ahead.

<blockquote class="blockquote text-center mt-5 mb-5">
"If your old bike gets GPS and an electric power engine with battery,<br>
you'll notice it more than your car's Bluetooth firmware upgrade."
</blockquote>

The older the project is, the more value from such tooling gets.

<br>

## What's the way out?

As **maintainers**, we want to:

* use the latest PHP
* use the latest *favorite framework*
* deliver the tool to as many developers as possible
* make it fun to create but also easy to maintain in years to come
* make it easy to contribute to others
* **have fun** in long term

<br>

As **developers who want to use a tool**, we want to:

* install the package with `composer require x/y`
* use it on my project

<br>

In e-commerce, the solution is called **omnichannel**. Shops sell all kinds of goods; you can buy them and choose your way to deliver. Do you prefer the post office? It's there. Do you fancy DHL with a driver calling you? We got it covered. Do you want to pick it up in your country's standard boxes?


<img src="/assets/images/posts/2024/delivery.jpg" class="img-thumbnail" style="max-width: 15em">

<br>

Does the size of post office boxes limit the goods e-commerce can deliver?

No.

<br>

The sold goods are **completely separated from the means of delivery**.

<br>

* What if the tool development process and the package delivery to users were wholly separated, too?
* What if you could create PHP tools in PHP 9.x-dev, but even users on PHP 7.3 could enjoy it from day one?
* What if you did not have to invest any time in dealing release with this?

You'd be free to create and innovate, while users would be free to use your tool.

<br>

## Separate Development and Delivery

This is not an idea in the air. We have used this approach for years for [Easy Coding Standard](https://github.com/easy-coding-standard/easy-coding-standard), for [Rector](http://github.com/rectorphp/rector-src), and PHPStan uses it too.
Many WordPress plugins [are on board](https://leoloso.com/) as well.

<br>

That's why these tools are so popular, because it's easy to install them on any kind of project.

<br>

In the software world, the process is called **downgrade and scope**.

* **Downgrade** is narrowing down features from the latest PHP to the lowest PHP you want to support, typically PHP 7.2
* **Scope** allows to install Symfony 7.1 code on a project running Symfony 3 by prefixing tool classes in their vendor

<br>

You can read more about the technical process here:

* [How to release PHP 8.1 and 7.2 package in the Same Repository](https://tomasvotruba.com/blog/how-to-release-php-81-and-72-package-in-the-same-repository/)
* [How to bump Minimal PHP Version without Leaving Anyone Behind?](https://getrector.com/blog/how-to-bump-minimal-version-without-leaving-anyone-behind)
* [How to Develop Sole Package in PHP 8.1 and Downgrade to PHP 7.2](https://tomasvotruba.com/blog/how-to-develop-sole-package-in-php81-and-downgrade-to-php72/)

<br>
<br>

Plug this process into your tool and make it fun to maintain and easy to install again:

```diff
 {
     "require": {
-       "php": "^7.4|^8.0",
+       "php": "^8.2",
-       "laravel/container": "^8.0|^9.0|^10.0|^11.0",
+       "laravel/container": "^11.0",
-       "symfony/console": "^5.4|^6.4|^7.0",
+       "symfony/console": "^7.1",
-       "symfony/finder": "^5.4|^6.0|^7.0"
+       "symfony/finder": "^7.1"
    }
}
```

<br>

Happy coding!
