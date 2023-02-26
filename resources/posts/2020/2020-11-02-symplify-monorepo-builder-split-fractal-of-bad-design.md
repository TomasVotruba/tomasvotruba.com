---
id: 286
title: "Symplify Monorepo&nbsp;Builder Split - Fractal of Bad&nbsp;Design"
perex: |
    Splitting monorepo is a trivial operation of getting some code to some repository. Unless your take into rocket science like Symplify does. It is slow, complicated, and doesn't work on GitHub, where the open-source lives.

---

## 1. It's Super Slow

[Symplify/MonorepoBuilder](https://github.com/symplify/monorepo-builder) is easy to set up and easy to use:

```bash
vendor/bin/monorepo split
```

But the split operation itself is not really Usain Bolt among [instant feedbacks](/blog/2020/01/13/why-is-first-instant-feedback-crucial-to-developers/):

<blockquote class="blockquote text-center" markdown="1">
"...symplify/monorepo-builder ... **takes ~20 minutes** to go through and roll out all packages, one by one"
    <footer class="blockquote-footer">
        <cite><a href="https://github.com/zf1s/zf1/issues/33#issuecomment-732113017">Zend Framework split</a></cite>
    </footer>
</blockquote>

<br>

As for Symplify, if we merge pull-request, **it takes ~8 minutes to use the code**.
It takes ~4&nbsp;minutes of waiting for Travis to notice, then 3 minutes to split ~15 packages, and 1 minute to trigger Packagist.

Found a typo in the return type? Commit and... wait 8 minutes. That is bad and breaks the flow and your productivity.

## 2. It's so Slow, Despite having Parallel Run

A typical solution for such performance issues is [running it in parallel processes](https://phpstan.org/blog/from-minutes-to-seconds-massive-performance-gains-in-phpstan). The speed gain is x-times, where `x` is the number of CPUs your machine has.

```bash
vendor/bin/monorepo-builder split --max-processes 6
```

[Before we added parallel run](https://github.com/symplify/symplify/pull/620), it took **over 7 minutes** on just **8 packages**!

## 3. It's Rocket Science at it's Worst

**What is a monorepo split?**

- take some subdirectory
- push it into some remote git repository

<br>

That's it! Nothing fancy, nothing that needs an MIT degree. Still, the Symplify implementation includes these layers:

- git to do the split
- [163-lines bash script to handle the git](https://github.com/symplify/monorepo-builder/blob/db9a1aa840092a66234c166cbcc9d6d9196d81b1/packages/Split/bash/subsplit.sh)
- PHP command to wrap the bash script
- Travis bash script to run the PHP command in CLI
- ...

<img src="/assets/images/posts/2020/symplify_monorepo_split.jpg" class="img-thumbnail">

Why keep it simple, right?

## 4. It has The Worst Error Message Output

Thanks to previous Inception-complexity, the code can break in any of these layers. From git to invalid bash syntax to a tool that wraps the Git API in PHP or typo in the target directory.

In [reality](https://travis-ci.com/github/symplify/symplify/jobs/363743493), you get something like this:

<img src="/assets/images/posts/2020/symplify_monorepo_fail.png" class="img-thumbnail">

I do appreciate a good error message.

## 5. It Depends on both Travis and GitHub

If you want to make it work on your monorepo, [you have to follow these steps](/blog/2018/07/19/how-to-make-github-and-travis-split-monorepo-to-multiple-git-repositories-for-you/):

- go to GitHub to get a token
- go to Travis, set the token
- add .travis.yml
- add monorepo-builder.yml

<img src="/assets/images/posts/2020/symplify_monorepo_long.png" class="img-thumbnail">

The [vendor-lock](/blog/2019/03/11/why-we-migrated-from-nette-to-symfony-in-3-weeks-part-3/) on 2 services - GitHub and Travis - might be do-able for open-source maintainers. But if you just want to start with monorepo, learning **2 services at once is a killer**.

<br>

There is no need for that. And yes, there is a better way to handle automated monorepo split. I'll show you in the next post.

<br>

Happy coding!
