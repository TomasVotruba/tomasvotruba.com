---
id: 316
title: "Introducing ECS Prefixed and Downgraded to PHP 7.1"
perex: |
    ECS is part of Symplify that requires PHP 7.3 version as minimal. That cuts down options for you as an end-user. And that's not the only problem. Do you require Symfony 2.8 or 3.4? You're blocked.
    <br><br>
    How can we **improve your experience while keeping the latest features**?
tweet: "New Post on #php 🐘 blog: Introducing #ecs Prefixed and Downgrade to PHP 7.1"
---

When we try to install ECS with miss conflicting dependency...

```bash
composer require symfony/console:2.8
composer require symplify/easy-coding-standard --dev
```

❌

...we'll end up with composer "ECS requires at least symfony 4.4 version, cannot install" error.

Some of you might know there is a way around with ECS Prefixed version. This package is built on every ECS release into [special standalone repository](https://github.com/symplify/easy-coding-standard-prefixed). It has **own dependency namespace prefixed** so that you can install it with the same package of another version, like symfony/console:

```bash
composer require symfony/console:2.8
composer require symplify/easy-coding-standard-prefixed --dev
```

✅

Imagine you have keys from your car. They work like regular keys most of the week, but on weekends not. Unless, of course, you drop them 2x times before putting the key inside the lock.

Requiring a suffix to remember is not very intuitive and creates an awful developer experience.
So how can **we improve this developer experience**?

## Prefix on Release

How would you expect ECS to install?

```bash
composer require symfony/console:2.8
composer require symplify/easy-coding-standard --dev
```

✅

Me too. So how can we do that? First, we need to understand the current project architecture.

<br>

Symplify is a like Symfony, Laravel or luminas [a monorepo](/cluster/monorepo-from-zero-to-hero).

**What does *monorepo* mean?** That:

- PR is merged to `symplify/simplify ↓
- then `symplify/easy-coding-standard` is split ↓
- then build of `symplify/easy-coding-standard-prefixed` is published

<br>

Now we need to merge the last 2 steps into 1.

If the process were a git diff, it would look like this:

```diff
 - PR is merged to symplify/symplify ↓
-- then symplify/easy-coding-standard is split ↓
-- then build of symplify/easy-coding-standard-prefixed is published
+- then build of symplify/easy-coding-standard is published on split
```

Thanks to monorepo, this is a piece of cake. We need to take the prefixing script and move it before the split itself:

- monorepo `symplify/symplify` has original PHP code

- **CI build step #1: scope code**
- **CI build step #2: publish split package**
- split `symplify/easy-coding-standard` repository has scoped PHP code

Now instead of splitting the original code, we split already prefixed code.

## Downgraded by Default

Do you know middlewares? They're like a layer of you lasagne that you can insert anywhere between request and response. Do you need to add an extra security layer? Just add security middleware.

Layer in CI can be stacked on each other in similar way. If we can handle scoping, why not add downgrading too?

- monorepo `symplify/symplify` has original PHP code

- **CI build step: scope code**
- **CI build step #2: downgrade code to PHP 7.1**
- **CI build step #3: publish split & downgraded package**

- split `symplify/easy-coding-standard` repository has scoped and downgraded PHP code

<br>

Before we had:

- `symplify/easy-coding-standard` on PHP 7.3 without scoping
- `symplify/easy-coding-standard-prefixed` on PHP 7.3 scoped

Now we have:

- `symplify/easy-coding-standard` on **PHP 7.1 with scoping**

✅

## How to Upgrade?

The ECS with PHP 7.1 and scoped code is available since version 9.3.10. You can run bare `composer update` or specify the exact version to be sure:

```bash
composer remove squizlabs/php_codesniffer
composer remove friendsofphp/php-cs-fixer
composer update symplify/easy-coding-standard:^9.3.10
```

Do you use `symplify/easy-coding-standard-prefixed`? Replace it with a new package:

```bash
composer remove squizlabs/php_codesniffer
composer remove friendsofphp/php-cs-fixer
composer remove symplify/easy-coding-standard-prefixed
composer require symplify/easy-coding-standard:^9.3.10 --dev
```

## What has Changed?

There might be a BC break because we're scoping ECS dependencies from now on. That means php-cs-fixer 3.0+ and PHP_CodeSniffer 3.6+ code is now accessible only for `vendor/bin/ecs` run.

**Do you write your Fixer or Sniffs?** Add required packages:
require
```bash
composer require friendsofphp/php-cs-fixer --dev
composer require squizlabs/php_codesniffer --dev
```

Apart from that, everything should be running correctly. If not, let us know in [GitHub issues](https://github.com/symplify/symplify/issues/new).

<br>

Happy coding!
