---
id: 215
title: "Do you use PHP_CodeSniffer and PHP&nbsp;CS&nbsp;Fixer PHPStorm Plugin? You&nbsp;are&nbsp;Slow and Expensive"
perex: |
    People keep asking me about IDE plugins [for Rector](https://www.reddit.com/r/phpstorm/comments/am1qzv/update_phpdoc_comment_action/efqpv8o) and Easy Coding Standard.
    Do you want it too? Do you use one for PHP_CodeSniffer of PHP CS Fixer? Have you ever thought about the benefits and costs of them?
tweet: "New Post on #php 🐘 blog: Do you use #php_codesniffer and PHP CS Fixer @PHPStorm Plugin? You are Slow and Expensive         #phpcs"
tweet_image: '/assets/images/posts/2019/plugin/addict.jpg'
---

## Benefits of Instant Feedback

When you a PHPStorm plugin that tells you what to do, you have this extra information that you can follow:

<img src="/assets/images/posts/2019/plugin/plugin.png">

That's pretty cool, right? It will:

- save you running coding standard tool from command line later - forget `vendor/bin/ecs check src`
- help you to write code according to standards of the project
- help you to write better code
- **you have the information at the moment you made the mistake**

The last benefit is wired to our brains.

## Trap of Instant Gratification

<blockquote class="blockquote">
    "Everybody is addicted to dopamine."
</blockquote>

<img src="/assets/images/posts/2019/plugin/porn.jpg" class="img-thumbnail">

Slack notification, Instagram likes, underline of <span class="text-underline">typoes</span> and suggestions in your IDE.<br>
**This all makes us happy by brain design**.

<br>

Coding standard tools are in your IDE already, next are static analyzers and instant upgraders:

<div class="text-center">
    <img src="/assets/images/posts/2019/plugin/psalm.png">
    <br>
    Live report on <a href="https://psalm.dev/">Psalm.dev</a>
</div>

<br>

Everyone writes about the benefits of these plugins, but **how much do you pay for it**?

"It's free!"

## Attention Economy

Well, it's not. If you've never heard about *dopamine - notification* effect, read [Are You Using Social Media or Being Used By It?](http://www.calnewport.com/blog/2017/10/02/are-you-using-social-media-or-being-used-by-it) by *Cal Newport*. This amazing guy helped me to [quit Twitter](/blog/2017/01/20/4-emotional-reasons-why-I-quit-my-twitter/) and [go deeper](/blog/2017/09/25/3-non-it-books-that-help-you-to-become-better-programmer/#deep-work-by-cal-newport) in topics I really care about in my life.

<img src="/assets/images/posts/2019/plugin/addict.jpg">

Basically, notifications turn your beautiful and dynamic brain capable of high abstract thinking to the **brain of a heroin addict with instant feedback overloop**.


<br>

Let's get back to our code again.

### What Happens When we Type Code in PHPStorm with PHP Code Sniffer plugin?

<img src="/assets/images/posts/2019/plugin/plugin.png">

- we see an underscored text
- we move our cursor above it (2 s)
- we read the message (2 s)
- we try to understand it (3 s)
- we try to figure out what needs to be done to make it disappear (5-15 s)
- we try it (2 s)

**If we're lucky, it's gone** under 15 seconds. If not, we get back to "we try to understand it" step.
In time, we get better, faster and we create a small database of "message → solution" in our brains. In a few weeks, we learn how to write perfect code without any underscores.

Then our team extends rule set with [PSR-12](/blog/2018/04/09/try-psr-12-on-your-code-today/) and we have to upgrade our brain database. So we start to hate extending of coding standards and prefer less.

## Expensive Fun

Now the important question: do you know how expensive this is? If you have a boss who doesn't care about productivity and you can whatever you need without critical business thinking, **stop reading**, because you're primed to waste money by your work design.

**But if you're freelancer, or you pay your programmers or you desire to be effective by lazy**, I have a comparison for you:

Without PHPStorm plugins, you have to run the tool manually in your command line **once per git push**:

- open command line (2 s)
- run coding standard command (2 s)

- **4 seconds per push** vs **15 seconds per 1 error + persisted database in your brain**

What is cheaper in money and brain damage?

**Pro-lazy tip:** I'm too lazy to type more than 2-3 chars manually, so I use [composer scripts](https://blog.martinhujer.cz/have-you-tried-composer-scripts) and 2-3 chars long bash script shortcuts:

```bash
cs
# aliased to vendor/bin/ecs check app packages tests

fs
# aliased to: vendor/bin/ecs check app packages tests --fix
```

## Benefits of Deep Work and Scaled Automation

**Rule of the Thumb**: If something requires your attention multiple times with same *A → B* operation, automate the change and delegate it.

PHP_CodeSniffer can actually report what should be changed, without any clear suggestion of how to change it (read-only). But PHP CS Fixer fixes the code by default, so you don't even have to think about the change. So using PHP CS Fixer manually is a pure waste of life and money.

<br>

And this just a very limited example of 1 programmer and 1 project. Most companies have multiple programmers and projects. *The waste of time for 10-programmer teams having 20 projects escalates quickly:

- plugin way: 15 seconds per one error * 10 programmers * 20 projects * (let's be optimistic) 50 errors per pull-request...
- automated CI way: validate PR and click merge request - **20-40 seconds per pull-request**

In simple words: *exponential* costs vs *constant* costs

## Scale to Whole PHP World

Now imagine all the PHP projects in the world. Which of those is faster?

Does it ring a bell? Now think about the same way about instant upgrades or refactoring?

If there will be Rector plugin for PHPStorm (I honestly hope it won't), **you'd have to find every [new pattern](/blog/2019/04/15/pattern-refactoring/) there is in your project, again manually and randomly in your PHP files** and hit the "refactor" button.

If you're effective, lazy and don't want to produce waste, you'll run:

```bash
vendor/bin/rector process src
```

And save millions of your brain cells :)

<br>

Happy coding!
