---
id: 230
title: "8 Steps You Can Make Before Huge Upgrade to Make it Faster, Cheaper and More Stable"
perex: |
    "How much will cost upgrade from Symfony 3 to Symfony 4 with Rector?"
    <br><br>
    Similar questions fill my personal and [Rector email](https://getrector.org/contact) in the last 3 months. It's hard to give a reasonable answer without actually seeing the repository, so I reply with follow-up questions to get get more details.
    <br><br>
    Yet, I've discovered there are few repeated patterns, that make the upgrade easier and that **most projects can do by themselves** before migration starts.

tweet: "New Post on #php 🐘 blog: 8 Steps You Can Make Before Huge Upgrade to Make it Faster, Cheaper and More Stable"
tweet_image: "/assets/images/posts/2019/before/time.png"
---

These points make any code migration faster and easier. It also **decreases the time required to understand the code** by a person who sees the code for the very first time.

Based on my experience with 10+ legacy projects of size 100 k-800 k lines, these points can be applied generally.

## 1. PSR-4 Standard

What are the benefits of using [PSR-4](https://www.php-fig.org/psr/psr-4) standard? If you use it, all classes are **unique**, **autoloaded** and **easy to relate to file path**. We need that for effective coding - so we don't have to care about it - and thus also for effective migrations.

If you have PSR-4 standard applied, your `composer.json` looks like this:

```json
{
    "autoload": {
        "psr-4": {
            "App\\": "src"
        }
    },
    "autoload-dev": {
        "psr-4": {
            "App\\Tests\\": "tests"
        }
    }
}
```

- No `classmap`, no `files`
- No [Nette\RobotLoader](/blog/2020/06/08/drop-robot-loader-and-let-composer-deal-with-autoloading/) magic autoloading
- No [extra autoloading](https://stackoverflow.com/a/31847204/1348344) for PHPUnit
- No [PHPUnit magic autoloading](https://github.com/sebastianbergmann/phpunit/blob/c27ac794f809a73bb04bcd4cdd0c33f3265921a4/src/Runner/StandardTestSuiteLoader.php#L39)

If you meet all these conditions... wait, you need to make sure, there is also no code like:

```php
class Some_Fake_Namespace_Class
{
}
```

...nor manual file requirements...

```php
require __DIR__ . '/libs/SomeFramework/File.php';
```

...nor [multiple classes in one file](https://github.com/rectorphp/rector/blob/master/docs/rector_rules_overview.md#multipleclassfiletopsr4classesrector)...

```php
class SomeException extends Exception
{
}

class SomeOtherException extends Exception
{
}
```

...nor incorrect namespace/class case...

```php
# app/lowercased/SomeController.php
namespace App\Lowercased;

class Somecontroller
{
}
```

Should be:

```diff
-app/lowercased/SomeController.php
+app/Lowercased/SomeController.php
```

```diff
-class Somecontroller
+class SomeController
```

<blockquote class="blockquote text-center mt-5 mb-3">
    If you meet all this conditions,<br>
    the migration is <strong>~20 % cheaper</strong>.
</blockquote>

<div class="text-center">
    <em class="fas fa-4x fa-check text-success margin-auto"></em>
</div>

## 2. Explicit PHP Version

What? Every project has a PHP version... that's obvious for many projects, but there is still that can go wrong. How?

```json
{
    "require": {
        "php": "^7.1"
    },
    "config": {
        "platform": {
            "php": "7.2"
        }
    }
}
```

So... which is it?

```json
{
    "require": {
        "php": "7.1"
    }
}
```

Is that locked for PHP 7.1 for some reason... is it though?


```json
{
    "require": {
        "php": "^5.6|^7.2"
    }
}
```

2 major versions... is that an open-source? Btw, you should not be at PHP 5.6 at all, [it's dead](https://www.php.net/supported-versions.php).

```json
{
    "require": {
    }
}
```

Ups! Make some up your mind. It's gonna be so weird to read this: it's the most common situation.

Is it in the Docker? No way! Docker is not version control. It only runs what you allow it to. Are you sure it handles PHP 4?

<blockquote class="blockquote text-center mt-5 mb-3">
    If you meet one major PHP version,<br>
    the migration is <strong>~5 % cheaper</strong>.
</blockquote>

<div class="text-center">
    <em class="fas fa-4x fa-check text-success margin-auto"></em>
</div>

## 3. EasyCoding Standard with Basic Sets

Why are coding standards needed for the migration? The AST libraries that Rector uses aren't well-suited to make code look nice and consistent, so it's better to let coding standard tools do that.

The basic [ECS](https://github.com/symplify/easy-coding-standard) setup we use looks like:

```php
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Symplify\EasyCodingStandard\ValueObject\Option;use Symplify\EasyCodingStandard\ValueObject\Set\SetList;

return static function (ContainerConfigurator $containerConfigurator): void {
    $parameters = $containerConfigurator->parameters();
    $parameters->set(Option::SETS, [
        SetList::PSR_12,
        SetList::CLEAN_CODE,
        SetList::COMMENTS,
        // very nice to have ↓
        SetList::SYMPLIFY,
    ]);
};
```

Run it:

```bash
composer require symplify/easy-coding-standard --dev
vendor/bin/ecs check src --fix
```

<blockquote class="blockquote text-center mt-5 mb-3">
    If you meet all the basic coding standard sets,<br>
    the migration is <strong>~5 % cheaper</strong>.
</blockquote>

<div class="text-center">
    <em class="fas fa-4x fa-check text-success margin-auto"></em>
</div>

## 4. PHPStan on Level 8

Coding style is one of *smoke testing* layers. It means it runs all over your code, without being explicitly told to. From that, the static analysis is just one step away.

```bash
composer require phpstan/phpstan
vendor/bin/phpstan analyse src --level 0
```

It's better to start small, then go high (like with any other drugs):

```bash
vendor/bin/phpstan analyse src --level 1
vendor/bin/phpstan analyse src --level 2
...
vendor/bin/phpstan analyse src --level 8
```

There are many Rector rules, [that help you with rules jumping](https://github.com/rectorphp/rector/blob/master/docs/rector_rules_overview.md#addclosurereturntyperector).

<blockquote class="blockquote text-center mt-5 mb-3">
    If you make PHPStan to level 8 and passing,<br>
    the migration is <strong>~15 % cheaper</strong>.
</blockquote>

<div class="text-center">
    <em class="fas fa-4x fa-check text-success margin-auto"></em>
</div>

## 5. Newbie Composer Install Under 2 Hours

This is how we usually run a good-quality project:

```bash
git clone your-project.git

# install backend dependencies
composer install

# install frontend dependencies
npm install
```

- create local database
- create `.env.local` with local database credentials

```bash
# run database migration
bin/console doctrine:migrations:migrate

# run Symfony 5 project
php -S localhost:8000 -t public
```

That's it. It takes 15-30 minutes to run [pehapkari.cz](https://github.com/pehapkari/pehapkari.cz) project locally, if you see it first.

<br>

The projects we meet are often hosted on Bitbucket, Github or Gitlab. Someone needs to add your SSH key there.

**If it takes more than a day, something is wrong**:

- Sometimes there is [Satis](https://getcomposer.org/doc/articles/handling-private-packages-with-satis.md) for handling private packages.

- Sometimes there is an SVN.

One project took me 2 weeks to ask for SSH keys 3 different people by 7 mails, 4 callings, one VPN... I still can't run `composer install`.

**Flawless install under 2 hours is a luxury.**

<blockquote class="blockquote text-center mt-5 mb-3">
    If you make <code>composer install</code> under 2 hours,<br>
    the migration is <strong>~5 % cheaper</strong>.
</blockquote>

<div class="text-center">
    <em class="fas fa-4x fa-check text-success margin-auto"></em>
</div>

## 6. 70 % Code Coverage

When we come to a completely new project, **we need instant feedback, if we break something**. It would be nice to have 100 % code coverage, but even my open-source project rates as high as 75 %.

**It's like having CTO who rose the project constantly at your side for any change you make.**

We don't care if it's functional, integration or unit tests - we just need the coverage to be sure nothing is wrong with the code. Without tests, **any change in the code is like shooting blindfolded in the dark without hands at a target that is both invisible, moving and Shrodinger's cat**.

On the other hand, if you have a code coverage over 80 % percent, even change of [the framework can be as fast as 80 hours](/blog/2019/08/26/how-we-migrated-54-357-lines-of-code-nette-to-symfony-in-2-people-under-80-hours/).

<blockquote class="blockquote text-center mt-5 mb-3">
    If you make it pass ~70 % code coverage,<br>
    the migration is <strong>~50 % cheaper</strong>.
</blockquote>

<div class="text-center">
    <em class="fas fa-4x fa-check text-success margin-auto"></em>
</div>

## 7. Not Versioned Vendor

I know it sounds crazy again, but it's not. Many projects we get have 2 `vendor` directories. One is versioned by `composer.json` and the other is versioned... *somehow*.

Why use [composer patches](https://github.com/cweagans/composer-patches) or custom forks, if you can version packages locally. It's fast, it's healthy, it's all you wish for.

But getting packages out of *local vendor* is the real adventure. We need to compare every file in both directories, ~~discover~~ guess the version, ~~test~~ hope it's the right one, prevent from duplications with *real vendor* and so on.

<blockquote class="blockquote text-center mt-5 mb-3">
    If you don't version your <code>vendor</code>,<br>
    the migration is <strong>~10 % cheaper</strong>.
</blockquote>

<div class="text-center">
    <em class="fas fa-4x fa-check text-success margin-auto"></em>
</div>


## 8. Solid Gitlab CI

What if you have all the items above? When is the last time you've checked them? You don't know?

If the answer is not "at every commit", it's not good enough. You need to have CI. And I don't mean Bitbucket CI.

Why? It's not that Bitbucket CI is worse than Gitlab CI or GitHub actions. It's the ecosystem support. **The Gitlab CI has the longest support for CI of a private project there is.**

That means:

- a lot of tutorials
- a lot of people who know how to configure it
- a lot of custom Docker tutorials
- community support in case of troubles

As a side bonus, **it's free for private projects with unlimited users** and 2 000 build minutes per month (I've never reached that).

<blockquote class="blockquote text-center mt-5 mb-3">
    If you use Gitlab CI on every commit,<br>
    the migration is <strong>~10 % cheaper</strong>.
</blockquote>

<div class="text-center">
    <em class="fas fa-4x fa-check text-success margin-auto"></em>
</div>


## Worth It?

<img src="/assets/images/posts/2019/before/time.png" class="img-thumbnail">

Let's say your goal is to migrate the whole framework or switch a legacy framework for a modern one. If you had skills, time and money to do that, you'd probably be there. **It takes the experience with many legacy migrations to there effectively without years of time and full-rewrite.**

But... these steps above don't depend on such experience. You can implement in your in-house team. **Such work will reduce work on our side and make your code solid on your side with not such a big overhead**.

Just pick one and start slowly.

<br>

Happy coding!

<br>
