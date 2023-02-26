---
id: 203
title: "Pattern Refactoring"
perex: |
    In [Removing Static - There and Back Again](/blog/2019/04/01/removing-static-there-and-back-again/) post we tried looked at anti-patterns in legacy code from a new point of view. It can be static in your code, it can be active record pattern you needed for fast bootstrapping of your idea, it can be moving from the code in controllers to command bus.

    **They can be coupled in your code in hundreds of classes. That's a big problem, you might think, but it's only single pattern**.
---

## Use Your Personal Preferences

For pattern, refactoring is not important what I believe is the best or is considered *general best practice* (if such weak thing can even exist). I'm kicking myself in the nuts now, but I feel I have to write it.

No external consultant or blog post can give you a qualified answer on what to do with a code he or she saw for a few days. I mean, they can give you tip and qualified feedback, because they saw dozens of similar code bases, but in the end, it's up to you to do the experiment and verify it on your code base.

The best thing in the code is decided by the team, that works with the code every day.

## And for a Time, It Was Good

Let's look at our use case inspired by the problems of the people around me. Imagine you have an active record pattern all over your code base. In 1 000 places, so it's easier to work with it:

```php
<?php

$product = Product::find(5);
$product->name = 'Train Ticket';
$product->save();
```

This pattern helped you to grow your minimal viable product, deliver features, enjoy growth and make money. It was very useful to you at a certain time in the past. The same way it was useful to live with parents when we went to school.

Your company grew every year for last 5 years, with growing code bases and new modules you have more bugs with weak typing and you've **decided to move** to Doctrine and separate Entity and Repository.

## Let's Refactor

The **why** is clear and the decision to change the codebase has been made. Now **how** do you refactor your 1000 places that use active record?

Before answering, keep in mind, that you have to explain these options to your boss (CEO, product owner...) because he or she cares about following in this order:

- **How much time** will it take? *The faster the better*
- **How much money** will it cost? *The cheaper the better*
- **How big code good quality** it brings? The *higher* the better

Well, your boss will probably not ask the last question, but I've added it just for the sake of our programming perception of the relationship of code and business.

### 1. Rewrite = Write the Same Code in Clean Way

How could the pitch for rewrite look like?

"*The active record is coupled with everything, it's in controllers, in commands, and in services. Even if we change it in a single class, it will probably influence most of the rest code. After we rewrite adding features would be much easier, because we would not have to switch between the old and new code base. We know what we want and it would take less time in the end than any other solution.*"

