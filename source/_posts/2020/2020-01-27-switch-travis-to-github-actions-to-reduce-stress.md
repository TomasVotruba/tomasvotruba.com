---
id: 236
title: "Switch Travis to GitHub Actions to Reduce Stress"
perex: |
    In the previous post, we looked at [Why is First Instant Feedback Crucial to Developers?](/blog/2020/01/13/why-is-first-instant-feedback-crucial-to-developers/).
    <br><br>
    We know why now we look at *how*. How exactly migrate all jobs from Travis to GitHub Actions, **reduce stress from long feedback loops** and live a more healthy life as a programmer.
    <br><br>
    Yes, in code samples :)
tweet: "New Post on #php 🐘 blog: Switch Travis to GitHub Actions to Reduce Stress."
---

In this post, we'll look at examples of migration. I'll share my good and bad times with GitHub Action for my last 3 weeks using it on 5 open-source repositories with over 25 packages. 

## The Speed  

<blockquote class="blockquote text-center">
    From ~15 minutes to just 3 minutes. 
</blockquote>

I mean, that's a crazy improvement of 80 %. You can read about in [the previous post](/blog/2020/01/13/why-is-first-instant-feedback-crucial-to-developers/), so let's move on.   

## The Developer's Joy

<blockquote class="twitter-tweet"><p lang="en" dir="ltr">Github Actions just make everything so easy... <br><br>We merged 177 PR in <a href="https://twitter.com/rectorphp?ref_src=twsrc%5Etfw">@rectorphp</a> in the last month.<br><br>Instant feedback is killer feature for you devs. Don&#39;t let them wait...<a href="https://t.co/hP9Epe2CZW">https://t.co/hP9Epe2CZW</a> <a href="https://t.co/0Md9iIisXm">pic.twitter.com/0Md9iIisXm</a></p>&mdash; Tomas Votruba (@VotrubaT) <a href="https://twitter.com/VotrubaT/status/1220126675436032005?ref_src=twsrc%5Etfw">January 22, 2020</a></blockquote> <script async src="https://platform.twitter.com/widgets.js" charset="utf-8"></script>

Cocaine, Facebook, Twitter, and Instagram work the same way. A short feedback loop of dopamine.

The significant advantage of GitHub is that at the end of your addiction is not an endless loop in the brain, but **higher productivity of your project**. 

## Simple Unit Test in Multiple PHP Versions

We'll jump right into the most common case - unit tests.

In **Travis CI**, we had to:
 
- install dependencies, 
- set PHP versions 
- and run unit tests.  

```yaml
# .travis.yml
os: linux

language: php

php:
    - '7.2'
    - '7.3'
    - '7.4'

before_install:
    # turn off XDebug
    - phpenv config-rm xdebug.ini

install:
    - composer install --no-progress

jobs:
    include:
        -
            stage: test
            name: "Unit Tests"
            script:
                - vendor/bin/phpunit --testsuite main
```

In **GitHub Action** the same process look like this:


```yaml
# .github/workflows/code_checks.yaml
name: Code_Checks

on:
    pull_request: null
    push:
        branches:
            - master

jobs:
    tests:
        runs-on: ubuntu-latest
        strategy:
            matrix:
                php: ['7.2', '7.3', '7.4']

        name: PHP ${{ matrix.php }} tests
        steps:
            # basically git clone
            -   uses: actions/checkout@v2

            # use PHP of specific version
            -   uses: shivammathur/setup-php@v1
                with:
                    php-version: ${{ matrix.php }}
                    coverage: none # disable xdebug, pcov
        
            # if we 2 steps like this, we can better see if composer failed or tests
            -   run: composer install --no-progress

            -   run: vendor/bin/phpunit
```

That's pretty huge for single and confusing to read at the same time, right?

All that clutter just for `vendor/bin/phpunit` to pass.
## Re-Use Code in Github Actions? Hell No! 

What is this? 

```yaml
-   uses: actions/checkout@v2
```

Github Actions allow references to external *recipes*. It's usually just a set of actions, packages into a couple of lines in our workflow. You can see it on Github, e.g. [actions/checkout](https://github.com/actions/checkout). It's the recommended way to re-use code because there is no other way. 

In reality: "Do you want to re-use 5 lines of install and setup YAML code? Create a repository on Github, write 100 lines in JavaScript, and you're ready to go!"

### No DRY

Saying that we'll either have to create custom workflow repository or get used to these lines being repeated over and over:

```yaml
-   uses: actions/checkout@v2

-   uses: shivammathur/setup-php@v1
    with:
        php-version: '7.3'
        coverage: none # xdebug is used by default

-   run: composer install --no-progress
```

After a bit of experimenting, I got used to it for now. Also, Github Actions don't have anything close to **YAML Anchors** or re-use of previous job configuration like we have in Travis, e.g., share `install` for every job:

```yaml
# .travis.yml
install:
    - composer install --no-progress
```

