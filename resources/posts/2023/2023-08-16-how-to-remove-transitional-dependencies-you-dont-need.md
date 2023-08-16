---
id: 389
title: "How to Remove Transitional Dependencies You don't&nbsp;Need"

perex: |
    In the last post [I shared a trick](/blog/unleash-the-power-of-simplicity-php-cli-app-with-minimal-dependencies) on how to drop your CLI project /vendor size by 70%. Today we'll trim off a bit more with the no-so-known composer feature.
---

<blockquote class="blockquote mt-5 mb-5 text-center">
    "Perfection is achieved, not when there is nothing more to add,
    but when there is nothing left to take away."
</blockquote>

Some packages require another set of packages to delegate responsibility to an external source. Like any other dependency in life, that could be helpful and save time, but it also requires attention and care - it *depends*.

<br>

The following applies to *any package that is bloated with transitional dependencies* we don't use. But I'll use the one I work with daily as an example.

<br>

Let's say we install `symfony/console` to a brand new empty project:

```bash
composer require symfony/console
```

<br>

Have a guess: how many packages do we have in our `/vendor` now?

```bash
psr/container                    2.0.2
symfony/console                  v6.3.2
symfony/deprecation-contracts    v3.3.0
symfony/polyfill-ctype           v1.27.0
symfony/polyfill-intl-grapheme   v1.27.0
symfony/polyfill-intl-normalizer v1.27.0
symfony/polyfill-mbstring        v1.27.0
symfony/service-contracts        v3.3.0
symfony/string                   v6.3.2
```


Wow, **9 in total**.

Briefly looking at the list, 5 packages deal with strings and language. These packages bring value if we use non-standard language operations - but for native English, those are redundant.

We also use `symfony/console` just to invoke a PHP method called to render the output. Nothing fancy like console forms or dynamic games in the command line.

<br>

That means we don't need the following packages:

* symfony/polyfill-ctype
* symfony/polyfill-intl-grapheme
* symfony/polyfill-intl-normalizer
* symfony/polyfill-mbstring
* symfony/string

<br>

## Little Experiment Beats Complex Assumptions

How do we know these packages are not needed? Let's verify our thesis.

We check for a "Symfony\Component\String\" string in the `symfony/console` Github repository [in search](https://github.com/search?q=repo%3Asymfony%2Fconsole%20Symfony%5CComponent%5CString&type=code)

<br>

We can see 4 cases - primarily to measure the width of the terminal window:

<img src="https://github.com/TomasVotruba/tomasvotruba.com/assets/924196/96de05cb-f3f3-41dc-882f-7dbe1715825c" class="img-thumbnail">

<br>

We try to comment out those cases and run code. All good? We can remove it.

## Why Even Bother... and When?

If you use this package locally in a single project and have enough space and DevOps take care of PHP extensions, there is little value in removing it.

But if the package:

* is an open-source project,
* has [nearly 2 000 000 downloads/month](https://packagist.org/packages/rector/rector/stats),
* is designed to run on the worst PHP code bases in the world,
* has reported bugs because of missing intl extension,
* and its killer feature is to deal with legacy code,

... maybe it could be helpful for the developers that use it to make it easier to run and install.

<br>

I also check the `symfony/string` package size to see its download trace:

```bash
composer require tomasvotruba/lines --dev
vendor/bin/lines measure vendor/symfony/string/ --short --json
```

↓

```json
{
    "lines_of_code": {
        "code": 5561,
        "comments": 570,
        "total": 6131
    }
}
```

That's **5 561 lines of PHP code with every download** and [4 intl/mbstring packages](https://packagist.org/packages/symfony/string) in transition for 4 simple method calls. Those packages can prevent crash runs or installation on some nasty legacy project code, so if we get rid of them, we'll make our product more usable and cheaper to maintain.

<br>

## "Replace" as in "Remove"

Let's remove it. The first solution is using the `remove` command in Composer:

```bash
composer remove symfony/string
```

And Composer replies in big red letters:

```bash
Removal failed; symfony/string is still present; another package may require it. See `composer why symfony/string`.
```

Damn... We don't have it in our `composer.json`, so we can't really remove it, can we?

<br>

What other options do we have? Run `rm -rf /vendor/symfony/string` might help, but we'd have to put it in some weird bash script, which seems like a code smell.

<br>

How do we use Composer to help us?

The Composer has this special section called "replace":

```json
{
    "replace": {
        "symfony/string": "*",
    }
}
```

Now run `composer update` and see what happens:

```bash
Updating dependencies
Lock file operations: 0 installs, 0 updates, 4 removals
  - Removing symfony/polyfill-ctype (v1.27.0)
  - Removing symfony/polyfill-intl-grapheme (v1.27.0)
  - Removing symfony/polyfill-intl-normalizer (v1.27.0)
  - Removing symfony/string (v6.3.2)
```

**The packages we don't use are gone, yay!**

<br>

Wow, it looks like magic! It is, until we read [the "replace" documentation](https://getcomposer.org/doc/04-schema.md#replace).

In short, it tells the Composer: "This root package has the same features as `symfony/string`".

The Composer then sees your root package and the `symfony/string` package and decides: "Hm, there are 2 packages with the same name. Let's use the root one and remove the dependency from vendor".

<br>

## Think Critically

Simple but effective solution. It's not for everyone, but if you care about it and having fewer dependencies brings your project more value and less code to worry about, then:

* pose a thesis
* check the actual usage
* measure the package size
* remove the package
* test your project without it
* if it's a success, repeat

<br>

Happy coding!
