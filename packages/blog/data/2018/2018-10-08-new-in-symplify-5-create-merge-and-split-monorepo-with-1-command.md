---
id: 143
title: "New in Symplify 5: Create, Merge and Split Monorepo with 1 Command"
perex: |
    Do you want to create, validate and manage your monorepo like a pro? There is no science behind it, just a few routine steps that you need to repeat.
    <br><br>
    And now there is a **tool that will handle these steps for you**.
tweet: "New Post on my Blog: New in Symplify 5: Create, Merge and Split #Monorepo with 1 Command"

updated_since: "December 2020"
updated_message: |
    Removed the deprecated `split` command. Use faster and reliable [GitHub Action](/blog/2020/11/09/new-in-symplify-9-monorepo-split-with-github-action/) instead.
---

This package was initially released in Symplify 4.5, but it took some time to test in practice, remove WTFs and be sure it contains all people need.

Now there are 7 commands in total, but today we'll focus **on the 4 most important ones**.

First, install this package to an empty repository:

```bash
composer require symplify/monorepo-builder --dev
```

## 1. Create Your Monorepo

You'll run this command **just once** to create a monorepo in the empty repository.

```bash
vendor/bin/monorepo-builder init
```

It will prepare basic monorepo structure with 2 packages:

```bash
/packages
    /first-package
        /src
        composer.json
    /second-package
        /src
        composer.json
composer.json
monorepo-builder.yml # basic configuration
```

Use `composer.json` as you know it for most sections. But to manage **`require`, `require-dev`, `autoload` and `autoload-dev`** sections use only `/packages/first-package/composer.json`, `/packages/second-package/composer.json` like they were standalone packages.

Extra - mostly-dev - dependencies are managed by `monorepo-builder.yml`.

Is that unclear to you? Don't worry, you'll see how it works in `merge` command below.

## 2. Validate it

This command will tell you if your dependency versions are the same in every packages' `composer.json` and in root `composer.json`.

```bash
vendor/bin/monorepo-builder validate
```

<img src="/assets/images/posts/2018/symplify-5-monorepo-builder/validate.png" class="img-thumbnail">

You don't have to run this command manually since it's included in the next one.

## 3. Merge `composer.json`

```bash
vendor/bin/monorepo-builder merge
```

Here you'll understand the magic from `init` command. With `merge` command, the Monorepo Builder will join all packages' `composer.json` - after validation of course.

And what about extra dependencies like PHPUnit, coding standards and static analysis?

```yaml
# monorepo-builder.yml
parameters:
    # for "merge" command
    data_to_append:
        require-dev:
            phpunit/phpunit: '^7.3'
```

That's what `data_to_append` section is for. These packages and versions will be added to `composer.json`.

*Btw, all packages are nicely sorted by name so you always find them quickly.*


### Do you Want to Know More?

Discover other commands:

```bash
vendor/bin/monorepo-builder
```

and read [`README`](https://github.com/symplify/monorepobuilder) for detail usage and tricks.

<br>

Happy monorapping!
