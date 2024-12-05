---
id: 999
title: "Programmer's Guide to Legacy Upgrade"
perex: |
    We're doing couple hard PHP upgrades a year with Rector team. By "hard" I mean projects that were not renovated for past 10-15 years. Imagine you'd not use a central heating, but a coal in winter - those types of projects.
---

Many companies can't afford to set budget aside to hire an upgrade company like us. Yet they might hire a new developer with strong frustration of old codebase, but also strong need to stay in the company and do the upgrade. I want to share our process, tricks and tips with you, reader of PHP-Arch, so you might inspired in our approach as a checklist to build upon.

We validate and iterate our approach with every single upgrade. Like kaizen does in factories, we eliminate frictions and blind paths step by step. If we spend a long time on a daunting manual work, that will be repeated in next project, we take time, energy and patience to automate it. This way the next project facing the same challange will be piece of cake.

That's how the same upgrade path becomes easier and easier.
But it doesn't mean every upgrade is the same, and we have boring work without challanges. Every project is legacy for different reason, or rather group of reasons. It's similar to healthy lifestyle. If you stay up late one night a month and go sleep at 3 AM, you'll be probably fine. But if we do it every week, have day and night shifts, drink 3 beers a day, smoke and only stay at home alone all week, our body will probably not be in the best shape.

That's why we expect the unexpected, and allocate reserve for problems we've never faced before.


<br>


I'll take you step by step by our process. How do we think about project upgrades, how do we prepare, how do we execute and mainly, how do make sure we delivery service to our customer on conditions we agreed on.

There will be 2 parts - one in emotional level, other in rational level. Both are important, the same way we have cannot live without irational emotations nor without rational thinking.

# A. The Human part


## 1. Build Trust

I've just hinted something very important &ndash; a tiny emotion that grows in time can affect the result of the upgrade. Feeling of trust. Whether we're doing upgrade for our boss or for an external client, we build our mutual trust. And keep working on it during the upgrade process.

At first, we discuss the upgrade with CTO. But we have to get on board other 2 parties:

* the CEO or descission maker,
* the developers team

