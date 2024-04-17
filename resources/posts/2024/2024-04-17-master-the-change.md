---
id: 406
title: "Master the Change"
perex: |
    When we upgrade a new project to the best version possible, the latest PHP and framework versions, it's not only about changing syntax sugar to a more fancy one.

    It's about huge focus shift in project management so far. It's a change to master. I want to share the basic rules we apply to make the "impossible" upgrades successful and steady path.
---

## How do legacy projects look like

The legacy project is rarely about having an old PHP version. It's usually the surrounding ecosystem that keeps the project having the old version, despite many efforts by the programmers to change it. The project lead or owner already invested usually around 1-2 years to change it, but **there are counter forces that keep the project unchanged**.

It's frustrating and only dig us more profound to the ground.

The same way we approach our health. Eating a burger once a month for lunch won't most likely affect our health. But if we are in the range of obesity, we won't get out of it by excluding burgers. We have to change our pre-sleep-eating habits, include daily exercise, change our social group, and more.

## Feel the forces

The same way we approach legacy projects. We have to detect, what are the blocking forces that keep the project in the old version. It can be *learned helplessness*, as term from psychology that describes situation when we tried over 10 times and always got a negative response. Why should it work for the 11th time? We learned not to try.

It can also be a fear of the unknown. In some cases, it is the team leader who keeps us from moving. They don't want to change the codebase because they're the ones who **bear full know-how of the project in their head**. They're precious to the project, and they know it. What would happen if the project was in great shape and no longer depended on them? Maybe they would get fired as they were no longer needed.

## Measure everything...?

One of the attempts to deal with legacy codebase in the wild is to **apply lots of metrics**. It gives an impression that once we know how "much" of "X" the codebase has, we'll be able to deal with it "somehow".

It's like trying to get your body into shape by hourly measuring your weight, recoding fat/sugar/protein in every meal you eat and in every liquid you drink. It give you lot of various data that can lead to various conclusions. Yet, **nothing changes unless you change** the way you eat.

<br>

The same applies to codebases. I've seen Sonarcube, PHPmd, and similar programs provide over 100 various metrics over a 36-month span.

* "What is the conclusion?" I asked the project owner.
* "We are in bad shape for past 36 month, plus/minus around the same".

Another way to complicate the situation, even more, is to apply PHPStan baselines that record every possible static analysis violation we can have. It's like having a personal trainer that tells you every day what you've done wrong since the moment you've hired them.

This force helps to keep the situation the same and prevents us from making the change happen.

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

If we jump to the upgrade on similar note, we'll take 12 months budget, add few metrics but get nowhere in the end.

<br>

## From milestones to baby steps

How do we approach the change in the project to make it successful, then? Some fancy AI software? Lot of computation power? Team of top 20 senior developers in the industry?

<blockquote class="blockquote text-center">
"A journey of a thousand miles<br>
begins with a single step"
</blockquote>

No, we just keep it simple. We take 2-3 month milestones and split them into the smallest baby steps possible—but not smaller. We pick any topic that tends to paralyze us while coding and make it so easy that we can't say no.

Instead of milestones like "stop eating burgers for a whole year",
we go with baby steps: "What do I want to eat for breakfast to feel good in my body?"

## Rule of thumb

In my experience, if we pick a goal that **takes more than 2 weeks**, it will become a snowball that stops the upgrade. That's why we do an [Intro analysis battle plan](https://getrector.com/hire-team#process) that targets tasks as small as 1-2 days.

Do you need a more practical example? I'll share the exact steps we've applied in 2023/2024 upgrades.

## From PHPStan 8 levels to 330 easy levels

Typical goal is to "reach PHPStan level 8". On the projects I've seen, this can take 2-5 years or more often, never at all. The problem is that levels are hugely disproportional and include many rules at once. Going from level 2 to 3 can take 3 % of effort, while going from 5 to 6 % will take 95 % effort.

Typically we enable next rule in PHPStan, see 3000 errors or so, fix 10 of them, create pull request and go to the to old level. We keep the bar the same, it fees like we didn't improve much. This is problem typical for all static analysis tools, so we try to make it more fun by making it seem like a more fun game:

