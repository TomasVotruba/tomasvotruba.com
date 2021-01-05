---
id: 295
title: "Why Coding Standards Should Not Be Part of CI"
perex: |
    Why do you use coding standards? To standardize design of your code, so any contributor will produce somewhat similar PHP code.
    <br><br>
    Where do we use it? In CI pipelines on every pull-request, locally in command line or withing PHPStorm.
    **Now take these 3 places and drop coding standard from them.**
tweet: "New Post on #php 🐘 blog: Why Coding Standard Should Not Be Part of CI"
---

"What?" Wait a sec, we'll get to that.

Do you use using PHPStorm plugins for coding standard? I [argued it's a waste of time](/blog/2019/06/24/do-you-use-php-codesniffer-and-php-cs-fixer-phpstorm-plugin-you-are-slow-and-expensive/) and that you should move to CI. In recent months, I come to a conclusion, **that even coding standard in CI is rather limiting for daily work**. We have to wait for CI feedback, process the feedback and pay for CI work-minutes.

It can be optimized to better form. Why even care? [Because faster CI feedback is](/blog/2020/01/13/why-is-first-instant-feedback-crucial-to-developers/), the addictive is coding against it. We'll get to that.

<br>

What do we actually talk about when we say "coding standards"?

## 1. Coding Standard is about How Code Looks

If we run coding standard on our code, the only thing that should change is **its design**:

```diff
-   class    SomeClass { }
+class SomeClass
+{
+}
```

There features and the logic should remain the same.

- Does your coding standard handle refactoring or upgrades? It should be by [Rector](https://github.com/rectorphp/rector).
- Does your coding standard tell you that `dump()` was left in the code? [PHPStan](https://github.com/phpstan/phpstan) should handle that.

## 2. Coding Standard is Deterministic

**Deterministic means that each input has exactly one output**.

E.g. you have a car. If you turn its steering wheel to the left, the car will turn left. If you turn it right, the car will turn right. If turning steering wheel one direction does not produce same output in same conditions, we know that car is broken or we're too drunk. We should stop driving in both cases as in can have non-deterministic consequences.

Same applies for coding standards. If we say A → we must add B.

- We don't use composed constants → we use each on their own line.

```diff
-const ONE = 'two', TWO = 'one';
+const ONE = 'two';
+const TWO = 'one';
```

- We don't use old arrays → we use new ones.

```diff
-$items = array(
+$items = [
     // ...
-);
+];
```

We can't say: "we don't use old arrays → we use anything else". That's not coding standard, but unfinished opinion.

## 3. Coding Standard Rules must be Fixable

Let's get back to the car. Steering wheel is deterministic, turn it left → car turns left, turn it right → car turns right. If we turn left and turns right, we know the car paradigm is broken and take it into car repair shop. They already know how car should behave without us telling them. They know **how to fix it**, no need to discuss car standards.

Same applies for coding standard:

```diff
-$items = array(
+$items = [
     // ...
-);
+];
```

That's why every Coding Standard rule **should be fixable by default**. Actually, [PHP CS Fixer](https://github.com/FriendsOfPHP/PHP-CS-Fixer) is build on this premise and each rule is a fixer by default. Good job!

If it's not possible to come up with fixable version, a PHPStan rule should handle this case.

<br>

What happens if apply point 1, 2 and 3 to our coding standard set? It handles code design, it is deterministic and it is fixable. That looks like work for robots...

## Why do we put Coding Standards into CI?

Well, one reason is very obvious - *status quo*, we're used to that.

Also, we want the code look the same on every merge. Apart that, there is not much to say.

## How Coding Standard in CI Wastes Time?

If we have a project with 15 pull-request a day and coding standard run in CI for every pull-request, that's at least 15 runs a day.
One run can take 3 mins on average, that's 45 minutes. What happens when coding standard fails? We have to check it manually, fix it and push the commit into the branch.

The circle repeats. That's **at least 90 minutes of waiting time wasted a day for your whole team**. Also, now robots are controlling humans. Our CI is adding extra work to developers and developers have to silently obey to make CI robot happy. Dooms day already?

<br>

Let's get back to the main purpose of coding standards. Our PHP code should look the same.

Could you think of better solution that does not waste 90 minutes a days and turn us into slaves, but keeps our PHP code looks nice and pretty?

<br>

Happy coding!
