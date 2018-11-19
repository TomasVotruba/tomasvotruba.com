---
id: 160
title: "When You Should Use Monorepo and When&nbsp;Local&nbsp;Packages"
perex: |
    Recently I gave [a few talks about monorepo in PHP](/talks/#monorepo) and how to integrate it to companies in a useful way. I'm very happy to see many people already use it and know what problems it solves.
    <br><br>
    Before monorepo hype takes over private PHP projects, I think **you should know about its limits**: When is the best time for you to [go monorepo](https://gomonorepo.org/)? When you gain less complexity while integrating it? How can you make the transition better? Is it really needed?
tweet: "New Post on My Blog: When You Should Use #Monorepo and When Local Packages    #maturity #transition #balls @LekarnaNovinky #lekarnacz"
---

There are already [6 posts](/clusters/#monorepo-from-zero-to-hero) about why is monorepo so good in dealing with complexity. So if you hear about it the first time or still don't believe it might help you, go check those.

Today we'll not focus on open-source projects, but **rather on your private code**. All from point of **timing and transition**. Let me show you the architecture that [Lekarna.cz](https://www.lekarna.cz/) uses to this day and how did we get there despite very chaotic start.

### 1. Monolith

We started development in a single repository, adding features here and there. It grew, the `/src` directory had over 150 classes very soon and it became more and more cluttered. The development was coupled to one monolithic code and the architecture started to disappear from code-review focus and from the code itself. **Not good.**

↓

### 2. Many-Repository

So we've decided to split too many repositories. There was a repository `lekarna/cms`, `lekarna/shop`, `lekarna/warehouse` etc.

First, it looked cool, each with own repository, own `composer.json`. Now you can spot this *academic coolness* by **focusing mostly on technical facts** (e.g. each repository has own `composer.json`) rather **than how it feels to use it** (e.g. it really sucks to do one change in many places).

In every PR more energy and attention was invested in the maintenance of there many-repositories and their mutual dependencies, than to PR itself. **Not good.**

↓

### 3. Local Packages

We realized, we needed the code to be at one place **and** keep it as separate as possible. In 2014 it didn't have a name, but I came with an idea to create a `/packages` directory in our main repository and *pretend* we have repositories there. *Treat it like* one day it might be decoupled to own repository, with own dependencies and own tests. Few years later it became known as [local packages](/blog/2017/12/25/composer-local-packages-for-dummies/).

This lead new programmers to focus deeply on a single part of huge codebase at a time and also make changes the easiest way possible. **We didn't have to explain them anything**, the knowledge was embodied in the code. This is a **golden pattern you look for** in any part of business or life - you just give people smartphone and they *intuitively know* what to do.

↓

### 4. Open-Sourced Monorepo

This next step can be used to build a community to fuel your code-base. It's a state when your **need a feedback from the community to push the quality further** and also turning that code to a product for others. That's what happened to all companies that were private in the start - Symfony, Sylius or Shopsys.

But this was not a step for Lekarna.cz code-base. It has no such ambitions and it doesn't make sense. You don't have to do everything you can, just because you can.

<br>

So do *local packages* have meaning? **Yes, they do**, because their purpose is not to become an open-source monorepo company, but to make your private/public code as easy to work with as possible while it grows. In short: **to enjoy coding in very large code-bases.**

<br>

## When You Should Evolve?

As you can see, we had to make many costly transitions. Switching from monolith to many-repo and then to local packages is no cheap fun.

The rule that guides a **good transition** is:

<blockquote class="blockquote text-center">
    Value after transition > Cost lost by transition
</blockquote>

E.g. if your project is 20 % easier to contribute, but it costs 50 % of your team energy on learning new technologies, fixing regression bugs or not believing in it, it's not worth it.

As future is unclear and hindsight is always 20/20, these numbers are not easy to establish before you do any real change. **So how to decide?** You can see the code **is maturing and is ready to evolution jump**. If we take a broader picture and look at PHP frameworks, in the last 5 years there is a big evolution jump from service locators to constructor injection. Do you want a more recent example? Weak typing to strict typing.

You just feel it. It's not an easy skill to build but **the more and more you develop and make transitions, the more you'll improve this skill and your success rate**. You'll get there by trying, believe in yourself and learn from mistakes. How crazy this might sound, the more mistakes you make, the faster you learn.

<br>

## When Monorepo Makes sense For Private Projects?

I was asked this question at [Developer Day 2018](https://www.hubbr.cz/udalosti/events/developer-day-2018) (amazing event by amazing brothers duo). To answer completely: if I were you, I'd **start every project with local packages by default**. You never ever have to jump to monorepo nor open-source, but you make any future possible transition very cheap. Very!

All it costs you is to learn what local packages are how to work with them. Too busy to learn? Just [give me a call](/contact/) and I'll explain it to you.

Because one day, you might see one of obvious **reasons to go monorepo**:

1. Your product becomes useful to others
2. Your component becomes useful to others
3. You develop **multiple-projects with the same code base**, e.g. 5 projects running on Symfony 4.1
4. You develop the **single project for multiple clients**, e.g. one e-commerce platform for 10 customers

And we're still in privates here.

But these **reasons never have to come**, so just going monorepo blindly from the start might actually hurt your development.

<br>

Happy evolving!
