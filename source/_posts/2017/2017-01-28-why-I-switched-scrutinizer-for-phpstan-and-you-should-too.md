---
layout: post
title: "Why I Switched Scrutinizer for PHPStan and You Should Too"
perex: '''
    I used Scrutinizer for a few years now for code coverage and code quality. Configuration was far complex, some issues appeared and build kept failing. But I really wanted a code quality checker for my open-source projects and this was the best tool there is.
    <br><br>
    But last week I had an issue with simple <code>composer install</code> command and I have had enough. Then <strong>my attention turned to PHPStan</strong>, soon-to-be its replacement.
'''
lang: en
---

## What is PHPStan

**PHPStan is a tool for static analysis of PHP code**. It's open source and free to use.
You can read more about it in post with very true title - [PHPStan: Find Bugs In Your Code Without Writing Tests!](https://medium.com/@ondrejmirtes/phpstan-2939cd0ad0e3)

<img src="https://raw.githubusercontent.com/phpstan/phpstan/master/build/phpstan.gif" alt="PHPStan in action" class="thumbnail">


## Why I Prefer It over Scrutinizer (and You Should Too)

### It Is Open-Source

**I can improve it, I can add an issue, I can see its development**. That's all I can't do in Scrutinizer. It used to be [open-source](https://github.com/scrutinizer-ci/scrutinizer) but got hidden. That's was a huge step back.

### It Focuses Just on PHP

**It's PHP tool that checks PHP code.** Scrutinizer, on the other hand, focuses on various languages - Python, Ruby, soon Java and Scala. That's definitelly a good direction, but not it if simple `composer install` command breaks and is not fixed for months.

### I Can Control It

I can use it for private packages. I can download it, extend it and write own checks.



For all these reasons I support [PHPStan on Patreon](https://www.patreon.com/phpstan). Try giving **50 % of your hourly rate** a month. It can make a huge impact on PHP world.


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
        "phpstan": "vendor/bin/phpstan analyse src --level=1"
    }
}
```

Now you can run with the same script, even if the settings changes (and it will):

```bash
composer phpstan
```

### 4. Add to `travis.yml`

```yml
script:
  - composer phpstan
```

Commit and... push! Now you are running PHPStan on your open-source project. Congrats!


## The One Thing I Love About PHPStan

One last thing. You may have noticed the `level` option. What's that for? PHPStan has now 5 levels (in time of writing this article) - **1 = least strict, 5 = the most strict**.

This allowed me to **put PHPStan to action without any huge work**. I start with level 1, 12 errors were found and fixed them.

Next week, when I'm rested and full of joy, I can go to level 2, fix another 8 errors.

I love this approach over traditional overwhelming "50 errors found. Fix them all or CI will keep failing.". That usually leads to removing the tool and to very long fixing process. I remember my long night hours with Scrutinizer just to get from code quality from 6 to 10.


### Try It Out...

...on your open-source or local projects and let me know how you like it.

Happy coding!



