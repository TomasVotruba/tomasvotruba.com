---
id: 61
layout: post
title: "What can You Learn from Menstruation and Symfony Releases"
perex: '''
    I [wrote about monorepo and how it turned me into lazy programmer](/blog/2017/01/31/how-monolithic-repository-in-open-source-saved-my-laziness/) before.
    <br><br>
    As monorepo is use [more and more](https://blog.shopsys.com/how-to-maintain-multiple-git-repositories-with-ease-61a5e17152e0), we should look at it again.
    Today from a bit atypical point of view: **combined with bit of blood and sunshine**.
    <br><br>
    Are you ready?
'''
_tweet: "..."
_tweet_image: "..."
related_posts: [25]
---

*Disclaimer: this post has no intention to put menstruation into any bad light. Exactly the opposite - I admire them and love to learn from them.*

## *Natural* Releases

I don't want to get into technical view, there is already [semantic versioning](http://semver.org/), Symfony bc promotes etc.


But rather from view of *women* and *nature*.

Why? when i need to solve some complicate architecture problem and don't know the solution yet, i go for a walk. and just observe what is going on around me.

mostly in city or parks

how does world work, i look for an inspiration without expectations, aka serendipity

To be honest, many patterns or technology we use are originated from nature.
Processor and hard drives from brain, camera lenses from eyes and design pattern from pattern of nature. What else are falling leaves of tree in autumn? An **event subscriber**.



## Menstruation as Inspiration


When we look at our main ability to survive, to reproduce ourselves, we see it's very easy to follow. 

Menstruation comes in cycles of roughly 28 days, we know when it happens, how long it takes, when it ends (again, roughly).

All 4 periods of period have certain meaning for the body of women and her ability to pregnant. 


<img src="/assets/images/posts/2017/menstruation/periods.jpg" class="img-thumbnail">

Could you imagine somebody having menstruation 4 weeks in a row or somebody else having menstruation every 36 weeks?

Neither do I.

Now the interesting part: *can you imagine software having release cycles in 28 days?* 

...rhetorical question, just keep reading.


## A Year Cycle

A bit longer periodical system in nature, that works for some time now, is a year cycle

<img src="/assets/images/posts/2017/menstruation/seasons.jpg" class="img-thumbnail">

Every takes roughly 3 months and restarts every 12 month.

Again, **every 4 periods** of this period has its meaning:

- *Spring* - growing and getting stronger
- *Summer* - reaching it's peak and making seeds
- *Autumn* - preparing for rest, cleaning up
- *Winter* - peaceful time of sleep and retrospective of the whole year

  
Now back to the interesting part: could you imagine having release plan synced with the year periods?

<br>

Did you know women that spend lot of time together **tend to sync their menstruation**? I will get back to the later software-wise.


## Basic Stone for Evolution

When we look at those patterns of nature, they have one important sign in common. A sign that we can see more and more in software development - **predictability**.

You **know** when the winter comes or when **your spouse needs more attention and care** when her period starts. You **can plan, you can prepare and you can learn this by heart**. Thanks to that, you can focus on more dynamic things that are not to predictable - like your emotions, ideas, needs to explore culture around you or **priorities on development of your project**.


### Symfony Cycle nature adoptions

<img src="/assets/images/posts/2017/menstruation/symfony.jpg" class="img-thumbnail">

I first realized this at Fabien's talk about Symfony new release cycle on [SymfonyCon Paris 2015](https://pariscon2015.symfony.com/):

- **Major versions every 2 years, minor every 6 months.**

So simple yet so amazing. I don't think about "when will the new Symfony come?" no more.


### What about PHP?

<img src="/assets/images/posts/2017/menstruation/php.png" class="img-thumbnail">

