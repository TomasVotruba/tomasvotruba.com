---
id: 379
title: "Why I Migrated This Website From Symfony To Laravel"

perex: |
    It's been exactly a week since I migrated this website from Symfony to Laravel. I had never done such a migration before and feared the pitfalls waiting for me.

    The migration itself was easy and swift. It took me 2 trips on Lisbon trains, one afternoon in a cafe, and a few hours at the hotel to finish. I'll talk about the process later, and it's a fascinating set of techniques.

     **You kept me asking "why", so here it is.**
---


*Disclaimer: If you expect this post to be technical, or code quality analysis, it will not. It's my personal experience. Also, this is not comparison or shaming post, I use Symfony and Laravel on daily basis and enjoy working with both of them.*

<br>

<blockquote class="twitter-tweet"><p lang="en" dir="ltr">How I migrated my blog from Symfony to <a href="https://twitter.com/laravelphp?ref_src=twsrc%5Etfw">@laravelphp</a> in 4 hours?<br><br>✅ regex Twig to Blade, syntax is 99 % similar <br>✅ 2 custom <a href="https://twitter.com/rectorphp?ref_src=twsrc%5Etfw">@rectorphp</a> rules for controller class names, view() and routes<br>✅ copy configs from raw project<br>✅ tidy details<br><br>Here is result 🥳<a href="https://t.co/vmMbZMIHEI">https://t.co/vmMbZMIHEI</a></p>&mdash; Tomas Votruba (@VotrubaT) <a href="https://twitter.com/VotrubaT/status/1627606800760438784?ref_src=twsrc%5Etfw">February 20, 2023</a></blockquote>

<script async src="https://platform.twitter.com/widgets.js" charset="utf-8"></script>


<br>

## How I came to Portugal to have a rest

I came to Portugal in the middle of January to get a bit of traveling, a bit of sun, and have a focused time for my work. I thought I'll spend most time alone or with short chats in the coffee.

**I had no idea what would hit me next month**. I saw Nuno Maduro tweet about some meetups in Portugal. We were never in contact, but I decided to write him.

Soon, we're having a great dinner the night before the meetup, and I'm one of the speakers at PHP Porto #1 talking about PHPStan packages.

<img src="/assets/images/posts/2023/porto_guys.jpg" class="img-thumbnail" style="width: 40rem">

I thought, "that was nice. I've met a few people, we had a good laugh and tasty IPAs, and now I'll return to my hotel room to my normal life".

<br>

But 3 things happened that changed the direction of my life:

