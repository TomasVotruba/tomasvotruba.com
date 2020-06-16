---
id: 256
title: "How to create&nbsp;a&nbsp;Monorepo from Existing&nbsp;Repositories in 7&nbsp;Steps"
perex: |
    It seems like PHP companies are opening to **the most comfortable way to manage multiple projects and packages at once**.
    <br>
    <br>
    I've heard questions like "how do we make monorepo if have like 15 repositories?" **3 times just last month** - from the Czech Republic, Croatia, and the Netherlands.
    <br>
    <br>
    I'm so happy to see this because lazy dev = happy dev, happy dev = easy code to read.
    <br>
    So how to start a monorepo if you already have existing repositories?

tweet: "New Post on #php 🐘 blog: How to create Monorepo from Existing Repositories in 7 Steps"
tweet_image: "assets/images/posts/2020/monorepo_builder_merge.png"
---

**Disclaimer**: are you're into git history? Read [How to Merge 15 Repositories to 1 Monorepo, Keep their Git History and add Project-Base as Well?](https://www.shopsys.com/how-to-merge-15-repositories-to-1-monorepo-keep-their-git-history-and-add-project-base-as-well-6e124f3a0ab3/).

In practice, keeping git history before merging **is not worth the invested time**. Why?

- A) It takes you 4-6 weeks to figure out how `git` works in merging to different paths
- B) Then it takes you 1-2 weeks to balance all packages together code-wise - pull-request, that change paths anyway, move code, refactor it, merge classes, etc.

If you want to bump your company about a massive pile of money for low gain and want it, go for A + B.

I'm an honest and pragmatic developer, and my customers want to deliver features, not to play around technology sandbox, so we **always take path B**.

<br>

Now that's clear, let's **dive in to merge practice**.

<br>
<br>

## What Repositories do you Have?

The right time to think about monorepo is usually around 5 repositories. The longer you wait, the more you'll add to your future developers - exponentially. Companies typically get the idea around 15 repositories - we'll work with only 2, but apply the same for whatever count.

First repository: `lazy-company/ecoin-payments`

With following code:

```bash
/src
/test
composer.json
ecs.yaml
phpstan.neon
phpunit.xml
rector-ci.yaml
```

Second repository: `lazy-company/drone-delivery`

With following code:

```bash
/src
/test
composer.json
ecs.yaml
phpstan.neon
phpunit.xml
rector-ci.yaml
```

## 1. Create a Monorepo repository

Go to your Gitlab or Github and create a new repository. Name it `lazy-company/lazy-company` (by convention) or `lazy-company/lazy-company-monorepo` (in case the previous is taken).

Clone it locally.

## 2. Copy Repositories to `/packages`

Don't worry, no git harakiri. Just copy paste your other repositories to `/packages` directory:

```bash
/packages
    /ecoin-payments
        /src
        /test
        composer.json
        ecs.yaml
        phpstan.neon
        phpunit.xml
        rector-ci.yaml
    /drone-delivery
        /src
        /test
        composer.json
        ecs.yaml
        phpstan.neon
        phpunit.xml
        rector-ci.yaml
```

Not bad, right?

## 3. Merge all `composer.json` to Root One

In the root directory, we only have the directory with all packages:

```bash
/packages
```

