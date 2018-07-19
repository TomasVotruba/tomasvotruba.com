---
id: 124
title: "How to Delegate Monorepo Split to Multiple Repositories to Github and Travis in Secure Way"
perex: |
    ...
tweet: "..."
related_items: [82, 25]
---

Do you use a [monorepo](https://gomonorepo.org/)? Then you know [How to maintain multiple Git repositories with ease](https://blog.shopsys.com/how-to-maintain-multiple-git-repositories-with-ease-61a5e17152e0). If you're not there yet, you may wonder [How to Merge 15 Repositories to 1 Monorepo and Keep their Git History](https://blog.shopsys.com/how-to-merge-15-repositories-to-1-monorepo-keep-their-git-history-and-add-project-base-as-well-6e124f3a0ab3).

It's great to be alive in this era. We have solved maintaining multiple repositories, even merge migration of their git history to one repository. Creating huge code bases was more never cost effective and never had steeper learning curve.

<img src="https://qph.ec.quoracdn.net/main-qimg-20062c05515851f0ef99477a40c0e1b3.webp">

The same way it was never easier to drive an autonomous car. You just sit in the car, pres the button of your destination, and Tesla will drive you there when you check all the comments on your Smart Phone.

Well, monorepo paradigm is no there yet. But it's getting there.

## The Split Problem

One of the last problem I'm aware of is splitting the monorepo to particular packages. Imagine you have [Symfony monorepo](https://github.com/symfony/symfony) and you're trying to split it to all standlone packages like [symfony/console](https://github.com/symfony/console), [symfony/dependency-injection](https://github.com/symfony/dependency-injection) and so on.

### Current Status

This whole "take a code from this directory and put it into this repository in `master` branch and last tag" process is now:

- complex
- slow
- requires lot of setup 

Instead, we want it to be:

- **simple so you will understand it in the end of reading this post**
- **fast like Travis build of your project**
- **easy to setup in 1 composer package and 5 lines of YAML**

Why? So you could amaze your friends at the party that you just set-up a monorepo split and they can enjoy merged PRs in matter of minutes (even if you know almost nothing about git or PHP).
  
Do you think we can get there? You'll see. 

## Gain & Pain Points of Current Solutions

Feel free to explore these solutions. I did it for you and here are reasons they're not good enough to be massively adopted. 

On the other hand, **I'm grateful for each one of them, because they're pushing the evolution further and further. Thanks to them we don't have to reinvent the wheel and we can build on their shoulders**.  

**splitsh/lite**
 
- https://github.com/splitsh/lite
- You need to know Go and bash and be able to resolve conflicts of their dependencies.

**dflydev/git-subsplit**

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

That could do right? At least from [developer's experience](https://symfony.com/blog/making-the-symfony-experience-exceptional) view.

<br>

But what would [security expert Michal Špaček](https://www.michalspacek.com/) say?

<img src="http://joshowens.me/content/images/2015/Feb/security-keys-meme.jpg">

## How To Avoid Rape on Github and Travis?

<blockquote class="blockquote text-center">
    "So anyone can now push to your repository whatever he wants?"
</blockquote> 

This is valid question that was probably scratching your mind when you saw "Github + Travis + git" combination with open-source.
Travis is basically a terminal, that runs few command lines. What would prevent someone from using this to "play with" your repository?

Let's look at repository adress in our example:
  
```bash
git@github.com:Symplify/MonorepoBuilder.git
```

This basically means we need to make ssh key or user name and a password public. That sound like a very good idea, right?

Don't worry, Github and Travis though about these cases - with a `GITHUB_TOKEN`.

- 1. [Create a Personal Access Token on Github](https://help.github.com/articles/creating-a-personal-access-token-for-the-command-line/)

**tl;dr;**

- Go to your [Github Tokens](<img src="https://github.com/settings/tokens">)
- Click *Generate new token*
- Check only *repo* scope 
- Click *Generate token*

    <img src="/assets/images/posts/2018/monorepo-split/github-token.png">

- 2. [Defining Variables in Repository Settings on Travis](https://docs.travis-ci.com/user/environment-variables/#Defining-Variables-in-Repository-Settings)

**tl;dr;**

- Go to Travis settings of your repository, e.g. `https://travis-ci.org/Symplify/Symplify/settings`
- Jump to *Environment Variables* section
- Create `GITHUB_TOKEN` with value from Github
- Click *Add*

    <img src="https://docs.travis-ci.com/images/settings-env-vars.png">

In the end it should look like this:

    <img src="/assets/images/posts/2018/monorepo-split/github-token.png">

### Github and Travis Protects You

Now the best part. If you accidentally commit your access token in `.travis.yml` (like I did while testing), **it will immediatel disable it** and sends you an email (I found out the next day after 4 hours of debugging why the token is not working).

And if you add token to your repository on Travis as above, **it will hide it in all logs for you**. No need to hash it. 

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

Now we have:
 
- the `symplify/monorepo-builder` package as local dependency
- configured package to repository paths in `monorepo-builder.yml`
- secured `GITHUB_TOKEN` in Travis settings for your monorepo repository
- a command to run the split: `vendor/bin/monorepo-builder split` 

What is missing?

Oh right, when will the monorepo be split? Do we have to do it manually? How often? Should Travis CRON do it on daily basis?

## When is the Best Time to Split our Monorepo?

Let's get back to our ideal product:

- "1 command to install"
- "zero setup" 
- "1 command to run"
- **"1 minute to finish the whole process"**
- "split only what I and peopl e really need"

It often happens we merge fix or feature to monorepo and we want to try it before rushing to tagging a stable release. We want to do it as soon as possible, without manually triggering Travis to do it. Also, we don't want Travis to waste energy on pull-requests that are not merged to master. That would only slow the whole CI process down and frustrate contributors and maintainer.

So how would `.travis.yml` would look like?

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

# tests and other scripts

after_script:
  # split monorepo to packages - only on merge to master
  - |
    if [[ $TRAVIS_EVENT_TYPE == "push" && $MONOREPO_SPLIT == true && $TRAVIS_BRANCH == "master" ]]; then
      vendor/bin/monorepo-builder split -v
    fi
```

That way the split command is run only merge to master and **after each merge**. That way you can test your feature in matter of minutes... 

How fast is it? To give you an idea about the speed, this is Symplify build with split of 10 packages:

<img src="/assets/images/posts/2018/monorepo-split/speed.png">

**It takes under 7,5 minutes** including all the tests, static analysis and code style validation. 


### Are You Into Git Internals?

I knew you are! 

All it' wrapped in a bash file at the moment. It could be done in `symfony\process`, but the original source [subsplit.sh](https://github.com/dflydev/git-subsplit) was in bash so I used it.


There are ~160 lines but most of them are setup of arguments, options, their resolving, preparing the repository and other boring stuffs. The interesting part is really [in these](https://github.com/Symplify/MonorepoBuilder/blob/db9a1aa840092a66234c166cbcc9d6d9196d81b1/packages/Split/bash/subsplit.sh#L107) [4 lines](https://github.com/Symplify/MonorepoBuilder/blob/db9a1aa840092a66234c166cbcc9d6d9196d81b1/packages/Split/bash/subsplit.sh#L123-L126):

```bash
git remote add origin "git@github.com:Symplify/MonorepoBuilder.git"

git checkout -b "master"
git subtree split -q --prefix="/packages/MonorepoBuilder" --branch="master"

git push -q --force origin "master"
```

I used "real values" instead of $VARIABLES, so it's more clear to you.
In human word, it works like this:

```bash
# in what repository should we push the code?
git remote add origin "git@github.com:Symplify/MonorepoBuilder.git"

# what branch do we push there?
git checkout -b "master"

# the split magic!
git subtree split -q --prefix="/packages/MonorepoBuilder" --branch="master"

# push this branch to remove branch
git push -q --force origin "master"
```

That is really it!

If you're git split geek like me, feel free to explore whole [`subsplit.sh` script](https://github.com/Symplify/MonorepoBuilder/blob/master/packages/Split/bash/subsplit.sh). There are many nice little details to learn from.

<br>

So, do you think you're ready to fascinate your friends tonight with all your brand new monorepo split setup?

 
 