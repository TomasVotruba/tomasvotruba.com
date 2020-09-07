---
id: 276
title: "How Static&nbsp;Methods Kill&nbsp;You Like&nbsp;Corona"
perex: |
    Do you know [the Broken Window theory](https://blog.codinghorror.com/the-broken-window-theory/)? It's a social pattern in code, a great post written by a guy behind one small manual website - StackOverflow.
    <br><br>
    If you combine this theory and Static Methods in Your code, you'll get Corona. As we already know, it might or might not be deadly. How it spreads, what are ways to protect, can we cure it?

tweet: "New Post on #php 🐘 blog: How Static Methods Kill You Like Corona"
tweet_image: "/assets/images/posts/static_corona_grand_parents.jpg"
---

## Single Child in Classroom Problem

It's September here, and many of friends walked their children to the school. Some for the first time in their life. Everyone is very cautious because it takes precisely **1 corona-infected** child, parent, or teacher to spread to another person very effectively.

<img src="/assets/images/posts/static_corona_child.jpg" class="img-thumbnail">

Some of my friends are teachers, and their prognosis is that most schools get closed down till the end of September. Why? Because if you have 1 infected child in a classroom of 30 children, all their teachers and schoolmates can be infected too. The problem is, tests take too long. **It's not instant feedback**. So we have to wait, hope, make up rationalizations in the meantime.

Playing statistics with fear now, but I think you've already got used to it from the news for the last 9 months.

## Elderly and Weak People are the Corona Goal

"Ok, so few schools get closed down, what's a big of a deal?" you might think. And you might be right. There is not much health risk in not going to a place. Have you heard of a person that died from not going to the cinema?

The problem is when the corona spreads to fragile people - the elderly and those with other illnesses. What happens when a lot of young and strong people get infected? Let's say they have a temperature, but no-one dies. As everything is connected in today's world, this **speeds up corona spread** between *strong population* and *weak population* and puts enormous danger on the latter.

So much for corona.

## Corona Infected Child in a Classroom is like a Static Method

If we only were as conscious and reacted fast with static methods as we react with a single corona infected person. **But don't care, because it's [not instant feedback](/blog/2020/01/13/why-is-first-instant-feedback-crucial-to-developers/)**.

- Imagine *static methods* is like a strong population infected by corona. They can go anywhere; they don't care; they will live.

- Imagine *service methods* (non-static) are like a weak population; they were not infected yet. They're ok, for now.

## "Show me Some Code!"

Do you want real meat? Let's look at code that can put down the company for many months:

### Strong Population

```php
final class InfectedYoungChild
{
    public static function spreadInfection()
    {
        // ...
    }
}
```

```php
final class InfectedYoungParent
{
    public static function spreadInfection()
    {
        // ...
    }
}
```

```php
final class ClassRoom
{
    public static function spreadInfection()
    {
        InfectedYoungChild::spreadInfection();
        // ...
        InfectedYoungParent::spreadInfection();
        // ...
    }
}
```

Imagine 100 more static methods in your code, so we save some paper.
To give your real-life example, one of the projects we migrate now has over 350 static methods in 110 000 lines of code.

### Weak Population

Let's say grandma feels lonely and **wants to see their grandchildren**, that gives her joy and purpose to live.

<img src="/assets/images/posts/static_corona_grand_parents.jpg" class="img-thumbnail">

At least once in a *while* (pun intended!):

```php
final class HealthyGrandma
{
    public function seeGrandChildren(array $youngChildren)
    {
        foreach ($youngChildren as $youngChild) {
            if ($youngChild instanceof HuggableInterfae) {
                $youngChild->hug($this);
            }
        }
    }
}
```

But she's afraid, so she wants to be sure her grandchild won't kill her.

```php
final class HealthyGrandma
{
    public function seeGrandChildren(array $youngChildren)
    {
        foreach ($youngChildren as $youngChild) {
            if ($youngChild instanceof InfectableInterface) {
                if ($youngChild->isInfected()) {
                    // the child is infected, can't happen, sorry :(
                    continue;
                }
            }

            if ($youngChild instanceof HuggableInterfae) {
                $youngChild->hug($this);
            }
        }
    }
}
```

## The Italy Mayhem Problem

How can we detect `InfectableInterface` in a static class? How can we collect data about infections in static classes?

We can't. And that's what happened in Italy this spring. Little control over the strong and weak part of your code population.

What can we do about it? I know you won't like it, [it's a lot of work](/blog/2019/04/01/removing-static-there-and-back-again/) that has no impact right now - refactor static to non-static:

```diff
-final class InfectedYoungChild
+final class InfectedYoungChild implements InfectableInterface
 {
-    public static function spreadInfection()
+    public function spreadInfection()
     {
         // ...
     }
 }
```

And also...

```diff
-final class InfectedYoungParent
+final class InfectedYoungParent implements InfectableInterface
{
-    public static function spreadInfection()
+    public function spreadInfection()
     {
         // ...
     }
 }
```

**And mostly, all their dependencies... and their dependencies...**

```diff
-final class ClassRoom
+final class ClassRoom implements InfectalbeImterface
 {
+    private InfectedYoungChild $infectedYoungChild;
+
+    InfectedYoungParent $infectedYoungParent
+
+    public fuction __construct(
+        InfectedYoungChild $infectedYoungChild,
+        InfectedYoungParent $infectedYoungParent
+    ) {
+         $this->infectedYoungChild = $infectedYoungChild;
+         $this->infectedYoungParent = $infectedYoungParent;
+    }

-    public static function spreadInfection()
+    public function spreadInfection()
     {
-        InfectedYoungChild::spreadInfection();
+        $this->infectedYoungChild->spreadInfection();
         // ...
-        InfectedYoungParent::spreadInfection();
+        $this->infectedYoungParent->spreadInfection();
         // ...
     }
 }
```

This is the only way to save [from fractal static spread](/blog/2018/04/26/how-i-got-into-static-trap-and-made-fool-of-myself/).

<br>
<br>


**It might take time, work, effort, sweat & tears, but it's worth it for the future.**

<br>

<img src="/assets/images/posts/static_corona_safe.jpg" class="img-thumbnail">

<br>

<blockquote class="blockquote text-center" markdown=1>
Great programmers do not live for today's delivered features,<br>
they live for [the top of the mountain](/blog/2018/04/30/programming-climbing-a-huge-mountain/) in years of work.
</blockquote>

<br>

## What if We Find the Corona Cure?

```php
$coronaCure = new CoronaCure();

$infectables = $container->getByType(InfectableInteface::class);

foreach ($infectables as $infectable) {
    if (! $infectable->isInfected()) {
        continue;
    }

    $coronaCure->cure($infectable);
}
```

Be sure to have all your services in your container, so they're available **when you need them**.

<br>

Happy coding!
