---
id: 188
title: "How we Migrated from Nette to Symfony in 3 Weeks - Part 1"
perex: |
    On the break of January/February 2019, we **migrated whole [Entrydo](https://entry.do) project from Nette to Symfony**. It was API backend with no templates, but still, it wasn't as easy as I expected.


    Many ☕ and 🍺 were drunk during this migration. 0 programmers were too frustrated to give up.
    <br>
    Yet, you'd laugh if you knew what took us the most time.

---

And when I write we and 3 weeks, I mean myself and [Jan Mikeš](https://janmikes.cz) and **3 weeks of occasional codding**, not full-time. In total, we **spent around 30-40 hours** each on this migration.

<div class="text-center">
    <img src="/assets/images/posts/2019/nette-to-symfony/nette-to-symfony.png" class="img-thumbnail mt-5">
</div>

How did that happen? One day I met with Honza to cowo-talk. No big topics, you know:

<blockquote class="blockquote text-center">
"..."<br>
"Why don't you try Symfony?"<br>
"I don't have time to play with it. There's already pressure for new features, damn Tom!"<br>
"I'm pretty sure it's easier than you think. Let's give it 1 week and you'll see."
</blockquote>

And that's how it started. In this short series, we'll share our short story about nights without sleep, PHP code and Neon parsing and short-term pain of having no clue what that code does. Maybe even... happy ending?

## 1. Getting Ready 🤔

A year ago we tried to migrate from unmaintained Kdyby and Zenify packages to Contributte and other implementations. And we ended licking our burns.

That's why planning the migration is much more important than the migration itself. You have to talk about resources - **how much energy and time are we both willing to invest**. The problem with migration (or coding in general) is that there is a chance that 80 % will be done in 2 days, but then there is this 1 bug that makes most of the database tests fail. You try and try and fail and fail... and after a week you have depression and after 2 weeks you give up and never go back.

## 2. Making a Commitment 💍

Also, we had to **decide to give this a priority**. I was planning to put up new Pehapkari.cz website with training admin and new design and Honza had to deliver the feature. We had a time span of 3 weeks, month top, to do this. Everything else is secondary.

When one sleeps, the other codes. We called each other at 3 AM in the morning to talk about frustration, we shared the joy when one solved the issue. Sometimes we slept for 2 hours, then coded some more because we felt we are very close and this must be the day we finish it. In 20 cases it wasn't, **but we persisted**. We persisted because we decided to.

If one would decide in the middle of a migration to work for a week on another project, the mutual motivation could go to garbage very quickly. No way!

## 3. Automated Migration > Manual Changes 🤖

The basic idea was to do automated instant migration. Anything manually changes on more than 1 place is a potential future black hole.

We quickly discovered, it's better to use [PHP factories over config coding](/blog/2019/02/14/why-config-coding-sucks/) and [kill all parents we could](/blog/2019/01/24/how-to-kill-parents/) (except our own ones of course).


### Use Rector for PHP Changes

In the start, we run **Rector with generic rules with brute-force way**. Don't think, just try it. That gave us more idea about the code - we started to spot places we can write in Rector rule.

In the end, [Rector](https://getrector.com) helped us with many following changes:

- Response and Request ✅
- Presenter to Controller ✅
- RouterFactory to Controller Annotation ✅ - REST Routes and Invocable Controllers included
- Kdyby/Events and Contributte/Events to Symfony/EventDispatcher ✅
- Kdyby/Doctrine to Doctrine/DoctrineBundle ✅
- Kdyby/Events to Symfony/EventDispatcher ✅
- Kdyby/Translation to Symfony/Translation ✅
- Nette DI methods to Symfony/DependendyInjection ✅

The tricky part was to discover differences and create the bridge between both frameworks - *"In Nette, you use this, in Symfony you'd use this."*

**Now it's done**, so you can use them [in `NETTE_TO_SYMFONY` set](https://github.com/rectorphp/rector/blob/267989bb05372db937a5e9ece7f2d68cfdec34bf/config/set/nette-to-symfony.php) Rector set to migrate your code.

<br>

We didn't have to change any of these parts, because the code didn't use them:

- Components to Controllers ❌

- CompilerExtensions to Bundles/CompilerPasses ❌

Rector can automate them when some project will need them. Maybe your project :)

### Non-PHP Migrations

- <a href="/blog/2019/02/11/introducing-neon-to-yaml-converter/">Neon to YAML</a> ✅ - this package was created for needs of Entrydo

- <a href="/blog/2018/07/05/how-to-convert-latte-templates-to-twig-in-27-regular-expressions/">Latte to TWIG</a> ✅

### Coding Standards

After many many changes in the code, we didn't care about spaces, tabs or where the `{` is. That's the job of [EasyCodingStandard](https://github.com/symplify/easy-coding-standard). **We had to focus full attention on code structure**, not to play with style.

Actually, Nette uses tabs and Symfony spaces, so ECS actually helped with migration a lot too.

## 4. WTFs Everywhere! 🤦

This code worked in Nette:

```php
<?php

$this->translation->translate('Hi, my name is %name%', [
    'name' => 'Tom'
]);
```

In Nette:

```bash
Hy, my name is Tom.
```

After migration tests started to fail:

```bash
Hy, my name is %Tom%.
```

Damn! Why it's broken?

In this case, we had to look to the contents of the `translate` method of the previous service. Kdyby/Translation actually automatically [wraps key name to `%`](https://github.com/Kdyby/Translation/blob/6b0721c767a7be7f15b2fb13c529bea8536230aa/src/Translator.php#L172), while Symfony doesn't:

```diff
 <?php

 $this->translation->translate('Hi, my name is %name%', [
-    'name' => 'Tom'
+    '%name%' => 'Tom'
 ]);
```

**Problem solved** in 20 minutes, doh! Now it's part of Rector set, so you can actually forget this paragraph. But be ready for such issues. **The hardest problems are usually behind simple differences** - like extra `%`.

<br>

Luckily, Honza is xdebug expert, so he deep dives into the code to find the spots where code failed. It took him some time, but in the end, he **disclosed magic and fixed all the issues we had**. You can read more about that, Doctrine events and HttpRequest as a services migration in the next post.

## There are no Happy Endings...

...or are they?

- Do you wonder how the project ended-up?
- How many changes the PR has?
- When and *if* will be the migration merged?

The project was published in staging for 2 days. The answer to the last question is *Happy Valentine*. That's the day **the Symfony application was published to production**.
