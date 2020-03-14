---
id: 238
title: "How to Test Monorepo After Split Before Actual Split"
perex: |
    In 14 months old post [How to Test Monorepo in 3 Layers](/blog/2018/11/22/how-to-test-monorepo-in-3-layers/#3-after-split-testing) we talked about testing monorepo in 3 layers. So you can be sure every package works as it should.
    <br>
    <br>
    3 layers are testing in a monorepo, testing package directory, and testing after a split.
    **The latter takes a huge amount of time**. The time we don't have to spare in 2020.
    <br>
    <br>
    **How can we make it faster while keeping the test quality high?**
tweet: "New Post on #php 🐘 blog: How to Test Monorepo After Split Before Actually Split"
tweet_image: "/assets/images/posts/split_before_split.png"
---

Do you use Github Actions on your Github projects? Well, if you do, **[it might cut your commit feedback loop](/blog/2020/01/27/switch-travis-to-github-actions-to-reduce-stress/) from 17 minutes to just 3** and make you super productive by accident. 

We now use Github Actions on Symplify and Rector for over a month, and in January 2020, it helped us [to merge amazing 177 pull-requests](https://twitter.com/VotrubaT/status/1220126675436032005).

## Warning: Cleaning Up Might Cause you to Spot more Bottle Necks 

That's why so few developers like to tidy up. It usually only shows worse and worse mess in the code.

The worse mess in our code was the 3rd layer of monorepo testing - **after split tests**. 

We had to wait till the full monorepo is split (+ 5 minutes), and CI runs on each split package (+ 2 minutes). So instead of 3 minutes, we're now back on 10 minutes. Also, the split testing can **only happen after the PR is merged into the `master` branch**.

Do your 1st and 2nd layer pass? All good? **You have to wait till the split to find out it's not all good**. Doh.

## Short Reminder: What is the 3rd layer of Monorepo Testing?

Let's say you run:
 
```bash
git clone git@github.com:symfony/console.git

composer install
# all needed dependencies are installed, symfony/* and all external in /vendor directory

vendor/bin/phpunit
```

And that's all! The standard way of testing packages.

**But** with monorepo, the symfony/console is just one of directories in [big monorepo symfony/symfony repository](https://github.com/symfony/symfony/):

```bash
symfony/src/Symfony/Component/Console
symfony/src/Symfony/Component/EventDispatcher
symfony/src/Symfony/Component/HttpKernel
...
```

To run unit tests on symfony/console only, we'd have to call PHPUnit on the directory:

```bash
vendor/bin/phpunit -d symfony/src/Symfony/Component/Console  
```

But there is no:

```bash
symfony/src/Symfony/Component/Console/vendor/*
```

**So we can't test it**. That's why we have to **wait after the split is done** and trigger tests in a split repository - `symfony/console` the way we did at first:

```bash
git clone git@github.com:symfony/console.git

# all needed dependencies are installed, symfony/* and all external
composer install

vendor/bin/phpunit
```

You can read more about it [in original post](/blog/2020/02/10/how-to-test-monorepo-after-split-before-actual-split/).

<br>

## Status Quo: It Sucks, but it's the Best Testing

3rd layer of monorepo testing sucks

- it takes + 10 minutes
- it doesn't test pull-requests - it's like tests without CI basically
- it confusing to have few tests in one repository and one repository

It's so bad that **the most monorepo projects don't have this 3rd layer** - Symfony nor Laravel. And I don't blame them. I was about to drop it too because having feedback about one line of code from 2 sides with entirely different settings hurts my brain.

The problem is, **this 3rd layer testing is the closest to the reality of how these packages are used**. So missing it will cause you headaches with bugs, so simple to find by users of your packages that you won't believe them.

## Could it be possible with GitHub Actions?

This bottleneck got me thinking... what do we need to simulate?

```bash
git clone git@github.com:symfony/console.git

# all needed dependencies are installed, symfony/* and all external
composer install

vendor/bin/phpunit
```

Just locally for every package.

<blockquote class="blockquote text-center mt-5 mb-5">
    Albert Einstein always taught me:
    <br>
    "think in patterns, my young padawan",
    <br>
    so I knew there must be a way.
</blockquote> 

~~Then I suddenly knew what to do!~~ 

<br>

I won't lie, it took me 3-4 hours of trial and error and many toilet *eureka* visits to figure out the working model. What went wrong at first?

- **Symlink cannot be used**, because real/relative paths are different from standard package testing
- **All packages can't run in one workflow**, because it's super slow
- **We cannot use shared vendor**, the dependencies are conflicting

### So What Worked?
 
- Every package `composer.json` had to require mutually dependencies (e.g `symplify/easy-coding-standard` requires `symplify/package-builder`) with `*` 
- Every other package had to require other packages via [composer *path* repository](https://getcomposer.org/doc/05-repositories.md#path):

<img src="/assets/images/posts/split_before_split.png" class="img-thumbnail">

<br>

There was **one more pit to fall into**. How many repositories we have to add here?

```diff
 {
     "require": {
-         "symplify/package-builder": "^7.3"
+         "symplify/package-builder": "*"
     } 
 }
```

Just this one, right?

```diff
+"repositories": [
+        {
+            "type": "path",
+            "url": "../../packages/package-builder",
+            "options": {
+                "symlink": false
+            }
+        }
+}
```

Well, I assumed so, but this would eventually download `symplify/package-builder` with local version, **but all the rest of `symplify/*` packages from packagist**. In the end we'll have a mess like:

- `symplify/package-builder` - local <em class="fas fa-fw fa-check text-success"></em>
- `symplify/autowire-array-parameter` - from packagist before split !== different code that we use <em class="fas fa-fw fa-times text-danger"></em>

We have to **add all the possible local repositories**, so the package always has the local version of the code.

## Showcase: Symplify Workflow

It makes sense, but it sounds like a lot of manual work, right? Not for long!

<br>

You know me too well, me and *manual work* don't get on very well with each other.

**I made a command for MonorepoBuilder that does all the work above for us**:

```bash
vendor/bin/monorepo-builder localize-composer-paths
``` 

All we need to do is run it before `composer update`.

<br>

This simple idea is running split tests in 14 Symplify packages. How?

- switch to the package directory
- modify paths to use local path packages **without** symlink to prevent path false positives
- install dependencies
- run `vendor/bin/phpunit` there 

```yaml
# .github/workflows/after_split_testing.yaml 
name: After Split Testing

jobs:
    after_split_testing:
        runs-on: ubuntu-latest
        steps:
            -   uses: actions/checkout@v2
            -   uses: shivammathur/setup-php@v1
                with:
                    php-version: 7.4
                    coverage: none

            -   run: |
                    composer install --no-progress
                    
                    # this is the magic
                    packages/monorepo-builder/bin/monorepo-builder localize-composer-paths
                
                    # testing one package
                    cd packages/easy-coding-standard
    
                    # download dependencies
                    composer update --no-progress
                    
                    # run tests
                    vendor/bin/phpunit
```

And that's it! **1 extra new line in workflow** is a worth hour of human-hours a week on one project.

<br>

See [full workflow](https://raw.githubusercontent.com/symplify/symplify/151b7a6be28a26fcb94252d91e4c3d061eec7617/.github/workflows/after_split_testing.yaml) prevent repeating code.

**[See full pull-request](https://github.com/symplify/symplify/pull/1755)**.

A reminder: **this all is possible thanks to super-fast Github Actions** with 20 concurrent workers. On Travis with only 3 workers, this might take extra 15-25 minutes, which is utterly annoying.

<br>

Happy speed coding!