I recall wondering when PHP 5.5, 5.6 or 7.0 will be out. No more thanks to **[yearly period beginning of December](http://php.net/supported-versions.php)** since PHP 7.0.


### Menstruation synchronization

<img src="/assets/images/posts/2017/menstruation/together.png" class="img-thumbnail">

What is interesting is that **Symfony matches PHP cycle every 2 years**. Coincidence? I don't think so. 

I think they're doing **the right thing** right. 


## We should all Menstruate Together

We're getting back software, to releases and monorepo. If you see term *monorepo* first time, read [this legendary post by *danluu*](http://danluu.com/monorepo/). 

Some people say that **big disadvantage** of monorepo is that **they have to tag their packages all together** (like Symfony). I see it as **advantage**, because that systematically leads to release cycle and open possibility to synchronization with other projects, like PHP + Symfony.  

### See for Yourself 

Which application would you maintain and upgrade based on these `composer.json` if you'd have **these 3 options**?

<br>

**A** with per package versioning: 


```javascript
{
  	"require": {
		"symfony/http-foundation": "3.3",
		"symfony/console": "3.1",
		"symfony/dependency-injection": "2.8",
		"symfony/event-dispatcher": "3.2",
		"doctrine/orm": "2.5",
		"doctrine/dbal": "2.3",
		"doctrine/annotations": "1.7",
		"nette/utils": "2.3",
		"nette/finder": "3.0"
  	}
}
```

<br>

or **B** with *per-vendor-sync*

```javascript
{
  	"require": {
		"symfony/http-foundation": "3.3",
		"symfony/console": "3.3",
		"symfony/dependency-injection": "3.3",
		"symfony/event-dispatcher": "3.3",
		"doctrine/orm": "2.5",
		"doctrine/dbal": "2.5",
		"doctrine/annotations": "2.5",
		"nette/utils": "3.0",
		"nette/finder": "3.0"
  	}
}
```

<br>

or **C** that looks like sci-fi: 

```javascript
{
  	"require": {
		"symfony/http-foundation": "3.3",
		"symfony/console": "3.3",
		"symfony/dependency-injection": "3.3",
		"symfony/event-dispatcher": "3.3",
		"doctrine/orm": "3.3",
		"doctrine/dbal": "3.3",
		"doctrine/annotations": "3.3",
		"nette/utils": "3.3",
		"nette/finder": "3.3"
  	}
}
```

<br>

###  Advantages of Synced Vendor 

B is nested next step. Synced vendor - single version for `symfony/*` gives me soo much freedom:

- to update it, i just change 1 version and run `composer update`
- they work the best together - I don't have to check if console 3.3 is compatible with event-dispatcher 4.0 - **I just know**

The best advantages - i know I will upgrade my application once it's released, to first week in december every 2 year.


### Disadvantage of  Vendor out of synced


And what if every package has it's own destiny?

I remind you, this is point of end-users' view.

- I'm stressed when any of my 20 symfony/* dependencies changes 
- I'm afraid that I will have to google on Github, what version depend on which
- I can't plan any upgrades, because nobody knows any data


## Call Out to Package Maintainers

All this is not related only to Symfony, Doctrine, Nette or any [other huge PHP players](https://gophp71.org/) like Zend, Laravel, CakePHP or Yii.

**Every dependency and its own versioning means increased work for the maintainer** (PHP developers). That's stands if you agree with cycles or not. **Version C** being the easiest and version **A** being the most difficult and expensive. 

**Do you want to add extra work** to developer's back to study your vendor release system?

On the other hand a *package user*, I bet you recall at least one package you **longed to be tagged, but were frustrated not knowing when**. It's quite common that first 3 versions are tagged in withing 1 year, but then followed by 1+ year long pause.


### Syncing with Symfony "menstruation"

I like this synchronization, predictability, stability and maturation in our PHP ecosystem. That's why I'm trying to **synchronize with Symfony release cycles**. As from huge to small, as from seasons of year to menstruation, as from PHP to Symfony,

I bet you didn't notice, but with [Symplify](https://github.com/Symplify) I try to **release mayor version to minor of Symfony**. Version 2.0 was released on [July 6th 2017](https://github.com/Symplify/Symplify/releases/tag/v2.0.0), right after [Symfony 3.3 release](https://symfony.com/roadmap?version=3.3#checker) in May.

Being predictable with BC breaks, support for older version and consistent with adding new version.

## No Force, Just Inspiration

Do you maintain a package? What approach do you have on predictability of releases and why? Please, let me know in comments. I always love to hear new ideas.

Happy syncing!


