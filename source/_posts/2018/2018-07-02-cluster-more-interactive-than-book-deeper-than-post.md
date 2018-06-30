---
id: 119
title: "Cluster: More Interactive than a Book, Deeper than a Post"
perex: |
    I started this 2-part series by [Don't Read Books](/blog/2018/06/28/dont-read-books). What should we do instead?
    <br><br>
    There is no silver bullet, but I have few proposals that anyone sharing knowledge in text form can do to rise quality of the content better and make readers get more out of it in the long-term.
    <br>
    It will not earn as much money as "bestselling" books, but if you value education more than money like me, keep on reading.
tweet: "New Post on my Blog: Cluster: More Interactive than a Book, Deeper than a Post #education #books #reading #ai"
related_items: [118]
---

## Tested Content is Easier To Keep Up-To-Date

Is there a code in your post or chapter, piece of text? It <a href="https://pehapkari.cz/blog/2017/01/12/why-articles-with-code-examples-should-be-CI-tested/">should be tested</a>. Back to our Symfony book. Imagine you buy it, you read it and there is section about forms. But thanks to *everlasting deprecation* effect, you happen to use Symfony 4.1, but book is about Symfony 3.0, so it doesn't work and you've just wasted 20 $ in money for a book and hundreds $ in time investment.

Now imagine this post would be tested, with unit tests and Travis CI that would run daily to make sure the content is still valuable in the present. The day new Symfony with BC break is released, the new version of post is published. And that works super easily for version 4, version 5, version 6...

Btw, I know you can find up-to-date know-how for Symfony Forms in the documentation, but this is just an example. It could be any more complex non-documented code, like writing a [Console application with Dependency Injection](/blog/2018/05/28/build-your-first-symfony-console-application-with-dependency-injection-under-4-files/).

## Let People Into Your Hearth

...or at least your thoughts. **A book content is one-man-show, with one-man ideas** and I think that is dangerous, unless we talk about God that helps us all and everyone can agree on that. I don't think my ideas are the best and that I know everything (or anything). I just want to open discussion in the best direction I believe at the moment.

<blockquote class="blockquote text-center">
    "The community of people knows much more<br>
    than the most skilled person in it."
</blockquote>

If people cooperate and talk to each other, they can bring much more value and higher quality solutions.
That's probably the reason companies have more than 1 employee, right? :)

How to allow this in written content? **Simple - allow comments.** Allow them all the time, [without censorship of opposed ideas](/blog/2018/01/29/how-to-deal-with-haters-in-comments-and-github/).

That way people can express themselves and improve content to be:

- community-proofed
- validated by more than 1 person
- questioned if that is really the best there is (at the time of reading)

## Let People Build the Content

If you allow comments, it's the first step to improve the content. But most people read the content and not the comments - by naturally, you need to read the post, so you could understand comments under it.

What if the readers could change the content? And what if you make it as easy for them as [clinking a link](https://github.com/TomasVotruba/tomasvotruba.cz/edit/master/source/_posts/2018/2018-06-28-dont-read-books.md).

That way the content is cooperative work of more people, more authors would be happy to share it and get credit for it. 3 posts about Symfony Console [proven practice](https://www.linkedin.com/pulse/proven-practices-process-stan-garfield) by 3 different authors can never be as good as 1 written by 3 authors together.

<div class="text-center mb-3">
    <img src="/assets/images/posts/2018/no-books/contributors.png" class="img-thumbnail">
    <br>
    This is number of contributors to this blog. It's not like we're writing together all the time, but I'm very happy it's more than 0, much more. Thank <a href="https://github.com/TomasVotruba/tomasvotruba.cz/graphs/contributors">you all</a>!
</div>

## Levelling Up Tailored to Your Personal Needs

Each of us has different experience, past, motivation and know-how. It makes no sense that all of us read whole book about Symfony.

Also each skill has a group - *a cluster* of knowledge of various levels that you can master.

<div class="text-center mb-3">
    <img src="/assets/images/posts/2018/no-books/hollocracy.png" class="img-thumbnail" width=500>
    <br>
    <em>A cluster pattern in <a href="https://sylius.com/blog/holacracy-our-future-is-teal">Holacracy organization</a>
</div>

It makes no sense that everyone would read the same book from beginning till the end. Instead you can start at level 5, I can start
at level 2, and somebody only wants the level 10, if there is something new he can learn in the field he thinks he masters already.

With a cluster of posts, you can see clearly where to start and what you want to read.

### The Next Nearest Skill

This is connected to *prerequisite skill* system. Each skill is followed by the nearest one that is super easy to learn thanks to previous experience. Compare 2 learning scenarios:

- You drive a bike, then motorcycle, then a car and than a bus.
- You drive a bike and jump directly to the bus.

Which can build on previous experience and utilize neuron patterns faster?

## Inform Clearly about Deprecations and Changes

The last but most least, people often return to content they believe in and look for iterative improvement in it. That's why we use StackOverflow and see answers that worked in 2015 to have 100 vote, but also there is a new one in 2018, that has already 20 votes and in 2020 there will come even better one...

That's why I try to make clear what and how changed right in the beginning on such posts.

<a href="/blog/2017/12/17/new-in-symplify-3-doc-block-cleaner-fixer/">
    <img src="/assets/images/posts/2018/no-books/updated-post.png" class="img-thumbnail">
</a>

And also not to promote outdated deprecated know-how:

<a href="/blog/2017/06/19/symbiotic-controller-nette-presenter-with-freedom/">
    <img src="/assets/images/posts/2018/no-books/deprecated-post.png" class="img-thumbnail">
</a>

These posts are also moved from the main page into [own *deprecated posts* page](/deprecated-posts). That way you can still find them, but they don't confuse new readers by outdated know-how.

You might see this as redundant work and that I'm a nerd (guilty!), but **I'm super-focused on you, my readers and I want you to understand every change that happens so you can learn fast in safe environment**.

<br>

## The Clusters in Progress

First, I started this series as to show you the idea of [clusters](/clusters/) on my site, but then it went much further beyond that.

Check the idea and feedback me, what do you think about that.

<br><br>

Happy clustering!