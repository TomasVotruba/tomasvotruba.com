---
id: 999
title: "Programmer's Guide to Legacy Upgrade"
perex: |
    We execute several challenging PHP upgrades each year as the Rector team. By 'challenging,' I refer to projects untouched or outdated for the last 10–15 years. Imagine relying on coal instead of central heating during winter—such is the nature of these projects.
---

Many companies cannot allocate a budget to hire an upgrade service like ours. However, they might hire a new developer who feels frustrated with the outdated codebase but remains motivated to modernize it. I want to share our process, tips, and tricks with you, the PHP-Arch reader, to inspire you to use our approach as a foundational checklist.

We validate and iterate our approach with every single upgrade. As kaizen does in factories, we eliminate friction and blind paths step by step. If we spend a long time on daunting manual work, we should take time, energy, and patience to automate it. This way, the next project facing the same challenge will be a piece of cake.

That's how the same upgrade path becomes easier and more manageable.

But it doesn't mean every upgrade is the same, and we have tedious work without challenges. Every project is a legacy for a different reason.

It's similar to a healthy lifestyle. If you stay up late one night a month and go to sleep at 3 AM, you'll be OK. But if we do it every week, have day and night shifts, drink 3 beers a day, smoke and only stay at home alone all week, our body will probably not be in the best shape.

That's why we expect the unexpected and allocate reserve for problems we've never faced.


<br>


I'll take you step by step through the process. How do we think about project upgrades, how do we prepare, how do we execute. How do we ensure we deliver service to our customer on conditions we agreed on?

There will be two parts - one on an emotional, human level, the other on a rational, technical level. Both are important, the same way we cannot live without irrational emotions or rational thinking.

# A. The Human part


## 1. Build Trust

I've just hinted at something essential &ndash; a tiny emotion that grows in time can affect the upgrade result—the feeling of trust. Whether we're  upgrading for our boss or an external client, we build our mutual trust. And keep working on it during the upgrade process.

At first, we discuss the upgrade with the CTO. But we have to get on board the other two parties:

* the CEO or decision maker,
* the developer team

