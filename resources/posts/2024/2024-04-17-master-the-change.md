---
id: 406
title: "Master the Change"
perex: |
    When we upgrade a new project to the best version possible, the latest PHP and framework versions, it's not only about changing syntax sugar to a more fancy one.

    It's about the vast focus shift in project management so far. It's a change to master. I want to share the basic rules we apply to make the "impossible" upgrades successful and steady.
---

## How do legacy projects look like

The legacy project is rarely about having an old PHP version. It's usually the surrounding ecosystem that keeps the project having the old version, despite many efforts by the programmers to change it. The project lead or owner already invested usually around 1-2 years to change it, but **there are counter forces that keep the project unchanged**.

It's frustrating and only dig us deeper under the ground.

The same way we approach our health. Eating a burger once a month for lunch won't most likely affect our health. But if we are in the range of obesity, we won't get out of it by excluding burgers. We have to change our pre-sleep-eating habits, include daily exercise, change our social group that encourages such habits, and more.

## Feel the forces

The same way we approach legacy projects. We have to detect, what are the blocking forces that keep the project in the old version. It can be *learned helplessness*, a term that describes the situation when we try over 10 times and always get a negative response. Why should it work for the 11th time? We learned not to try.

It can also be a fear of the unknown. In some cases, it is the team leader who keeps us from moving. They want to keep the codebase the same because they're the ones who **bear complete know-how of the project in their head**. They're precious to the project, and they know it. What would happen if the project was in great shape and no longer depended on them? They may get fired as they are no longer needed.

## Measure everything...?

One attempt I saw to deal with legacy codebases in the wild is to "report all possible code metrics". This gives the impression that once we know how "much" of "X" the codebase has, we'll be able to address it. But in reality it has quite negative effect.

It's like trying to get your body into shape by hourly measuring your weight and recording fat/sugar/protein in every meal you eat and in every liquid you drink. This method gives you a lot of data that can lead to various conclusions. Yet, nothing changes unless you *actually change* the way you eat.

<br>

The same applies to codebases. Let's say our legacy project use services like Sonarcube, Scrutinizer, Healthchecks, and to report 500+ code quality metrics on every commit. The project has been collecting data for past 36 months.

I ask the project owner:

* "What is the conclusion from these data?"

They reply:

* "We are in a bad shape for the past 36 months and not improving".

That's usually pretty clear to the whole team even without these metrics. What is worse, that it takes focus and power from actually making the change.

<br>

## From joy to shame

Another way to distract team from change is to create huge PHPStan baseline files that record every possible static analysis violation. It's like having a personal trainer who tells you every day what you've done wrong since you hired them.

What is the effect of constantly reminding mistakes over celebrating success? It shifts our focus from "we made a change for better" to "this is every mistake we made", from joy to shame.

## Choice Paralysis

It's easier to pick from [3 ice cream flavors, but quite impossible from 25](https://medium.com/age-of-awareness/heres-what-you-should-do-when-you-can-t-decide-according-to-science-484026c1eeca). The overwhelming CI report information flood reminds choice paralysis.

* Proposal: "We could increase PHPStan level from 3 to 4"
* Fear: "But that could take months and break the rest of the codebase."

<br>

* Proposal: "We could add more unite tests to have more reliable code."
* Fear: "But we'd have to understand the code first."

<br>

* Proposal: "We could upgrade from PHP 5.6 to 8.3"
* Fear: "But that could break our due to external dependencies....

<br>

**The final decision is often the same—"if it works, don't touch it."

And just to be sure, add one more metric to the CI pipeline of analysis checks.

<br>

<div class="text-center">
<img src="https://imgs.xkcd.com/comics/standards.png" style="width: 30em" class="mt-4 mb-5">
</div>

If we jump to the upgrade on a similar note, we'll take a 12-month budget and add a few metrics but ultimately get nowhere.

<br>

## From milestones to baby steps

How do we approach the change in the project to make it successful, then? Some fancy AI software? A lot of computation power? A team of the top 20 senior developers in the industry?

<blockquote class="blockquote text-center">
"A journey of a thousand miles<br>
begins with a single step"
</blockquote>

No, we just keep it simple. We split 2-3 month milestones into **the tiniest baby steps possible**. We pick any topic that tends to paralyze us while coding and make it so easy that we can't say no.

Instead of milestones like "stop eating burgers for a whole year",
we go with baby steps: "What do I want to eat for breakfast to feel good in my body?"

## Rule of thumb

In my experience, if we pick a goal that **takes more than 2 weeks**, it will become a snowball that stops the upgrade. That's why we do an [Intro analysis battle plan](https://getrector.com/hire-team#process) that targets tasks as small as 1-2 days.

Do you need a more practical example? I'll share the steps we've applied in the 2022-2024 upgrades with success.

## From PHPStan 8 levels to 330 easy levels

The typical goal is to "reach PHPStan level 8." On the projects I've seen, this can take 2-5 years or more often, never at all. The problem is that levels are hugely disproportional and include many rules at once. Going from level 2 to 3 can take 3 % of effort while going from 5 to 6 % will take 95 % of effort.

How does a typical workflow look like? We enable next PHPStan level, see 3000 errors, fix 10 of them, create a pull request, and revert to the old level. **We kept the bar the same; we didn't improve much**. This is a problem typical for all static analysis tools, so we try to make it more fun by turning it into a game:

