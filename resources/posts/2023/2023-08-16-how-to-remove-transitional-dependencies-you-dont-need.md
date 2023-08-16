---
id: 389
title: "How to Remove Transitional Dependencies you don't&nbsp;Need"

perex: |
    In the last post [I shared a trick](/blog/unleash-the-power-of-simplicity-php-cli-app-with-minimal-dependencies) how to drop your CLI project /vendor size by 70%. Today we'll trim of a bit more with no-so-knonw composer feature.
---

<blockquote class="blockquote mt-5 mb-5 text-center">
    "Perfection is achieved, not when there is nothing more to add,
    but when there is nothing left to take away."
</blockquote>

Some packages require another set of packages to delegate responsibility to external source. As in any other dependency in life, that could be helpful and save time, but also requires attention and care - it *depends*.

<br>

Following applies to any package that is bloated with transitional dependencies we don't use. But I'll use the one I work with daily as an example.

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

Briefly looking at the list, 5 of them deal with strings and language. These packages bring value in case we use non-standard language operations - but for native English those are redundant.

We also use `symfony/console` just to invoke an PHP method call render the output. Nothing fancy like console forms or dynamic game in command line.

<br>

That means we don't need following packages:

* symfony/polyfill-ctype
* symfony/polyfill-intl-grapheme
* symfony/polyfill-intl-normalizer
* symfony/polyfill-mbstring
* symfony/string

<br>

## Little Experiment beats Complex Assumptions

How do we know these packages are not really needed? Let's verify our thesis.

We check for a "Symfony\Component\String\" string in the `symfony/console` Github repository [in search](https://github.com/search?q=repo%3Asymfony%2Fconsole%20Symfony%5CComponent%5CString&type=code)

<br>

We can see 4 cases - mostly to measure width of terminal window:

<img src="https://github.com/TomasVotruba/tomasvotruba.com/assets/924196/96de05cb-f3f3-41dc-882f-7dbe1715825c" class="img-thumbnail">

<br>

We try to comment those cases and run code. All good? We can remove it.

## Why Even Bother... and When?

If you use this package locally in single project and have enough space and devops taking care of PHP extensions, there is not much value in removing it.

But if the package:

* is open-source project,
* has [nearly 2 000 000 downloads/month](https://packagist.org/packages/rector/rector/stats),
* is designed to run on the worst PHP code bases in the world,
* has reported bugs because of missing intl extension,
* and its killer feature is to deal with legacy code,

... maybe it could be useful for the developers that use it to make it easier to run and install.

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

That's **5 561 lines of PHP code with every download** and [4 intl/mbstring packages](https://packagist.org/packages/symfony/string) in transition for 4 simple method calls. Those packages can make prevent crash run or install on some nasty legacy project code, so if get rid of them, we'll make our product more usable and cheaper to maintain.

<br>

## "Replace" as in "Remove"

Let's remove it then. The first solution is using `remove` command in composer:

```bash
composer remove symfony/string
```

And composer replies in big red letters:

```bash
Removal failed, symfony/string is still present, it may be required by another package. See `composer why symfony/string`.
```

Damn... We don't have it in our `composer.json`, so we can't really remove it, can we?

<br>

What other options we have? Run `rm -rf /vendor/symfony/string` might help, but we'd have to put it in some weird bash script and it seems like code smell.

<br>

How we use composer to actually help us?

Composer has this special section called "replace":

```json
{
    "replace": {
        "symfony/string": "*",
    }
}
```

<<<<<<< HEAD
<<<<<<< HEAD
@todo
=======
=======
>>>>>>> d678b2bd (misc)
Now run `composer update` and see what happens:

```bash
Updating dependencies
Lock file operations: 0 installs, 0 updates, 4 removals
  - Removing symfony/polyfill-ctype (v1.27.0)
  - Removing symfony/polyfill-intl-grapheme (v1.27.0)
  - Removing symfony/polyfill-intl-normalizer (v1.27.0)
  - Removing symfony/string (v6.3.2)
```

<<<<<<< HEAD
**The packages we don't use are gone, yay!**

<br>

Wow, looks like magic! It is, until we read [the "replace" documentation](https://getcomposer.org/doc/04-schema.md#replace).

In shot what it does is telling the composer: "this root package has same features as `symfony/string`". The composer then sees your root package and the `symfony/string` package and decides "hm, there are 2 packages with same name, lets use the root one and remove the dependency from vendor".

<br>

## Think Critically

Simple but effective solution. It's not for everyone, but if you care about it and having less dependencies brings your project more value and less code to worry about, then:.

* pose a thesis
* check the real usage
* measure the package size
* remove the package
* test your project without it
* if it's success, repeat

<br>

Happy coding!
>>>>>>> 1d52840b (fixup! misc)
=======
The packages we don't use are gone, yay!


>>>>>>> d678b2bd (misc)
