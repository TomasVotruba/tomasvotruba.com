---
id: 182
title: "Monorepo Composer Magic"
perex: |
    Symfony is by far the best source to learn about the monorepo practice. I learned most just by looking into it's `composer.json` - monorepo and package one.
     <br>
     <br>
     Today, I'd like to share secrets behind **biggest WTF** that monorepo composer setup has.

tweet: "New Post on #php 🐘 blog: Monorepo #Composer Magic     #symfony #symplify #sylius #shopsys"
tweet_image: "/assets/images/posts/2019/dev-alias/root-only.png"
---

In each Symplify, Symfony, Shopsys or Sylius package `composer.json`, you'll find:

```json
{
    "minimum-stability": "dev",
    "prefer-stable": true
}
```

"So I get unstable dev dependencies to my project by installing `symplify/*`?"

No! These section are *root-only*. The most known is `require-dev` and also:

<img src="/assets/images/posts/2019/dev-alias/root-only.png">

When you install a package, e.g. `composer require symplify/easy-coding-standard --dev`, you won't download PHPUnit even if that package has it in `require-dev`.

"So why is this needed? Do these monorepo packages use unstable versions?"

**Of course not.** When you install any package standalone, you'll get stable dependencies. It's not even possible to install dev dependency without explicitly saying that (e.g. `composer require symplify/statie:@dev`).

## What is Branch Alias for?

Here comes the magical combo. Each package `composer.json` also includes:

```json
{
    "extra": {
        "branch-alias": {
            "dev-master": "5.5-dev"
        }
    }
}
```

Do you think it will install `composer require symplify/statie:5.5-dev`? **No.**

The rule is: when last minor version is `5.4`, the alias will be `+0.1` version (`5.5`).
E.g. the latest released Symfony version is `4.2.x`, next one will be `4.3`, so the alias is:

```json
{
    "extra": {
        "branch-alias": {
            "dev-master": "4.3-dev"
        }
    }
}
```

Is that clear? Good.

"So what the `branch-alias` and the `minimum-stability` have in common?"

## After-Split Interdependency

The main goal of all this magic is simple. To use **the most recent mutual dependencies**. E.g., I added a new feature in the monorepo that changed the code of `symplify/statie` and `symplify/package-builder`.

When the monorepo is split, the dev `symplify/statie` uses the dev version of `symplify/package-builder`.

## Monorepo Release Maintenance

Let's say we'll release next Symplify version `v5.4.2`. What we have to handle before it's tagged and released?

### 1. Bump each Interdependency to the Release Version

```diff
 {
     "name": "symplify/statie",
     "require": {
-        "symplify/package-builder": "^5.5"
+        "symplify/package-builder": "^5.4.2"
     }
 }
```

### 2. Tag Current Version

```bash
git tag v5.4.2
```

### 3. Push the Tag

```bash
git push --tags
```

### 4. Bump the `branch-alias` if Needed

`5.4.x` still uses `5.5-dev` as the alias, so we don't have to change anything for `5.4.2` version.

In case we'll release `5.5` in the future, it would look like this:

```diff
 {
     "extra": {
         "branch-alias": {
-            "dev-master": "5.5-dev"
+            "dev-master": "5.6-dev"
         }
     }
 }
```

Now we can continue committing and packages will always use last committed code.

<br>

## "Monorepo is Hell of Work"

← You must be thinking right now. Well, these steps are basically *find and replace* - **perfect weak spot to automate**. All these 4 steps  can replace single command from [Symplify/MonorepoBuilder](https://github.com/symplify/monorepobuilder):

```bash
vendor/bin/monorepo-builder release v5.4.2
```

The lazy job is done :)

<br>

Happy coding!
