---
id: 373
title: "How can we Generate Unit Tests - Part 1: Testability Score"
perex: |
    As you have noticed, apart from writing micro-packages for PHPStan, my passion is exploring the field of **generated unit tests**. I want to share my thoughts on this topic with you to get your feedback and find a path to the best solution.


    You will learn a lot about making man-like working/thinking tools.


    I am still determining where this goes, but I love the journey. Let's dive in.
---

## The Journey Begins

I went to a pub with my friend Kerrial and a month and 2 days. It was a typical evening, restaurants were full due to Christmas parties, and we had to find a place with black beer (the only beer I love). We ended up in 4th class pub with local leftovers (the higher number, the worse), which was very suspicious regarding provided services. Lucky for us, the beer was good, and the food was even better.

<br>

Maybe it was this shockingly positive mood that got us into the following chat:

* "Man, this project I upgrade has no tests. It's like a minefield. They do it manually once in a while."
* "I feel you. My project has no tests too. They make so much money it covers the regression, but it's not sustainable, and there are no changes, and it's super stressful for me."
* "There must be a way to cover those with unit tests somehow, right?"
* "Well, we could use some mix of Rector, PHPStan, and AST to create tests, right? It's just a PHP code, after all..."

<br>

<blockquote class="twitter-tweet"><p lang="en" dir="ltr">Current status: in a pub for a beer with a friend.<br>Making algorithm for generating useful unit test from existing PHP code 🤗 <a href="https://t.co/HPrXwzfGwW">pic.twitter.com/HPrXwzfGwW</a></p>&mdash; Tomas Votruba 🇺🇦 (@VotrubaT) <a href="https://twitter.com/VotrubaT/status/1598768004212396041?ref_src=twsrc%5Etfw">December 2, 2022, </a></blockquote>


Since then, I have been thinking about this topic during open-source days and stuffing my belly with potato salad and chicken schnitzels. I've been hooked for a month, and experiments started to happen. Typical for me, I go almost fanatically deep into work to solve the problem or find out, but it's not possible (yet).

<br>

💡 Before we get into writing or generating tests themselves, we don't want to waste time on testing crap or burning out on fractal code tests. We have to make sure we know **what to test**.

Funnily enough, we'll see how raw technical thinking joins the practical philosophy of test value.

## What do we Test?

We have a new project that has 300 classes and zero tests. There is 0 % test coverage.
**How do we decide what to test first?**

The easiest item that comes to mind is a getter of scalar value, right?

```php
public function getName(): string
{
    return $this->name;
}
```

<br>

It's a simple method that returns a value. It's easy to test and find with [raw AST traversing](https://tomasvotruba.com/blog/2018/10/25/why-ast-fixes-your-coding-standard-better-than-tokens/).

```php
public function testGetName(): void
{
    $post = new Post();
    $post->setName('How to Increase Type Coverage');
    $this->assertSame('How to Increase Type Coverage', $post->getName());
}
```

That's it! With this single pattern, we can generate tests for all the scalar getters in our project. Wow!

<br>

But wait, what **is the added value of such a test**? We don't want to puke out test coverage just for its sake. I don't see any value in this test, and it's a test creep. Test that technically "test something", but it's not helpful.

We have to take care of the test, maintain it and extend it if the original code changes.
The test does not even test any logic; it only **tests the PHP works as expected**.

<br>

⚠️  &nbsp;<u>So no getters and setters!</u>

## What Else do We Test?

During the discussion, we came to an idea:

"You know what could be cool? If we test the repository that fetches the talk entities with Doctrine and traverses that all relations are set properly".

Yes, that would be cool, but...

<br>

💡 We want **tests that make us feel more confident** when:

* we change the code manually
* we run coding standard tool
* we run Rector with automated code modification or removal
* we run composer update
* without overlapping another tool that already handled it

<br>

What does the test above actually test?

* the database connection works
* The Doctrine package works

That's not our business logic, but **a 3rd party code**. We would test something that's already been tested somewhere else by the packages/tool provider. If we change our business logic and it breaks, these tests will never notice it.

<br>

⚠️  &nbsp;<u>So no 3rd party testing, neither external services.</u>

<br>

## 3 Groups of Code Testability

After a few similar mental iterations, we end up 3 groups:

* A. what not to test because it's way too simple, boring, or impossible
* B. what not to test because it's way too complex and tests 3rd party code
* C. what is left, our business logic, exclusively our code, that spreads across middle and complex operations

<br>

To give you an idea, here are a few examples for each group:

* A.
    * getters and setters
    * fetch of static property
    * method without a return value

* B.
    * controller action
    * code that uses 3rd party service in the constructor
    * class that extends a parent class

* C.
    * a public method that computes price value
    * foreach that returns value by provided key
    * a concat method that joins a few arguments, a local constant, and a number and returns a string value

<br>

This is our battle plan to find the low-hanging fruit - a public method that is very simple, but there has some logic worth testing.

## How do we Transfer it Into Code?

Remember, we have a new project that has 300 classes. With roughly 700-800 public methods. How do we find the ideal candidates for testing among them?

<br>

As you might have guessed, **we use nikic/php-parser** to get the AST!

1. we parse the PHP code into nodes
2. we traverse the nodes to find a public method
3. we evaluate the public method's **testability score** based on 3 groups above

<br>

## Testability Score

Testability score is a number from 0 to `INF` that says **how hard or pointless it is to test the method**. The lower score, the better.

* If the public class method is not in very popular group A or B, it gets 1000-10000 testability points.
* If it's in group C, the one we want to test, it gets only 100 points.

<br>

We will then write/generate a test for the method with **the lowest testability score**.

<br>

We create a testability judge node visitors for every criteria we could think of:

<img src="/assets/images/posts/2023/testability-judge.png" class="mb-3 mt-3 shadow img-thumbnail">

<br>

Then we run the script on our project and voilá:

<img src="/assets/images/posts/2023/testability-score.png" class="mb-3 mt-3 shadow img-thumbnail">

We get the best candidates to start testing on.

<br>

What if we find out that the `SpecialRoomType::getValues()` uses a static call that creates a database connection? We add a *testability judge* to give a score penalty for that and re-run the tool again.

## We have the Best Next Method to Test... What's Next?

I love that even if we stop the work here, we already have a lot of value. We have a list of methods to test that are not too complex and not too simple. We can start writing tests for them manually and actually enjoy it. I've done that for the first fine, to be sure :)

<br>

But we're lazier than that, right? We want to automate it. We want to generate tests for the methods we found. Stay tuned for the next part to see how we do that.


<script async src="https://platform.twitter.com/widgets.js" charset="utf-8"></script>
