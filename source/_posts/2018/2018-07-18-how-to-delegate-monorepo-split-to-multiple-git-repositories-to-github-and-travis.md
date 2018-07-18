---
id: 124
title: "How to Delegate Monorepo Split to Multiple Repositories to Github and Travis"
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
- requires lot of setup (like [splitsh/lite](https://github.com/splitsh/lite) package written in Go) 

Instead, we want it to be:

- **simple** so you will understand it in the end of reading this post
- **fast** like Travis build of your project
- **easy** to setup in 1 composer package and 5 lines of YAML

Why? So you could amaze your friends at the party that you just set-up a monorepo split and they can enjoy merged PRs in matter of minutes (even if you know almost nothing about git or PHP).
  
Do you think we can get there? You'll see. 

## Gain & Pain Points of Current Solutions

Feel free to explore these solutions. I did it for you and here are reasons they're not good enough to be massively adopted. 

On the other hand, **I'm grateful for each one of them, because they're pushing the evolution further and further. Thanks to them we don't have to reinvent the wheel and we can build on their shoulders**.  

**splitsh/lite**
 
- https://github.com/splitsh/lite
- You need to know Go.

**dflydev/git-subsplit**

- https://github.com/dflydev/git-subsplit
- Extra splits that are not needed, complex configuration.
- Requires manual bash install.
- It was used by Laravel and then by Symplify.

## What Would the Ideal State Look Like?

Before diving into solution and how to do it, I try to stop and go to a wonderland. What would the ideal solution look like? How would I use it? How would I explain it to others? How fast would it be?

I try to break away from your know-how limits (because they're limiting my thinking obviously) and come up with absolutely non-sense answers:

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

That could do right?



@todo
@travis and github help
@security fisrt

michale would kill use her



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

 
 