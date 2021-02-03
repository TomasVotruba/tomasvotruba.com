---
id: 300
title: "Effective Debug Tricks: Narrow Scoping"
perex: |
    Writing a code that only you work with is easy. Debugging such code is a bit harder. Writing a code that someone else reviews is quite hard. We must past the code review, the code must be understandable to the other reader.
    <br><br>
How hard is reading a code that someone else wrote 3 months ago?
    <br>
    <br>
    But what about debugging a code that someone wrote a year ago?
tweet: "New Post on #php 🐘 blog: Effective Debug Tricks: Narrow Scoping"
---

The effective debugging is not about finding the responsible line,  not about it's not about fixing the bug.

The effective debugging is about **getting to the minimal amount of code that is *probably* causing the error**.


## Why it's important?

When we report a bug in a project's issue tracker, we're usually interested in its fix. Why would we report if really don't care? **The faster bug gets fixed the better for us**, so we can continue with our original coding goal. We need [instant feedback](/blog/2020/01/13/why-is-first-instant-feedback-crucial-to-developers) to stay in the flow.

<br>

Today we look a on technique, that I use for open-source projects report and for teaching private projects to debug their legacy code with peace. We also have it as a standard in Rector and Symplify projects. It saves us many hours wasted on issues tracker comment ping-pong and help us to focus on the cooperative solution.

You might notice it's similar to *minimum common denominator* process that you probably know from 5th grade. What a powerful concept.

<br>

## Level 1: "There is a bug" report

What will happen if we provide report like this?

- *The printing does not work, it ends with fatal error.*

**What can the maintainer do?** Can they fix the error and close the issue? Probably not, because there is no information about where to look first. So what will the maintainer do? They will ask us for more information:

- *ask us about the exact fatal error message*
- *ask us about stack trace with `--debug` or `-vvv` option in command line*
- *ask us about version we use, because maybe this bug got fixed on `master` 2 days ago already and the work would be duplicated*

---

This interaction is a problem already, because **you've lost energy that you're willing to invest** to reporting the bug and the **maintainer lost energy that is willing to put** into narrow the scope of bug.

It's like going to a mall, stopping right in front of the first door and shouting "open the door". It will take couple of minutes time to security to get to you and figure out what you want. Why not just take the handle yourself? Good luck next time you'll report a robbery to them - they will to help will be a bit lowered, if not depleted.

---

## Bug Fix Effectiveness

Remember, the goal of reporting an issue, is to invest as little energy from our side and maintainer's side and get the bug fixed at the same time. The bug report effectivity formula could be written like this:

<blockquote class="blockquote text-center">
    Bug Fix Effectiveness = (Reporter Work + Maintainer Work) / 1
</blockquote>

- If *Bug Fix Effectiveness* = 0.01, you have both more energy left to fix even more bugs
- If *Bug Fix Effectiveness* = 5, the issue will probably end up in stale of dozens of comments that never fix anything

<br>

So can we do it better for everyone?

## Level 2: "There is a bug with this full message" report

*The printing does not work, it ends with fatal error.*

*Here is the output I got with `--debug`/`-vvv`:*
*...*

*I use Rector v0.9.4*

<br>

Good job! we've just saved both ourselves and the maintainer a big amount of energy. Now the maintainer know **what happens** to use and how it looks like.

---

Have you seen any detective movie lately? There is a murder, the detective arrives to the scene and the first witness shows the body and location. That's what we just did in our issue report. "Here is somebody dead, I saw him at 22:00". What happens next? The detective smiles, says thank you and goes home... that would be weird, right? They always ask the same question:

- "How did you get there?"
- "Where were you before?"
- "Do you know the person?"
- "If so, what was the relationship to him/her?"

The detective is looking for prerequisites. They need this information about you to get specific idea how you fit the whole picture. Maybe you're innocent, maybe you're a murderer. They don't know, but they have to decided - so they ask.

---

Could you figure out, what is the report missing? Yes, *the prerequisites*.

We know what is wrong with your issue now. But the maintainer doesn't know, **what did you do before that you got yourself into this state**.

- What CLI command did you run?
- What controller did you click on?

<br>

How can we do it better?

## Level 3: "I did this, then this happened" report

*I tried to run Rector on my project with this `rector.php`*
*I used `vendor/bin/rector process p src`*