Suddenly, your colleague comes. You don't like him, because he's too "smart", younger and has less experience than you (he's younger, how can he have more experience, right?) and he starts to argue:

"*Rewrite from scratch is one of the things you should never do. Why? Because rewriting from scratch has a bad history of failures. Joel Spolsky, CEO, and co-founder of StackOverflow wrote [Things You Should Never Do](https://www.joelonsoftware.com/2000/04/06/things-you-should-never-do-part-i) in 2000.*"

<div class="text-center mb-4">
    <img src="https://i1.wp.com/www.joelonsoftware.com/wp-content/uploads/2016/12/Pong.png?zoom=1.100000023841858&w=230&ssl=1">
    <p>
        <em>Joel hodling a ping pong paddles in black/white</em>
    </p>
</div>

Imagine you're the new CEO of your company. Would you go for rewrite after hearing this?

In my experience, every developer has to fuck-up one project by rewriting from scratch, to obtain this knowledge, so no article can help you if you're not there yet. Still, it's fun to **read about failures of others**.

### 2. Gradual Refactoring = Change Old Code while Adding new Features

Your ballsy colleague continues his pitch: *It's much better to refactor as part of our coding. Only if we need it. If there is a new feature that affects, e.g. product object, we should refactor product to repository and entity. That way we can keep delivering features, we'll always have one code base that works and we don't have to split our attention for a month or potentially years.*

## Which Approach is Better?

Let's get to the questions that are in the position of CEO. The goal of the CEO is to keep the project running, make it grow in all fronts together. He or she will assess both options:

- **How much time** will it take?
- **How much money** will it cost?
- **How big code good quality** it brings? *Pretty good*

### How is Rewrite Doing?

The CEO: *"So we have to basically pay programmers to create the same set of features as we had before, but it will cost us 2 months of work whole team? And in the end, we'll have exactly the same set of features, but the code will be nicer for you to work with?"*

### How is Continuous Refactoring Doing?

The CEO: *"If I understand this correctly, you say that our application will slowly transform into a new one, we can still add features, it will only take slightly longer. In the end, it might take 12-14 months, but we'll get there? And during those 12-14 months, you'll have to work with 2 approaches in the same code-base?"*

<br>

*"Well, both solutions are expensive and slow, but I slightly prefer..."*

## Attention Disruption

We forgot one big problem that both approaches suffer from.

The best way to assess code quality is **to let junior to work with it and count WTFs**. The less the better (WTFs, not juniors). Juniors are like kids, honest and creative by nature. They don't know what they shouldn't tell and shouldn't do, so they find solutions much quicker than most of the older people... or people that work with the code base for a very long time and suffer from conformity bias. That's why I enjoy meeting "less skilled" people because I can learn from them much more than from "the experts".

If the company growth is the main reason to refactor the code, so must expect to more PHP developers joining the project. Next new programmer coming to this code...

```php
<?php

// in one place
$product = Product::find(5);
$product->name = 'Train Ticket';
$product->save();

// in some other place
$product = $this->productRepository->get(5);
$product->changeName('Train Ticket');
$this->productRepository->save($product);
```

...would be probably **confused**:

- Why is the other team working on the new code and we have to work with this shit-code for next year?
- Why there is one entity with active record and other with the classic entity?
- Why do you keep returning my code on code-reviews, since you there is active record all over the application?
- Why there are 2 ways to get an item from the database with no clear boundary when to use which?
- Why there is no documentation for when to use which pattern?
- Why we have to implement every feature twice, once in the old code and once in the new code?
- ...

And so on.

All this leads to moving focus from [deep work](/blog/2017/09/25/3-non-it-books-that-help-you-to-become-better-programmer/#deep-work-by-cal-newport) and actually creating features to talking about meta-programming. You talk and answer and explain, but nothing in the code changes.

## Design Code for Understanding

I've started to code new Lekarna.cz in 2015 on Nette from scratch (exactly!). First 5 months I was all alone, then a new programmer joined me. He started to code in the same quality as the previous code, I didn't have to teach him almost anything. I was curious:

- "Where did you learn work so well Doctrine, Nette and using patterns?"
- "I did 2 small projects on Nette without Doctrine, but I just use what's already there."

I was so happy! Once I can write readable code, second the code doesn't depend on my expertise and I don't have to waste both our times in *meta-programming* and explaining what code should explain.

The code can be designed to either confuse people or to lead them. It's a matter of thoughtful decision to make code understandable first, then it's pretty easy.

## Pattern Refactoring

How can we keep the attention focused, code understandable and also make CEO happy?

- **How much time** will it take? 1 month
- **How much money** will it cost? Expenses for 1 month

Do not focus on the code or on its size - that all is now just an implementation detail. Use the code that you and your colleagues build. Go for patterns:

- How do you define **active record pattern**?
- How do you define **entity**?
- How do you define **repository**?
- What is **takes step by step to change** this code from active record to entity and repository?

```diff
-$product = Product::find(5);
+$product = $this->productRepository->get(5);

-$product->name = 'Train Ticket';
+$product->changeName('Train Ticket');

-$product->save();
+$this->productRepository->save($product);
```

- What is the most efficient way to achieve it?

<div class="alert alert-success mt-3" markdown=1>
Play with these question, **ask them**, **break limits of your colleagues** and **look for the cheaper and faster solution that brings you better code quality** at the same time.
</div>

There are many ways already:

- pattern refactoring is already in PHPStorm, the first kick off is [Code Cleanup](https://blog.jetbrains.com/phpstorm/2019/02/phpstorm-2019-1-eap-191-5109-15)  feature
- regular pattern
- the most advanced is AST refactoring (I spoke about it in [this interview](https://blog.shopsys.com/2019-trends-in-the-world-of-php-interview-with-tomas-votruba-c70f138c92a3))

<br>

Learn this minds set and tool kit - they will give you the power to move massive code bases with just a couple hours of preparation. **Next time you'll be thinking of "rewrite vs. gradual refactoring", remember pattern refactoring**. There probably already is an easier way behind the corner that will make happy both your and your CEO.

<br>

Happy coding!
