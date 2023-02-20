---
id: 229
title: "How to Get Rid of Technical Debt or What We Would Have Done Differently 2 Years ago"
perex: |
    We talked about cleaning legacy code with Rector 2 months ago on 40th meetup of PHP friends in Prague.
    <br>
    Who is *we*? Me and CTO of the [company I worked for](https://spaceflow.io/en), a great leader and technical expert who taught me a lot, [Milan Mimra](https://www.linkedin.com/in/milanmimra).

    The talk was not full of shallow tips, nor [about framework migration](/blog/2019/08/26/how-we-migrated-54-357-lines-of-code-nette-to-symfony-in-2-people-under-80-hours/). Instead, **we talked about small decisions that were made 2 years. Decisions, which took 3 months to get rid of**.

tweet: "New Post on #php 🐘 blog: How to Get Rid of Technical Debt or What We Would Have Done Differently 2 Years ago        #symfony #rector #ecs @doctrineproject #uuid #legacycode"
tweet_image: "/assets/images/posts/2019/spaceflow_10_points/07.png"

updated_since: "August 2020"
updated_message: |
    Updated Rector/ECS YAML to PHP configuration, as current standard.
---

**Do you speak Czech?** Go check [64 slides](https://docs.google.com/presentation/d/1QSpTVqmtXpE8RvB73cYDBrDYpuChjp_Uz3sLJhLnLXo/edit?usp=sharing) and [watch the talk video recording on Facebook](https://www.facebook.com/pehapkari/videos/vl.404478763568491/399224180756304/?type=1) (it is 60 minutes long and the picture is broken from ~27th minute, but the audio is good).

<a href="https://www.facebook.com/pehapkari/videos/vl.404478763568491/399224180756304/?type=1">
    <img src="/assets/images/posts/2019/spaceflow_10_points/main.png" class="img-thumbnail col-12 col-md-8">
</a>

<br>

**For all the rest of you who speak English better or don't want to copy-paste code instead of watching the video, I'm writing this post.** So you can learn from our pain and make your brand new mistakes :)


## The Size ~~Does~~ Doesn't Matter

This talk was about my work as *cleaning lady* in a project, that **wanted to improve their PHP codebase across the whole application**. Any PHP projects I recently consulted would benefit from these changes, so keep reading even if you already run on the latest version of your favorite framework.

Instant upgrades effectivity doesn't depend on project size, but people still want size numbers, so there you go:

- Symfony 4.* backend for mobile application
- 253 endpoints = routes
- 105 000 lines of code (bigger than [Symfony with ~90 000 lines](https://medium.com/@javiereguiluz/30-of-laravel-code-is-symfony-a49dcf30e809))

<br>

From all the work we've done, **we've picked 10 most important changes that brought code quality to another level**.


## 1. Advance Your Coding Standard

<img src="/assets/images/posts/2019/spaceflow_10_points/01.png" class="img-thumbnail col-12 col-md-8">

When people say "we use coding standard", it usually means "we use only PSR-2 but nothing more".

That's like saying "we use Symfony" when you're using only controllers.

### Why You Should use It?

- the best code improvements in exchange for massively improved code - for 10 minutes, life-time code check
- it's very easy to start using it if you don't use it yet
- further refactoring with Rector or analysis with PHPStorm or PHPStan is more robust

<img src="/assets/images/posts/2019/spaceflow_10_points/02.png" class="img-thumbnail col-12 col-md-8">

It's better to start slow, so the first things we did was:

- moving from string `"SomeClass"` to `SomeClass::class`
- coding standard enforced line-ending - not just sniff, but a fixer
- [finalized (almost) everything](/blog/2019/01/24/how-to-kill-parents/)
- accidental fatal error check of an empty array

<img src="/assets/images/posts/2019/spaceflow_10_points/04.png" class="img-thumbnail col-12 col-md-8">

### How to Apply?

Just run [ECS](https://github.com/symplify/easy-coding-standard) with following set:

```php
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Symplify\CodingStandard\Fixer\LineLength\LineLengthFixer;
use PhpCsFixer\Fixer\ClassNotation\FinalClassFixer;

return function (ContainerConfigurator $containerConfigurator): void {
    $services = $containerConfigurator->services();

    // keep line-length max. 120 chars long
    $services->set(LineLengthFixer::class)
        ->call('configure', [[
            'lineLength' => 120
        ]]);

    // every non-Entity non-abstract class must be final - you have to skip those that are used + have children, like this ↓
    $services->set(FinalClassFixer::class);
};
```

And the result?

<img src="/assets/images/posts/2019/spaceflow_10_points/03.png" class="img-thumbnail col-12 col-md-8">

## 2. Single Class = Single Place

<img src="/assets/images/posts/2019/spaceflow_10_points/05.png" class="img-thumbnail col-12 col-md-8">

Have you heard about PSR-4? Well, so did many people before you, but it's very to forget it thanks to:

- PHPUnit magic autoload - you can place your tests anywhere, PHPUnit will find them
- [Nette\RobotLoader](/blog/2020/06/08/drop-robot-loader-and-let-composer-deal-with-autoloading/) - load anything from anywhere, for free
- composer "classmap" - just load the directory, who cares
- DDD - Domain Driven Design - apparently, one of the most favorite principles is to put all files in one directory, templates, configs, services, objects... just put it there

<img src="/assets/images/posts/2019/spaceflow_10_points/06.png" class="img-thumbnail col-12 col-md-8">

This all helps a lot to messy applications. As basic as this rule seems, I must sadly say that **lack of PSR-4 is one of the biggest problems of legacy applications**.

### Forget Service Autodiscovery, Use Ze Handz!

To add more salt into the wound, now imagine **you want to use some sort of services autoregistration** (and you should, unless you want to kill your project slowly). That means you tell your dependency injection container "load these services from this directory".

One of such [implementaions is PSR-4 autodiscovery in Symfony](/blog/2017/05/07/how-to-refactor-to-new-dependency-injection-features-in-symfony-3-3/):

<a href="/blog/2017/05/07/how-to-refactor-to-new-dependency-injection-features-in-symfony-3-3/">
<img src="/assets/images/posts/2019/spaceflow_10_points/08.png" class="img-thumbnail col-12 col-md-8">
</a>

Such a simple yet powerful idea... which all the Symfony applications I've seen so far struggle. There Symfony 4 and 5 projects, where programmers still have to write each service manually.

<img src="/assets/images/posts/2019/spaceflow_10_points/07.png" class="img-thumbnail col-12 col-md-8">

I wonder, isn't there something better to do? Like **writing unique PHP code** that cannot be automated?

So that's what we did:

<img src="/assets/images/posts/2019/spaceflow_10_points/09.png" class="img-thumbnail col-12 col-md-8">



### How to Apply?

This one requires lof ot manual configuration tweaking of [Rector](https://github.com/rectorphp/rector) rules, but you can run basic migration with following set:

```php
use Rector\Autodiscovery\Rector\FileSystem\MoveServicesBySuffixToDirectoryRector;
use Rector\PSR4\Rector\Namespace_\NormalizeNamespaceByPSR4ComposerAutoloadRector;
use Rector\Config\RectorConfig;

return function (RectorConfig $rectorConfig): void {
    $rectorConfig->rule(MoveServicesBySuffixToDirectoryRector::class);

    // configure `composer.json` to desired way
    $rectorConfig->rule(NormalizeNamespaceByPSR4ComposerAutoloadRector::class);
};
```

## 3. Make Your CI Fast or Die Trying

<img src="/assets/images/posts/2019/spaceflow_10_points/10.png" class="img-thumbnail col-12 col-md-8">

This is rather a general tip than a specific migration.

**In the beginning we had**:

- slow database test
- Doctrine fixtures that were added on every integration test

Not only CI but also local testing was slow. That leads to **frustration** due to **loooooooooong feedback loop**. In reality, it means check test = having a coffee or go smoking. And you don't want to hurt your team intentionally like that, do you?

<br>

So after a lot of work **we had super fast CI that**:

- loaded database just once
- reference fixtures via IDs in constants instead of in-Database references
- checked coding standards in standalone job
- checked static analysis in standalone job
- checked Rector in standalone job

<img src="/assets/images/posts/2019/spaceflow_10_points/11.png" class="img-thumbnail col-12 col-md-8">

## 4. Remove What You Don't Use

<img src="/assets/images/posts/2019/spaceflow_10_points/12.png" class="img-thumbnail col-12 col-md-8">

I see you now thinking "we don't have any dead code, or PHPStorm would tell us". No, it wouldn't, at least no in level the static analysis can.

How do we know? Well, we were you in the start... "no, we don't have any dead code":

<img src="/assets/images/posts/2019/spaceflow_10_points/13.png" class="img-thumbnail col-12 col-md-8">

The moment you realize:

- the code is broken
- you have to fix it
- you have to maintain it
- oh, it's used in tests
- good
- wait...
- **it's used only in the test but nowhere else!**

<img src="/assets/images/posts/2019/spaceflow_10_points/14.png" class="img-thumbnail col-12 col-md-8">

God, don't do this manually! Automate ↓

```php
use Rector\DeadCode\Rector\Class_\RemoveUnusedDoctrineEntityMethodAndPropertyRector;
use Rector\Set\ValueObject\SetList;
use Rector\Config\RectorConfig;

return function (RectorConfig $rectorConfig): void {
    $rectorConfig->import(SetList::DEAD_CODE);

    $rectorConfig->rule(RemoveUnusedDoctrineEntityMethodAndPropertyRector::class);
    // + some extra private rules :)
};
```

**Soon, you'll be 10-20 % slimmer and you'll fit into your favorite bathing suite :)**

Also, you'll avoid skipping code-reviews:

<img src="/assets/images/posts/2019/spaceflow_10_points/15.png" class="img-thumbnail col-12 col-md-8">

## 5. Make Your Ground Base ~~Rock~~ Diamond SOLID

<img src="/assets/images/posts/2019/spaceflow_10_points/16.png" class="img-thumbnail col-12 col-md-8">

Have you ever seen code like this?

<img src="/assets/images/posts/2019/spaceflow_10_points/17.png" class="img-thumbnail col-12 col-md-8">

I'd never expect `string` turn into `null`, but it happened anyway. **Silently**. And not just from this function.

**We wanted this code to throw an exception and tell us what's wrong**. Right in the place where it happened, not just "string expect, null given" later.

There is a [Safe package](https://github.com/thecodingmachine/safe) that can partially protect you, but it's rather general and with a generic exception.

### Nette SOLID

But there is even better one - [Nette\Utils](https://github.com/nette/utils):

<img src="/assets/images/posts/2019/spaceflow_10_points/18.png" class="img-thumbnail col-12 col-md-8">

How to get it into your code? Easy:

```php
use Rector\Nette\Set\NetteSetList;
use Rector\Config\RectorConfig;

return function (RectorConfig $rectorConfig): void {
    $rectorConfig->import(NetteSetList::NETTE_UTILS_CODE_QUALITY);
};
```

## 6. Config SPAM

<img src="/assets/images/posts/2019/spaceflow_10_points/19.png" class="img-thumbnail col-12 col-md-8">

We already wrote that config suffers from manual services registration spam. But there is more...

<img src="/assets/images/posts/2019/spaceflow_10_points/20.png" class="img-thumbnail col-12 col-md-8">

<img src="/assets/images/posts/2019/spaceflow_10_points/21.png" class="img-thumbnail col-12 col-md-8">


This is **[famous Symfony tag spam](/blog/2018/11/12/will-autowired-arrays-finally-deprecate-tags-in-symfony-and-nette/)**.

- not needed
- overly promoted in documentation
- thus at the end used by everybody
- making application stupidly complex and hard to maintain and extend in the end

Another typical issues is **[manual parameter spam](/blog/2018/11/05/do-you-autowire-services-in-symfony-you-can-autowire-parameters-too/)**.

### Add Spam Filter

We don't like spam, do you? So we dropped all this crap and used 2 compiler passes to make it KISS:

- [`AutowireArrayParameterCompilerPass`](https://github.com/symplify/package-builder#autowire-array-parameters)
- [`AutoBindParametersCompilerPass`](https://github.com/symplify/package-builder#autobind-parameters)

↓

<img src="/assets/images/posts/2019/spaceflow_10_points/22.png" class="img-thumbnail col-12 col-md-8">


## 7. Remove All the Copy-Paste Legacy

<img src="/assets/images/posts/2019/spaceflow_10_points/23.png" class="img-thumbnail col-12 col-md-8">

We all know how the story goes:

- "They did it"
- "I just maintain it"
- "It's faster to copy-paste it..."
- **"I don't have time to fix it now..."**
- 3 years later...
- "Oh my, what's this? How do we get out of it? **Rewrite?**"

In our case, it was json, written as a string:

<img src="/assets/images/posts/2019/spaceflow_10_points/24.png" class="img-thumbnail col-12 col-md-8">

It was hard to maintain, even read or change a value in that mess. We had many bugs just because of invalid quote concat, missed closing quote or new-line issues. **We needed to use a normal array like most people do**, but it was spread in over 70 use cases, some of them nesting array into 5 levels.

What now?

Easy, we made a Rector rule for that:

```php
use Rector\CodingStyle\Rector\String_\ManualJsonStringToJsonEncodeArrayRector;
use Rector\Config\RectorConfig;

return function (RectorConfig $rectorConfig): void {
    $rectorConfig->rule(ManualJsonStringToJsonEncodeArrayRector::class);
};
```

<img src="/assets/images/posts/2019/spaceflow_10_points/25.png" class="img-thumbnail col-12 col-md-8">

## 8. Know ALL Your Properties

<img src="/assets/images/posts/2019/spaceflow_10_points/26.png" class="img-thumbnail col-12 col-md-8">

I already wrote about this here: [How we Completed Thousands of Missing @var Annotations in a Day](/blog/2019/07/29/how-we-completed-thousands-of-missing-var-annotations-in-a-day/)

**This is super important to make instant refactoring reliable!**

## 9. Enter Anywhere?

<img src="/assets/images/posts/2019/spaceflow_10_points/27.png" class="img-thumbnail col-12 col-md-8">

I already wrote about it... twice:

- [Don't Ever use Symfony Listeners](/blog/2019/05/16/don-t-ever-use-listeners/)
- [How to Convert Listeners to Subscribers and Reduce your Configs](/blog/2019/07/22/how-to-convert-listeners-to-subscribers-and-reduce-your-configs/)

<img src="/assets/images/posts/2019/spaceflow_10_points/28.png" class="img-thumbnail col-12 col-md-8 mt-5">

**Clean, simple, PHP without config magic!**

## 10. Switch ID to UUID?

This was the biggest problem in the whole project. First, it used classic `int` ids. Then uuid was about to be used (for whatever good reasons), but for the lack of refactoring time only, new entities used it. So part of the old approach, part of the new approach. Sometimes one class uses one of those and another of those.

**Total mess, maintanece of 2 huge layers and negative effective on input/output layer**, because it had to be consistent. There cannot be a situation where user sees urls like:

```bash
/building/edit/1
/building/edit/b9a33908-56c8-431f-a159-e4bec344e56c
```

And not only the user but also external API, mobile applications, invoice systems, etc.

This was a real challenge for the whole team - it included database, Doctrine refactoring rules, external service unification refactoring and also changing PHP types all over the application.

<br>

## Benefits?

And that's all folks, all we made happen in 3 months work of... 1 person.
We wanted to show you, that all big changes don't have to be "whole-team-2-years-refactoring-super-expensive-no-features".

It can be **smooth, step by step, closed incremental iterations, done by one full-time person**.

Now the code is:

- 200 % more readable
- instead of 3 locations, the class now only has 1 location
- the database has 1 way to use ids instead of 2
- configs are 95 % smaller
- is 40 % safer thanks to exceptions of `null`/`false`
- most of the time the developer doesn't visit configs at all and can focus on PHP code of the feature instead

And moreover:

- **it has 80 % fewer anti-patterns, so any new developer will produce high-quality code by default, just by reading the already written code**

<br>

<blockquote class="blockquote text-center mt-5 mb-5">
    This is Inspiration and Proof, it Can Be Done. Now Yours is Choice to Make:
    <br>
    What Are You Going to Clean Tomorrow in Your Code Base?
</blockquote>
