---
id: 319
title: "How Exception to the Convention Does More Harm than Good"
perex: |
    We have a project and to make it easier, we use certain standards. E.g. we use spaces in every file.
    <br><br>
    But sometimes we get to a situation, when these standards are really stressful. We don't understand them and just want them bend over. How does our short term need for pleasure affect **long-term well being** of the project?

tweet: "New Post on #php 🐘 blog: How Exceptions for Conventions Do More Harm than Good"
tweet_image: "https://user-images.githubusercontent.com/924196/120189092-338f5580-c217-11eb-86f4-2022102c3f7a.jpg"
---

## Hit by a Car on Zebra Crossing

This is a true story from 6 years ago. We went by a street, it was green light and we were crossing. She hold my hand strongly. So strongly it hurt. It was an extreme force form a woman who was 2/3 of my weight.

* "Ouch. What's going on?" I asked her.
* "I have this bad feeling around cars now."
* "Why? We're safe on zebra crossing and there is green light. The cars are stopped and waiting for us."
* "That might be the case now, but I got hit by a car in the very exact situation. I flew 2 meters."

She had this reaction on every zebra crossing, crossing or near any moving car.

I think one exception out of rule didn't mean much for the driver. They probably didn't even notice. But it meant a lot for my girlfriend's life. So much, I felt real pain in bones of my hand many months later after she had this experience.

**Fast forward to yesterday**. I'm thinking about this post, what should be the story to bridge the experience of real life and coding. I'm crossing a street with 3 roads. I'm crossing the one when there is no car going in any direction.

On the other 2 roads, there is [walk] with a green light on it. It's pretty safe, just 4 meters long. Cars have a red lights and they can go only from one direction cross this sidewalk. We would hardly look for a safer place to use to cross that street.

Well, unless we add a just little exception. There was car going ~40 km/h from one of the roads on the right side. Opposite the direction that cars can go through the sidewalk. Wait for it. The car is turning left and crossing the sidewalk that has a green light on it. Very smoothly, with elegance and 40 km/h.

Car crossing a zebra crossing that has a green light is not that rare common. There were no people crossing it anyway, right? Well, that can be safe exception, but the car is **going contra the one-direction road**.  In a faction of a second, it is crossing the [zebra] and going further. There is another car in the one-direction road going against it. This story might have some real-life consequences, there was a huge luck. The car in right direction is going extremely slowly so it has time to slow down and avoid front-to-front collision in around 60 km/h.

Single one exception.

## Hit by An Exception

We know people are dying on the streets because of "just-one-time" exceptions, but how is that related to our coding?

In the whole project we use PHPStan, ECS and Rector to watch our standards. We have the same standard on `/tests` directory. e work on PR and PHPStan keeps failing... why? Why should be have standards on tests? It's bothering us for 10 minutes and the stress is so high, so we ignore them.

We get on a review and get a question about the ignored directory. We convince the reviewer "it's just 3 files" and "it's not production code anyway".

Soon the same colleague who made our code-review is contributing her own PR. She comes to similar situation, when 5 files show some error she does not understand. Using our trick, she ignores her files from ECS and Rector.

Few weeks later, almost half of tests are excluded in tests and our CTO looks at the project. He has one idea "we should standardize this, why not ignoring all the tests?"

Now, all your tests and test fixtures have no requirements for standards at all. There is no need for autoloading, so we can finally drop `psr-4` standard and move to `classmap` to autoload classes we need. So much freedom feels great.

This is scientifically **proven effect called [broken windows theory](https://blog.codinghorror.com/the-broken-window-theory/)** (written by Stackoverflow co-founder).

## Instant Gratification

We're happy now. We have lot of freedom and we can create code faster without arguing with some CI tools about it. But what about developers who will come to the project when we're long gone?

<blockquote class="blockquote">
If we make an exception in real-life and nothing bad happens, life goes on and nobody notices it.<br>
In code we make an exception and even if nothing bad happens in the moment, it's forever encoded in the code.
</blockquote>

## 2 Options to do One Thing = 200 % Cognitive Load

Every such exception of standard is a major distraction for the person just learning project. They don't that the code their work on is only 1 exception out well designed standard. They see it as 2 equally strong options:

<img src="https://user-images.githubusercontent.com/924196/120189092-338f5580-c217-11eb-86f4-2022102c3f7a.jpg" class="img-thumbnail">

The project we looked at above is not a fiction I made up. It's one of typical structures we have to deal with while [reconstructing legacy projects](/blog/2019/12/16/8-steps-you-can-make-before-huge-upgrade-to-make-it-faster-cheaper-and-more-stable/). It's not old PHP, old Symfony or missing constructor injection. It's infinite split of exceptions and edge-cases.

We don't need senior developers, we need [senior code bases](/blog/2020/03/02/we-do-not-need-senior-developers-we-need-senior-code-bases/). Code bases that are easy to understand to junior develops who just got on board. These projects are sustainable, easy to modify and cheap to maintain.

Next time you'll be frustrated and tempted to exclude just this one file for now, take a pause and 1 deep breath. What future will you shape for you project? Safe and stable or possible hit by car?

<br>

Happy coding!




