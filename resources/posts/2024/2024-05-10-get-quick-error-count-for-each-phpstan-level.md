---
id: 408
title: "Get Quick Error Count for each&nbsp;PHPStan&nbsp;level"
perex: |
    When I come to a new project, I want to make a rough idea of what I'm dealing with in a few minutes. I usually check `composer.json` and [measure the lines](/blog/easy-and-quick-way-to-measure-lines-of-code-in-php).

    Then, I'd love to run PHPStan and get a rough idea of the current state. But are there baselines, custom extensions, global ignores, or no PHPStan at all?
---

## Fast Project Feedback

PHPStan is a great tool to check your code for static errors. It's also a great way to visualize where we are today and how much work it would take to reach the next level. The problem is that it can only analyze one level at a time—usually, the one defined in the `phpstan.neon` file.

Also, even if we switch the level easily with the `--level` CLI option, we might get a false positive result: 0 errors.

**Why false positive?** That's because 400 errors are ignored in either baselines or in `phpstan.neon` itself. We'd have to go through this, create a bare `phpstan.neon` and run PHPStan again.

However, there are also extensions that add their own errors. When we run the same PHPStan on the same code, we'll get different results based on enabled extensions.

## Standardized Results

What I need are standardized results so we can:

* **compare status of multiple projects**,
* and also **report progress in time** - so the business can see we're making progress by lowering the error count

We need a bare `phpstan.neon` that runs, records error count, and then runs again with the next level, from 0 to 1, 1 to 2, etc. Creating `phpstan.neon` per project and running PHPStan multiple times is not my favorite activity, so I thought about automating it.

## PHPStan Bodyscan

I needed a package you can install anywhere on PHP 7.2+:


```bash
composer require tomasvotruba/phpstan-bodyscan --dev
```

<br>

Then you can run it locally in the current project:

```bash
vendor/bin/phpstan-bodyscan
```

<br>

Or on the project in a different directory:

```bash
vendor/bin/phpstan-bodyscan run ../favorite-project
```

<br>

And then, it would be great to see a simple result table:

```bash
+-------+-------------+
| Level | Error count |
+-------+-------------+
|     0 |           0 |
|     1 |          35 |
|     2 |          59 |
|     3 |          93 |
|     4 |         120 |
|     5 |         125 |
|     6 |         253 |
|     7 |         350 |
|     8 |         359 |
+-------+-------------+
```

<br>

That I could copy-paste to GPT and get a simple chart:

<img src="/assets/images/posts/2024/levels-chart.png" class="img-thumbnail">

<br>

So, I made the package and used it for the first project.

Here it is - [phpstan-bodyscan](https://github.com/TomasVotruba/phpstan-bodyscan).

<br>

Happy coding!
