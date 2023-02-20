---
id: 300
title: "Effective Debug Tricks: Narrow Scoping"
perex: |
    Writing code that only you work with is easy. Debugging such code is a bit harder. Writing code for someone else review is quite hard. The code must be understandable to the other reader to pass the code review.


    How hard is reading code that someone else wrote three months ago?
    <br>
    <br>
    But what about debugging code that someone wrote a year ago?
tweet: "New Post on #php 🐘 blog: Effective Debug Tricks: Narrow Scoping"
---

Effective debugging is not about finding the responsible line, nor about fixing the bug.

Effective debugging is about **getting to the minimal amount of code that is *probably* causing the error** with the minimal energy invested.

## Why it's important?

When we report a bug in a project's issue tracker, we're usually interested in its fix. Why would we report if we don't care? **The faster the bug gets fixed, the better for us**, so we can continue with our original coding goal. We need [instant feedback](/blog/2020/01/13/why-is-first-instant-feedback-crucial-to-developers) to stay in the flow.

<br>

Today we look at one technique that I use for open-source project reports and for teaching private projects to debug their legacy code with peace.

We also have it as a standard in Rector and Symplify projects. It saves us many hours wasted on issues tracker comment ping-pong and focuses on the cooperative solution.

You might notice it's similar to * the minimum common denominator* process that you probably know from 5th grade—what a powerful concept.

<br>

## Level 1: "There is a bug" report

What will happen if we provide a report like this?

- *The printing does not work. It ends with a fatal error.*

**What can the maintainer do?** Can they fix the error and close the issue? Probably not, because there is no information about where to look first. So what will the maintainer do? They will ask us for more information:

- *ask us about the exact fatal error message*
- *ask us about stack trace with `--debug` or `-vvv` option in command line*
- *ask us about the version we use, because maybe this bug got fixed on `master` 2 days ago already and the work would be duplicated*

---

This interaction is a problem already because **you've lost energy that you're willing to invest** to report the bug and the **maintainer lost energy that he/she is willing to put** into narrow the scope of the bug.

It's like going to a mall, stopping right in front of the first door and shouting "open the door". It will take a couple of minutes for security to get to you and figure out what you want. Why not just take the handle yourself? Good luck next time you'll report a robbery to them - the will to help will be a bit lowered, if not depleted.

---

## Bugfix Effectiveness

Remember, the goal of reporting an issue is to invest as little energy from our side and maintainer's side and get the bug fixed at the same time.

The bugfix effectiveness formula states:

<blockquote class="blockquote text-center">
    Bugfix Effectiveness = (Reporter Work + Maintainer Work) / 100
</blockquote>

- If *BFE* < 0.5, you have both more energy left to fix even more bugs ✅

- If *BFE* > 5, the issue will probably end up in stale of dozens of comments that never fix anything ❌

<br>

So can we do it better for everyone?

## Level 2: "There is a bug with this full message" report

*The printing does not work, it ends with a fatal error.*

*Here is the output I got with `--debug`/`-vvv`:*
*...*

*I use Rector v0.9.4*

<br>

Good job! We've just saved both ourselves and the maintainer a significant amount of energy. Now the maintainer knows **what happens** to use and how it looks like.

---

Have you seen any detective movies lately? There is a murder, the detective arrives at the scene, and the first witness shows the body and location. That's what we just did in our issue report. "Here is somebody dead, I saw him at 22:00". What happens next? The detective smiles, says thank you, and goes home... that would be weird, right? They always ask the same question:

- "How did you get there?"
- "Where were you before?"
- "Do you know the person?"
- "If so, what was the relationship to him/her?"

The detective is looking for prerequisites. They need this information about you to get a specific idea of how you fit the whole picture. Maybe you're innocent; maybe you're a murderer. They don't know, but they have to decide - so they ask.

---

Could you figure out what the report is missing? Yes, *the prerequisites*.

We know what is wrong with your issue now. But the maintainer doesn't know, **what did you do before that you got yourself into this state**.

- What CLI command did you run?
- What controller did you open in your browser?

<br>

How can we do it better?

## Level 3: "I did this, then this happened" report

*I tried to run Rector on my project with this `rector.php`*

*I used `vendor/bin/rector process p src`*

*The printing does not work, it ends with a fatal error.*
*Here is the output I got with `--debug`/`-vvv`:*

*...*

*I use Rector v0.9.4*

<br>

**Great job!**

Now both you and the maintainers know:

- what steps were made
- what happened
- what is the reported fatal error
- where exactly the fatal error happened in the code

