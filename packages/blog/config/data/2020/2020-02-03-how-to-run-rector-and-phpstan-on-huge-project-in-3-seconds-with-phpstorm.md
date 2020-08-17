---
id: 237
title: "How to run Rector and PHPStan on Huge Project in 3 Seconds with PHPStorm"
perex: |
    Today we'll look at the command line and run performance heavy dev tools like Rector or PHPStan on our projects. I'm guessing that it takes more than 10 seconds to run whatever project you use.
    <br>
    <br>
    How would you like it be **less than 3 seconds with just one click** in PHPStorm?

tweet: "New Post on #php 🐘 blog: How to run Rector and PHPStan on Huge Project in 3 Seconds with PHPStorm"
tweet_image: "/assets/images/posts/2020/external_tools_rector.gif"
---

Let's say we have a reasonably big codebase, like Rector, with over 70 000 lines of code. Projects I consult lately usually fit into 200-600 000 lines of code, though. But let's take Rector as the baseline, as that's the project we have data for.

We add a new feature, change test, fix a bug, 300 added lines, 200 removed.

Now, **we want to run Rector and PHPStan to make sure the code is clean and working**. So we run PHPStan:

<img src="/assets/images/posts/2020/external_tools_normal_phpstan.gif" class="img-thumbnail">

And wait...

<br>
<br>
<br>

...**40 seconds**!

<br>


## How do we Hack It?

It takes so long. We might adapt and **push it into the remote repository and let the CI server handle it**. After all, it's often much faster than our local laptop. I used this approach at big codebases so far.

Another way is to copy the directory path and run tool only on it:

```bash
vendor/bin/phpstan analyse src/SomeDirectory
vendor/bin/rector process src/SomeDirectory
```

But we have to:

- copy directory path or type it
- open command line
- type whole command we usually use
- remove argument we typically use
- add our directory argument
- click to run it

Instant flow killer :(

## Too Slow? Go Home!

When I talk with developers, this is one of the main reasons they don't use such tools. It takes too long to get feedback from them. It destroys their focus on the features.

<blockquote class="blockquote text-center mt-5 mb-5">
Frustration is always the sign we should ask this question:
<br>
How can we do better?
</blockquote>

<br>

## Narrow Scope → Increase Focus

Last week [Honza](https://twitter.com/mikes_honza) showed me PHPStorm tip that fits [into instant feedback loop flow](/blog/2020/01/27/switch-travis-to-github-actions-to-reduce-stress) I discovered recently.

<a href="https://twitter.com/mikes_honza/status/1222557580507127811">
<img src="/assets/images/posts/2020/external_tools_tweet.png" class="img-thumbnail">
</a>

**It might trim off some more frustration** in your daily work and turn you into a flow developer as a result. The bigger your codebase, the more it might help you get under 3 seconds.

<br>

## How does it Work?

You run **the tool only on a selected directory in the left tree**.

- Without the command line.
- Without changing paths.
- Without copy-pasting.

### Domain Directory Separation

This approach requires us to keep a decoupled structure, e.g., with to [local packages](/blog/2017/12/25/composer-local-packages-for-dummies) or domain-driven design.

What does it mean? That our code that handles security is not all over the place in `/src` directory but in standalone `packages/Security` directory. So if we work with security, we ~~only~~ mostly work in that directory.

## How to Configure External Tool in PHPStorm?

In PHPStorm open:

- Settings →
- Tools →
- External Tools

<br>

Click on *+*:

<img src="/assets/images/posts/2020/external_tool_add.png" class="img-thumbnail">

<br>

**Name the tool and configure parameters**

- Program: `$ProjectFileDir$/vendor/bin/rector`
- Arguments: `process $FilePathRelativeToProjectRoot$`
- Working directory: `$ProjectFileDir$`

<img src="/assets/images/posts/2020/external_tools_one.png" class="img-thumbnail">

<br>

Save.

## How to Use It?

Pick the directory you want to process in the file tree.

Run action.

Type "Rector" or "PHPStan" and hit enter:

<img src="/assets/images/posts/2020/external_tools_rector.gif" class="img-thumbnail">

<br>

Kaboom :) That's it!

<br>

Happy flow coding!