Why do all 3 parties have to be on board? One of 3 situations can happen (and we've experienced them all):

* the developers don't want the upgrade as it gives them control over the project; they'll slowly sabotage upgrade team efforts and will not help in fixing bugs - it's like 2 teams paddling on the same boat, just an opposite direction
* the CEO was forced to do this under threat of leaving by developers; the CEO doesn't see a point in wasting money on something that works and looks for new developers that don't need an upgrade - once he or she finds them, the upgrade will be stopped as an unnecessary expense
* the CTO has been in the company for many years and doesn't know how to work with new patterns and fresh code; the team of developers is young, and the CEO wants the company to grow, but the CTO is afraid of losing his years-long salary; he'll slowly discourage the upgrade team from further work and ill-spoken about the effort

That's why all 3 parties must be on board before the upgrade starts. Are all 3 parties motivated? Great.

<br>

## 2. Agree on Shared goals

Now we have all 3 parties understand the challenge of such an upgrade:

* why is the upgrade essential for the business to prosper in the longterm
* why is high code quality important for developers to stay in the company, work effectively, and deliver features quickly and with job
* how long the upgrade will take in months
* there will be challenges we cannot predict, and we're ready to handle them
* what is the upgrade scope - are we targeting PHP 8.0 or Symfony 6? do we need to switch old framework to Laravel?

The goal definition is important because adding new goals after the upgrade starts is like changing the plane's direction from the original destination to a much further country - will the fuel be enough?

## 3. Define Fosuced and Realistic Goal

It's OK to have a lesser goal at first, e.g., upgrade to PHP 8.2 and Symfony 5.4. We know these versions will be deprecated the moment we finish the upgrade. So why not go to "the most recent" versions instead?

It's easier to split huge tasks into 2, as we can put more focus and effort into finishing them. Instead of running a marathon during your first year of running, we can split the goal into running 10 km every month.

Then, once the first upgrade wave is finished and completely closed, we can open 2nd iteration to get to the latest PHP/framework. It will be more joyful work and a smaller budget allocation for the company.




## 4. Rule of thumb: Year Maximum

Based on our experience and experience of failed upgrades in companies I know of, the timeline must be at most 12 months. When the project upgrade crosses 12 months, it crosses 2 business periods. The year is a long period in software so we'll probably forget about our original investment into an upgrade and lose traction about the budget.

This will lead to extending the budget more and more, and the upgrade will slowly turn into a vortex for money.

We'll lose pressure and focus from both developers and businesses and the need for speed. That's why all real estate reconstructing projects have the starting and end dates publicly printed on paper in front of the structure (at least in our country).

Two project upgrades I know of crossed this period extended from 1,5 years to 4 years.
If your project upgrade definition crosses 12 months, trim of last couple of steps and get it under those 12 months. It doesn't matter if we do not accomplish all our plans. It means approaching the project with responsibility and caution.



## 5. Work on the Long Term Relationship


Some consultants and companies approach upgrades like a money drain project. They start to get as much money from the company as they can. Upgrade gets slowly into stale-mate, and they convince clients there is not much to do and they should stop. The final feelings of the client are sour, angry, and frustrated.

This is not a customer experience we want to see. Because the next time the client wants to upgrade, they'll be suspicious from the start and prefer to stay safely at the old code without losing money. It's nearly impossible to convince such a CEO to change their mind just because of one bad experience.


<blockquote>
What is the goal of the first date?

To get to a second date.
</blockquote>


Instead, we should aim at making this a regular event. Even yearly. Like family going home from the world's farthest corners to meet with everyone on Thanksgiving day.

There should be an "upgrade weekend" once a year. It can be during summer when most businesses have lower peaks because of vacations. E.g., every first week of July, we upgrade to the latest PHP and framework.

Why it's only a week when we talked about 12 months on the first run? Too good to be true?
The never the code is, the easier it is to upgrade it. Upgrading Rector from PHP 7.0 to 8.0 could have taken months because of all the type coverage those 2 versions differ from each other.

But going from PHP 8.0 to 8.3 is a matter of 20 mins. It's fully automated. There is no new type of coverage. Just class constants and those are filled based on constant value. Easy.

That's why we should aim at finishing the first upgrade and only then plan a regular date for the next one. Then, plan a regular date for the next one, and so on. They'll get cheaper and faster, and project value will grow. Developers love to work in an environment where code quality is not something extra or unique but a regular check-in.

In the same way, we have a yearly check of gas heaters, so we don't have any dangerous gas leaks and have formal verification the machine works as expected.


## 6. Prepare Ahead

The feeling of safety is one of the most powerful forces we can use to push through the hard times during an upgrade. If we have a builder we trust, we can leave him at home, give him keys, deposit, and go for a short holiday. We know he'll finish on budget, in time and we will not have to deal with unexpected surprises. He'll handle it as he handled it many times before. That's trust.

If we don't trust our builder, we will not leave him at home alone. We'll pay him after the work is done and after we carefully check that the job is done as expected.

How do we build trust from the start? We make thorough preparations.
We don't jump into a project upgrade like 1, 2, 3... Yes, we should start within a certain time range, but both sides should know what to expect. That's why we do a 2-3 weeks intro analysis of the project. We dive into the codebase:

* we scan it with an automated tool to discover weak spots we've already encountered in previous projects
* we look into the code to spot any antipatterns we haven't encountered yet, prepare for them, and inform our client

We create a plan step by step. What needs to be done first, what is 2nd phase, and how many weeks will these phases take? This plan has around 12-15 steps, based on starting shape of the project.

We present this plan to our client. We integrate the client's feedback and use the final version as a step-by-step battle plan.


## 7. Rule of thumb: Decide in 2 months

If the CEO, CTO, and employees agree on an upgrade, the company allocates the budget and requests an intro analysis from your upgrade team or service. We aim at fast intro analysis delivery (2-3 weeks after order), so we decide quickly, when there is a will to go.

We've met a client, who requested an upgrade when they had a will to go. But then there was a hesitation: what if it will take longer? What if we don't have enough money? What if we don't make it that far? We're still in "planning mode." it's been 2 years, and I can say there is no motivation to make this upgrade happen.

When you're ready to go, go. It's like buying a house. You've seen 10 houses, you like 2 of them, you agree with your wife to give an offer. But what if...? You give the offer too late, and the house is gone. Then you'll see 5 more houses, none of them as good as the first one. You regret moving too slowly and give up the effort until "the right house" reappears.

> It's never perfect; it's never the right time,
> but it needs to be done.

Go for it when you have a gut feeling to make the upgrade happen this year. Not next year, not 3 years later.

Make the final decision in two months from the first moment you have the CEO, CTO, and developer team on board and enough budget to go for it. The more descisive you'll be, the more seriously will the upgrade team and your team take you. The higher is chance, the whole upgrade will be a success.


## 8. Avoid IM; make Monthly Meetings instead

Upgrade requires a long-term focus on the topic. It takes time and deep thinking to figure out how to map Framework A to Framework B. The upgrade team should have undistracted continuous time to do their work.

**Avoid instant messaging, emailing, or even worse, Slack, where everyone has access and messages fly back and forth.**

In our experience setup of instant messaging leads to delays in delivery distractions on both the client's and the upgrade team's side.

If you need to decide something, take note and plan repeated monthly 30 mins meeting to discuss and decide.

If there is a more urgent decision to make, ask about the place where the code is. E.g., Github, Gitlab, or Bitbucket pull-request. Ask, decide, merge.


## 9. Run Upgrade Parallel to Business

I've mentioned the importance of *continuous long-period focus*. To make it work, the upgrade team cannot jump between business features and upgrades.

It's like reconstructing a house, one day replacing old dangerous electric cables, the next day picking up decoration pictures for the bedroom. Those are two different areas that require different skills and tuning.

Switching between those 2 topics will make the upgrade team exhausted, distracted, and prone to errors. Instead, ensure the upgrade team parallels your business features team. The in-house team must keep delivering business features during the upgrade. The company has no reason to stop growing because you've decided to improve your codebase.

Let feature developers keep delivering new business features; in parallel, let the upgrade team handle the upgrade. Both teams have their own separate responsibilities, goals, and flow. They only share a codebase.

This is also **safety net in case of disruptive change**. Let's say you decide to upgrade and put a full team on it. After 6 months, another pandemic comes and radically decreases your income. It doesn't have to be a pandemic:

* It can be a new funding with new board members, that will require certain KPI.
* Or your company will be acquired by another one, and you'll have to merge with another PHP project.
* The investor may not deliver the promised funding because of external issues or legislative errors.

You'd have to stop the upgrade and shift the whole back to the feature delivery. But it will take time and focus to adopt to different work and deal with unexpected disruption simultaneously. This *all-in* approach already destroyed a few companies I know of. These things happen, and it's better to be ready than to juggle with fireballs when a flood is coming.

Spread resources wisely and work on **both business features and upgrade in parallel**. Whether it's an external company like the Rector or 2 developers from your company, it doesn't matter.



## 10. Treat Pull-Request like a Standalone Finished Work

There is one more safety precaution that helps to build trust. It also allows me to be flexible when facing unexpected situations. Let's have an example of situations you've probably encountered. Which of these 2 cars with identical features would you rather buy:

* A. 150 000 $, pay everything in advance, use for at least 3 years before being able to sell
* B. 150 000 $, pay a 3 % monthly fee; if you pay for 3 years, the car is yours; cancel anytime with monthly notice

Consider the situation where you have a mortgage, wife, children, and other monthly payments. I'd go for option B to have my options open and keep the stakes low. We'll have a car in both situations, just more flexibility and finance with option B.

Our client should always be able to stop the upgrade with monthly notice. We should not force our client to "pay everything for a year as we agreed, or there will be no upgrade" situation.

Instead, we should treat every month and every request like a standalone micro contract. Every pull request should be in the form of finished work that instantly brings project value.

After every pull request, the company should be able to say, "We apologize, but we have some other priorities now and need to pause the upgrade". And it should be OK for us.

I'm not saying this helps to finish the upgrade, and it's common practice.

Quite the contrary, having this option motivates our client to stay with the upgrade. Not because they *have to*, but because they want to. **The motivation is not "because we've signed a contract" but "because we really want the upgrade to finish, so we want to grow further and faster next year".**

This puts the right amount of pressure on the upgrade team as well. Before creating a pull request, we have to consider the size and scope of every pull request. When we started to provide an upgrade as a service, we could open a PR that changed 3000 files and had 3 more as those depend on it to work correctly.

Then we learned it's easier for review and project independence to do much smaller PR with a single area of focus. That's why we've introduced [levels in Rector 1.0](https://getrector.com/blog/rector-1-0-is-here) to do the upgrade one step a a time.

Avoid massive *everything* pull requests that over-promise on delivery and rarely work.
Go for small, single-topic, focused, narrow improvement. Let me give you an example so it's easier to understand.

* Instead of "Symfony 4 upgrade," which might take weeks to create, finish, and deliver, pick a tiny fraction and get it merged the same day.
* It can be "upgrade Symfony 4 event dispatcher deprecated event class" or "prepare Symfony 4 controller container autowire".


## 12. Merge Within 1-2 days to stay in Delivery Flow

Now, we'll talk about the intensity of pull-request mergers. To be able to grow fast, we have to merge fast. A pull request like "Symfony 4 upgrade" will invoke resistance, fear of BC breaks, delay in merge, and constant rebates. Pull requests like "upgrade Symfony 4 event dispatcher deprecated event class" can be merged the moment it passes CI.

**Small PRs should be merged instantly, within 1-2 days max**. They have to pass CI; that's the minimum criteria. This way the upgrade team can go fast and stable at the same time. Within a week, it can be 50 pull requests that get us further—within a month, 200. Within a year... you got it.


<br>


Now that we've defined the overall strategy and approach to planning, communication, merge-request, and building trust; we'll shift our focus on the technical approach from our perspective - the upgrade team. We'll go technical, step by step. We'll talk about tools, the order we use them, the setup in CI we use, and the mutual influences these tools have on each other.

It will be fun.


# B. The Technical Part


In the intro analysis, we separate ~12 milestones into 2 groups:

* building foundations
* PHP + framework upgrade

The PHP + framework upgrade is about upgrading syntax, upgrading removed concepts to new solutions, and increasing package versions in the `require` section of `composer.json` to the highest available. That's how most people imagine a "project upgrade".

Yet, we have to start with building foundations. If we place our house into the sand, soon they will start to diverge. If we diverge a little, this will later become a large divergence.

**Building foundations** are steps that make our subsequent work 100 % reliable, smooth, and successful. What does it include?

* param and return type coverage in native PHP
* later property and constant type coverage
* unused class removal
* unused public methods removal
* narrow scope - final classes, private elements of public/protected if
* no magic that allows *any* type without possibility to strict-control it, e.g__get(), __call() methods

* no reflection calls that allow *any* type without strict-control
* PHP code over docblocks, strict array validation using `Assert` over vague array docblocks

<br>

These steps help to know for sure that:

* the property actually *is* of `EventDispatcher` type, and not an `object`
* this `private` method is never used outside and can be removed and not maintained anymore
* this param is `int`, but the method accepts `string` only, so we have to fix the type

How much time should we spend on building foundations?
Roughly 50 %.

This time is crucial; skipping it would result in upgrade failure. The Rector is designed not to create invalid code.

There are many cases where @return docblocks in frameworks have invalid types - they're never validated. That's why Rector only works if code is strictly typed, so we can trust its results.

They might seem like tiny steps, but make no mistake. The compound effect will result in a strong upgrade force. It's also more fun to start with the low-hanging fruit, which gives us confidence. In the same way, running 2 km a day is more fun to enjoy the process and eventually prepare for a marathon.

**The upgrade should be fun from start to end**. Not easy, not boring, but challanging fun. Only that way can we face challenges with a smile on our face.


Let's start.



## 1. The `.editorconfig` Metafile

First, we make sure the project has a tiny file called `.editorconfig`. In a gist, it helps our code keep the same spacing across various file types. It also helps IDE to load this configuration. Keep it simple: 4 spaces for all types.


## 2. Slim Fit CI

You're on an old ship without electricity or GPS in the middle of the sea. Dark night has come, and you know you'll be home soon. But where are you? You remember a lighthouse near the cliff near your home port. But where?

If you see the lighthouse light too late, your ship will crash. If it is fast but it will light only a narrow strip of the sea, your ship might crash the shore.

CI is like a lighthouse to guide you through the storm - it must be fast, clear, and precise to be useful.


First, we set up or make sure our CI runs as close to our platform as possible. Do we use GitHub? Use Github Actions. Bitbucket? Use Bitbucket Pipelines. Gitlab? Use Gitlab CI. We ensure we don't use external services like Coveralls, CircleCI, SonarCloud, etc. The further the feedback is, the more cloudy the lighthouse gets.

<br>

We use a few rules while working with CI. If your project has them, skip it. If you find inspiration to improve your CI, even better. Note: we use this approach to set simple jobs at the start:

* PHP process runs on bare PHP
* no Docker or otherwise complex setup
* no caching, back-and-forth loading
* no "cool" tricks
* re-use shared setup and use parallel runs for the different parts

Note: Why no Docker? Because we want to be lean and fast. We do not ship our project to production; we build a CI that is fast and easy to maintain. Our goal is to have feedback within 30 seconds or push commit. Adding Docker turns this from a no-brainer job to weeks of wasted resources.


## 3. Lint PHP in CI

Let's set up our first check-linter. It's important to know our code uses valid and existing syntax. Especially when we start to upgrade to newer PHP versions, where some syntax is removed. But also to validate our assumption our whole codebase uses valid PHP. It's not always the case.

Let's not touch `composer.json` yet to play it safe and add the package in CI directly:

```bash
run:
    - composer require --dev php-parallel-lint/php-parallel-lint
    - vendor/bin/parallel-linter lint src tests
```

That's it! Now we know the code in `/src` and `/tests` is valid within our PHP version.

## 4. Validate `composer.json` in CI

The next step is a simple command that helps us keep `composer.json` valid:

```bash
run:
    - composer validate
```

Fix the problems and let it run in CI. Now, we have composer feedback on every commit.


Do you find it easy? Let's add `--strict` to deal with the rest of the issues:

```bash
run:
    - composer validate --strict
```

## 5. Remove `--ignore-platform-reqs`


Composer requires PHP version 7.4, but CI runs on PHP 8.0. How is that possible?

Somewhere in our build process, we can find the magic `--ignore-platform-reqs` option of the composer. It says, "Install dependencies, but don't care about PHP version".

This is a hazardous security issue, as you can install a package for PHP 8.0 without knowing it. Once somebody manages to trigger such package code on your production, you have a fatal error.

We give this a priority and remove the `--ignore-platform-reqs` as soon as possible, to make `composer.json` responsible again.




## 6. Add ECS

Regardless existing coding standard tool, we always add ECS. It has parallel run, PHP syntax, and prepared sets beyond PSR-x easy to work with. We use the salami technique here... adding a single rule at a time, slowly raising the level:

```bash
composer require symplify/easy-coding-standard --dev

# creates an empty config
vendor/bin/ecs
```

Where to start? One of the great first-candidate rules is:

* `IndentationTypeFixer`

It will turn all tabs into spaces; yay!

```php
# ecs.php
use Symplify\EasyCodingStandard\Config\ECSConfig;

return ECSConfig::configure()
    ->withPaths([
        __DIR__ . '/src',
        __DIR__ . '/tests',
    ])
    ->withRules([
        \PhpCsFixer\Fixer\Whitespace\IndentationTypeFixer::class,
    ]);
```


Run the ECS:

```bash
vendor/bin/ecs
```

Commit the `ecs.php` setup and the applied changes. Create pull-request and merge. That's it!

<br>

### PHP 7.2 Ready

One of the other advantages of ECS is min requires a PHP version. It's downgraded to PHP 7.2, so you can install it on any PHP 7.2+ directly using `composer require`.

If you're on PHP 7.1 and below, simply flip it to the `create-project` approach:

```bash
composer create-project symplify/easy-coding-standard utils
utils/bin/ecs
```

That way, you can improve your coding standard before running a higher PHP version.


### Avoid running Coding Standards in the CI

We don't add the ECS to CI because it would only annoy us and slow us down. We don't want to be slaves of our coding standard. We want to apply all the rules enabled and add one by one.

This is really important, as once you turn the CI back on your team, they'll fight back to get free again.

### One Rule at a Time

Adding coding standard rules requires a bit of manual review from time to time. Try to stick with 1 rule = 1 PR approach, so there are **no blockers for merge and no rebases are needed**.

It might take some time, but you'll increase your code quality safely:

```diff
 use Symplify\EasyCodingStandard\Config\ECSConfig;

 return ECSConfig::configure()
     ->withPaths([
         __DIR__ . '/src',
         __DIR__ . '/tests',
     ])
     ->withRules([
         \PhpCsFixer\Fixer\Whitespace\IndentationTypeFixer::class,
+        \PhpCsFixer\Fixer\ClassNotation\ClassAttributesSeparationFixer,
    ]);
```

Avoid using whole sets, even though it might seem faster. In case of a bug, it takes a few hours to spot. If the bug reveals itself after a few days, pining down the changed line in 5000 files might be a nightmare.

It's not necessary to concentrate all energy on adding ECS. We add **10 new rules per week on average** to slowly increase code quality, but also enjoy other more creative work. If we change too many files, rebasing feature PRs can become a strong push against using ECS in the first place. So play it safe, play it fun.

## 7. Add PHPStan

PHPStan helps us spot invalid or buggy code right in CI. Let's add it:

```bash
composer require phpstan/phpstan --dev
```

First, we have to adapt the PHPStan level to the project. Let's add level zero:

```yaml
# phpstan.neon
parameters:
    level: 0

    paths:
        - src
        - tests
```

And run PHPStan:

```bash
vendor/bin/phpstan
```

How many errors do we see?

* 5 → let's fix them and commit
* 500 → fix 10-15 of them and commit

Create a request, let CI pass, and merge.

<br>

Once we reach `level: 0` without any errors, we can finally add PHPStan to CI:

```bash
script:
    - vendor/bin/phpstan
```

That way, it will cover our back and ensure our code quality is at least up to the mentioned level.

Or is it? Some projects use a similar workaround like `--ignore-platform-reqs`. PHPStan has a feature called baselines that dumps a list of ignored errors to make CI pass. It's the same logic as dumping a list of failing tests to make the test pass in CI. Forever.

This feature is useful to avoid making the codebase even worse. But it has no place for upgrades, because it gives us false feedback about code being OK, despite it's actually broken.

**The first thing we do is remove the baseline file. We want to work with honest feedback from PHPStan.** We don't want to hide any errors that will hunt us down later. We want to start with a transparent plate, even if it means going from level 4 (with thousands of ignored errors) to level 0 (with none)

<br>

### Step by Step

PHPStan is slightly different from ECS. Instead of a tool working for us, we have a tool that adds more work. The work is meaningful, but we can't do 100 manual fixes in a row. That's exhausting and strongly discouraging. That's why most projects we encounter reach level 4 or 5 but then burn out and never touch the PHPStan level again. It's a pity because they have the skill and will.

We don't fix a hundred errors per day, either. Instead, we use a technique called "level crawling":

1. First, we bump `phpstan.neon` to the next level

```diff
 parameters:
-   level: 0
+   level: 1
```

2. Then we fix 20 static errors

Commit, make CI pass.

3. If there are still some errors left, we go down to the previous level

```diff
 parameters:
-   level: 1
+   level: 0
```

4. Now we re-run PHPStan because our fixes might improve further code parts that PHPStan can now see and report.

5. We commit, make CI pass, and merge.


Repeat this process once a day to push code quality higher slowly. In a week, we have 100 fixed errors. In a month? 400.

We also juggle this fix with running ECS to relax from hard thinking and give our brains a bit of rest. It's perfect, because running ECS one rule at a time gives us peace of mind we need to be able to work on fixing PHPStan errors.


<br>

Note: PHPStan also uses PHP 7.2, so apply the same logic as during ECS installation.

## 8. Rector

Now that we have 2 tools in a mix let's add 3rd one that will work for us - Rector:

```bash
composer require rector/rector --dev

# creates config on the first run
vendor/bin/rector
```

At first, we'll align Rector with our PHP version.

Let's say we're using PHP 7.1. Can Rector help us without changing PHP 7.2? Changing the PHP version is quite a complex process because we have to upgrade servers or get another one, automate deployment, etc.

But PHP 7.1 in `composer.json`, is not good enough evidence that we're using PHP 7.1 syntax. We could be using PHP 5.3 syntax, which runs on PHP 7.1.

We can ask Rector to upgrade our code PHP 5.3, like the following:

```php
# rector.php
use Rector\Config\RectorConfig;

return RectorConfig::configure()
    ->withPaths([
        __DIR__ . '/src',
        __DIR__ . '/tests',
    ])
    ->withPhp53Sets();
```


We run Rector and check changed files. Does it look good? Let's commit `rector.php` and changed files, create pull-request and merge.

Now we can iterate:

```diff
 # rector.php
 use Rector\Config\RectorConfig;

 return RectorConfig::configure()
     ->withPaths([
         __DIR__ . '/src',
         __DIR__ . '/tests',
     ])
-    ->withPhp53Sets();
+    ->withPhp54Sets();
```

Run Rector, commit, PR, merge.

Switch with another task, like ECS and PHPStan, to ensure we're simultaneously pushing the bar. We repeat untill we reach our `composer.json` version.

### Add Rector to CI

To make sure our code is up-to-date, we add Rector to CI:

```bash
scripts:
    - vendor/bin/rector --dry-run
```

The `--dry-run` option works as a change-trigger. If the Rector finds a code change, it will crash the CI and let us know.

Eventually, Rector will work for us. This is just a warmup to get the tool into our daily workflow.

### Check-in

Let's recap: we have minimal CI running with all 3 essential tools - ECS, PHPStan, and Rector. We have repeated tasks on each of them to incrementally improve the codebase.


## The end-game

Every legacy project is different, but all should look the same in the end. We always aim at the state of the art in 2024 (= current year). What would a project you'll start today look like?

At the very beginning, we define a final vision of what kind of codebase we want to see in the end. For every single tool. I'll share the final configs of these 3 tools so that you can use them as a reference:

<br>

This is how the ECS config will look like:

```php
use Symplify\EasyCodingStandard\Config\ECSConfig;

return ECSConfig::configure()
    ->withPaths([__DIR__ . '/config', __DIR__ . '/src', __DIR__ . '/tests'])
    ->withRootFiles()
    ->withPreparedSets(psr12: true, common: true);
```

* all `common` sets on board, PSR-12 out of the box
* we include the source but also tests - so we can use Rector to upgrade tests and let ECS tidy them up

<br>

How about PHPStan?

```yaml
parameters:
    paths:
        - src
        - config
        - tests
        - rector.php
        - ecs.php

    level: 8
```

* we aim at level 8, the higher levels care about `mixed` types that are usually used on purpose
* we could go higher but end up with pointless ignores
* include `rector.php` and `ecs.php` to make sure they're valid PHP and are checked as well, e.g., for nonexisting rules or deprecated methods

<br>

Last but not least, Rector:

```php
use Rector\Config\RectorConfig;

return RectorConfig::configure()
    ->withPaths([__DIR__ . '/src', __DIR__ . '/config', __DIR__ . '/tests'])
    ->withImportNames()
    ->withPreparedSets(
        codeQuality: true,
        codingStyle: true,
        naming: true,
        privatization: true,
        typeDeclarations: true,
        instanceOf: true
    )
    ->withPhpSets()
    ->withAttributesSets();
```

* `/config` directory is included as well, as we use PHP syntax in configs by now
* include PHP attributes to get the best out of PHP 8+
* the PHP sets are included based on your `composer.json` version = with any composer upgrade, Rector will catch up instantly
* make use of almost all prepared sets; add one by one
* handle import of FQN class names to tidy up after renamed/moved classes

<br>

These are the final shapes; we finish all the project upgrades with them. Yet, using only these tools would be like driving a Tesla only on highways. **There are a couple more gem tools** we use in every project.

## Stepping stones

During legacy project upgrades, investing in the long game is crucial. That's what brings real change into the legacy codebase:

> If you want to go fast, go alone
> If you want to go far, go together

We can run a sprint of 100 meters to show off in front of girls we've just met. Or we can run 4 miles weekly to get our bodies into shape for years.

Saying that ECS, PHPStan, and Rector combo are one-third of the tooling we use. We've found **the fun and interactive upgrades allow us to dive into deep work** and explore the dark mine of legacy have a higher chance of being finished and bringing value to the project.

Also, the first 3 tools focus on the code's syntax/logical structure. But we are interested in more aspects, like unused code, its type quality level, sealed classes, etc. Here are 5 more tools that we use in every project. **They're often tailored to do one job well**, and we find them easier to use and integrate into PHP projects.

## 1. Type Coverage

This PHPStan extension is a game changer on rising a type coverage. It adapts to the size of your project and your team's time budget.

```php
composer require tomasvotruba/type-coverage --dev
```

Do you know test coverage? We can measure how much code is covered by tests. Then, have a check in CI that measures all the test coverage, and if it goes below X you've defined upfront, the CI will crash.

Type coverage is the same. You define your minimal required type coverage for return, param, and property types. PHPStan will count all used types, all possible types. If it's below the defined threshold, the CI will crash:

```yaml
# phpstan.neon
parameters:
    type_coverage:
        return: 5
        param: 25
        property: 30
```

That way, you can increase your type coverage by 1 % at a time. Do 1 % percent a day; in 3 months, you're from 0 to 90 % type coverage. That's an amazing result.

<br>

## 2. Class leak

PHPStorm and other tools can warn about methods that might not be used. But what about whole classes?

```bash
composer require tomasvotruba/class-leak --dev
```

This is a standalone tool that will do simple maths:

* get all existing classes, interfaces, and traits
* get all class usages - in types, in method calls, and static calls
* subtract one from the other
* ORM/ODM entities and classes with serialized markers are skipped by default to play it safe

```bash
vendor/bin/class-leak check /src /tests
```

The goal of this tool is **to quickly provide a list of most suspected classes/interfaces/traits that we can safely remove**.

It presents unused traits first as more straightforward to remove. Then, classes with no parent class or interface and remaining unused classes.

This is a reporting tool; it will let you decide whether to skip it or remove it.

<br>

## 3. Unused Public

Let's say we eliminate all unused classes from our code base. There might be plenty of code that is still not used. The methods, properties, and constants.

```bash
composer require tomasvotruba/unused-public --dev
```

This is a PHPStan extension that works in a similar way as class-leak.

* it goes through the code and looks for used public methods
* then it finds all public methods
* it subtracts one from the other and reports a list of unused public methods

```bash
# phsptan.neon
parameters:
    unused_public:
        constants: true
        properties: true
        methods: true
```

This tool is efficient - it can even report public methods that are used only in tests but not in our code. In that case, we're testing code that's never been used. It's like adding traffic lights into the middle of the forest. In that case, we can safely remove the method and the pointless tests.

@todo Embed tweet or screenshot
https://x.com/VotrubaT/status/1863881193483067807

<br>

## 4. Finalize classes

PHPStan and Rector work with the current scope only. What does that mean? If we have a command that extends the `Command` class, we know it has a parent. But what about its children? If there are some children, adding a return type declaration to the public method might break logic in the child class.

This is where the Swiss Knife tool comes in:

```bash
composer require rector/swiss-knife --dev
```

This CLI tool goes through the code and builds the whole class family tree. From the youngest child classes to the oldest parent.

```bash
vendor/bin/swiss-knife finalize-classes /src /tests
```

If there are some classes without children, it will mark them `final`. This will increase PHPStan and Rector coverage, as now we can add any type declaration safely.

<br>

If you're into mocking and adding `final` would be a no-go, there is a smart [bypass-finals](https://github.com/dg/bypass-finals) package that helps you with mocking final classes safely.

<br>


## 5. Smoke tests used packages or missing dependencies

Have you ever upgraded from Doctrine 2 to Doctrine 3? In Doctrine 2, we would define `doctrine/orm` and get full packages of transitional dependencies. But after upgrading to Doctrine 3, we must explicitly name `doctrine/common`, `doctrine/annotation`, and so on.

```bash
composer require shipmonk/composer-dependency-analyser --dev
```

Composer dependency analyzer is a CLI tool that spots these problems:

* is the class used in `/src` but only mentioned in `require-dev`?
* is this class used in a dependency, that's gone now?
* is this class non-existent in our code? maybe we have the wrong namespace

```bash
vendor/bin/composer-dependency-analyser
```

It's fast and easy to use, and we immediately add it to every PHP project.

<br>

## 6. PHP-only configs

There are some MVC frameworks that prefer to define configuration in YAML files or ini files. Yet, they often offer alternative PHP syntax. Again, if we have YAML files, we only see strings. But if we have PHP files, we can run ECS, PHPStan, and Rector to get the best out of it.

That's why we switch to PHP configs as soon as possible. PHPStan will warn us about deprecated methods, Rector configs as slim as possible and more.

If you're on Symfony, this would be a nightmare to work. Some projects have 50-100 such configs. Any mistake in the space of the indent could make the project crash.

We got you covered:

```bash
composer require symplify/config-transformer --dev
```

This is a CLI tool that will convert all YAML configs to PHP. It will keep the same structure but ensure it's valid PHP. This step is one of the most powerful upgrades any Symfony project can get.

<br>

## 7. Lint everything you can

Last but not least: lint, lint, lint!

Linting can be seen as a low-level operation run on the worst codebases. But that's the one we have, right? This perception is as false as closing eyes on driving a car in the city with eyes closed just because "we're a great driver who needs lights".

This could not be further from the truth. We're great drivers because we know how the system works, where is safe spaces, when it is green, and how to react to red. We're great drivers because we know and follow the rules.

The same goes for linting. It's easy to plug & play single-line CLI command that give us instant feedback about one specific area of our codebase. If it passes, we don't have to ever worry about it again and can focus on more complex problems.

Here is a list of such linters that we add to every project.

We can lint our framework's container:

```bash
bin/console debug:router
php/console cache:warmup
```

We can lint database mapping (annotations and entity relationships), Doctrine fixtures loading, etc.:

```bash
bin/console doctrine:schema:validate --env=ci --skip-sync
```

Linting of non-PHP formats is very important, as we have no other way to check they're valid:

```bash
bin/console lint:yaml app/config src --ansi

bin/console lint:twig src --ansi
```

We can also write our custom linter to check an area specific to our company:

```bash
php bin/detect-missing-translations.php
```

<br>

You can find more tools on [tomasvotruba.com/tools](https://tomasvotruba.com/tools), including *Type Perfect* and other cool commands of *Swiss Knife*. I also share the first step to use them and explain why they're important in more detail.

<br>

## Becoming Master of Upgrade

The tools above are available to anyone who can install the composer package. Their true power is not in the tools themselves but in how we use them. Combine them, put them in proper order, and use them step by step.

> If you improve your project by just 1 % every day,
> It will make your project 3700 % better in a year.

(@todo source: https://www.ricklindquist.com/blog/my-two-favorite-math-equations)

Use the tools, improve your project, and harden your CI. Your project is specific. You can create custom linters for it, you'll create custom PHPStan rules for it, and custom Rector rules to transition old patterns into 1 000 files in a matter of seconds.

Now reconstruct the legacy codebase to code full of joy, 100 % type coverage, and 0 % dead code.

<br>

Thank you for reading and...

Happy coding!
