---
id: 24
title: "Why I Switched Scrutinizer for PHPStan and You Should Too"
perex: |
    I used Scrutinizer for a few years now for code coverage and code quality. Configuration was far complex, some issues appeared and build kept failing. But I really wanted a code quality checker for my open-source projects and this was the best tool available.

    But last week I had an issue with simple `composer install` command and I have had enough. Then **my attention turned to PHPStan**, soon-to-be its replacement.
---

## What is PHPStan

**PHPStan is a tool for static analysis of PHP code**. It's open source and free to use.
You can read more about it in this post with very true title - [PHPStan: Find Bugs In Your Code Without Writing Tests!](https://medium.com/@ondrejmirtes/phpstan-2939cd0ad0e3)

## Why I Prefer It over Scrutinizer

### It Is Open-Source

**I can improve it, I can add an issue, I can see its development**. I can't do anything like that with Scrutinizer. It used to be [open-source](https://github.com/scrutinizer-ci/scrutinizer) but got closed. That's was a huge step back.

### It Focuses Just on PHP

**It's a PHP tool that checks PHP code.** Scrutinizer, on the other hand, focuses on various languages - Python, Ruby, soon Java and Scala. That's definitely a good direction, but not if a simple `composer install` command breaks and is not fixed for months.

### I Can Control It

I can use it for private packages. I can download it, extend it in various ways (I can define magic behaviour of my classes) and even write my own checks.

## How to Switch from Scrutinizer to PHPStan in 4 Steps

### 1. Disable Scrutinizer Code Rating

Drop this from `.scrutinizer.yml`:

```yaml
checks:
    php:
        code_rating: true
```

### 2. Add PHPStan Dependency

```bash
composer require phpstan/phpstan --dev
```

### 3. Setup Command in `composer.json`

This step is optional and it might seem weird seeing it for the first time, but I like the united usage (on all different projects and environments).

```json
{
    "scripts": {
        "phpstan": "vendor/bin/phpstan analyse src --level=0"
    }
}
```

Now you can run with the same script, even if the settings changes (and it will):

```bash
composer phpstan
```

### 4. Setup you CI

```yaml
# .travis.yml
script:
    - composer phpstan
```

Commit and push! Now you are running PHPStan on your open-source project. Congrats!

## The One Thing I Love About PHPStan

One last thing. You may have noticed the `level` option. What's that for? PHPStan has now 6 levels (in time of writing this article) - **0 = least strict, 5 = the most strict**.

This allowed me to **put PHPStan to action without any huge work**. I start with level 0, 12 errors were found and fixed them.

Next week, when I'm rested and full of joy, I can go to level 1, fix another 8 errors.

I love this approach over traditional overwhelming "500 errors found. Fix them all or CI will keep failing.". That usually leads to removing the tool and to very long fixing process. I remember my long night hours with Scrutinizer just to get from code quality from 6 to 10.


### Try It Out...

...on your open-source or local projects and let me know how you like it.

Happy coding!



