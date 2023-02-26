---
id: 379
title: "Why I Migrated This Website From Symfony To Laravel"

perex: |
    It's been exactly a week since I've migrated this website from Symfony to Laravel. I never done such migration before, and I was a bit afraid of what pitfalls are waiting for me.

    It took me 2 trips in Lisbon trains, one afternoon in a caffee and few hours at hotel to finish. I'll talk about the process later, it's really interesting set of techniqutes.

     **You kept me asking "why", so here it is.**
---

<blockquote class="twitter-tweet"><p lang="en" dir="ltr">How I migrated my blog from Symfony to <a href="https://twitter.com/laravelphp?ref_src=twsrc%5Etfw">@laravelphp</a> in 4 hours?<br><br>✅ regex Twig to Blade, syntax is 99 % similar <br>✅ 2 custom <a href="https://twitter.com/rectorphp?ref_src=twsrc%5Etfw">@rectorphp</a> rules for controller class names, view() and routes<br>✅ copy configs from raw project<br>✅ tidy details<br><br>Here is result 🥳<a href="https://t.co/vmMbZMIHEI">https://t.co/vmMbZMIHEI</a></p>&mdash; Tomas Votruba (@VotrubaT) <a href="https://twitter.com/VotrubaT/status/1627606800760438784?ref_src=twsrc%5Etfw">February 20, 2023</a></blockquote>

<script async src="https://platform.twitter.com/widgets.js" charset="utf-8"></script>

*Disclaimer: If you expect this post to be technical, feature comparison or code quality analysis, it will not. It's my personal experience.*

<br>

## How I came to Portugal to Rest

I came to Portugal in the middle of January to get a bit of travelling, bit of sun and have a focused time for my work. I though I'll spend most time alone, or with short chats in the coffee.

**I had no idea what will hit me next month**. I saw Nuno Maduro tweet about some meetups in Portugal. We were never in contact, but I've decided to write him.

Soon, we're having a great dinner the night before meetup and I'm one of the speakers at PHP Porto #1 talking about PHPStan packages.

<img src="/assets/images/posts/2023/porto_guys.jpg" class="img-thumbnail" style="width: 40rem">

I thought, "that was nice, I've met few people and now I'll go back to my hotel room to work". But 3 things happened that changed the direction of my life:

* a guy looking like an established business man, [Patricio](https://twitter.´com/ijpatricio), invited me to his town for a weekend
* there was PHP meetup in Lisbon on Tuesday next week
* there was some conference for PHP devs Thursday next week, also in Lisbon

<br>

Important note: I'm an experience-driven person. You can tell me stories all night long about this place we should visit, this food I have to try or that tool that will make my life easier. It has no effect, quite the contrary.

I need to experience these things first hand, see how it really feels for me. That's where Patricio clicked to my way of learning. We had a meeting in a restaurant 1 day before conference and I was telling him - "lets try this Laravel, I want to use it on [testgenai.com](https://testgenai.com/), but I need your help".

## The Patricio Experience

So we took working project written in Symfony and tried to mimic the behavior in Laravel. Render a controller, submit form, process the form, save to database, delete item, use something called "Livewire" to make response time faster, make use of some queues, that works on the background...

**The queues were amazing** - I could just run them with bare PHP, the worker would call the GPT on background. This is now part of testgenai.com and allows to submit many tests as once and get response without stale or waiting.

Later, I had 3 more calls with Patricio and he's helping me to this day. With deploying a staging server in Laravel, transition from Webpack to Vite, debugging on the server, Livewire components, explaning magic of models and much more.

## Overcoming Bad Experience

To be honest, my first emotion to the Laravel for past few years was rejection. I had a very painful experience with Laravel 5 project, that we upgraded 4 years ago.

I'm used to Zend 1, where classes use underscore `_` magic notation and join with shared namespace prefix. But in Laravel 5, there were classes that didn't exist, that took a few weeks to figure out. PHPStan and Rector basically crashed for missing class/method on anywhere and so did my will to use it.

I tried to help the company, but we didn't get far over 4 months period. This being my first experience, however related to poor quality of project in general, discouraged me for year.

## Follow your Intuition

I bought one-way ticket to Portugal on New Years eve. I didn't know why, but I felt it's the right direction.

I went to the Laracon in Lisbon with 0... or rather, -20 Laravel experience. I didn't know why, but it felt like right time and place to be in.

I feel like I'm changing my religion of 10 years for a different one. I feel uncertain, scared, but also **growing and expanding my reality and what I think of the world**.

## Great Laravel Community

What I enjoy the most so far is the general approach of Laravel people. If don't know something, I can ask on Twitter or in person, **and get direction to next post, documentation or "ont supported" comment**. It's extreemely helpful for me in the early stages.

When I want to add the feature to the Laravel, **I get encouraged to create an open-source package**. So I did.

When I want to talk about Rector on conference, Nuno tells me to just submit the call for papers. So I did.

Thank you for all being there and making this exciting and warm experience!

## Little Experiements

When facing an unclear decision, that might have wide consequences, I always tell my clients:

<blockquote class="blockquote">
We cannot know the future, that's why it's always uncertain.
<br>
Yet, we can do tiny experiment, to gather more information. Then we decide better informed and with stronger will.
</blockquote>

Take 2-3 week experiment, gather information and evaluate again. If you find the new technology crappy, expensive and slow, you can switch back knowing the reasons.

## This my Experiment

Switching this website and testgenai.com is an experiment for me. I have no idea what I'm doing, so watching me code in Laravel must be entertaining.

I still follow these code-quality criteria:

* the code must work for Rector, ECS and PHPStan
* I don't want to maintain the code, I want to code and relly on it
* I want to enjoy coding and make use of 2020+ features (yeah, I realize I've been sleeping couple of years)

<br>

So where this leads me so far? I want to explore the ecosystem, make the leaky parts more solid and contribute to the community. Learn by doing.

* Yesterday, I've leaked a new package on [my Twitter](https://twitter.com/votrubat) , that helps to manage configs.

* I'm about to convert getrector.com website, so far the biggest project, from Symfony to Laravel. The configs are solved, so the hardest part will be probably the Javascript to highlight the textarea (yes, the PHP framework part is easy to convert).

* I'll write a technical post soon, about what I like about Laravel and what I don't.

<br><br>

What was your first experience with Laravel? Let me know in comments or [on Twitter](https://twitter.com/votrubat).

<br>

Happy coding!