Why all 3 parties have to be on board? One of 3 situations can happen (and we've experienced them all):

* the developers don't want the upgrade as it gives them control over project, they'll slowly sabotage upgrade team efforts and will not help in fixing bugs - it's like 2 teams paddling on same boat, just oppositve diration
* the CEO was forced to do this under threat of leave by developer; CEO doesn't see point in wasting money in something that works and looks for new developers that don't need an upgrade - once he or she find them, the upgrade will be stopped as unnecesary expense
* the CTO is in the company for many years, and doesn't know how to work with new patterns and fresh code; the team of developers is young and CEO wants the company to grow, but CTO is affraid of losing his many years-long sallary; he'll slowly discourage the upgrade team from further work and illspokes about effort

That's why all 3 parties have to be on board before the upgrade starts. Are all 3 parties motivated? Great.

<br>

## 2. Agree on Shared goals

Now we have all 3 parties understand the challange of such an upgrade:

* why is the upgrade is important for the business to prosper in the longterm
* why is the high code quality important for developers to stay in the company, work effectively and delivery features quickly and with job
* how long the upgrade will take, in months
* what is the scope of upgrade - are we targeting PHP 8.0 or Symfony 6? do we need to switch old framework to Laravel? the goal are important, because adding new goals after the upgrade starts is like changing plane's direction from original destination to much further country - will the fuel be enough?
* there will be challanges we cannot predict, and we're ready to handle them
ě



## 3. Define Fosuced and Realistic Goal

It's ok to have lesser goal at first, e.g. upgrade to PHP 8.2 and Symfony 5.4. We now these versions will be deprecated the moment we finish the upgrade. So why not go to "the most recent" versions instead?

It's easier to split huge task into 2, as we can put more focus and effort to finish them. Instead of running an marathong your first year of running, we can split the goal into running 10 km every month.

Then, once the first wave of upgrade is finished and completely closed, we can open 2nd iteration to get the to the latest PHP/framework. It will be more joyful work, and also smaller budget allocation for the company.




## 4. Rule of a thumb: Year Maximum

Based on our experience and experience of failed upgrades in companies I know of, the timeline cannot cross 12 months. When the project upgrade cross 12 months, it crosses 2 business periods. Year is a such a long period in software, that we'll probably forget about our original investment into an upgrade and loose traction about budget. This will lead to extending budget more and more and upgrade will slowly turn into malestrom for money.

It will lose pressure, focus by both developers and business and need for speed. That's why all real estate reconstructing projects have starting date and end date publicly printed on piece of papter in front of the structure (at least in our country).

Two project upgrades I know of that crossed this period extended from 1,5 year to 4 years.
If your project upgrade definition cross 12 months, trim of last couple steps, get it under those 12 months. It doesn't matter we will not accomplish all our plans. It means approach serious project with responsibility and caution.



## 5. Work on the Long Term Relationship


Some consultants and companies approach upgrade like a money drain project. They start, get as much money from the company they can. Upgrade gets slowly into stale-mate and they convince client there is not much to do and they should stop. The final feeling of client is sour, anger and frustration.

This is not a good I want to see. Because next time the client will want to upgrade, they'll be suspicious from the starts and will prefer to safely stay at the old code, and without loosing any money. It's nearly impossible to convince such a CEO to change its mind, just because of one bad experience.


<blockquote>
What is goal of the first date?

To get to a second date.
</blockquote>


Instead, we should aim at making this regular event. Even yearly. Like family going home from the farest corners of the worlds to meet with everyone on Thanksgiving day.

There should be an "upgrade weekend" once a year. It can be during summer when most business have lower peaks because of vacations. E.g. every first week of July, we do the upgrade to the latest PHP and framework.

Why it's only a week, when we talked about 12 months one the first run? Too good to be true?
The never the code is, the easier is to upgrade it. Upgrading Rector from PHP 7.0 to 8.0 could have taken months, because of all the type coverage those 2 versions differ from each other.

But going from PHP 8.0 to 8.3 is a matter of 20 mins. It's fully automated. There is no new type coverage. Just class constants and those are filled based on constant value. Easy.

That's why we should aim at finishing first upgrade, then plan regular date for next one. Then plan regular date for next one and so on. They'll get cheaper and faster and project value will grow. Developers love to work in an environment, where code quality is not something extra or special, but rather regular check in.

The same way we have a yearly check of gas heaters, so we don't have any dangerous gas leaks and have formal verification the machine works as expected.


## 6. Prepare Ahead

The feeling of safety is one of the most powerful forces we can use to push through the hard time during an upgrade. If we have a builder we trust, we can leave him at home, give him keys, deposit, and go for a short holiday. We know he'll finish on budget, in time and we will not have to deal with unexpected surprises. He'll handle it as he handled many times before. That's trust.

If we don't trust our builder, we will not leave him at home alone. We'll pay him after the work is done and after we carefully check the job is done as we expected.

How do we build trust from the start? We make thorough preparation.
We don't jump into project upgrade like 1, 2, 3... Yes, we should start within a certain time range, but both sides should know what to expect. That's why do a 2-3 weeks intro analysis of the project. We dive into the codebase:

* we scan it with automated tool to discovery weak spots we've already encoutered in previous projects
* we look into the code, to spot any antipatterns we didn't encounter yet, prepare for them and inform our client

We create a plan, step by step. What needs to be done first, what is 2nd phase, how many weeks will these phases take. This plan has around 12-15 steps, based on starting shape of the project.

We present this plan to our client. The we integrate client's feedback and use final version as step-by-step battle plan.


## 7. Rule of thumb: Decided in 2 months

If CEO, CTO and eveloeprs agree on upgrade, company allocates the budget, requested an intro analysis from your upgrade team or service. We aim at fast intro analysis delivery (2-3 weeks after order), so we decide quickly, when there is a will to go.

We've met a client, who requested an upgrade when they had a will to go. But then there was a hesitation, what if it will take longer, what if we don't heave enough money, what if we don't make it that far. We're still in "planning mode", it's been 2 yeas and I can say for certain there is no motivation to make this upgrade happen.

When you're ready to go, go. It's like with buying a house. You've seen 10 houses, you like 2 of them, you agree with your wife to give an offer. But what if...? You give the offer too late, and the house is gone. Then you'll see 5 more houses, none of them as good as the first one. You regret of moving to slow and decide to give up the effort untill "the right house" will show up again.

It's never perfect, it's never the right time, but it needs to be done. So when you have a gut feeling to make the upgrade happen this year, not next, not 3 years later, go for it.


Make final decisssion in 2 months from first moment you've CEO, CTO and developer team on board and enough budget to go for it. The more descisive you'll be, the more seriously will the upgrade team and your team take you. The higher is chance the whole upgrade will be a success.


## 8. Avoid IM, Make Monthly Meetings instead

Upgrade requires a long-term focus on topic. It takes time and deep thinking to figure out, how to map framework A to framework B. The upgrade team should have undistracted continuous time to do their work. Avoid instant messaging, emailing or even worse, Slack where everyone has access and messages fly back and forth. In our experience setup of instant messaging leads to delays in delivery, distrations on both client's and upgrade team's side.

If you need to decide something, take a note and plan repeated monthly 30-60 mins meeting to discuss and decide.

If there is a more urgent descission to make, ask in the place where code is. E.g Github, Gitlab or Bitbucket pull-request. Ask, decide, merge.


## 9. Run Upgrade Parallel to Business

I've mentioned importance of *continuous long-period focus*. To make it work, the upgrade team cannot be jumping between business features and upgrade. It's like doing reconstruction of house, one day replacing old dangerous electrict cabels, the next day picking up decoration pictures to bedroom. Those are 2 different areas that require different skills and tuning.

Switching between those 2 topics will make upgrade team exhausted, distracted and prone to errors. Instead, make sure the upgrade team is parallel to your business features team. The business features must be delivered during upgrade. There is no reason for company to stop growing, because you've decided to improve your codebase.

Let feature developers keep delivered new business features, in parallel, let the upgrade team handle the upgrade. Both teams have their own separated responsibilities, goals, and flow. They only share a codebase.

This is also **safety net in case of distruptive change**. Let's say you decide to upgrade and put full team on it. After 6 months, another pandemic comes and radically decreases your income. It doesn't have to be pandemic:

* It can be a new funding with new board members, that will require certain KPI.
* Or your company will be aquired by another one, and you'll have to merge with another PHP project.
* Mabye the company will not be delivered funding it was promised, because of some external issues or legaslavive eror.

You'd have to stop upgrade, and shift whole back to the feature delivery. But it will take time and focus to adopt to different work and deal with unexpected disruption at the same time. This *all-in* approach already destroyed few companies I know off. These things happen and it's better to be ready, than to juggle with fire balls when a flood is coming.

Spread resources wisely and work on **both business features and upgrade in parallel**. Whether it's external company like Rector or 2 developers from your company, it doesn't matter.



## 10. Treat Pull-Request like Standalone Finished Work

There is one more safety precaution that helps to build trust. It also allow to be flexible when  faicing unexpected situations. Let's have an example in situations you've probably encontered. Which of these 2 cars with identical features would you rather buy:

* A. 150 000 $, pay everything in advance, use for at least 3 years before being able to sell
* B. 150 000 $, pay 3 % monthly fee, if you pay for 3 years, car is yours; cancel anytime with monthly notice

Think about situation, where you have morgage, wife, children and other monthly payments. I'd go for opition B to have my options open and keep stakes low. I'll have car in both situations, just more flexibility and finance with option B.

Our client should be always able to stop the upgrade with monthly notice. We should not force our client to "pay everything for a year as we agreed, or there will be no upgrade" situation.

Instead, we should treat with everymonth, and every every pull-request like a standalone micro contract. Every pull-request should be finished work, that instantly brings project value. After every pull-request, the company should be able to say "we appologise, but we have some other priorities now and need to pause the upgrade". And it should be ok for us.

I'm not saying this helps to finish the upgrade and it's common practise. Quite the contrary, having this option motivates our client to stay with the upgrade. Not because they *have to*, but because they want to. **The motivation is not "because we've signed a contact", but "because we really want the upgrade to finish, so want grow furher and faster next year".**

This puts right amound of on the upgrade team as well. Before we create a pull-request, we have to think about size and scope of every pull-request. When we've started to provide upgrade as a service, we could open a PR that changed 3000 files and had 3 more like those depend on it to work propperly.

Then we've learned it's easier for review and for project independence to do much smaller PR with single erea of focus. That's why we've introduced [levels in Rector 1.0](@todo link), to do the upgrade one step a a time.

@todo 1 % a day is 370 % in a year

Avoid huge *everything* pull-requets that overpromise on delivery and hardly work.
Go for small, single-topic, focused, narrow improvement. Let me give you an example so it's easier to understand.

* Instead of "Symfony 4 upgrade" that might take weeks to create, finish and delivery, pick a tiny fraction and get it merged the same day.
* It can be "upgrade Symfony 4 event dispatcher deperecated event class", or "prepare Symfony 4 controller container autowire".


## 12. Merge Within 1-2 days to stay in Delivery Flow

This leads us to intensitify of pull-request mergers. To be able to grow fast, we have to merge fast. While pull-request like "Symfony 4 upgrade" will invoke hesistance, fear of BC breaks, delay in merge and constant rebases. Pull-request like "upgrade Symfony 4 event dispatcher deperecated event class" can be merged the moment is passes CI.

**Small PRs should be merged instantly, within 1-2 day max**. They have to pass CI, that's minimum criteria of course. This way the upgrade team can go fast and stable at the same time. Withing a week, it can be 50 pull-reuqest that get us further. Withing a month, 200. Withing a year... you got it.


<br>


Now that've defined overall strategy and approach to planning, communication, merge-reuqest and building trust, we'll shift our focus on technical approach from our perspective - the upgrade team. We'll go technical, step by step. We'll talk about tools, the order we use them in, the setup in CI we use and mutual influences these tools have at each other.

It will be fun.


# B. The Technical Part


In the intro analysis we separate ~12 milestones into 2 groups:

* building foundations
* PHP + framwork upgrade

The PHP + framework upgrade is about upgrading syntax, upgrading removed concepts to new solutions and increasing package versions in `require` section of `composer.json` to the highest available. That's how the most people imagine "project upgrade".

Yet, we have to start with building foundations. If we place our ideas into sand, soon they will start to diverge. If we diverge a little, this will later become a large divergence.

**Building foundations** are step that make our next work 100 % reliable, smooth and successful. What does it include?

* param and return type coverage in native PHP
* later property and constant type coverage
* unused classes removal
* unused public methods removal
* narrow scope - final classes, private elements of public/protected if
* no magic that allows *any* type without possibility to strict-control it, e.g__get(), __call() methods

* no reflection calls that allow *any* type without strict-control
* PHP code over docblocks, strict array validation using `Assert` over vague array docblocks

<br>

These steps helps to know for sure that:

* the property actually *is* of `EventDispatcher` type, and not an `object`
* this `private` method is never used outside and can be removed, and not maintained anymore
* this param is `int`, but method accpets `string` only, so we have to fix the type

How much time do we spend on building foundations? Rough estimate, 50

%. This time is very important, and skipping it would result in upgrade failure. Rector is designed not to create invalid code. There are many cases where even framework docblocks have bugs &ndash; they're never validated actually. That's why Rector only works if code is strictly typed, so we can trust its results.

They might seem as tiny steps, but make no mistake. Compound effect will result in strong upgrade force. It's also more fun to start with the low hanging fruit, that gives us confidence. The same way it's more fun to run 2 km a day, to enjoy the process and eventually, prepare for marathon.

**The upgrade should be fun from start to end**. Not easy, not boring, but challanging fun. Only that way we can face challanges with vity smile on our face.


Let's start.



## 1. The `.editorconfig` Metafile

First, we make sure project has tiny file called `.editorconfig`. In a gist it helps to our code to keep same spacing across various file types. It also helps IDE to load this configuration. Keep it simple, 4 spaces, for all types.


## 2. Slim Fit CI

You're on an old ship, without electricity nor GPS, in the middle of the sea. Dark night has come and you know you'll be home soon. But where are you? You remember there is light house near cliff close to your home port. But where?

If you see lighthouse light too late, your ship will crash. If it will be fast, but it will light only narrow strip on the sea, your ship might crash the shore.

CI is like lighthouse to guide you through storm - it must be fast, clear and precise to be useful.


First, we setup or make sure our CI runs as close to our platofmr as posisble. Do we use GitHub? Use Github Actions. Bitbucket? Use Bitbucket Pipelines. Gitlab? Use Gitlab CI. We make sure we don't use any external services like Coveralls, CircleCI, SonarCloud etc. The further the feedback is, the more cloudy the lighthouse gets.

<br>

We use few rules while working with CI. If your project has them, skip it. If you find inspiration to improve your CI, even better. Note: we use this approach to setup simple jobs at start:

* PHP process runs on bare PHP
* no Docker or otherwise complex setup
* no caching, back and forth loading
* no "cools" tricks
* re-use shared setup and use parallel runs for the different parts

Note: Why no Docker? Because we want to be lean and fast. We do not ship our project to production, we build a CI that is fast and easy to maintain. Our goal is to have feedback withing 30 seconds or pushin commit. Adding Docker turns this from no-brainer job to weeks of wasted resources.


## 3. Lint PHP in CI

Let's setup our first check - linter. It's important to know our code uses valid and existing syntax. Especially when we start to upgrade to newer PHP versions, where some syntax is removed. But also to validate our assumption our whole codebase uses valid PHP. It's not always the case.

Let's not touch `composer.json` yet to play it safe, and add the package in CI directly:

```bash
run:
    - composer require @todo/parallel-linter ---dev
    - vendor/bin/parallel-linter lint src tests
```

That's it! Now we know the code in `/src` and `/tests` is valid within our PHP version.


## 4. Validate `composer.json` in CI

Next step is simple command, that help us keep `composer.json` valid:

```bash
run:
    - composer validate
```

Fix the problems and let it run in CI. Now we have composer feedback on every commit.


Do you find it easy? Let's add `--strict` to deal with rest of issues:

```bash
run:
    - composer validate --strict
```

## 5. Remove `--ignore-platform-reqs`


Composer requires PHP version 7.4, but CI runs on PHP 8.0. How is that possible?

Somewhere in our build process we can find magic `--ignore-platform-reqs` option of composer. It basically says "install dependencies, but don't care about PHP version". This is very risky security issue, as you can install package for PHP 8.0 without knowing it. Once somebody manages to trigger such package code on your production, you have fatal error.

We give this a priority and remove the `--ignore-platform-reqs` as soon as possible, to make `composer.json` responsible again.




## 6. Add ECS

Regardless existing coding standard tool, we always add ECS. It has parallel run, PHP syntax and prepared sets beyond PSR-x easy to work with. We use salami technique here... adding single rule at a time, slowly rising the level:

```bash
composer require symplify/easy-coding-standard --dev

# creates empty config
vendor/bin/ecs
```

Where to start? One of great first candidate rules is:

* `IndentationTypeFixer`

It will turn all tabs to spaces, yay!

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

One of other advantages ECS is min require PHP version. It's downgraded to PHP 7.2, so you can install it on any PHP 7.2+ directly using `composer require`.

If you're on PHP 7.1 and bellow, simply flip it to `create-project` approach:

```bash
composer create-project symplify/easy-coding-standard utils
utils/bin/ecs
```

That way you can improve your coding standard before running higher PHP version.


### Avoid running coding standard to the CI

We don't add the ECS to CI, because it would only annoy us and slow us down. We don't want to be slaves of our coding standard. We want to apply all the rules enabled and add one by one.

This is really important, as once you turn the CI back on your team, they'll fight back to get free again.

### One rule at a Time

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

Avoid using whole sets, even though it might seem faster. In case of bug, it takes few hours to spot. If the bug will reveal itself after few days, it might be nightmare to pin down the changed line in 5000 files.

It's not necessary to concentrate all energy on adding ECS. We add **10 new rules per week on average**, to slowly increase code quality, but also enjoy other more create work. If we change too many files, rebasing of feature PRs can turn into strong push against using ECS in the first place. So play it safe, play it fun.

## 7. Add PHPStan

PHPStan helps us spot invalid or buggy code right in CI. Let's add it:

```bash
composer require phpstan/phpstan --dev
```

First, we have to adapt PHPStan level to the project. Let's add level zero:

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


How many errors we see?

* 5 → let's fix them and commit
* 500 → fix 10-15 of them and commit

Create pull-request, let CI pass and merge.

<br>

Once we reach `level: 0` without any errors, we can finally add PHPStan to CI:

```bash
script:
	- vendor/bin/phpstan
```

That way it will cover our back and make sure our code quality is at least up to mentioned level.

Or is it? Some projects use similar workaround like `--ignore-platform-reqs`. PHPStan has feature called baselines, that dumps list of ignores errors, to make CI pass. It's same logic like dumping list of failing tests, just to make the test pass in CI. Forever.

This feature is useful to avoid making codebase even worse. But it has no place for upgrades, because it gives us false feedback about code being OK, despite it's actually broken.

**First think we do, is removing basline file. We want to work with honest feedback from PHPStan.** We don't want to hide any errors, that will hunt us down later. We want to start with clear plate, even if it means going from level 4 (with thousands of ignored errors) to level 0 (with none)

<br>

### Step by Step

PHPStan is slightly different from ECS. Instead of tool working for us, we have a tool that adds us more work. The work is meaninful, but we can't do 100 manual fixes in row. That's exhausting and strongly discouraing. That's why most projects we encouter reach level 4 or 5, but then burnout and don't ever touch PHPStan level again. It's a pity, because they have the skill and will.

We don't fix hundred errors per day either. Instead we use technique called "level crawling":

1. First we bump `phpstan.neon` to next level

```diff
 parameters:
-	level: 0
+	level: 1
```

2. Then we fix 20 static errors

Commit, make CI pass.

3. If there are still some errors left, we go down to previous level

```diff
 parameters:
-	level: 1
+	level: 0
```

4. Now we re-run PHPStan, because our fixes might improve further code parts that PHPStan now can see and reports.

5. We commit, make CI pass and merge.


Now repeat this process once a day, to slowly push code quality higher. In a week you have 100 fixed errors, in a month it's 400. We also juggle this with running ECS, to relax from hard thinking and give our brain a bit of rest. It's perfect, because running ECS one rule at a time gives us peace of mind we need to be able to work on fixing PHPSzan errors.


<br>

Note: PHPStan uses also PHP 7.2, so apply the same logic as during ECS installation.

## 8. Rector

Now that we have 2 tools in a mix, let's add 3rd one that will work for us - Rector:

```bash
composer require rector/rector --dev

# creates config on first run
vendor/bin/rector
```

At first, we'll align Rector with our PHP version.

Let's say we're using PHP 7.1. Can Rector help us without changing PHP 7.2? Changing PHP version is quite complex process, because we have to upgrade servers or get another one, automate deploy somehow etc.

But PHP 7.1 in `composer.json`, is not good enough evidence that we're using PHP 7.1 syntax. We could be using PHP 5.3 syntax, that runs on PHP 7.1.

We can ask Rector to upgrade our code PHP 5.3, like following:

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

Switch with other task like ECS and PHPStan, to make sure we're pushing the bar simultaneously. We repeat untill we reach our `composer.json` version.

### Add Rector to CI

To make sure our code is up-to-date, we add Rector to CI:

```bash
scripts:
    - vendor/bin/rector --dry-run
```

The `--dry-run` option works as change-trigger. If Rector will find a code change, it will crash the CI and let us know.

Eventually, Rector will work for us. This is just a warmup to get the tool into our daily workflow.

### Check-in

Let's recap, we have minimal CI running, with all 3 important tool - ECS, PHPStan and Rector. We heave repeated task on each of them, to incrementally improve the codebase.


## The end-game

Every legacy project is different, but all should look the same in the end. We always aim at the state of the art in 2024 (= current year). How would a project you'll start today look like?

At the very beginning, we define a final vision of what kind of codebase we want to see in the end. For every single tool. I'll share the final configs of these 3 tools, so you can use them as a reference:

<br>

This how ECS config will look like:

```php
use Symplify\EasyCodingStandard\Config\ECSConfig;

return ECSConfig::configure()
    ->withPaths([__DIR__ . '/config', __DIR__ . '/src', __DIR__ . '/tests'])
    ->withRootFiles()
    ->withPreparedSets(psr12: true, common: true, strict: true);
```

* all `common` sets on board, PSR-12 out of the box
* the `strict` set is nice have
* we include the source, but also tests - so we can use Rector to upgrade tests and let ECS to tidy them up

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
* we could go higher, but end-up with pointless ignores
* include `rector.php` and `ecs.php` to make sure they're valid PHP and are checked as well, e.g. for non existing rules or deprecated methods

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
* include PHP attributes, to get the best out of PHP 8+
* the PHP sets are included based on your `composer.json` version = with any composer upgrade, Rector will catch up instantly
* make use of almost all prepared sets, add one by one
* handle import of FQN classes names, to tidy up after renamed/moved classes

<br>

This is the final shapes, we finish all the project ugprades with. Yet, using only these tools, would like driving Tesla only on highways. **There are couple more gem tools** we use in every project.

## Stepping stones

During legacy project upgrades, it's crucial to invest in the long-game. That's what bring real change into legacy codebase:

> If you want to go fast, go alone
> if you want to go far, go together

We can run a sprint of 100 meters to show off in-front of girls we've just met. Or we can run 4 miles every week to get our body into shape for years to come.

Saying that, ECS, PHPStan and Rector combo is one thirds of the tooling we use. We've found **the upgrades that are fun and interactive, allow to dive into deep work** and explore the dark mine of legacy, have higher chance to be finished and bring value to the project.

Also, the first 3 tools focus on syntax/logical structure of code. But we are interested in more aspects, like unused code, it's type quality level, sealed classes etc. Here is 5 more tools that we use in every project. **They're often tailored to do one job well**, and we find them easier to use and integrate to PHP projects.

## 1. Type Coverage

This PHPStan extension is a game changer on rising a type coverage. It adapts to the size of your project and to the time-budget of your team.

```php
composer require tomasvotruba/type-coverage --dev
```

Do you know test coverage? You probably have a check in CI, that measures all the test coverage and if it goes bellow X you've defined upfront, the CI will crash.

Type coverage is the same. You define what's your minimal required type coverage for return, param and property types. PHPStan will count all used types, all possible types. If it's bellow defined treshold, the CI will crash:

```yaml
# phpstan.neon
parameters:
    type_coverage:
        return: 5
        param: 25
        property: 30
```

That way you can increase your type coverage 1 % at time. Do 1 % percent a day, and in 3 months you're from 0 to 90 % type coverage. That's amazing result.

<br>

## 2. Class leak

PHPStorm and other tools can warn about methods might not be used. But what about whole classes?

```bash
composer require tomasvotruba/class-leak --dev
```

This is a standalone tool, that will do simple maths:

* get all existing classes, interfaces and traits
* get all class usages - in types, in method calls and static calls
* subtract one from the other
* ORM/ODM entities and classes with serialized markers are skipped by default, to play it safe

```bash
vendor/bin/class-leak check /src /tests
```

The goal of this tool is **to quickly provide a list of most suspected classes/interfaces/traits that we can safely remove**.

It presents unused traits first, as easier to remove. Then followed by classes with no parent class or interface. Followed by rest of unused classes.

This is reporting tool, it will let you decide whether to skip it or remove it.

<br>

## 3. Unused Public

Let's say we eliminate all unused classes from our code base. There still might be plenty of code that is not used. The methods, properties and constants.

```bash
composer require tomasvotruba/unused-public --dev
```

This is a PHPStan extension that works in similar way as class-leak.

* it goes through the code and look for used public methods
* then it finds all public methods
* it subtracts one from the other and report list of unused public methods

```bash
# phsptan.neon
parameters:
    unused_public:
        constants: true
        properties: true
        methods: true
```

This tool is really practical - it can even report public methods, that are used only in tests, but not in our code. In that case, we're testing code that's never been used. It's like adding traffic lights into middle of the forest. In that case, we can safely remove the method and the pointless tests.

@todo Embed tweet or screenshot
https://x.com/VotrubaT/status/1863881193483067807

<br>

## 4. Finalize classes

PHPStan and Rector work with current scope only. What that means? If we have a command, that extends `Command` class, we know it has a parent. But what about its children? If there are some children, adding return type declaration to public method might break logic in child class.

This is where Swiss Knife tool comes in:

```bash
composer require rector/swiss-knife --dev
```

This is a CLI tool that goes through the code, builds the whole class family tree. From the youngest child classes to the oldest parent.

```bash
vendor/bin/swiss-knife finalize-classes /src /tests
```

If there are some classes without children, it will mark them `final`. This will increase PHPStan and Rector coverage, as now we can add any type declarations safely.

<br>

If you're into mocking and adding `final` would be a no go, there is a smart [bypass-finals](https://github.com/dg/bypass-finals) package that helps you with mocking final classes safely.

<br>


## 5. Smoke tests used packages or missing dependencies

Have you ever upgraded from Doctrine 2 to Doctrine 3? In Doctrine 2, we would define `doctrine/orm` and get full packages of transitional dependencies. But after upgrade to Doctrine 3, we have to explicitly name `doctrine/common`, `doctrine/annotation` and so on.

```bash
composer require shipmonk/composer-dependency-analyser --dev
```

Composer dependency analyser is a CLI tool that spots these problems:

* is class used in `/src` but only mentioned in `require-dev`?
* is this class used in a dependency, that's gone now?
* is this class non-existing in our code? maybe we have wrong namespace

```bash
vendor/bin/composer-dependency-analyser
```

It's fast, easy to use and we add it to every PHP project right from the start.

<br>

## 6. PHP-only configs

There are some MVC frameworks that prefer to define configuration in YAML files or ini files. Yet, the often offer alternative PHP syntax. Again, if we have YAML files, we only see strings. But if we have a PHP files, we can run ECS, PHPStan and Rector to get the best out of it.

That's why we switch to PHP configs as soon as possible. PHPStan will warn us about deprecated methods, Rector will configs as slim as possible and more.

If you're on Symfony, this would be a nightmare to work. Some projects have 50-100 such configs. Any mistake in space of indent could make project crash.

We got you covered:

```bash
composer require symplify/config-transformer --dev
```

This is a CLI tool that will convert all YAML configs to PHP. It will keep the same structure, but will make sure it's valid PHP. This step is one of the most powerful upgrade any Symfony project can get.

<br>

## 7. Lint everything you can

Last but not least: lint, lint, lint!

Linting is often seen as a low level operation run on the worst codebases there are. But that's the one we have, right? This perception is as false as closing eyes on driving a car in city with eyes closed, just because "we're a great driver; who needs lights".

This could not be further from the truth. We're great drivers, because we know how the system works, where is safe spaces, when is green and how to react to red. We're great drivers, because we know the rules and we follow them.

Same goes for linting. It's easy to plug & play single line CLI command, that give us instant feedback about one specific are of our codebase. If it passes, we don't have to ever worry about it again and can focus on more complex problems.

Here is list of such linters, that we add to every project.

Is our PHP syntax correct?

```bash
composer require --dev php-parallel-lint/php-parallel-lint
vendor/bin/parallel-lint app src test --colors
```

We can lint our framework's container:

```bash
bin/console debug:router
php/console cache:warmup
```

We can lint database mapping (annotations and entity relationships), Doctrine fixtures loading etc.:

```bash
bin/console doctrine:schema:validate --env=ci --skip-sync
```

Linting of non-PHP formats is very important, as we have no other way to check they're valid:

```bash
bin/console lint:yaml app/config src --ansi

bin/console lint:twig src --ansi
```

We can also write our custom linter, to check an area specific to our company:

```bash
php bin/detect-missing-translations.php
```

<br>

You can find more tools on [tomasvotruba.com/tools](https://tomasvotruba.com/tools), including *Type Perfect* and other cools commands of *Swiss Knife*. I also share first step to use them and explain why they're important in more details.

<br>

## Becoming Master of Upgrade

The tools above are available to anyone who can install composer package. The true power of them is not in the tools themselves, but in the way we use them. Combine them, put them in right order, use them step by step.

> If you improve your project just 1 % every day,
> it will make your project 3700 % better in a year.

(@todo source: https://www.ricklindquist.com/blog/my-two-favorite-math-equations)

Take the tools, use them, improve your project and harden your CI. Your project is specific. You can create custom linters for it, you'll create custom PHPStan rules for it and custom Rector rules to transition old pattern in 1 000 files in a matter of seconds.

Now go and reconstruct legacy codebase to code full of joy, 100 % type coverage and 0 % of dead code.

<br>

Thank you for reading and...

Happy coding!
