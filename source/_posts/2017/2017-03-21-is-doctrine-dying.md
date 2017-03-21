---
layout: post
title: "Is Doctrine Dying?"
perex: '''
I've been thinking over 2 years about this post. I wasn't sure it's temporal feeling (negative hype) or real think. It's still the same so I think it's about time to write about it.

Do you use Doctrine ORM? If so, do you follow it's evolution on Github? Symfony is evolving, Laravel is evoling, Nette is evoling, world is evolving...
'''
lang: en
---


## Why I'm Dropping My Doctrine Packages

I miss this evolution with Doctrine. In programming, I need to do every day work with less and less work. That's why I use open-source packages, that's why you use Wordpress (or  Statie) for a blog.

When some packages is stuck for a 2-3 years on one place, there is probably already better replacement. Either on Packagist or in your mind.

As [I mentor and coach teams](link) to write better code with less work, **I'd go against my own believes by putting vendor lock in their applications.**

That why I'm dropping my packages build on Doctrine.


### What Repositories is This Concerned?

- https://github.com/Zenify/DoctrineFixtures
- https://github.com/Zenify/DoctrineBehaviors
- https://github.com/Zenify/DoctrineExtensionsTree - use https://github.com/rixxi/gedmo instead
- https://github.com/Zenify/DoctrineMigration - use
https://github.com/robmorgan/phinx instead




Just to be clear: this is not the end, this is the current state.



## 3. Things That Might Save Do


### Having a Concurent

"Where you are the best, there is no one to learn from."

That's related also to open-source projects.



### Using Monorepository

...link



### Using Dependency Injection by Default

If send PR 2 years ago for simple thing - having modlar filters. If there would be Construotr injection, there would be no need for that PR.


Static is fast. I mean, a fast way that leads to vendor locking.




## Where to go: Redis ORM

Having this discussion with Jáchym, we came to conslusion.