But where is `composer.json`? We can ~~create it manually~~ use a CLI tool that does it for us - [MonorepoBuilder](https://github.com/symplify/monorepo-builder).

Use [prefixed version](/blog/2019/12/02/how-to-box-symfony-app-to-phar-without-killing-yourself/) to avoid dependency conflicts with your packages.

```bash
composer require symplify/monorepo-builder-prefixed --dev
```

Now that we have [this power-tool for working](/blog/2018/10/08/new-in-symplify-5-create-merge-and-split-monorepo-with-1-command/) with monorepo, we can do:

```bash
vendor/bin/monorepo-builder merge
```

And...

<img src="/assets/images/posts/2020/monorepo_builder_merge.png" class="img-thumbnail">

Damn, what is this?

## 4. Balance External Dependencies

We have to look into `composer.json` files to find out what happened:

```json
{
    "name": "lazy-company/ecoin-payments",
    "require": {
        "symfony/http-kernel": "^4.4|^5.0"
    }
}
```

and

```json
{
    "name": "lazy-company/drone-delivery",
    "require": {
        "symfony/http-kernel": "^3.4|^4.4"
    }
}
```

We have 2 packages that require **different versions of the same dependency**. One allows Symfony 3; the other does not, but can run on Symfony 5.

What version do they share?

- `^4.4`

The number **must be identical for all packages**. One package cannot have `^4.3`, and the other `^4.4`.


```diff
 {
     "name": "lazy-company/ecoin-payments",
     "require": {
-        "symfony/http-kernel": "^4.4|^5.0"
+        "symfony/http-kernel": "^4.4"
     }
 }
```

and:

```diff
 {
     "name": "lazy-company/drone-delivery",
     "require": {
-        "symfony/http-kernel": "^3.4|^4.4"
+        "symfony/http-kernel": "^4.4"
     }
 }
```

### The Easiest, Common Version Problem

We have to figure out **the package version that would be easier to use**. Sometimes the new version requires some refactoring.

In current project I migrate 15 packages, that have these requirements:

- A: "symfony/http-kernel": "^5.0"
- B: "symfony/http-kernel": "^3.4"
- C: "symfony/http-kernel": "^2.8"

If we pick `^3.4`, we have to make sure the code of A and C packages will be updated or downgraded to that version. You get the idea.

<br>

When we have all versions synced, we can run the merge command:

```bash
vendor/bin/monorepo-builder merge
```

Tadá!

We should see something like this:

```json
{
    "require": {
        "symfony/http-kernel": "^4.4"
    },
    "require-dev": {
        "symplify/monorepo-builder-prefixed": "^8.0"
    },
    "replace": {
        "lazy-company/drone-delivery": "self.version",
        "lazy-company/ecoin-payments": "self.version"
    }
}
```

Do you? Good!

<br>

What is the `replace` section? We'll use it in step 5 ↓

## 5. Balance Mutual Dependencies

It's standard that packages depend on each other. Drone delivery is a service a customer pays for - with bitcoins. So we need it here:

```json
{
    "name": "lazy-company/drone-delivery",
    "require": {
        "symfony/http-kernel": "^4.4",
        "lazy-company/ecoin-payments": "^2.0"
    }
}
```

What if 2 packages require a different version of the same package?

- A. "lazy-company/ecoin-payments": "^2.0"
- B. "lazy-company/ecoin-payments": "^3.0"

Do we apply the same approach as in step 4? **No**. Instead of the most accessible common version, we'll go with **the latest version** - `^3.0`.

These numbers also tell us what the first monorepo release version will be. It has to be a [major version](https://semver.org/) because there will be BC breaks: so ^4.0.

<br>

### What about that `replace?`

Here we also use the [`replace` composer feature](https://getcomposer.org/doc/04-schema.md#replace).

If we run `composer install` in monorepo, it will install all dependencies of `lazy-company/drone-delivery`. This package needs `lazy-company/ecoin-payments` (the other package). Normally, the composer would go to Packagist and download the package to `/vendor`. But that might end-up in collision:

```bash
/packages/ecoin-payments/src # some code
/vendor/lazy-company/ecoin-payments/src # same code?
```

The `replace` option tells the composer not to download anything because the `lazy-company/ecoin-payments` is already in `/packages/ecoin-payments/src`.

```diff
 /packages/ecoin-payments/src
-/vendor/lazy-company/ecoin-payments/src
```

## 6. Merge Static Analysis tools to Run on Root Only

All right, we have working `composer.json` with united versions. That was the most challenging part, so great job!

No, we need to [clean configs](/cleaning-lady-checklist/) of tools that help us with daily development:

- ECS
- PHPStan
- Rector
- ...

Instead of many configs, paths, setups, and rules, there is only 1 source of Truth - root configs.

```diff
 /packages
     /ecoin-payments
         /src
         /test
         composer.json
-        ecs.yaml
-        phpstan.neon
         phpunit.xml
-        rector-ci.yaml
     /drone-delivery
         /src
         /test
         composer.json
-        ecs.yaml
-        phpstan.neon
         phpunit.xml
-        rector-ci.yaml
+ecs.yaml
+phpstan.neon
+rector-ci.yaml
```

This step is pretty easy... well, it depends.

**What is the thing that can happen?** One of your packages has PHPStan level 1, but all others have PHPStan 8.

We can either take time and update the PHPStan level 1 to 8 or lower all to 1. I'd go with * drop all to 1* options now, and do this after creating the monorepo. If we mix too many tasks at once, we can prolong *build a monorepo* tasks for weeks.

<br>

Pro-tip: do you want to make sure all versions of all dependencies of all `composer.json` files have united version?

```bash
vendor/bin/monorepo-builder validate
```

## 7. Merge tests to root `phpunit.xml`

Very similar to step 6, just with unit tests.

```diff
 /packages
     /ecoin-payments
         /src
         /test
         composer.json
-        phpunit.xml
     /drone-delivery
         /src
         /test
         composer.json
-        phpunit.xml
 ecs.yaml
 phpstan.neon
 rector-ci.yaml
+phpunit.xml
```

Update paths in `phpunit.xml` and prepare a common environment for all tests.

<br>

In the end, we have to be able to run:

```bash
vendor/bin/phpunit
```

And see the result of all tests.

Everything else will be more complicated than it has to, will annoy us, and demotivate us from actually writing the tests. **So make it easy and simple**.

<br>

If you're serious about monorepo testing, read [How to Test Monorepo in 3 Layers](/blog/2018/11/22/how-to-test-monorepo-in-3-layers/).

## Final Touches

Don't forget to add `.gitignore` with `/vendor`. Then `git push` and we're finished.

Congrats!

<br>

## Where to go next?

Be sure to read each post from [Monorepo: From Hero to Zero](/clusters/#monorepo-from-zero-to-hero).

Then go to your Gitlab or Github and make your `company/company-monorepo` package. It's easier when you start.


<br>

Happy coding!
