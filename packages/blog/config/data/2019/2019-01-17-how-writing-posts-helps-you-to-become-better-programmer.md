---
id: 178
title: "How Writing Posts Helps you to Become Better Programmer"
perex: |
    I love to write about my packages. Why? The code you type **you know**, **you know** the name of variables, **you know** how to interface looks, you know the architecture. It's like raising your own child and **knowing** his favorite ice-cream...
tweet: "New Post on #php 🐘 blog: How Writing Posts Helps you to Become Better Programmer"
tweet_image: '/assets/images/posts/2019/write-to-code/autstin.gif'

deprecated_since: "2020-03"
deprecated_message: |
    Statie was deprecated with last version 7, because 99 % of features are covered in Symfony application.<br>
    <br>
    To create static content, migrate to Symfony app and [SymfonyStaticDumper](https://github.com/symplify/symfony-static-dumper).
---

## What does Your Child Eat?

 The fun comes, when your neighbor needs to run to the hospital to visit his dying father and he drops you his daughter at your door. You're a good man, so you say you look after her. It getting late so you want to make some dinner.

But what does she like? Can she eat spicy food or sweets? **Is she allergic to something?** Many questions you wished you asked your neighbor now, but that didn't come to your mind before, because you know by heart these things about your child.

Luckily for us, a code is a bit different - when it eats something different than it supposed to do, the worst thing that can happen is an exception or invalid input type. We don't think about other peoples' code much - compared to ours. It's either not tested, crap, legacy or full of static (pick your own favorite label for the code you read and that is not yours).

## Why you Should Yourself to One's Shoes?

If you learn how to think like somebody you're not, you get into [begginers mind](http://www.agillo.net/zen-and-the-art-of-programming-beginners-mind/).
It might save a life of a child and also - in your case - you'll be able to write code not only you but **also other people understand**.

You'll get many benefits over your peers, just to name a few:

- You'll be more favorable in your team than others
- You'll be more successful in selling your ideas to implement to your code
- The other will contribute to your code more than to the one the only author understands

**How do put there?**

## 1. Use Your Own Package

`composer require your-package/name`, a plugin with bundle or extension and run. You probably have done this dozen of times. Do you think this will give you nothing new? On contrary - you might be surprised, how hard to configure your package is.

## 2. Let Your Friend use You Package - LIVE!

Ask a good friend who you're patient with to use your package **personally right in front of you**. Make task simple, e.g.

- download Easy Coding Standard and
- check your code with it

They will probably follow steps in `README`... oh, I've learned almost nobody reads them in these tests. They just find `composer require your-package/name` and wait for the first exception before going to any README at all. Not really intentionally :)

In your eyes it might look like this:

<img src="/assets/images/posts/2019/write-to-code/autstin.gif" class="img-thumbnail mb-5">

I'm always tempted to give them clues - "click there, it's not *exclude*, it's *excluded*", but that **would ruin your feedback**. Stop any verbal and non-verbal output = **shut up [and listen](https://www.youtube.com/watch?v=yA1b2iJlBP0&feature=youtu.be&t=39)**.

*Friend testing* is really the best way to get feedback. You can read more about this process in short book [Rocket Surgery Made Easy](https://www.amazon.com/Rocket-Surgery-Made-Easy-Yourself/dp/0321657292) by my favorite common sense author *Steve Krug*.

On the other hand, it is consuming and you have to have many friends that are both willing to use your package and are feeling comfortable in failing to even run it.

Isn't there something you can do just by yourself that would keep their shoes on you? Something where all you need is yourself and free time and the only judge in your head? No, I don't mean masturbation.

## 3. Write Post About It

I love writing about packages, about [those I create](/blog/2018/09/20/new-in-symplify-5-3-new-cool-features-of-package-builder/) and [those I use](/blog/2018/07/30/hidden-gems-of-php-packages-nette-utils/). It starts very simply, why it exists, what problems it solves and how you can use it in your home today. Why I love this writing?

**It helps me shift my thinking from fast to slow one**. It's very rare in programming because thanks to IDE we barely type in words:

<div class="text-center">
    <img src="/assets/images/posts/2019/write-to-code/typeless.gif" class="img-thumbnail">

    <p>This code took 10 seconds and exactly 34 keystrokes to type. It's <strong>233 chars long</strong>.</p>
</div>

<br>

When we're in the flow and have clear idea what we code, most of our programming works looks like this.

Btw, if missed Social Psychology 101 on the university, [Thinking, Fast and Slow](https://www.amazon.com/Thinking-Fast-Slow-Daniel-Kahneman/dp/0374533555) covers 90 % of it. Be careful, after reading it you'll see how lazy human brains is by design - in politics, in coding even or your family.

<blockquote class="blockquote text-center">
    "Indeed, the ratio of time spent reading versus writing is well over 10 to 1. We are constantly reading old code as part of the effort to write new code. ...[Therefore,] making it easy to read makes it easier to write."

    <footer class="blockquote-footer">Robert C. Martin, <cite>author of Clean Code</cite></footer>
</blockquote>

...and we don't talk about open source here, where the ratio might be way over 1000 views per year even in small packages.

<br>

- How long does it take to write about 233 chars-long code?
- How long will the text be?
- **What can you learn from this process?**

It will take around 2-5 minutes to write such text and it will be over 500 chars long.

That's *slow thinking* - with patience to detail, understanding in context and most importantly - **critical thinking**:

- Is that method really needed?
- Could I remove this?
- Why is this named like this?
- Should this be reused as a service?

Writing such text - in private, for your colleagues or on your public blog - **will give you many hints what you can do better with the code**.

## How Post about Statie improves Statie

One example for all - after writing of my last 2 posts - [11 Steps to Migrate From Sculpin to Statie](/blog/2019/01/14/11-steps-to-migrate-from-sculpin-to-statie/)
and [9 Steps to Migrate From Jekyll to Statie](/blog/2019/01/10/9-steps-to-migrate-from-jekyll-to-statie/) I realized, it's really difficult to switch from one static website to another. You need to follow many steps, in the correct order:

- move files
- remove files
- replace syntax in files A → B
- load files in another file
- make this path longer

This got me to *slow thinking*:

- "Do I really want to let people do this manually?"
- "There are many PHP developers who use Sculpin or Jekyll, who might want to migrate to Statie."
- "Do they really have to go through this list? How many of them will pass all the steps?"

**I didn't think about this until I started writing the text**. I assumed the post will give reader X points to follow. He or she will follow it, finish it and everybody is happy.

So I continued:

- "What if there would a command that will do all this?"
- "A command in Statie, that you can run in your project?"
- "Something like `vendor/bin/statie migrate-jekyll`"

The hypothesis was made. Now only I only had to **make a prototype**. I did it [over the weekend](https://github.com/symplify/symplify/pull/1339) and used it to [migrate blog](https://github.com/tomasfejfar/blog/pull/2) of my friend and great mentor not only for PHPStorm - [Tomáš Fejfar](https://www.tomasfejfar.cz/). It works and it was fun to explore static migration rules and limits. I learned a lot.

## Write about Your Code

But this post is not about Statie. It's about **how text about your code can give you ideas you never knew were there**. Ideas that are awesome and push you further to create better and shorter code, that more people understand.

Give your code a text try and you'll see how deep the rabbit hole goes.

<br>

Happy coding!
