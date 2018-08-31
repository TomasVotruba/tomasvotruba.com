---
id: 136
title: "Ways I Fucked Up Open Source Code: Mock Everything and Test Units"
perex: |
    In a normal job, decisions are made by those above you. They pay you and when it goes down, you leave in 2 months. Open-source code is different because **you're the one making choices but also the one who deals with results when it fails**. Moreover, if you love the project and want to spend years with it.
    <br>
    <br>
    Today I'll show you one of my many fuckups - let's mock units!
tweet: "Ways I Fucked Up #opensource Code: Mock Everything and Test Units #php #phpunit #mocking #failfastfailsafe #learning"
related_items: [53]
---

I was using [ApiGen](https://github.com/apigen/apigen) back in 2014. I had no commit for past 2 years so no surprise it didn't work for PHP 5.5+. I was young inexperienced... *a complainer* in that time, so I created issues and I blamed every contributor for creating such a bad project.

At the same time, I had a baby on the way and I didn't want to teach him to complain. *Change it or let it go* - that's what I wanted to teach him, so I wrote an email to Jarda, joined the project, **started making the code better and failing at it**.

## Mock (Almost) Everything

The project had 0 % coverage, so every line I changed raised my heart-beat and blood pressure to enormous levels. I broke completely unrelated code (= regression bug) many times making the project even more bugged than it was before I came.

"Let's increase the coverage to 90 %", I thought. I read this number on the Internet, so it had to be true. In that time I was pretty new to unit testing and mocking was on the hype. I didn't know that *hype* is *fake success* and that everything popular needs to be tested first instead of blindly integrated.

I took mocking, aimed for a high coverage and make unit tests strictly unit. Each class had standalone. After all, that will make the project, successful and easy to maintain. That what I thought at least. I managed to go to 84 % of coverage and burn out in the process. But it was "clean" and I thought the open-source is hard to make, so it *felt* right.


## How I Found Out I Failed?

The story could end here with success final note. I turned 0  % coverage to 84 % on a project I never coded before. I used mocks and unit test - that's cool and great, right? It wasn't until 2 years later I worked on the code again to experience the real "added" value. Without exposing to feedback there is no place to improve.

<img src="/assets/images/posts/2018/fuckups/before-after.png" class="img-thumbnail">

ApiGen used 3rd party reflection package that was not developed for 4 years in that time. The only reasonable way to add PHP 5.5+ and features was to use BetterReflection. I won't go in much details here, but you can [read the whole story of migration here](/blog/2017/09/04/how-apigen-survived-its-own-death/).

<img src="/assets/images/posts/2018/fuckups/change.png" class="img-thumbnail">

The specific change is not that important. It might have been another situation like Symfony\Console was dead for 5 years and a new better package needed to replace it, or PHP 8.0 is out with many new API changes. The important part is mocking and unit testing. **Do you know what happens if you have 84 % test coverage, mostly unit-tested with mocks and you need to switch 1 single package**?
Refactoring usually mean that you replace the code in `/src` with a new one, run tests and fixes anomalies. Not in this case! I had to refactor all the unit tests to respect the new API. Rename every single class, rename every single method in your tests and find out how they work internally and re-simulate they behavior with the new package:

```diff
-/** @var OldReflectionClass|\PHPUnit\Framework\MockObject\MockObject $oldReflectionClassMock */
+/** @var NewReflectionClass|\PHPUnit\Framework\MockObject\MockObject $oldReflectionClassMock */
-$reflectionClassMock = $this->createMock('OldReflectionClass');
+$reflectionClassMock = $this->createMock('NewReflectionClass');
-$reflectionClass->method('oldReflectionMethod')
+$reflectionClass->method('newReflectionMethod')
-    ->willReturnCallback(...);
+    ->willReturnCallback(...);
```

Uff. I was refactoring old code to a new one for weeks. It was hell, hell that made me think why I'm doing it like this? What I **really need**? **I really needed to develop safely when working code of ApiGen and enjoy it**. If I don't enjoy it, I burn out and no matter how "professional" code looks like, it will perish in the past. *Better done than perfect*.

Let's turn it into the code language:

```php
<?php declare(strict_types=1);

namespace Apigen\Tests;

use PHPUnit\Framework\TestCase;

final class ApiGenTest extends TestCase
{
    public function test()
    {
        exec('bin/apigen tests/test-source --output tests/generated-source');

        $this->assertSame('tests/expected-source', 'tests/generated-source');
    }
}
```

This is clearly opposite extreme thinking, that has its flaws. Mock everything and unit test every single class? Run just bin file and before/after? In the end, I found the best deal is somewhere in the middle:

- Instead of testing the lowest levels, **I started to use the main parts of the application with before/after approach**. You can see it nicely in this [Symplify\CodingStandard test](https://github.com/Symplify/Symplify/blob/e35b7e0564e08028f626241ca4860123c29a5b5e/packages/CodingStandard/tests/Fixer/Property/ArrayPropertyDefaultValueFixer/ArrayPropertyDefaultValueFixerTest.php#L34-L40). They've proven to be easily extendable and easier to understand. You see PHP code before and PHP code after. Good old common sense.

- Instead of mocking, **I started to use [anonymous classes](/blog/2018/06/11/how-to-turn-mocks-from-nightmare-to-solid-kiss-tests/)**. They've proven to be readable, programmers understand them (it's PHP code, you know) and there are no strings or plugins attached.

## What I Learned From this Fail?

**I ask more** and go for experience and small experiments.

### 1. Is it Hype or is it Quality?

Why do I think it's a good solution? Is it based on opinions or experience? If the first one, I make a little experiment to know and prevent huge consequences. I also found out that famous people are taken seriously... or rather miss taken seriously. Without knowing their experience and whys, people take blindly their statements. Take me as an example - I went for strict unit testing and 90 % coverage without really knowing why.

These statements are usually out of context and **based on their specific experience**. I might be working with 10-years-old PHP project where there is no space (budget) for automated refactorings or code-self-improvement, so I go for unit tests of everything I can. But **does it make sense in your context** where your leader is more educated and focuses on the effectivity in the long-run over today?

Advice have meaning in their context, **you have to create your own**. The same applies to my me. I share my own views, that is based on my own experience. It might work for you, but it doesn't mean it's right for everybody. Even if I don't write explicitly in every post my whole history, try to think about the background I'm from, what projects I work on and how that relates to you.

This *false positive* is called [anecdotal evidence](https://www.google.cz/search?q=anecdotal+evidence+example) in psychology research.

<img src="https://pixfeeds.com/images/32/608973/1200-608973-7125124.jpg" class="img-thumbnail">

*Fail fast and fail safe.* Try 2 frameworks to understand the first one, try [3 e-commerce projects to make yours better](/blog/2017/10/02/easy-coding-standard-and-phpstan-meet-3-symfony-ecommerce-projects/), try 10-20 projects to understand yours.

### 2. What Maintenance Cost it Brings?

If I'd work in the agency that takes every year new project, I would not mind. I would create code that somebody else would have to rewrite in 5 years and got paid for it. But **I prefer long-term projects and the challenge of keeping them fit and slim even after many years of development**.

Every feature will make something better, so it doesn't make sense to ask if the project will be better with it. Even the school system gives you something. That's not the point. Instead for every new code feature, I ask - **is added value bigger than an increase in maintenance cost it adds to our daily routine**? I mean it's great to have online support form in javascript, but if it takes 1 hour daily due to bugs of the unstable package... - again, there is space for an experiment.

<br>

## Out of Context Takeaways

So that's my fuckup story based on repeated in headlines and one-line advice I've heard.

What should you take as one-line takeaways?

- Personal experience from fast and safe experiment over external ideas out of context.
- Stick with a project at least for a year, to see consequences of your early decisions.
- It's ok to fail. It's not ok to fail over and over again with the same mistake.
- Go out and fail to learn!

<br>

Happy failing!