Do you want YAML Anchors in Github Actions? [Let them know](https://github.community/t5/GitHub-Actions/Support-for-YAML-anchors/td-p/30336).

<br>

Now that we have the worst feature of Github Actions behind us, **let's look at the excellent stuff**.
 
## Power of External Workflows

On the other hand, external workflows can solve a lot for us. Mainly for us, who don't want to dev-ops experts forever CI there is.

How do you enable code coverage by PHPUnit? **Change single line**:

```diff
jobs:
    test_coverage:
        runs-on: ubuntu-latest
        steps:
            -   uses: actions/checkout@v2

            -   uses: shivammathur/setup-php@v1
                with:
                    php-version: '7.3'
-                   coverage: none
+                   coverage: pcov

            - run: vendor/bin/phpunit --coverage-clover coverage.xml build/logs/clover.xml
```
 

## Coding Standards

What is the most common use case for CI? Run *single line* in this *specific PHP version* → e.g., run coding standards on PHP 7.2.

In **Travis CI**:

```yaml
language: php

install:
    - composer install --no-progress

jobs:
    include:
        -
            name: ECS
            php: 7.2
            script:
                - composer check-cs
```

In **Gitub Actions**:

```yaml
# .github/workflows/code_checks.yaml 
jobs:
    ecs:
        runs-on: ubuntu-latest
        steps:
            -   uses: actions/checkout@v2
            -   uses: shivammathur/setup-php@v1
                with:
                    php-version: 7.2
                    coverage: none # disable xdebug, pcov
            -   run: composer install --no-progress
            -   run: composer check-cs
```


## Learn from Working Examples

- [`code_checks.yaml` in Symplify/Symplify](https://github.com/Symplify/Symplify/blob/373bbc80d73fd8c2777fdb5fb48386b456857b57/.github/workflows/code_checks.yaml)
- [`code_checks.yaml` in rectorphp/rector](https://github.com/rectorphp/rector/blob/1f4b36dfedb1b07d8425093474fc5951358b0590/.github/workflows/code_checks.yml)


## Bad Luck Organization with Some Private Repositories

Some organizations don't have access to Github Actions because of some open-sources/private accounts.
This sucks a lot. I tried to have GitHub Actions on [KnpLabs/DoctrineBehaviors](https://github.com/KnpLabs/DoctrineBehaviors/pull/494#issue-360222690), but it's not possible unless the whole *KnpLabs* switches to some paid accounts. I spoke with support over a dozen emails, and it's wont fix. 

How to work around this? If we create a new organization that is **open-source only**, it will work. Or we can just move the repository to an existing **open-source only** one. 

## Add Badge?

**In Travis**, you could add a badge for the whole build on a specific branch. It was nice that it skipped allowed failure.

**In GitHub Actions**, it's different. ~~The badge covers **1 specific workflow**. This workflow should contain all the relevant jobs~~.

Actually, on GitHub repository, there is only one badge for all:

<img src="/assets/images/posts/github_actions_badge.png" class="img-thumbnail"> 

## Concurrent Jobs

**Travis CI** allow only [3 concurrent jobs](https://travis-ci.com/plans). This forces us to group similar checks like coding standard, static analysis, and Rector to one big job. If it failed, we had to look inside to find out which of these 3 areas is it. 

**GitHub Actions** allows you... wait for it [**20 jobs**](https://help.github.com/en/actions/automating-your-workflow-with-github-actions/about-github-actions#usage-limits). 

Thanks to that, we can have one job for each of:

- `bin/console lint:yaml`
- `bin/console lint:twig`
- `bin/console lint:container`

When `bin/console lint:twig` fails, we know right in the pull-request it's something in our templates. That's good quality, precise feedback.

## Where Should We Stay with Travis CI

### Local Git 

GitHub Actions are tough to work with local git. I migrated Symplify/MonorepoBuilder and Symplfiy/ChangelogLinker to Github Actions, and it's only troubling. 

### Building of PHAR and Push to Another Repository

Another weakness is the inability to get the current tag. That's right. Getting something as simple as an existing tag is rocket science.

That's why we had to [revert `rector.phar` build and publish to Travis CI](https://github.com/rectorphp/rector/blob/1f4b36dfedb1b07d8425093474fc5951358b0590/.travis.yml).  

### Monorepo Split

The monorepo split is the most massive performance operation on the whole CI. Also, GitHub actions have different git settings that break it — saying that it makes sense to have 20 parallel jobs on GitHub Actions and the heaviest on Travis. **We kept [monorepo split](https://github.com/symplify/symplify/blob/716eef260fcdf21362361cc9a3dab51a5a0408eb/.travis.yml#L9-L16) on Travis as the only job** and it's now faster than ever.

<br>

I think that's due to service being pretty new to the market. I hope these issues will be seen as primitive soon. For the fest of the features, **I love GitHub Action** and think you'll too after having feedback under 3 minutes after the last commit :). 

<br>

Happy coding!
