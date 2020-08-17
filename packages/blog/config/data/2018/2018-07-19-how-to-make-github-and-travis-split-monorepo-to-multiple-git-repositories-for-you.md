---
id: 124
title: "How to Make Github and Travis Split Monorepo to Multiple Git Repositories for You"
perex: |
    Do you use a [monorepo](/clusters/#monorepo-from-zero-to-hero)? Then you know [How to maintain multiple Git repositories with ease](https://blog.shopsys.com/how-to-maintain-multiple-git-repositories-with-ease-61a5e17152e0). If you're not there yet, you may wonder [How to Merge 15 Repositories to 1 Monorepo and Keep their Git History](https://blog.shopsys.com/how-to-merge-15-repositories-to-1-monorepo-keep-their-git-history-and-add-project-base-as-well-6e124f3a0ab3).
    <br><br>
    Are you and your monorepo ready? Today we'll focus on **fast, secured and outsourced monorepo auto split** - all that under 10 minutes.
tweet: "New Post on My Blog: How to Make #Github and #Travis Split #Monorepo to Multiple #Git Repositories for You   #security"
tweet_image: "/assets/images/posts/2018/monorepo-split/found-keys.jpg"
---

It's great to be alive in this era. We have solved maintaining multiple repositories, even merge migration of their git history to one repository. Creating huge code bases was more never cost effective and never had a steeper learning curve.

The same way it was never easier to drive an autonomous car. You just sit in the car, press the button of your destination, and Tesla will drive you there when you check all the news on [/r/PHP](https://www.reddit.com/r/PHP).

Well, the monorepo paradigm is not there yet but it's getting there.

## The Split Problem

One of the last problems I'm aware of is splitting the monorepo to particular packages. Imagine you have [Symfony monorepo](https://github.com/symfony/symfony) and you're trying to split it to all standalone packages like [symfony/console](https://github.com/symfony/console), [symfony/dependency-injection](https://github.com/symfony/dependency-injection) and so on.

### Current Status

This whole "take a code from this directory and put it into this repository in `master` branch and last tag" process is now:

- complex
- slow
- requires a lot of setups

Instead, we want it to be:

- **simple so you will understand it at the end of reading this post**
- **fast like Travis build of your project**
- **easy to set up in 1 composer package and 5 lines of YAML**

Why? So you could amaze your friends at the party that you just set-up a monorepo split and they can enjoy merged PRs in a matter of minutes (even if you know almost nothing about git or PHP).

Do you think we can get there? You'll see.

## The Best Solutions to Split (So Far)

Feel free to explore these following solutions. I did it for you and here are a few blockers that hold them from being massively adopted.

On the other hand, **I'm grateful for each one of them, because they're pushing the evolution further and further. Thanks to them we don't have to reinvent the wheel and we can build on their shoulders**.

### 1. splitsh/lite

- https://github.com/splitsh/lite
- You need to know Go and bash and be able to resolve conflicts of their dependencies.

### 2. dflydev/git-subsplit

- https://github.com/dflydev/git-subsplit
- Extra splits that are not needed
- Complex configuration
- Requires manual bash install
- It was used by Laravel and then by Symplify

## What Would the Ideal State Look Like?

Before diving into solution and how to do it, I try to stop and go into a wonderland. What would the ideal solution look like? How would I use it? How would I explain it to others? How fast would it be? Try to break away from your know-how limits (because they're limiting your thinking) and be free to come up with absolutely non-sense answers:

- "1 command to install"
- "zero setup"
- "1 command to run"
- "1 minute to finish the whole process"
- "split only what I and people really need"

<br>

If we put it in the code, it might look like:

```bash
composer require symplify/monorepo-builder --dev
```

```yaml
# monorepo-builder.yml
parameters:
    directories_to_repositories:
        packages/MonorepoBuilder: 'git@github.com:Symplify/MonorepoBuilder.git'
```

```bash
vendor/bin/monorepo-builder split
```

That could do, right? At least from a [developer's experience](https://symfony.com/blog/making-the-symfony-experience-exceptional) view.

<br>

But what would [security expert Michal Špaček](https://www.michalspacek.com) say to lousy code like that?

<img src="/assets/images/posts/2018/monorepo-split/found-keys.jpg" class="img-thumbnail">

## How To Avoid Rape on Github and Travis?

<blockquote class="blockquote text-center">
    "So, anyone can now push to your repository whatever he wants?"
</blockquote>

This is a valid question that was probably scratching your mind when you saw *Github + Travis + git + open-source* combination.
Travis is basically a terminal, that runs few command lines. What would prevent someone from using this to "play with" your repository?

Let's look at the repository address in our example:

```bash
git@github.com:Symplify/MonorepoBuilder.git
```

This basically means **we need to make ssh key or username and a password public**. Does that sound like a good idea to you?

Don't worry, Github and Travis thought about these cases - with a hashed `GITHUB_TOKEN` environment variable.

### 1. Create a Personal Access Token on Github

First, you need to create a custom token, that will authorize access to your Github repositories from any command line where it will be used.

Read [the Github docs](https://help.github.com/articles/creating-a-personal-access-token-for-the-command-line) or use **tl;dr;**:

- Go to your [Github Tokens](https://github.com/settings/tokens)
- Click *Generate new token*
- Check only *repo* scope
    <img src="/assets/images/posts/2018/monorepo-split/github-token.png" class="img-thumbnail">
- Click *Generate token*

### 2. Add `GITHUB_TOKEN` in Repository Settings on Travis

Then we need to store this token to Travis build, so Travis can be authorized to manipulate with your repositories without any password or ssh key.

Read [the Travis docs](https://docs.travis-ci.com/user/environment-variables/#Defining-Variables-in-Repository-Settings-) or use **tl;dr;**:

- Go to Travis settings of your repository - `https://travis-ci.org/<your>/<repository>/settings`
- Jump to *Environment Variables* section
- Create `GITHUB_TOKEN` with the value from Github
- Click *Add*

In the end, it should look like this:

<div class="text-center">
    <img src="/assets/images/posts/2018/monorepo-split/token-after.png" class="img-thumbnail">
</div>

*If you got lost in this tl;dr;s, try [this nice post with so many screenshots](https://developer.ibm.com/recipes/tutorials/separating-continuous-integration-from-continuous-deployment-using-github-and-travis-ci).*

### GitHub and Travis Protects You

Now the best part. If you accidentally commit your access token in `.travis.yml` (like I did while testing), **GitHub will immediately disable it** and sends you an email (to bad I found that out the next day after 4 hours of debugging with that token).

And if you add the token to your repository on Travis as above, **it will hide it in all logs for you**. No need to hash it.

So instead of insecure

```bash
git@github.com:Symplify/MonorepoBuilder.git
# or
https://aFjk02FJlkj1675jlk@github.com/Symplify/MonorepoBulder.git
```

everyone will see:

```bash
https://[secure]@github.com/Symplify/MonorepoBulder.git
```

Sound and safe!

<br>

**Now we have:**

- the `symplify/monorepo-builder` package as a local dependency
- configured package to repository paths in `monorepo-builder.yml`
- secured `GITHUB_TOKEN` in Travis settings for your monorepo repository
- a command to run the split: `vendor/bin/monorepo-builder split`

Something is missing...

Oh right, **when** will the monorepo be split? Do we have to do it manually? How often? Should Travis CRON do it on daily basis?

## When is the Best Time to Split our Monorepo?

In times like these, get back to our ideal product:

- "1 command to install"
- "zero setup"
- "1 command to run"
- **"1 minute to finish the whole process"**
- "split only what maintainer and users really need"

It often happens **we merge fix or feature to monorepo and we want to try it** before rushing to tagging a stable release. We want to do it **as soon as possible**, **without manually triggering Travis** to do it. Also, we **don't want Travis to waste energy on pull-requests** that are not merged to master. That would only slow the whole CI process down and frustrate contributors and maintainer.

Saying that how `.travis.yml` should look like?

```yaml
language: php

# required for "git tag" presence for MonorepoBuilder split and ChangelogLinker git tags resolver
# see https://github.com/travis-ci/travis-ci/issues/7422
git:
  depth: false

matrix:
  include:
    - php: 7.2
      env: MONOREPO_SPLIT=true
    # ... other builds

install:
  - composer install

# ... other scripts

after_script:
  # split monorepo to packages - only on merge to master
  - |
    if [[ $TRAVIS_EVENT_TYPE == "push" && $MONOREPO_SPLIT == true && $TRAVIS_BRANCH == "master" ]]; then
      vendor/bin/monorepo-builder split -v
    fi
```

That way the split command is run only merge to `master` and **exactly once after each merge**. So you can test your feature in a matter of minutes...

Wait wait, no vague statements like *a matter of minutes*. How **fast it really is**? To give you an idea, this is Symplify Travis build with a split of 10 packages:

<img src="/assets/images/posts/2018/monorepo-split/speed.png" class="img-thumbnail mb-4">

**It takes under 7,5 minutes** including all the tests, static analysis and code style validation.

That's all folks. You're ready to go and try it on your monorepo.

<br>

## Are You Into Git Internals?

I knew you are, so here are few details.

All it' wrapped in a bash file at the moment. It could be done in `symfony\process`, but the original source [subsplit.sh](https://github.com/dflydev/git-subsplit) was in bash so I used it.

There are ~160 lines but most of them are arguments and options configuration, their resolving, preparing the repository and other boring stuff. The interesting part is really [in this 1](https://github.com/Symplify/MonorepoBuilder/blob/db9a1aa840092a66234c166cbcc9d6d9196d81b1/packages/Split/bash/subsplit.sh#L107) and [these 3 lines](https://github.com/Symplify/MonorepoBuilder/blob/db9a1aa840092a66234c166cbcc9d6d9196d81b1/packages/Split/bash/subsplit.sh#L123-L126):

```bash
git remote add origin "git@github.com:Symplify/MonorepoBuilder.git"

git checkout -b "master"
git subtree split -q --prefix="/packages/MonorepoBuilder" --branch="master"
git push -q --force origin "master"
```

I used "real values" instead of `$VARIABLES`, so it's more clear to you.

In human words it works like this:

```bash
# in what repository should we push the code?
git remote add origin "git@github.com:Symplify/MonorepoBuilder.git"

# what branch do we push there?
git checkout -b "master"

# the split magic!
git subtree split -q --prefix="/packages/MonorepoBuilder" --branch="master"

# push this branch to remote branch
git push -q --force origin "master"
```

That is really it!

**If you're git split geek (like me), feel free to explore the whole [`subsplit.sh` script](https://github.com/Symplify/MonorepoBuilder/blob/master/packages/Split/bash/subsplit.sh)**. There are many nice little details to learn from.

<br>

So, do you think you're ready to fascinate your friends tonight with all your brand new monorepo split setup?
