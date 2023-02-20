---
id: 319
title: "How Exception to the Convention Does More Harm than Good"
perex: |
    We have a project, and to make it easier, we use specific standards. E.g., we use spaces in every file.


    But sometimes, we get to a situation when these standards are stressful. We don't understand them and just want them to bend over. How does our short-term need for pleasure affects **long-term well-being** of the project?

tweet: "New Post on #php 🐘 blog: How Exceptions for Conventions Do More Harm than Good"
tweet_image: "https://user-images.githubusercontent.com/924196/120189092-338f5580-c217-11eb-86f4-2022102c3f7a.jpg"
---

## Hit by a Car on Zebra Crossing

I will tell you a true story from 6 years ago. We went by a street, it was green light, and we were crossing. She holds my hand firmly. So intensely it hurts. It was an extreme force from a very soft girl who was 2/3 of my weight:

* "Ouch. What's going on?" I asked her.
* "I have this bad feeling around cars now."
* "Why? We're safe on the zebra crossing, and there is a green light. The cars are stopped and waiting for us."
* "That might be the case now, but I got hit by a car in the very exact situation. I flew 2 meters through the air."

She had this reaction on every zebra crossing, crossing, or near any moving car for next 6 months.

That one exception to the rule didn't mean much for the driver. They probably didn't even notice. But it meant a lot for my girlfriend's life. So much, I felt real pain in the bones of my hand many months later after she had this experience.

## Zebra-Crossing with 0 People and Green Light... Safe?

**Fast forward to yesterday**. I'm thinking about this post, what should be the story to bridge the experience of real-life and coding.

I'm looking at a street with two roads. There is a zebra crossing with a green light on the left side of one road. It's pretty safe, just 4 meters long. Cars have a red light, and they can go only from one direction across this sidewalk. We would hardly look for a safer place to use to cross that street.

Well, unless we add a just minor exception. There was a car going ~40 km/h from one of the roads on the right side, opposite the direction that cars can go through the sidewalk. Wait for it. The car is turning left and crossing the sidewalk that has a green light on it. Very smoothly, with elegance and 40 km/h.

Car crossing a zebra crossing that has a green light is not that rare common. No people were crossing it anyway, right? Well, that can be the safe exception, but the car is **going contra the one-direction road**.  In a fraction of a second, the car is going through the zebra-crossing and riding further. There is another car in the one-direction road going against it.

This story might have some real-life consequences. Fortunately, The car in the right direction is going exceptionally slowly. It has time to slow down and avoid a front-to-front collision at ~60 km/h speed.

Single one exception.

## Exceptions in Coding

We know people are dying on the streets because of "just-one-time" exceptions. How is that related to our coding?

In the whole project, we use PHPStan, ECS, and Rector to watch our standards. We have the same standard on `/tests` directory. We work on a new feature, and it's almost finished. Just PHPStan keeps failing... why? Why should we have standards on tests? It's bothering us for 10 minutes already. The stress level is so high...  how can we solve it? We decide to ignore them.

We get on a review and get a question about the ignored directory. We convince the reviewer "it's just three files" and "it's not production code anyway".

## Two Makes a Crowd

Soon the same colleague who made our code-review is contributing her PR. She comes to a similar situation when five files show some errors she does not understand. Using our trick, she ignores her files from ECS and Rector.

A few weeks later, this approach is spreading from PR to PR. Now almost half of the tests are excluded from standards, and our CTO looks at the project. He has an idea: "It's a mess; we should standardize this. Why not ignore all the tests?"

Now, all your tests and test fixtures have no requirements for standards at all. There is no need for autoloading, so we can finally drop the `psr-4` standard and move to `classmap` to autoload classes we need. So much freedom feels great. In case you're interested in psychology, this effect is called [broken window theory](https://blog.codinghorror.com/the-broken-window-theory/) (written by Stackoverflow co-founder).

## Instant Gratification

We're happy now. We have a lot of freedom, and we can create code faster without arguing with some CI tools about it. But what about developers who will come to the project when we're long gone?

<blockquote class="blockquote blockquote-smaller text-center">
If we make an exception in real life and nothing bad happens,<br>
life goes on, and nobody notices it.
<br>
<br>
If we make an exception in the code and nothing bad happens at the moment,<br>
it's forever encoded in the code. Lurking.
</blockquote>

## 2 "Standard" Options? 200 % Cognitive Load

Every such exception to the standard is a major distraction for the person just learning the project. They don't know that the code their work on is the only exception to a well-designed standard. They see it as two equally strong options:

<img src="https://user-images.githubusercontent.com/924196/120189092-338f5580-c217-11eb-86f4-2022102c3f7a.jpg" class="img-thumbnail">

The project we looked at above is not a fiction I made up. It's typical project for [legacy reconstruction](/blog/2019/12/16/8-steps-you-can-make-before-huge-upgrade-to-make-it-faster-cheaper-and-more-stable/). It's not old PHP, not old Symfony, nor missing constructor injection. It's an infinite split of exceptions and edge cases.

We don't need senior developers, we need [senior code bases](/blog/2020/03/02/we-do-not-need-senior-developers-we-need-senior-code-bases/). Code bases that **are easy to understand to junior developers who just got on board**. These projects are sustainable, easy to modify, and cheap to maintain.

Next time you'll be frustrated and tempted to exclude just this one file. **For a moment, take a pause and a deep breath.**

What future will you shape for your project? Safe and stable to walk or possibly hit by a car?

<br>

Happy coding!




