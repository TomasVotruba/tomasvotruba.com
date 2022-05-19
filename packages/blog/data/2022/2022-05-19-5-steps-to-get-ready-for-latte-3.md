---
id: 352
title: "5 Steps to Get Ready for Latte 3"
perex: |
    After 3 months of intensive testing, David released [Latte 3.0](https://github.com/nette/latte/releases/tag/v3.0.0) two days ago, with massive evolution under the hood. We've been using it in Amateri past weeks, and today I will share our experience with you about how **to get prepared for it**.
tweet: "New Post on the 🐘 blog: 5 steps to Get Ready for Latte 3    #nettefw"
---

Today, our goal is not to get your project to Latte 3. Developers of Latte Extensions will need a few weeks to catch up with the upgrade and test it, as macros have entirely new syntax.

Despite that, there are a few steps that we can do before the upgrade itself by getting ready to get on board the beautiful ship called Latte 3.

## 1. Replace `{ifCurrent x}` by `{if isLinkCurrent(...)}`

The macro `ifCurrent` calls function `isLinkCurrent` on the background. It will trigger an error in Latte 3.

Now, use the function directly instead:

```diff
 <a href="/friends" class="
-    {ifCurrent friends}active{/ifCurrent}
+    {if isLinkCurrent('friends')}active{/if}
 ">Friends</a>
```

```diff
-{ifCurrent 'meeting:lunch'}eat{/ifCurrent}
+{if isLinkCurrent('meeting:lunch')}win{/if}
```

Do you use PHPStorm? This is [the regex](https://github.com/TomasVotruba/barista/blob/fce40f5805cfbd529e4ae1bcdd22890db9a66164/src/Upgrade/IfCurrentLatteSyntaxUpgrader.php#L16) that handles change for you.

<br>

There is a particular case for the `n:ifCurrent` macro that is better to handle manually:

```diff
-<a href="/profile/change-password" n:ifCurrent="profile">Change Password</a>
+{if isLinkCurrent('profile')}
+    <a href="/profile/change-password" n:ifCurrent="profile">Change Password</a>
+{/if}
```

## 2. Get the Latest Latte 2.* version

This version is the latest you can get from 2.11.*. It does not break anything, and it helps you with the upgrade:

```bash
composer require latte/latte:^2.11.3
```

Do you have it in your project? Great, now we can use it in the next step ↓

## 3. Try Latte Linter Today

Syntax changes are [described in this forum post](https://forum.nette.org/cs/35141-latte-3-nejvetsi-vyvojovy-skok-v-dejinach-nette#p219574), but how many of those are in our existing project? Maybe all of them? In our project, it was just 3-4.

But how can we find them quickly? With the new Latte linter command. Just run it in CLI:

```bash
vendor/bin/latte templates
```

During our testing, someone assumed linter is an intelligent static analyzer (like PHPStan) that will not let you make mistakes, and it is not.
The *linter* only **checks the syntax of the Latte language is valid**. It does not have the context or logic of your Latte files, something like `php -l`.

It has a few spots to improve:

* The linter does not use it Engine of your projects. That means it will miss all the custom macros and filters. You can hack in your Latte Engine, but it seems like a heavy workaround.
* The CI will keep failing because of these errors, so you have to allow the job to fail or skip it completely
* It only accepts one or more directories as arguments, so when you can't check a single Latte file

David is committing improvement coming the last 2 days, so upcoming patch versions might change it.

<br>

Latte syntax is now more CASE sensitive than before. So thanks to this linter, we fixed a bug like this:

```diff
-{foreach $items AS $item}
+{foreach $items as $item}
```

Let me know in the comments what deprecations you've found in your code.

## 4. Prepare for the Translations

Since Latte 3, there are few changes in translate macros as we know them:

```html
{_$bookTitle}
```

The single `_` still works, but the dual `_` macro will stop working:

```html
{_}Nice tree! How do you grow it?{/_}
```

The good news is that it will replace by `translate` tag:

```html
{translate}Nice tree! How do you grow it?{/translate}
```

The `_` looks like `-` or `:`, which could be a language syntax. I think this is an improvement, as "translate" is self-explanatory.

Warning: the `translate` macro is unavailable in Latte 2.x. I think it's worth mentioning, as in our case, it means a change of hundreds of cases, and we have to prepare a plan to get ready for it.

## 5. Ping Your Latte 2 Dependencies

Let's say our project is ready, we've updated all macros, Latte syntax, and Latte linter works. We want to go... but wait, what about the external Latte packages we use? Maybe they're ready, maybe not.

We can find out easily by triggering the requirements:

```bash
composer require latte/latte:^3.0
```

Does the install pass? Congratulations, there is no blocker in your vendor.

Has the composer reported some conflicts? Help out these packages to:

* allow `latte/latte` 3.0
* fix possible BC breaks

So far, we have come across these Latte 3 blockers:

* https://github.com/milo/embedded-svg
* https://github.com/contributte/mailing (dev-master allows)
* https://github.com/nextras/mail-panel

How to upgrade a Latte extension to Latte 3? Stay tuned for the next post.

<br>

Happy coding!
