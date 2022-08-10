---
id: 368
title: "How to Develop Sole Package in PHP 8.1 and Downgrade to PHP 7.2"
perex: |
    The PHP downgrades is a thing. Most beneficial for package developers who want to move forward to the latest PHP features, but also want to **keep easy access to the wide PHP community and legacy projects**.
    <br><br>
    The downgrade of tool is a no-brainer - [we downgrade the whole tool](https://getrector.org/blog/how-to-bump-minimal-version-without-leaving-anyone-behind) including vendor and we know it will run on PHP 7.2.
    <br>
    <br>
    But how to achieve the same with package with separated dependencies?

---

<br>

<blockquote class="blockquote text-center mt-5 mb-5">
    "You have an awesome package. But if people can't install it,<br>
    nobody will use it."
</blockquote>

 <br>

## 1. Developer in one Repository, Release in Another

Before we get into the downgrade of package itself, we have to have repository prepared for downgraded release. We have 1 repository to developer code, and another to release it.

<br>

Do you know such example from packages in your project?

- `symfony/symfony` is a development repository that is split into many `symfony/*` packages that we download via composer
- `rector/rector-src` is a development repository that we downgrade and prefix to `rector/rector`
- `phpstan/phpstan-src` is a development repository that we downgrade, wrap to phar and prefix to `phpstan/phpstan`

With 2 different repositories, we can put a downgrade process in the middle with single line in GitHub Action.

<br>

Do we have to repositories? Now comes the fun part.

<br>

## 2. What Required Dependencies do We Control?

With classical downgrade we can work with the newest dependencies possible, because we also publish and downgrade the vendor. With a package it's slightly different.

The package is still part of the dependency tree as we know it. We can only downgrade the `/src` of our package, and we still depend on packages in `"require"` section of `composer.json`.

There are 2 challenges we have to deal with. What packages are part of our PHP 8.1 ecosystem? Let's look at example of `symplify/phpstan-rules` that I wanted to release as downgraded package. It has awesome set of PHPStan rules that come very handy on legacy projects.

<br>

How does the `composer.json` look like?

```json
{
    "require": {
        "php": ">=8.1",
        "nikic/php-parser": "^4.14.0",
        "nette/utils": "^3.2",
        "phpstan/phpstan": "^1.8.1",
        "symplify/astral": "^11.0.9",
        "symplify/composer-json-manipulator": "^11.0.9",
        "symplify/package-builder": "^11.0.9",
        "symplify/smart-file-system": "^11.0.9"
    }
}
```

We see we have 3 external packages that we cannot influence, let's forget those for now.

### Getting Rid of Dependencies

Then we see 4 Symplify packages that we own. Those 4 Symplify packages require PHP 8.1, and will not allow to install this dependency on PHP 7.2. **What can we do about it?**

* we can downgrade those 4 packages too
* we can eliminate the dependency and inline the needed code

<br>

The 1st options seems as obvious one, yet we would have to also downgrade all the dependencies of those packages and all dependencies of those dependencies... suddenly we don't work with a single package, but with matrix of 4 times *n* packages.

Maybe the easier option is to review the need for our own packages. **If we require package for 10 or so classes, we could inline them** and solve the problem.

<br>

I explore the classes and see the need for 3 of these dependencies is matter of 2 classes. Let's copy them so we can downgrade them. The last package `symplify/astral` was a heavy dependency, but in the end I moved 5 classes to the `src` and made it work.

<br>

**Now we have single `/src` directory with code we own**. The rest of dependencies is external. Let's look at those.



## 3. What external Dependencies Allow Target version?

This is variable out of our control, but common standard is to target PHP 7.2 nowadays as minimum version. If not, you can always allow multiple package versions.

How about our specific packages?

```json
{
    "require": {
        "php": "^7.2|^8.0",
        "nikic/php-parser": "^4.14.0",
        "nette/utils": "^3.2",
        "phpstan/phpstan": "^1.8.1"
    }
}
```

* the `nikic/php-parser` required PHP 7.0 - we're good ✅
* the `nette/utils` requires PHP 7.2 ✅
* the `phpstan/phpstan` requires PHP 7.2 ✅

<br>

This looks great - all our dependencies require PHP 7.2 at least, so this package is now installable on PHP 7.2.

<br>

## 4. Add the Downgrade Step

The last step we have to do, is add the actual downgrade to release step. We have our PHP 8.1 `composer.json` for development, and the one above for PHP 7.2+. This process is same for all the downgrades. Do you want to learn more about it? [I wrote about it here](https://getrector.org/blog/how-to-bump-minimal-version-without-leaving-anyone-behind).

<br>

And the result?
We've already installed the package on PHP 7.4 project, use it in CI and discover bugs with 110 new PHPStan rules. Life is great :)


<blockquote class="twitter-tweet"><p lang="en" dir="ltr">We needed Symplify <a href="https://twitter.com/phpstan?ref_src=twsrc%5Etfw">@phpstan</a> rules on PHP 7.4 project. But Sympilfy packages require PHP 8.0 🤔<br><br>...3 days later I&#39;m excited to share first downgraded package (not a tool! 😉) from PHP 8.0 to PHP 7.2 ↓ <a href="https://t.co/dtW1eWVe2T">https://t.co/dtW1eWVe2T</a><br><br>🎉🎉🎉 <a href="https://t.co/WZNGb4jnvc">pic.twitter.com/WZNGb4jnvc</a></p>&mdash; Tomas Votruba 🇺🇦 (@VotrubaT) <a href="https://twitter.com/VotrubaT/status/1556764939473326081?ref_src=twsrc%5Etfw">August 8, 2022</a></blockquote> <script async src="https://platform.twitter.com/widgets.js" charset="utf-8"></script>

<br>

Happy coding!