<blockquote class="twitter-tweet"><p lang="en" dir="ltr">Crazy idea to share...<br><br>In <a href="https://twitter.com/rectorphp?ref_src=twsrc%5Etfw">@rectorphp</a> 1.0 we&#39;ve introduced 1 rule = 1 level approach to ease integration to any project, however old or complex. And you love it 😍 <br><br>PHPStan has ~268 rules in its core, but only 10 levels to enable/disable them. <br><br>This makes integration quite… <a href="https://t.co/RTnXL4jOvz">pic.twitter.com/RTnXL4jOvz</a></p>&mdash; Tomas Votruba (@VotrubaT) <a href="https://twitter.com/VotrubaT/status/1759617297453302183?ref_src=twsrc%5Etfw">February 19, 2024</a></blockquote>

If we look closer and split PHPStan levels into more granular ones, it has around 330 various rule configurations or easy levels. This **way can always increase the quality bar** - one easy level at a time. We have a custom PHPStan extension that generates these configs on the fly and our customers love it. They can finally see progress and feel the change.

## From Rector sets to set levels

The PHPStan approach above is actually inspired by the feature we introduced in [Rector 1.0](https://getrector.com/blog/rector-1-0-is-here) for dead code and type coverage sets. Running even a single full Rector set that contains around ~50 rules will change almost every file.

That isn't very encouraging:

<blockquote class="twitter-tweet"><p lang="en" dir="ltr">10 lines of code = 10 issues.<br><br>500 lines of code = &quot;looks fine.&quot;<br><br>Code reviews.</p>&mdash; I Am Devloper (@iamdevloper) <a href="https://twitter.com/iamdevloper/status/397664295875805184?ref_src=twsrc%5Etfw">November 5, 2013</a></blockquote>

<br>

What we do? We enable the set, fix few files, disable set, and achieved no visible progress.

<br>

Instead, we go with one rule at a time:

```php
use Rector\Config\RectorConfig;

return RectorConfig::configure()
     ->withTypeCoverageLevel(1)
     ->withDeadCodeLevel(1)
```

The rules are sorted from the easiest to the most advanced, so we see progress. As in life, we get more confident as we level up on the go.

## From type declarations everywhere to type coverage

Back to PHPStan. A few rules report missing param, return, or property type declaration. One rule can easily report errors across 1000+ files.

<blockquote class="blockquote text-center mt-5 mb-5">
Imagine a CI failing with the message:<br>
you don't have 100 % test coverage
</blockquote>

That's too demanding and somewhat annoying. Let's turn off the rules.

<br>

How can we make it more straightforward and doable even for junior developers?

We've introduced an open-source PHPStan package called [type coverage](https://github.com/TomasVotruba/type-coverage). Instead of an *all-or-nothing* approach, it lets you choose a percentage of required type coverage. If it's above the value, your CI is green. If it's below, it will fail.

```yaml
parameters:
    type_coverage:
        return: 50
        param: 35.5
        property: 70
```

That allows you to adjust to your paste and project size while still seeing progress at the end of the day. Some projects start at 10 %, some at 30 %. What matters is that once a week or so, we push 1&nbsp;% higher.

```diff
 parameters:
     type_coverage:
+        return: 50
-        return: 51
         param: 35.5
         property: 70
```

Does it seem too slow to you? The compound effect will kick in:

<img src="https://jamesclear.com/wp-content/uploads/2015/08/tiny-gains-graph-700x700.jpg" class="img-thumbnail" style="max-width: 22em">

<br>

## From unused public methods to unused coverage limit

Last, we use the PHPStan extension that detects unused [public method/properties/constants](https://github.com/tomasVotruba/unused-public). At first, this might seem pointless to check, as most developers will tell you all the code is used. But in reality, we discovered that 15-20 % of the code is not used. That means the company wastes 15-20 %  money on maintain code that doesn't generate any profit.

This package's primary goal is to spot and eliminate unused public methods. Yet, it's also an all-or-nothing approach: Either enable the rule and fix everything or remove the package.

But what about using the same step-by-step approach that works so well? So, we introduced the option to configure *maximum relative amount* of such unused methods:

```yaml
parameters:
    unused_public:
        methods: 2.5
```

Such configuration will report unused public methods only if they cross 2.5 % of all public methods.

This way, we can go 1 % at a time and take the pace we feel comfortable with.

## Kaizen, Upgrade everyday

These techniques are no surprise for coaches, business owners, or builders. Great projects take time, effort, **and especially persistence**.

Going to the gym daily for a month will not make you a strong human. But exercising 2 minutes a day for a year will get you in good shape. Let's take this even further:

<blockquote class="blockquote text-center">
    "Most people overestimate what they can do in one year<br>
    and underestimate what they can do in ten years."
<footer class="blockquote-footer">Bill Gates</footer>
</blockquote>

<br>

That's why I love *kaizen*—continuous daily improvement. Create a strong culture of constant improvement. You can always make or do things better, even if they work well in a particular moment.

<br>

These are the core ideas behind our approach to legacy projects. Anyone can write a code, but only with focused force and persistence can you make the necessary change happen.

<br>

Happy coding!