<blockquote class="twitter-tweet"><p lang="en" dir="ltr">Crazy idea to share...<br><br>In <a href="https://twitter.com/rectorphp?ref_src=twsrc%5Etfw">@rectorphp</a> 1.0 we&#39;ve introduced 1 rule = 1 level approach to ease integration to any project, however old or complex. And you love it 😍 <br><br>PHPStan has ~268 rules in its core, but only 10 levels to enable/disable them. <br><br>This makes integration quite… <a href="https://t.co/RTnXL4jOvz">pic.twitter.com/RTnXL4jOvz</a></p>&mdash; Tomas Votruba (@VotrubaT) <a href="https://twitter.com/VotrubaT/status/1759617297453302183?ref_src=twsrc%5Etfw">February 19, 2024</a></blockquote>

If we look closer and split PHPStan levels to more granular ones, it has actually around 330 various rule configuration, or easy levels. This **way can always go and stay** - that's important - one easy level at a time. We have a custom PHPStan extension that generates these configs on the fly and our customers love it. They can finally see progress and feel the change.

## From Rector sets to set levels

The PHPStan approach above is actually inspired by the feature we've introduced in [Rector 1.0](https://getrector.com/blog/rector-1-0-is-here) for dead code and type coverage set. Running even single full Rector set that contains around ~50 rules will change almost every single file.

That's rather discouraging:

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

The rules are sorted from the easiest to more advanced ones, so we see progress. We level up on the go and get more confident, as in life.

## From type declarations everywhere to type coverage

Back to PHPStan. It has few rules that report missing param, return or property type declaration. One rule can easily report errors across 1000+ files.

<blockquote class="blockquote text-center mt-5 mb-5">
Imagine a CI failing with message:<br>
you don't have 100 % test coverage
</blockquote>

That's too much demanding and rather annoying, right? Let's disable the rules.

<br>

How can we make it simpler and doable even for junior developer?

We've introduced an open-source PHPStan package called [type coverage](https://github.com/TomasVotruba/type-coverage). Instead of all-or-nothing approach, it lets you choose a % of required type coverage. If it's above the value, your CI is green. If it's under, it will fail.

```yaml
parameters:
    type_coverage:
        return: 50
        param: 35.5
        property: 70
```

That allows you to adjust to your paste, project size, while still seeing progress in the end of the day. Some project start at 10 %, some at 30 %. What matters is that once a week or so, we push 1&nbsp;% higher.

```diff
 parameters:
     type_coverage:
+        return: 50
-        return: 51
         param: 35.5
         property: 70
```

Does it seem too slow to you? Make no mistake, the compound effect will kick in:

<img src="https://jamesclear.com/wp-content/uploads/2015/08/tiny-gains-graph-700x700.jpg" class="img-thumbnail" style="max-width: 22em">

<br>

## From unused public methods to unused coverage limit

Last but not least, we use PHPStan extension that detects unused [public method/properties/constants](https://github.com/tomasVotruba/unused-public). At first, this might seem pointless to check, as most of the developers will tell you all the code is used. But in reality, we found out that 15-20 % of the code is not used at all. That means the company wastes 15-20 % extra money on nothing.

The main goal of this package is to spot and eliminate unused public methods. Yet, it's also an all-or-nothing approach. Either we enable the rule and fix everything, or we remove the package.

But what about using the same step-by-step approach that works so well? So we introduced maximum relavite amount feature:

```yaml
parameters:
    unused_public:
        methods: 2.5
```

This way, we can go 1 % at a time and take the pace we feel comfortable with.

## Kaizen, Upgrade everyday

These techqniues are no surprise for coaches, business owners or builders. Great project take time, effort, **and especially   persistence**. Going to gym everyday for a month will not turn you into strong human. But exercising 2 minutes a day for a year will get you in good shape. Let's take this even further:

<blockquote class="blockquote text-center">
    "Most people overestimate what they can do in one year<br>
    and underestimate what they can do in ten years."
    <footer class="blockquote-footer">Bill Gates</footer>
</blockquote>

<br>

That's why I love *kaizen* - a continuous daily improvement. Create a strong culture of constant improvement. You can always make or do things better, even if they seem to work well in a particular moment.

<br>

These are the core ideas behind our approach to legacy project. Anyone can write a code, but only with focused force and persistence, you can make the important change happen.

<br>

Happy coding!
