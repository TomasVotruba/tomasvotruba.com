---
id: 81
title: "Rector: Part 3 - Why Instant Upgrades"
perex: |
    Why are instant upgrades better than old school *manual upgrades*? Why is the path to find exact before/after like hell-road?
    Why you should use Rector and when?
todo_tweet: "New post on my blog: "
---

*Read also:*

- [Part 1 - What and How](/blog/2018/02/19/rector-part-1-what-and-how/)
- [Part 2 - Maturity of PHP Ecosystem and Founding Fathers](/blog/2018/02/26/rector-part-2-maturity-of-php-ecocystem-and-founding-fathers/)

<br>

## Why was Rector needed

You need 2 things to upgrade PHP application to newer version of the framework you use:

### 1. The Knowledge

Most of you follow changes in Symfony or Nette. You know that [`ProcessBuilder` was removed](https://github.com/symfony/symfony/blob/master/UPGRADE-4.0.md#process) and that you'll have to migrate to `Process` class only. You know that [`Nette\Object` was removed](https://forum.nette.org/cs/26250-pojdte-otestovat-nette-2-4-rc) and that you'll have to rewrite all `@method` annotation to real methods.

You read blogs, follow news on forum, read all `CHANGELOG.md` and `UPGRADE.md` files and sometimes commits, to find out what all has changed. **You have the knowledge**.

Or don't you? **Imagine you could drop delegate all this to a computer.**

### 2. The Resources

You work in a company where up-to-date it very important value. Your employer finances upgrades and also your education in it (pays you to get the knowledge). Once a 6 months you have dedicated paid time to update all packages to most recent versions. **You have the resources**

Do you find yourself in such situation? If so, **you belong to 5 % blessed and active people around me.**

## How Expensive are Upgrades Now?

From my experience with consulting over 50 PHP projects in last 4 years, it can take 80-400 hours per one minor version upgrade (e.g. Symfony 2.7 → 2.8), including all deprecations.

### 1. Teams are not Supported in Upgrades

Teams don't have space to find out what changed between Symfony 2.7 and Symfony 2.8. They don't care, because their employer cares mostly about creating new website as fast and as cheap as possible. And that's logical.

That's why most of [lectures](/mentoring-and-lectures) I make is about giving teams *the knowledge* that they can use with their very limited resources to make the best out of it.

Such approach also naturally leads to huge legacy code, team performance drop from 100 % to 20 %, which consequently leads to hiring 5x more people to keep productivity the same, more money, and pressure to faster development, which lead to huge legacy code...

### 2. Are Deprecations Easy to Find?

Let's say you have time to explore the Internet, follow [Symfony News on Twitter](https://twitter.com/symfony_en), read [every news post on Symfony Blog](https://symfony.com/blog/category/living-on-the-edge) or know where on the Nette forum are located [Release Notes](https://forum.nette.org/en/f78-release-announcements-news).

Sometimes if you're lucky there is `UPGRADE-x.md` in project's Github repository, like [`UPGRADE-4.0.md`](https://github.com/symfony/symfony/blob/master/UPGRADE-4.0.md) in Symfony 4 repository. But what if you need upgrade to version 3.x? Could you find it? Well no, but yes in [3.x branch](https://github.com/symfony/symfony/tree/3.4).

And sometimes these changes are in files called `CHANGELOG-x.md`. But more often **they're newer to be seen and you have to go `git blame` in Github specific line and hope for an answer**. And just pray that there is PR with more detailed changes with tests as well and not direct set of commits to the `master` branch without context.

<img src="/assets/images/posts/2018/rector-3/frustration.jpg" class="img-thumbnail">

### 3. When We Find Them, are They Valid?

Sometimes there can be bare useless description, like [in `UPGRADE-4.0`](https://github.com/symfony/symfony/blob/master/UPGRADE-4.0.md#process):

*The `Symfony\Component\Process\ProcessBuilder` class has been removed, use the `Symfony\Component\Process\Process` class directly instead.*

Do you mean like this?

```diff
-use Symfony\Component\Process\ProcessBuilder;
+use Symfony\Component\Process\Process;

-$builder = new ProcessBuilder();
+$builder = new Process();
 $builder->setArguments(['build', '-force', '-var "blah=blah"', 'path/to/json.json'])
    ->getProcess();
```

Unfortunately no and our investigative programming begins. Git blame..., Google? Symfony Docs?

Sometimes **they are embodied in the best place - the code**. Kudos to all developers who do it like that! When running this method, you'll be informed:

```php
public function add()
{
    trigger_error('Method add() is deprecated, use addHtml() instead.', E_USER_DEPRECATED);
}
```

### 4. Found them and Valid! But are They Standardized?

Sometimes the maintainer goes as far to deprecate code slowly (!= remove):

```php
trigger_error('Method add() is deprecated, use addHtml() instead.', E_USER_DEPRECATED);
```

Which is good enough for start. Symfony even has [PHPUnit Bridge](https://symfony.com/doc/current/components/phpunit_bridge.html) that tries to detect those deprecations. Would you know what exactly do you need to change?

<img src="/assets/images/posts/2018/rector-3/report.png" class="img-thumbnail">

But what class? What line?

But that's not the only "standard".

<a href="https://xkcd.com/927/
">
    <img src="https://imgs.xkcd.com/comics/standards.png" class="img-thumbnail">
</a>

There is one more way I wrote about in [How to write Open-Source in PHP 3: Deprecating Code](/blog/2017/09/11/how-to-write-open-source-in-php-3-deprecating-code/#today-s-topic-changed-method-name).

The `@deprecated` annotation:

```php
/**
 * @deprecated Method add() is deprecated, use addHtml() instead
 */
public function add()
{
    // ...
}
```

Which is rather note in the code than helpful to user, like concept post about that great idea your never published. **What happens when `->add()` method is called? Nothing.** And in next mayor version you get *calling non-existing method* error.

Similar tool to PHPUnit Bridge is [`deprecation-detector`](https://github.com/sensiolabs-de/deprecation-detector), that tries to catch code with `@deprecated` annotation. Again, not much standardized.

And that's Symfony, my friends, which **does the best job for [Backward Compatibility Promise](https://symfony.com/doc/current/contributing/code/bc.html) in PHP**. **What about those other 50 packages your application uses?**

### Outsource all this?

Would you like to do this job instead of developing your application? Most people wouldn't, so they hire me as a consultant to help them with it. **But would you hire somebody, who would download all dependencies your application need in correct version for you or would you rather start using composer?**

## Embodied Cognition instead of Investigative Programming

I consulted over 50 projects in great depth of legacy. We always tried to figure out, where to start.

- "This is how it's done in your project, and this is the way in Symfony."

or

- "This is how it's done in Symfony 2.8 and this is the way in Symfony 3.0."

### Legacy Projects === Well Paid Projects !== The Way Go

Over and over again, just version numbers and the desired framework change. After few years I started to feel like *a dumb copy-paster*. I follow every new feature on Symfony, test it, verify its usefulness, then distill 100 hours of my work to 3 hour lecture. I'm lazy and this started to itch my mind. Is this the really education I want to encourage in the world? Well, majority of lecturers do exactly same work, well paid work. But is that reason to do it too?

### Delegate → Procreate

I borrow a term from psychology - **embodied cognition**. It's something you don't have to remember, cause it's in you. It's like riding a bike. I don't know what words to use and where to find out how to ride a bike - I just know it, cause it's in  my internal reflexes.

Could something similar happen to upgrading applications? A single place that knows what to do and doesn't have to explain every programmer over and over again?

<br>

These all kinds of problems Rector solves for you.
