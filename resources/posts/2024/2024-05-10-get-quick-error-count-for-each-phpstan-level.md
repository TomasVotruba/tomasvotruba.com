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

<img src="https://private-user-images.githubusercontent.com/924196/328225841-fe54adc6-17d6-4240-85aa-dba5f0dc42bf.png?jwt=eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJpc3MiOiJnaXRodWIuY29tIiwiYXVkIjoicmF3LmdpdGh1YnVzZXJjb250ZW50LmNvbSIsImtleSI6ImtleTUiLCJleHAiOjE3MTUwNjcxNjMsIm5iZiI6MTcxNTA2Njg2MywicGF0aCI6Ii85MjQxOTYvMzI4MjI1ODQxLWZlNTRhZGM2LTE3ZDYtNDI0MC04NWFhLWRiYTVmMGRjNDJiZi5wbmc_WC1BbXotQWxnb3JpdGhtPUFXUzQtSE1BQy1TSEEyNTYmWC1BbXotQ3JlZGVudGlhbD1BS0lBVkNPRFlMU0E1M1BRSzRaQSUyRjIwMjQwNTA3JTJGdXMtZWFzdC0xJTJGczMlMkZhd3M0X3JlcXVlc3QmWC1BbXotRGF0ZT0yMDI0MDUwN1QwNzI3NDNaJlgtQW16LUV4cGlyZXM9MzAwJlgtQW16LVNpZ25hdHVyZT1lMjQ4MGRjMGE4ZDY5NTYxZjFiYTk4ZTY2ODJmYzNiOGIyOTE2ZjI5NjJkYzhhY2EwNTMxOWI0NWQ1NWUyMWI2JlgtQW16LVNpZ25lZEhlYWRlcnM9aG9zdCZhY3Rvcl9pZD0wJmtleV9pZD0wJnJlcG9faWQ9MCJ9.6YM47E_mBKJ0mMT2kr-maxC24WV5SaAv7SB2ZKI-IEU" class="img-thumbnail">

<br>

So, I made the package and used it for the first project.

Here it is - [phpstan-bodyscan](https://github.com/TomasVotruba/phpstan-bodyscan).

<br>

Happy coding!