*The printing does not work, it ends with fatal error.*
*Here is the output I got with `--debug`/`-vvv`:*

*...*

*I use Rector v0.9.4*

<br>

**Great job!**

Now both you and the maintainers now:

- what steps were made
- what happened
- what is the reported fatal error
- where exactly the fatal error happened in the code

<br>

Do you remember Bug Fix Effectiveness Formula?

<blockquote class="blockquote text-center">
    Bug Fix Effectiveness = (Reporter Work + Maintainer Work) / 1
</blockquote>

From level 1 to 3, we invested lot of more energy on first issue reporting:

Level 1:

- Bug Fix Effectiveness = (0.2 + 3.8) / 1 = 4.0

Level 3:

- Bug Fix Effectiveness = (0.4 + 1.6) / 1 = 2.0

But because the maintainer don't have to ask us more questoins and we don't have to reply them, we **also increased change to get bug fixed by ~100 %**.

<br>

There is one more step that we found to be the most effective. **It usually requires only 1 comment from report and 1 reply from the maintainer**. Well, if you count closing the issue with pull-request as a comment.

How can we do it better? Don't worry, it's not about learning project tests conventions and sending a failing pull-request.


## Level 4: "I did exactly this, then this happened" report

Let's look at the **narrow scoping**. What we can read from this report?

*I tried to run Rector on my project with this `rector.php`*
*I used `vendor/bin/rector process p src`*

- There is 1-INF rules and settings in `rector.php` and 1-INF files in `/src` directory.
- One of rule and one of rile is causing a bug.

The question is: **what rule and what file is causing this bug?**

We need to narrow the scope of INF * INF, to 1 * 1. How can we do it?

### Half-Half Cutting

This technique can be applied to services, to PHPStan rule, to Rector rules, to coding standard, to registered event subscribers... to anything. Similar algorithm is used to sort files in an array.

The idea is comment out half of the configuration, run the tool and see if the bug still remains:

```diff
 // rector.php
 return static function (ContainerConfigurator $containerConfigurator): void {
     $parameters->set(Option::AUTO_IMPORT_NAMES, true);

     $parameters->set(Option::SETS, [
         SetList::PHP_74,
         SetList::PHP_80,
-        SetList::CODE_QUALITY,
+//      SetList::CODE_QUALITY,
-        SetList::CODING_STYLE,
+//      SetList::CODING_STYLE,
    ]);
```

Is the bug still there? Let's go with other half:

```diff
 // rector.php
 return static function (ContainerConfigurator $containerConfigurator): void {
     $parameters->set(Option::AUTO_IMPORT_NAMES, true);

     $parameters->set(Option::SETS, [
         SetList::PHP_74,
-        SetList::PHP_80,
+//      SetList::PHP_80,
-        SetList::CODE_QUALITY,
-        SetList::CODING_STYLE,
    ]);
```

Is the bug gone? It must be one of them:

```diff
 // rector.php
 return static function (ContainerConfigurator $containerConfigurator): void {
     $parameters->set(Option::AUTO_IMPORT_NAMES, true);

     $parameters->set(Option::SETS, [
-        SetList::PHP_74,
-        SetList::PHP_80,
         SetList::CODE_QUALITY,
-        SetList::CODING_STYLE,
+//      SetList::CODING_STYLE,
    ]);
```

This way we discovery quickly which exact set and which exact rule is causing the problem.

<br>

But, running whole code base to find out single rule might take hours. How can we avoid it?
Let's apply narrow scoping on the filesystem first. Instead of running tool globally:

```bash
vendor/bin/rector p src
```

Let's run it only on one directory:

```bash
vendor/bin/rector p src/Controllers
```

It's not there? Pick another directory:

```bash
vendor/bin/rector p src/Repository
```

It's there, bingo!

<br>

Then we can report all the information that maintainer needs.

*I tried to run Rector on my project with `rector.php`*
*When I run `TypedPropertyRector` like this:
*I used `vendor/bin/rector process p src/Repository/AbstractRepository.php`*

*it fails with...*


We just reached the most effective value:

<blockquote class="blockquote text-center">
    Bug Fix Effectiveness = (0.6 + 0.6) / 1 = 1.2
</blockquote>

Thank you for reporting!

<br>

Happy coding!
