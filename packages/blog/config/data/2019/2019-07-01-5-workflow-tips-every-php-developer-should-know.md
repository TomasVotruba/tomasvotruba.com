---
id: 216
title: "5 Workflow Tips every PHP Developer Should Know"
perex: |
    I was surprised, how much of your attention got [5 Tips to Effective Work with Github Repository](/blog/2019/02/25/5-tips-to-effective-work-with-github-repository) post.
    <br><br>
    So [I started to collect tips](https://github.com/TomasVotruba/tomasvotruba.com/issues/226) I use on training and mentoring that I don't even notice, but others find fascinating. Here is 5 of them.
tweet: "New Post on #php 🐘 blog: 5 Workflow Tips every PHP Developer Should Know"
---

## 1. Find any file in Github Repository within a Second

Github repository is the best documentation, if [the code is written mindfully](/blog/2019/06/17/how-to-upgrade-meetup-com-api-to-oauth2-with-guzzle).

E.g. you use Rector and you're curious about its guts. The main bin command is "vendor/bin/rector process", so you're interested in `ProcessCommand`. What's inside?

You go to the Github repository:

- look for "process command" or "ProcessCommand"
- find 150 irrelevant results
- ...and close it 😠

Great documentation is useless without the even better search.

<br>

**Good news! Github is closer to PHPStorm than you think:**

<img src="/assets/images/posts/2019/php_workflow_tips/hit_t.gif" class="img-thumbnail" style="max-width:35em">

**Just press <span class="btn btn-light btn-outline-dark">t</span> on your keyboard and "TypeYourClass".**

<div class="fa-2x mt-3">👍</div>

## 2. Get rid of Phing

All Phing scripts I've seen look like someone programmed their own Arduino to open glass with water.
So I migrate them to composer scripts, so people can actually use them without having an MIT degree.

Martin already wrote about it in [Have you tried Composer Scripts? You may not need Phing](https://blog.martinhujer.cz/have-you-tried-composer-scripts)

<div class="fa-2x mt-3">👍</div>

## 3. Enable Colors in Composer Scripts

Let's say you already use composer scripts:

```json
{
    "scripts": {
        "fix-cs": "vendor/bin/ecs check bin src tests --fix"
    }
}
```

Great job! Now all you need to do is run it:

```bash
composer fix-cs
```

The scripts are running... Now imagine I'll come to you and slap you in the face for failing build `master` branch (that's just hypothetical case, we all know `master` can never fail, right?).

Your head turns around and you grab a part of the script output. Now...

**"Was it <span class="text-danger">red</span> or <span class="text-success">green</span>?"**

The output is hard to read quickly without [color patterns](https://www.amazon.com/Design-Everyday-Things-Donald-Norman/dp/1452654123).

How to fix this? Just add `--ansi`:

```diff
 {
     "scripts": {
-        "fix-cs": "vendor/bin/ecs check bin src tests --fix"
+        "fix-cs": "vendor/bin/ecs check bin src tests --fix --ansi"
    }
}
```

<div class="fa-2x mt-3 mb-4">👍</div>

Thanks for this tip to [Jan Mikes](https://janmikes.cz) ❤️️.

<br>

*For more composer tips, check [24 Tips for Using Composer Efficiently](https://blog.martinhujer.cz/17-tips-for-using-composer-efficiently).*

## 4. Get rid of PHPMD

You might know PHPMD as *PHP Mess Detector*. But not many people know, the package is dead:

<img src="/assets/images/posts/2019/php_workflow_tips/barely.png" class="img-thumbnail">

Dead packages are ok if there is no alternative that is continuously evolving. If there is, by staying at the same place, [you're shooting your business and development speed to the leg](/blog/2019/03/11/why-we-migrated-from-nette-to-symfony-in-3-weeks-part-3).

That's this case. PHPMD features are 99 % compatible with PHP Code Sniffer and PHP CS Fixer rules:

```diff
-PHPMD rule
+PHP_CodeSniffer alternative
```

E.g.:

```diff
-rulesets/codesize.xml/CyclomaticComplexity
+Generic.Metrics.CyclomaticComplexity

-rulesets/controversial.xml/CamelCaseMethodName
+PSR1.Methods.CamelCapsMethodName

-rulesets/design.xml/GotoStatement
+Generic.PHP.DiscourageGoto
```

I've migrated over 5 of these recently and you can see its a relief in developers eyes - they either have less code to maintain or (more often) they've finally got rid of that "black hole" code that no-one knew what it does.

**How to migrate?**

Just look at [PHPMD migration to PHP_CodeSniffer in Shopsys repository](https://github.com/shopsys/shopsys/search?p=2&q=phpmd&type=Commits).

<div class="fa-2x mt-3">👍</div>

## 5. Use Elementary Maths to become Master

Tips 2 and 4 have actually similar principals, [a pattern](/blog/2019/04/15/pattern-refactoring). If you learn to think in patterns, **you can merge 10 specific rules to 1 pattern** and [use that extra brain space for something else](/blog/2018/09/13/your-brain-is-your-garden).

### Least Common Denominator

I've learned this principle in 5th grade, it has fascinated me ever since. It fascinates me even more, how they are used to create effective code - easy to write, read, maintain and doing what it should do.

**What is the least common denominator?**

These 2 pictures explain it:

<div class="row">
    <div class="col-12 col-md-6">
        <img src="/assets/images/posts/2019/php_workflow_tips/least.gif" class="img-thumbnail">
    </div>
    <div class="col-12 col-md-6">
        <img src="/assets/images/posts/2019/php_workflow_tips/not_so_least.gif" class="img-thumbnail">
    </div>
</div>

Now, both pictures explain it. Which one do you prefer?

*Disclosure*

- the left image is actually self-explanatory - it's used *least common denominator* to explain *Least Common Denominator*
- the right image also explain it, but it uses much more extra data, that you don't need (e.g. root) and only slows down your neuron pipelines

**Use the left approach to explain issues and problems. They're easier to understand, focused on the problem you really have and will get to the effective solution faster.**

The right path is how the legacy code is manufactured in companies every day.

### What Else is There?

- *Occam's razor* - People that [use Wikipedia](https://simple.wikipedia.org/wiki/Occam%27s_razor) or studied university renamed *least common denominator* to Occam's razor and added more academic words. The logic is the same, but you might prefer it.

- *SOLID* - Again the same family, just different letters and vocabulary.

That's the beauty example of pattern thinking. Instead of remembering 3 different terms, you pick one and use it.

<div class="fa-2x mt-3">👍</div>

<br>

**Do you want to go *balls deep* and learn more about this?** Book it:

- [The Pragmatic Programmer: From Journeyman to Master](https://www.amazon.com/Pragmatic-Programmer-Journeyman-Master-ebook/dp/B003GCTQAE)
- [Don't Make Me Think, Revisited](https://www.amazon.com/Dont-Make-Think-Revisited-Usability/dp/0321965515) (3rd edition from 2014)


<br>

Happy coding!
