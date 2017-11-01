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
tweet: "What can we learn from nature cycles that #symfony and #php already naturally did? #monorepo #nature"
tweet_image: "assets/images/posts/2017/menstruation/together.png"
related_posts: [25, 26]
---

*Disclaimer: this post has no intention to put menstruation into any bad light. Exactly the opposite - I admire women and love to learn from them.*

## <strike>Technical</strike> Natural Releases
 
We will look on release management. Non from traditional technical point of view that is already described in [semantic versioning](http://semver.org/), [Symfony BC Promise](https://symfony.com/doc/current/contributing/code/bc.html) etc., but rather from view of *nature*.

**Why?** When I'm stuck with complicated architectural problem and can't figure it out, I take a break and **go for a walk**. **Just observing world around me**, absorbing inspiration without expectations - *serendipity*. 

To be honest, **many technologies we use originated from the nature**. Processor and hard drives from brain, camera lenses from eyes and design pattern from pattern of nature. What else are falling leaves of tree in autumn? An **event subscriber**.


### Menstruation Cycle as Inspiration

<img src="/assets/images/posts/2017/menstruation/periods.jpg" class="img-thumbnail">

When we look at our main ability to survive - to reproduce ourselves, we see it's very easy to follow. Menstruation comes in cycles of ~28 days. All 4 parts of period have special meaning for the body of woman and her ability to get pregnant. We **know when it happens, how long it takes and when it ends** (roughly).  

Now the interesting part: *can you imagine software having release cycles in 28 days?* 

Just keep reading.


### Four Seasons Cycle

<img src="/assets/images/posts/2017/menstruation/seasons.jpg" class="img-thumbnail">

A bit longer periodical system in nature that works for some time now. Again, each of 4 parts of this cycle has its meaning:

- *Spring* - growing and getting stronger
- *Summer* - reaching the peak and making seeds
- *Autumn* - preparing for rest, cleaning up
- *Winter* - peaceful time of sleep and retrospective

Every part takes 3 months and restarts every 12 month. **You just know when winter comes**.

  
Now back to the interesting part: *could you imagine having release plan synced with the year period?*

<br>

Also, did you know women that spend lot of time together **tend to sync their menstruation cycle with each other**? We will get back to the later software-wise.


## What is Basic Stone for Evolution?

When we look at those patterns of nature, they have one important sign in common. A sign that we can see more and more in software development - **predictability**.

You **know** when the winter comes or when **your spouse needs more attention and care** when her period starts. You can plan, you can prepare and you can learn this by heart. As a result, **you can focus on more dynamic things that are not to predictable** - like your emotions, ideas or **priorities on development of your project**.


### Symfony Cycle meets Nature Cycle

<img src="/assets/images/posts/2017/menstruation/symfony.jpg" class="img-thumbnail">

I first realized this at Fabien's talk about Symfony new release cycle on [SymfonyCon Paris 2015](https://pariscon2015.symfony.com/):

- **Major versions every 2 years, minor every 6 months.**

So simple yet so amazing. I don't think about "when will the new Symfony come?" any more.


### What about PHP?

<img src="/assets/images/posts/2017/menstruation/php.png" class="img-thumbnail">

I recall wondering when PHP 5.5, 5.6 or 7.0 will be out. No more thanks to **[yearly period beginning of December](http://php.net/supported-versions.php)** since PHP 7.0.


### Menstruation Synchronization

As I wrote earlier, women that spend lot of time together tend to sync their menstruation cycle with each other. Have you noticed that **Symfony matches PHP cycle every 2 years**? Coincidence? I&nbsp;don't think so. 

<img src="/assets/images/posts/2017/menstruation/together.png" class="img-thumbnail">

I think they're doing **the right thing** right. 


## Why We Should Menstruate Together?

We're getting back to software releases and monorepo. (If you see term *monorepo* first time, read [this legendary post by *danluu*](http://danluu.com/monorepo/)). 

Some people say that big disadvantage of monorepo is that **they have to tag their packages all together** (like Symfony) even if nothing changed in any of them. 

I see it as **advantage**, because that systematically leads to release cycle and open possibility to synchronization with other projects, like PHP + Symfony.  

### See for Yourself 

Which of these 3 applications would you pick to maintain and upgrade based on their `composer.json`?

<br>

**A** with *per-package* versioning: 


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

or **B** with [*per-vendor-sync*](https://getcomposer.org/doc/04-schema.md#name)

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


###  Advantages of Synced Vendor - project *B* 

Single version for every `symfony/*` package gives me so much freedom:

- to update it, I just change 1 version and run `composer update`
- they work the best together - I don't have to check if `symfony/console` 3.3 is or isn't compatible with `event-dispatcher` 4.0
- and **I know when** I can upgrade my application - **the first week in december every 2 years**


### Disadvantage of Per Package Versioning - project *A*

And what if every package has it's own destiny? 

- **I'm stressed when** any of my 20 `symfony/*` dependencies changes 
- I'm afraid that I will have to Google on Github, what version depend on which
- **I can't plan any upgrades**, because nobody knows the future


## Call Out to Package Maintainers

All this is not related just to Symfony, Doctrine, Nette or any [other big PHP players](https://gophp71.org/) like Zend, Laravel, CakePHP or Yii.

**Every package, every dependency that has own versioning system means increased work PHP developers**. That's stands if you agree with cycles or not. **Version C** being the easiest to upgrade and version **A** being the most difficult and also the most expensive. 

**Do you want to add extra work** to developer's back to study your vendor release system?

### Waiting for Tagging

On the other hand a *package user*, I bet you recall at least one package you **longed to be tagged, but were frustrated not knowing when**. It's quite common that first 3 versions are tagged in withing 1 year, but then followed by 1+ year long pause.


## Syncing with Symfony "menstruation" cycle

I like this synchronization, predictability, stability and maturation in our PHP ecosystem. That's why I'm trying to **synchronize with Symfony release cycles**. As from huge to small, as from seasons of year to menstruation, as from PHP to Symfony,

I bet you didn't notice, but with [Symplify](https://github.com/Symplify) I try to **release major version to minor of Symfony**. Version 2.0 was released on [July 6th 2017](https://github.com/Symplify/Symplify/releases/tag/v2.0.0), right after [Symfony 3.3 release](https://symfony.com/roadmap?version=3.3#checker) in May.

Being predictable with BC breaks, support for older version and consistent with adding new version.

### No Force, Just Inspiration

Do you maintain a package? What approach do you have on predictability of releases and why? Please, let me know in comments. I always love to hear new ideas.

<br>

Happy syncing!


