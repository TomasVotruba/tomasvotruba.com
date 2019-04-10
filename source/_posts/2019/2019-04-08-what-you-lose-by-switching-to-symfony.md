---
id: 201
title: "What You Lose by Switching to Symfony"
perex: |
    Switching one framework for another is nowadays getting easier and easier. What you could do for months only on your private projects, where income and delivering features are not important, is now an option for big websites with millions of lines of code. What seems like a divorce and switching a partner, is now as simple as changing your shoes from work to jogging ones.
    <br>
    <br>
    "Ok, we get it, it's simple. But why, what is the gain?"

tweet: "New Post on #php 🐘 blog: What You Lose by Switching to #symfony"
tweet_image: "/assets/images/posts/2017/symplify-deprecations/pr-notes.png"
---

I'm not writing this post because I use Symfony in my projects and love the community around it. I write this because in the last 2 weeks people around me asked me for help with Rector:

- from Laravel to Symfony
- from Lumen to Symfony
- [from Nette to Symfony](https://www.tomasvotruba.cz/blog/2019/02/21/how-we-migrated-from-nette-to-symfony-in-3-weeks-part-1/)
- from Zend to Symfony

It's possible and in a small fraction of costs of manual work, but "why not" is a very poor argument. That's like taking cocaine because it gives you more energy. Life is not about solutions, it's about trade-offs. I'd like to inform you about the cons of such migration, so you can decide with more solid arguments on both sides.

## What you Loose by Switching to Symfony?

### 1. "Your team will be surprised from the new framework they never used"

In one Symfony project I consulted, they hired 2 programmers who knew only Nette. They didn't give them much attention, because delivering feature was more important than to educate your juniors. The problem is, they produced Nette-ish code in Symfony application, because of lack of attention. Switch framework under programmer's hands in a very short period could cause much more serious damage.

### 2. "Your team loses all the social connections with your old framework"

The best way to learn and validate ideas is to go out and talk with people. You probably have many offline connections with people who use a similar framework, you use Slack channel for this framework, you know maintainers of packages from the framework ecosystem.
When you break up with your girlfriend, your friends from "her side" will soon perish.

<br>
<em>I'm obviously biased here, so if you have tips what will you loose, let me know in comments. I'll complete the article.</em>

## How to Switch <strike>Quickly</strike> Smoothly

The main goal in any transition is to **have everyone on board**. If your country creates reform for a pension that will take more money from young people, there will be frictions. If there is reform for cheaper education, that will decrease pensions to older people, there will be frictions. A reform, that improves educations for young people, that in conclusion will generate money for older people, will be much smoother. Not perfect, but smoother.

There is a practice-proven way to for all the problems above. Let's look at them:

### 1. "<strike>Your team will be surprised from the new framework they never used</strike>"

It doesn't make sense to switch in one week without discussing the whole team. It is as foolish as giving PHPStorm to someone who only used Sublime Test and expects them to know how to use it.

**Always give your team proper daily mentoring on how to use and master a new tool.** It will payback in higher quality code and faster feature delivery in the long term.

### How to do it better?

**Install [symfony/demo](https://github.com/symfony/demo)**, run it locally and try to break it.


Talk about WTFs with your team. Are there differences to your old framework? Just hate them, let the frustration out. It's normal to compare and feel this way.

**Look at [SymfonyCasts](https://symfonycasts.com), the best introduction to Symfony**, even better than documentation - the text is always for free (videos are paid, but they contain the same content as text) - huge thanks [Ryan Weaver](https://twitter.com/weaverryan) for funny videos.

**Hire an onsite/hot-line mentor for first 2 months**. It might be more expensive than paying a programmer, but cheaper than the technical debt that programmer without experience would create during these 2 months. The mentor or she will help you to quickly overcome all the WTFs and give you the confidence to master the framework yourself.

### 2. "<strike>Your team loses all the social connections with your old framework</strike>"

If you know people that use Nette, Laravel, Lumen or Zend, there is a high probability that your friends use Symfony as well.
Nowadays, *components over framework* model has become so popular, that most programmer actually uses more frameworks in one projects. So switching to Symfony only opens new topics to talk about.

If you look for an offline meetup where you can talk about Symfony, look at [Friends of PHP](https://friendsofphp.org). Don't look just for "Symfony" keyword, but also PHP meetups in general. There you'll be able to talk about Symfony too since in Central and Western Europe Symfony is de facto "the PHP framework".

## What you Gain by Switching to Symfony?

### Better Hiring Rates

Symfony ecosystem will not give you magically 10 new Symfony developers you now desperately need each year. But the symfony community trend is clear - it's active, it grows and attracts programmers that want to push their static programming to the next level.

<div class="text-center">
    <img src="/assets/images/posts/2019/loose-symfony/conferences.png">
    <em>There is 8 upcoming conferences in 2019 all over the Europe and America</em>
</div>

### Community of Testers

Nette Application is downloaded [2 296× a day](https://packagist.org/packages/nette/application/stats). Symfony alternative HttpKernel is downloaded [170 991× a day](https://packagist.org/packages/symfony/http-kernel/stats). Each Symfony package is tested by a huge amount of developers right after it is released. In this case, **there is 75× bigger chance the bug will be discovered and fixed**.

That's a huge advantage of the big and stable community.

### Rock-Solid Stability

Correct me if I'm wrong, but Symfony is **the only PHP framework that has [predictable release](https://symfony.com/roadmap)**. You can be 100 % sure that:

- every **6 months**, there will be a new release of Symfony with new hot features
- every **6 months** you can plan "upgrade day" for all you PHP projects
- every **2 years** there will be a new major version.
- every **2 years** there will be a new version with long-term support

<div class="text-center">
    <img src="/assets/images/posts/2019/loose-symfony/stable.png" class="img-thumbnail">
</div>

I wrote about the importance of leader stability for the rest ecosystem in [What Can You Learn from Menstruation and Symfony Releases](https://www.tomasvotruba.cz/blog/2017/10/30/what-can-you-learn-from-menstruation-and-symfony-releases/).

### You're Informed about Important Stuff

To get into Symfony you have to read every post, every tweet, read newsletters, be on Slack and go to meetups... well, that would be hell.

- All you need to know is in ["Living on the Edge"](https://symfony.com/blog/category/living-on-the-edge) category or Symfony blog. 2-3 months prior to the release (now 4.3), you'll find nice, short and sexy posts about upcoming features.

Do you have an extra 5 minutes a week?

- Just follow [@symfony_en](https://twitter.com/symfony_en) - cherry-picked tweets with important news

- I personally also sniff ["A Week of Symfony"](https://symfony.com/blog/category/a-week-of-symfony) every week, with newest issues, PRs and posts about Symfony (mostly to check if my posts are there ;)) to learn from others.

**[Javier Eguillez](https://github.com/javiereguiluz) is making this really easy for us, thank you!**

### You're Welcomed 🤗

There are no numbers to describe this, yet I find it the most important pillar of any community. **You're welcomed**. You're welcomed to talk about your ideas, to argue (as in "discuss with arguments", not to shout at each other) in issues on Github, to put arguments to support your statements.

I had many arguments with [Nicolas Grekas](https://github.com/nicolas-grekas) about parameters, console or dependency injection features. In the end, there is always someone who decides to go for it or stops it, there must be so it's not chaos. Was I always happy about the decision? No. **But I always feel respected. These discussions also help me to have a bigger picture and eventual implement the feature myself in a package.**

Sometimes I created a package, people found it useful and in a year or two, it becomes implemented in Symfony core. Then I could deprecate them, like [these Symplify packages in Symfony 3.3](https://www.tomasvotruba.cz/blog/2017/05/29/symplify-packages-deprecations-brought-by-symfony-33/):

<div class="text-center">
    <img src="/assets/images/posts/2017/symplify-deprecations/pr-notes.png" class="img-thumbnail">
</div>

I love how Symfony is ruled by decisions makers, but at the same time **is opened to a change**.

## It's not a Marketing, It's a Family

The best thing is, it's not just marketing, it's not a well-bended lie, it's not a way to get money from you then say goodbye.
It's a company setting, that you can feel in the atmosphere of Symfony conference.

When I was at my first [SymfonyCon in Paris 2015](https://pariscon2015.symfony.com/), there was a keynote by Fabien Potencier (Symfony founder and CEO) about "10 Years of Symfony". I thought it will be one of these boring self-promo talks about how is Symfony awesome and what it does and how big projects it runs.

Instead, Fabien **named people**, who contributed to Symfony in any way, **one by one and invited them to the stage, including his family**.

<img src="https://blog.radumurzea.net/wp-content/uploads/keynote.png" style="max-width:40em">

It made me cry a bit and I still have goosebumps when I remember it.

That's what you gain by switching to Symfony.

<br>

Happy coding!



