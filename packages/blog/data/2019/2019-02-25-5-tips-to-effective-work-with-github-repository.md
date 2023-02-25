---
id: 189
title: "5 Tips to Effective Work with Github Repository"
perex: |
    The best programmers aren't the smartest in the field. They're lazy, **they know their tools well** and **they know good tools** other programmers don't.


    Do you know the following tips?
tweet_image: "/assets/images/posts/2019/github-tips/list.gif"
---

## 1. Single-Char Console Commands for Your Tired Fingers

Which one is correct?

```bash
vendor/bin/rector process
vendor/bin/rector procces
vendor/bin/rector proccess
vendor/bin/rector proces
```

You don't want to think about this and you don't have to! Just **use the first letter**:

```bash
vendor/bin/rector p
```

Works every-time there is no command named with that letter:

```diff
-vendor/bin/phsptan analyse
+vendor/bin/phsptan a
```

```diff
-vendor/bin/ecs check
+vendor/bin/ecs c
```

👍

## 2. Be informed about New Packages - with no Spam!

When you "watch" a Github repository, you'll get a notification about every new release, issue, pull-request, or comments. This makes sense when you maintain a repository, but for most of the people, it's annoying spam.

GitHub recently introduced a very nice feature. It will add notification **only for releases**:

<img src="/assets/images/posts/2019/github-tips/github-subscription.png" class="img-thumbnail">

👍

## 3. Fix Typos with UP

Have you ever sent a comment with a typo? I barely do without, and always notice that after I hit the "send" (or CTRL + Enter).

Now move your cursor to the 3 dots in the right corner of the comment, click, select *Edit* and click again.

**No more!**

<img src="/assets/images/posts/2019/github-tips/up.gif" class="img-thumbnail">

Just **hit ↑** (arrow up), and you're there!

👍

## 4. Refine your Github

[sindresorhus/refined-github](https://github.com/sindresorhus/refined-github) is like a smart secretary that gives you tips you ever wanted to know.

<br>

It **narrows 3 click operation to single click** - creating a pull-request from a fresh branch:

<img src="https://user-images.githubusercontent.com/1402241/34099674-20433f60-e41b-11e7-8ca5-7ea23c70ab95.gif" class="img-thumbnail">

<br>

It **interlinks issues and PRs without opening them**:

<img src="https://user-images.githubusercontent.com/1402241/37037746-8b8eac8a-2185-11e8-94f6-4d50a9c8a152.png" class="img-thumbnail" style="max-width:35em">

<br>

When I work on different pc, I feel stupid without this one. **Issues sorted by activity** beats default *create time*:

<img src="/assets/images/posts/2019/github-tips/first-new.png" class="img-thumbnail">

<br>

Check more of them in [the README](https://github.com/sindresorhus/refined-github#highlights).

## 5. Extend Composer Scripts from CLI

Have you read the classic post [Have you tried Composer Scripts? You may not need Phing](https://blog.martinhujer.cz/have-you-tried-composer-scripts)? I love this approach for simple scripts like coding standard and static analysis:

```json
{
    "scripts": {
        "check-cs": "vendor/bin/ecs check bin src tests",
        "fix-cs": "vendor/bin/ecs check bin src tests --fix",
        "phpstan": "vendor/bin/phpstan analyse bin src tests --error-format symplify"
    }
}
```

### What's the Script Name?

But what if you forget it's "check-cs"? And what if you open a new project - what's the name in there?

<img src="/assets/images/posts/2019/github-tips/list.gif" class="img-thumbnail">

👍

<br>

Great, now we know the name! But what if you want to add **one extra option** just for a single run?

<img src="/assets/images/posts/2019/github-tips/cached.gif" class="img-thumbnail">

👍

<br>

- **What number is your favorite?**
- **Which tip did I forget?**

Tell me in the comments, please.

<br>

Happy coding!
