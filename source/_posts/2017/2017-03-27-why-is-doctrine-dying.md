---
id: 35
title: "Why Is Doctrine Dying"
perex: |
    Do you use Doctrine ORM? If so, do you follow its evolution on Github? Symfony is evolving, Laravel is evolving, Nette is evolving, world is evolving... Doctrine not. Today I will show you 3 reasons why.

deprecated: true
deprecated_since: "July 2018"
deprecated_message: |
    Doctrine came through <strong>many great and healthy changes during last ~18 months</strong>, so this statement is no longer true.
    <br><br>
    See <a href="/blog/2018/07/09/doctrine-is-alive-and-kicking/">Doctrine is Alive and Kicking post for more</a>.

related_items: [121]
---

I've been thinking over 2 years about this post. I wasn't sure if it's only a negative hype feeling or real thing. It's still the same so it's time to write about it.

## Doctrine is Awesome Tool

To be clear, I have been using Doctrine for many years and it is **the best ORM there is**. Of course there is [Propel ORM](http://propelorm.org/) and [Eloquent](https://laravel.com/docs/eloquent) from Laravel,
**but they use active record**.

I'm not an expert in databases, so active record might be actually a useful pattern, even architectonically, but I don't favor it now.


## Doctrine is stuck in its Legacy, Unable to Evolve

But the main I see, is that Doctrine is not evolving. Last useful feature was the **[2nd Level cache](http://docs.doctrine-project.org/projects/doctrine-orm/en/latest/reference/second-level-cache.html)** in `doctrine/orm` [released in 2015](https://github.com/doctrine/doctrine2/releases/tag/v2.5.0). In meaning not a few-person feature, but feature, that can influence most of applications.

Would you be still using Symfony if the last feature was released 2 years ago? **No, you would probably go for Laravel**, Yii2, CakePHP, Nette or **any other framework to see, if it works better for you**. Because we programmers evolve and we need to do the same work in less time and work.


### Who Caused This?

**This has nothing to do with code quality, number of active maintainers or too few people contributing.** Although all have great part in open-source life cycle.

To put you in my seat, I've maintained many own and foreign packages. They had difficulty to stay alive with same amount of energy invested. Does that sound like legacy code to you? Yep!

**I see this is rather a system setup.** As any other system, it can lead both ways - slow down, or speed up. Depends on the vector you prepare.


### Donate Your Liver, For Free

To show you an example how system can go both ways, I will show you something nice to do when you die.

Do you need your liver when you die? Somebody else might does, but it depends on your country's default law system, what will happen to it.

- In some countries you can save a person by default.
- In some countries you are just dead.

<div class="text-center">
    <img src="/assets/images/posts/2017/dying-doctrine/organ-donation.jpg" class="img-thumbnail">
</div>

Of course you can write an extra card saying "Take my organs when I die", but who will actually do it?



## Things That Might Save Doctrine

Where is death, there is life. I believe Doctrine or any slowly dying project can be saved - by just a system setup.


### 1. Create a Competition

"Where you are the best, there is no one to learn from, so you stop."

If Symfony would be the only framework, there would be no other choice, everyone would be using it. And Symfony would have no motivation to be better (better than who?).

I think Fabien was aware of that, so he wrote a series [Create your own framework... on top of the Symfony2 Components](http://fabien.potencier.org/create-your-own-framework-on-top-of-the-symfony2-components-part-1.html) in 2012, which promoted using only components of Symfony to create an own framework. So [Laravel](https://laravel.com) was made on top of Symfony components.

**Now these 2 frameworks are pushing industry standard like never before**. Awesome to watch!

There is no more powerful motivation than competition. More over in man's worlds. Missing one? Create one!



### 2. Using Monolithic Repository

I see Symfony, Laravel and many huge projects [using this pattern](/blog/2017/01/31/how-monolithic-repository-in-open-source-saved-my-laziness/) and I am profiting a lot from it. After ~8 years in open-source, I've tried too (don't judge, just try for yourself) and it's the best! Trust me.

**Having Doctrine (or any other project) split in over 20 repositories requires a lot of work**.

Now each repository:

- has own coding standard (if any)
- is tested on different database versions
- relies on hope that new change won't break other 19 packages
- has different maintainer signature and standards

That's how you spend more work on maintenance than on feature very easily.


### 3. Using Dependency Injection by Default

Last thing that might turn it to the right direction is Dependency Injection via Constructor, also called autowiring.
In many projects I see, while consulting big companies, this pattern **would save them from 20-30 % complexity they have now** (when used properly). Just by using it from the start.

1,5 year ago, I sent a pull-request for a simple thing - [having modular filters](https://github.com/doctrine/doctrine2/pull/1453). It is actually just a single-service constructor injection implementation. Huge problem to integrate. And there is a lot more static code like `new SomeService` that could be easily resolved with this.



## That's Why I'm Dropping My Doctrine Packages

I miss an evolution with Doctrine. When some packages are stuck for 2-3 years in one place, there is probably already a better replacement. Either on Packagist or in your mind.

As [I mentor and coach teams](/skoleni) to write better code with less work, **I'd go against my own beliefs by putting a vendor lock in their applications.**

That why I'm dropping my packages built on Doctrine.


### What Repositories are This Concerned?

- [Zenify/DoctrineFixtures](https://github.com/Zenify/DoctrineFixtures) - use [Nelmio/Alice](https://github.com/nelmio/alice) instead
- [Zenify/DoctrineBehaviors](https://github.com/Zenify/DoctrineBehaviors)
- [Zenify/DoctrineExtensionsTree](https://github.com/Zenify/DoctrineExtensionsTree) - use [minetro/doctrine-extensions](https://github.com/minetro/doctrine-extensions) instead
- [Zenify/DoctrineMigrations](https://github.com/Zenify/DoctrineMigrations) - use
https://github.com/robmorgan/phinx instead, they are [awesome and alive](https://php.libhunt.com/project/phinx/vs/doctrine-migrations)


## What about Your Project?

When you take a step back, you realize, all this can happen to your project too.
I recommend taking some time to reflect, where your project goes and what is the reason - you, company or project's system?


## Let me Know Your Opinion

What do you think? Do you see prosperity? Or alternatives like [NextrasORM](https://github.com/nextras/orm) or [Atlas ORM](https://github.com/atlasphp/Atlas.Orm)? Or even different pattern than ORM?
Let me know. My knowledge is very limited and I'd be happy to learn something new. Thank you!