<br>

Do you remember Bugfix Effectiveness Formula?

<blockquote class="blockquote text-center">
    BFE = (Reporter Work + Maintainer Work) / 100
</blockquote>

From level 1 to 3, we invested a lot of more energy on first issue reporting:

Level 1:

<blockquote class="blockquote text-center" markdown=1>
BFE = (20 + 380) / 100 = **4.0**
</blockquote>

Level 3:

<blockquote class="blockquote text-center" markdown=1>
BFE = (40 + 160) / 100 = **2.0**
</blockquote>

But because the maintainer doesn't have to ask us more questions and doesn't have to reply to them, we **also increased the chance to get a bug fixed by ~100 %**.

<br>

There is one more step that we found to be the most effective. **It usually requires only one comment from the reporter and one reply from the maintainer**. Well, if you count closing the issue with pull-request as a comment.

How can we do it better? Don't worry; it's not about learning project test conventions and sending a failing pull-request.


## Level 4: "I did exactly this, then this happened" report

Let's look at the **narrow scoping**. What can we read from this report?

*I tried to run Rector on my project with this `rector.php`*

*I used `vendor/bin/rector process p src`*

- There are 1-INF rules and settings in `rector.php` and 1-INF files in the `/src` directory.
- One of rule and one of file is causing a bug.

The question is: **what rule and what file is causing this bug?**

We need to narrow the scope of `INF * INF` to `1 * 1`. How can we do it?

### Half-Half Cutting

This technique can be applied to services, to PHPStan rules, to Rector rules, to a coding standard, to registered event subscribers... to anything. A similar algorithm is used to sort files in an array.

The idea is comment out half of the configuration, run the tool and see if the bug still remains:

```diff
 use Rector\Config\RectorConfig;

 return function (RectorConfig $rectorConfig): void {
     $parameters = $containerConfigurator->parameters();
     $rectorConfig->importNames();

     $containerConfigurator->import(SetList::PHP_74);
     $containerConfigurator->import(SetList::PHP_80);
-    $containerConfigurator->import(SetList::CODE_QUALITY);
+//  $containerConfigurator->import(SetList::CODE_QUALITY);
-    $containerConfigurator->import(SetList::CODING_STYLE);
+//  $containerConfigurator->import(SetList::CODING_STYLE);
    ]);
```

Is the bug still there? Remove the commended lines and repeated the half-commented/half-active process with the other half:

```diff
 use Rector\Config\RectorConfig;

 return function (RectorConfig $rectorConfig): void {
     $rectorConfig->importNames();

     $rectorConfig->import(SetList::PHP_74);
-    $rectorConfig->import(SetList::PHP_80);
+//  $rectorConfig->import(SetList::PHP_80);
-    $rectorConfig->import(SetList::CODE_QUALITY);
-    $rectorConfig->import(SetList::CODING_STYLE);
 ]);
```

Is the bug gone? It must be one of those commented sets, so comment out half of them and let the ofher half active:

```diff
 use Rector\Config\RectorConfig;

 return function (RectorConfig $rectorConfig): void {
     $rectorConfig->importNames();

-    $rectorConfig->import(SetList::PHP_74);
-    $rectorConfig->import(SetList::PHP_80);
     $rectorConfig->import(SetList::CODE_QUALITY);
-    $rectorConfig->import(SetList::CODING_STYLE);
+//  $rectorConfig->import(SetList::CODING_STYLE);
 ]);
```

This way, we discover quickly which exact set and which exact rule is causing the problem.

<br>

But, running **the whole codebase to find out a single rule** might take hours. How can we avoid it?
Let's apply narrow scoping on the filesystem first. Instead of running tool globally:

```bash
vendor/bin/rector p src
```

Let's run it only on one directory:

```bash
vendor/bin/rector p src/Controllers
```

Is it not there? Pick another directory:

```bash
vendor/bin/rector p src/Repository
```

Is it there? Bingo!

<br>

Then we can report all the information that the maintainer needs:

*I tried to run Rector on my project with `rector.php`*

<br>

*When I run `TypedPropertyRector` rule like this*:

*`vendor/bin/rector process p src/Repository/AbstractRepository.php`*

*it fails with...*


We just reached the most effective value:

<blockquote class="blockquote text-center" markdown=1>
BFE = (60 + 60) / 100 = **1.2**
</blockquote>

Now we get **4-times more bugs fixed** then with level 1.
Thank you for reporting!

<br>

Happy coding!
