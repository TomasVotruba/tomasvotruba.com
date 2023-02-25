---
id: 278
title: "How to Apply Coding&nbsp;Standard on PHP&nbsp;Snippets in Markdown&nbsp;Files?"
perex: |
    Have you been asking this question since the inventions of coding standards? Do you write `README.md`?
    If you maintain an open-source project, you do.


    Coding standards are a matter of adding a few lines into `composer.json` and your favorite CI.
    **But what about `README.md` files?

    Who will take care of them?** Should we accept clean code in `/src`, but crap code in PHP snippets in Markdown? What if someone reading `README.md` will adopt its bad coding habits?


    I say: "We shall not!"

---

**This feature [was contributed](https://github.com/symplify/symplify/pull/2118) to ECS 8.3 by [samsonasik](https://github.com/samsonasik). Thank you!**

<br>

We try to make new features **as intuitive to use as possible**, so you have to learn as less a possible to use them. Have more fun.

You already know ECS command `check`:

```bash
vendor/bin/ecs check src

# to change the code
vendor/bin/ecs check src --fix
```

## `check` but for Markdown Files

How to apply the same coding standard to markdown files?

Just use `check-markdown` command instead of `check`:

```bash
vendor/bin/ecs check-markdown README.md
```

You can use multiple files or directories:

```bash
# do you have multiple files?
vendor/bin/ecs check-markdown README.md packages

# or target names only?
vendor/bin/ecs check-markdown README.md packages/**/README.md
```

How to **fix the content**? Just add `--fix`:

```bash
vendor/bin/ecs check-markdown README.md --fix
```

All the rules that you defined in `ecs.php` will be applied the same way they're applied to PHP code.

<br>

## How does it Look in Practise?

<img src="/assets/images/posts/2020/check_markdown.gif" class="img-thumbnail">

## Composer Scripts Tip

How to add all this to your workflow? Another command to run every time? Nah, that's too [memory locking](/blog/2018/08/27/why-and-how-to-avoid-the-memory-lock/) and tedious.

Instead, we can extend [composer scripts](https://blog.martinhujer.cz/have-you-tried-composer-scripts/) in `composer.json`:

```diff
 {
     "scripts": {
-        "check-cs": "vendor/bin/ecs check",
+        "check-cs": [
+           "vendor/bin/ecs check",
+           "vendor/bin/ecs check-markdown README.md",
+        ],
-        "fix-cs": "vendor/bin/ecs check --fix"
+        "fix-cs": [
+            "vendor/bin/ecs check --fix",
+            "vendor/bin/ecs check-markdown README.md --fix",
+        ]
     }
 }
```

How does it change your workflow? Not at all! You can still use the same commands:

```bash
composer checks-cs
composer fix-cs
```

Just now they're much smarter ;)

<br>

Happy coding!