* a guy looking like an established businessman, [Patricio](https://twitter.´com/ijpatricio), invited me to his town for a weekend
* there was a PHP meetup in Lisbon on Tuesday next week
* there was a conference for PHP devs Thursday next week, also in Lisbon

<br>

## Personal experience first

Important note: **I'm an experience-driven person**. You can tell me stories all night long about this place we should visit, this food I must try, or that tool that will make my life easier, and it has no effect, quite the contrary.

I need to experience these things firsthand and see how it feels for me. That's where Patricio and I clicked perfectly to fit my natural way of learning.


<br>

<blockquote class="blockquote text-center">
"The best teachers are those who show you where to look,<br>
but don't tell you what to see."
<br>
<div class="blockquote-footer text-right">
Alexandra K. Trenfor
</div>
</blockquote>

<br>

We met in a restaurant a day before the conference, and Patricio encouraged me: "Tomas, do you still want to try Laravel?".

I already had around 4 beers that day, so I was ready to put my guard down and go with the flow.

"Okay, I  have this project [testgenai.com](https://testgenai.com/). We can convert it to Laravel. That would be very helpful to me".

## The amazing Patricio

So we took a working project written in Symfony and tried to mimic the behavior in Laravel:

* render a controller,
* submit a form,
* save data to the database,
* delete an item,
* use something called "Livewire" to make response time faster,
* use some "queues" that work in the background.

**The queues are excellent** - I can run them with bare PHP, and the worker calls the GPT in the background. This is now part of testgenai.com and allows you to submit many tests simultaneously and get a response without stale or waiting.

<br>

So far we've had 3 more calls with [Patricio](https://twitter.com/ijpatricio), and he's helping me to this day. With deploying a staging server in Laravel, a transition from Webpack to Vite, debugging on the server, Livewire components, explaining the magic of models, and much more.

He's one of the best teachers I've met in past 5 years. If you're looking to improve your life, contact him.


## Overcoming bad experience

My first emotion in Laravel for the past few years was rejection. **I had a painful experience** with Laravel 5 project, we upgraded 4 years ago.

I'm used to Zend 1, where classes use underscore `_` magic notation and join with a shared namespace prefix to invoke autoload. But in Laravel 5, there were classes that didn't exist that took a few weeks to figure out. PHPStan and Rector crashed for missing class/method anywhere, and so did my will to use it.

We tried to help the company, but we got little work done over 4 months. This being my first experience discouraged me for years.

## Follow my intuition

I bought a one-way ticket to Portugal on New Year's eve. I didn't know why, but I felt it was the right direction.

I went to the Laracon in Lisbon with 0... or rather -20 Laravel experience. I didn't know why, but it felt like the right time and place to be in.

I feel like I'm changing my religion of 10 years to a different one. I feel uncertain and scared, but also **growing and expanding my reality and what I think of the world**.

## Great Laravel Community 🧡

What I enjoy the most so far is the general approach of Laravel people. If I don't know something, I can ask on Twitter or in person, **and get direction to a helpful post, documentation, or "not supported" comment**. It's highly beneficial to me in the early stages.

* When I want to add the feature to Laravel, **I get encouraged to create an open-source package**. So I did.
* When I want to talk about Rector at the next Laracon, Nuno tells me to submit the call for papers. So I did.

<br>

Thank you for all being there and making this exciting and warm experience!

<br>

Thank you Nuno, Patricio, Francisco, Marcel, Lucas, Freek and Taylor for great personal chats and on-going support! 🧡

## Little experiements

When facing an unclear decision that might have broad consequences, I always tell my clients:

<blockquote class="blockquote text-center">
"We cannot know the future. That's why it's always uncertain.
<br>
Yet, we can do a tiny experiment to gather more information.
<br>
Then we decide better informed and with a stronger will."
</blockquote>

Take a 2-3 week experiment, gather information, and evaluate again. If you find the new technology crappy, expensive, and slow, you can switch back, knowing the reasons.

## This is my Experiment

Switching this website and testgenai.com is an experiment for me. I have no idea what I'm doing, so watching me code in Laravel must be entertaining.

**I still follow code-quality criteria:**

* the code must work for Rector, ECS, and PHPStan - no magic
* I don't want to maintain the code. I want to code and rely on it
* I want to enjoy coding and make use of 2020+ features (yeah, I realize I've been sleeping a couple of years)

<br>

## Learn by doing... and enjoy the process

Where does this lead me so far? I want to explore the ecosystem, make the leaky parts more solid and contribute to the community.

<blockquote class="twitter-tweet"><p lang="en" dir="ltr">in a week 🤣 <a href="https://t.co/eoIusDIZtw">pic.twitter.com/eoIusDIZtw</a></p>&mdash; Adrian Nürnberger 🐙 (@nuernberger_me) <a href="https://twitter.com/nuernberger_me/status/1620354616734121984?ref_src=twsrc%5Etfw">January 31, 2023</a></blockquote>

<br>

* I want to teach [PHPStan to run on Blade templates](https://twitter.com/VotrubaT/status/1625925547464196109).

* Yesterday, I leaked [my first Laravel package on Twitter](https://twitter.com/votrubat) that helps to manage configs.

* I'll convert the getrector.com website to Laravel, the most complex project so far. This will set grounds for a package to handle such migrations, including [Twig to Blade conversion](https://twitter.com/VotrubaT/status/1627277318254100482).

* **I'll write a technical post soon about what surprises me about Laravel** (in a good way)

<br><br>

What was your first experience with Laravel? Let me know in the comments or [on Twitter](https://twitter.com/votrubat).

<br>

Happy coding!
