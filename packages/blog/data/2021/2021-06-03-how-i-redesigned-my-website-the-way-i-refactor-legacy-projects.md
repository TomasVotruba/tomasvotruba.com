---
id: 321
title: "How I Redesigned my Website they Way I&nbsp;Refactor Legacy Projects"
perex: |
    I have wanted to redesign my website since Autumn 2020. I felt it's overly detailed with attention shotgun feeling. Because of intensive && extensive work on Rector to make it better, I could not get to it on the book with Matthias and writing posts. With no deadline, there is no rush, right?
    <br>
    <br>
    I had to hack around my brain to make it happen. It would be a shame to release Rector book on the old design, so I took [the book release](/blog/rector-the-power-of-automated-refactoring-book-released/) as a co-task.
    <br>
    <br>
    The first hour I struggled with the redesign approach, I realized I could use the skill I use daily - refactoring legacy.
tweet: "How I Redesigned my Website like Refactor Legacy Project"
tweet_image: "/assets/images/posts/2021/redesign_simple.png"
---

<blockquote class="blockquote">
    "Perfection is achieved, not when there is nothing more to add,<br>
    but when there is nothing left to take away."
    <footer class="blockquote-footer text-right">Antoine de Saint-Exupery</footer>
</blockquote>


*This redesign was inspired by Little Prince, [Don't Make me Think](https://sensible.com/), [Steal Like an Artist](/blog/2017/09/25/3-non-it-books-that-help-you-to-become-better-programmer/#steal-like-and-artist-by-austing-kleon) and [Show Your Work](https://austinkleon.com/show-your-work/).*

<br>

What do designer and legacy cleaner have in common? The final product must work, be intuitive and bring value to the user. I realized the old design had many spaces to improve, and a lot of ~~code~~ text and "cool features" should be removed.

When we [get a project to refactor](https://getrector.org/for-companies) in Rector company, the first 1-2 months we don't do any PHP or framework upgrades at all. We focus on preparing the environment, remove everything that is not needed for developers and business to grow. At first, it looks like we come to destroy the project by removing 30 % of it. But in the end, after solid and reliable code remains, we can move unbelievably fast with upgrades and pattern refactoring.

## Deprecating Clusters - Use Words People Understand

3 years ago I wanted to write a little book about a single topic. I was too scared, so I created a new work instead.
A word that is not for a book, not for a post. It's a collection of post on single topic - [clusters](/blog/2018/07/02/cluster-more-interactive-than-book-deeper-than-post/). There was a section "Clusters" in the menu for the last 3 years, and it contained eight groups of posts. The initial feedback was confusion. I was confused, too. It was time to [let go](/blog/2020/03/09/art-of-letting-go/) old fears and remove this section. **Either write a book or a post.**

## Occams' Razor

I use this idea or rather technique during refactoring legacy projects a lot. If some external tool handles something better than our custom code, remove the custom code and delegate it to an external tool. We'll save ourselves from testing our code, maintaining it, and teaching it to every new programmer.

One example from a project I was mentoring just yesterday: a custom autoload that finds all `autoload.php` files across the root directory and manually includes them. It was around 200 lines of magic (at least at first sight). We remove this part and used composer instead. Now it's 5 lines and code is [standardized](/blog/how-exception-to-the-convention-does-more-harm-than-good/). We will never have to think about autoloading again.

## 2600 Lines Removed

<img src="/assets/images/posts/2021/redesign_simple.png" class="img-thumbnail mb-5">

How did I apply Occams' razor to redesign my website?

* Google Search - there were two searches on the homepage, both with dummy website search - Could Google with "last year" filters beat this? Do people go to my website and only then google there? No, they just google instantly from the browser's URL field
  <br>→ **Remove Google search and let people use Google instead**

<br>

* Cleaning Lady List - the [cleaning lady list](/blog/2020/07/06/cleaning-lady-notes-from-class-mess-to-psr4-step-by-step-with-confidence/) was similar to *clusters*, just little side project with check-list of refactoring legacy. Thank you, [Kerrial](https://github.com/Kerrialn), for working on it and pushing it further. I didn't maintain it, nor used it myself, because every legacy project has its special problems
  <br>→ **Remove Cleaning Lady list and focus on content**

<br>

* Blog, archive, clusters... - there were at least three places to find posts. Why?
  <br>→ **Keep it simple on a "blog" page.**

<br>

* Tested posts - 2 years ago, I wrote a post about how [posts should be tested in CI](/blog/2019/09/16/why-software-articles-must-be-ci-tested/), so the content is easy to upgrade. In reality, this lead to 2 actions - first, I put a lot of work into making sure the content is tested, so the content itself was a bit neglected. Second, I was discouraged from creating tests for a new post because it was too much work. In the end, there were about 20 tested posts, so the coverage was inferior and not useful. Let's save this for a book
  <br>→  **Tests for articles are removed**

<br>

In the end, there are now fewer sections, less nesting, and more focus on content. You can read posts or get training. Simple. What changed in design?

<br>

## Design With Focus on Content

What if we apply the refactoring legacy approach to the website design itself?

* Do we need 1609 Font Awesome icons for the content? I've checked the content, and 95 % of them are ✅ and ❌ emojis
  <br>→**Drop Font Awesome**

<br>

* Do we need a special menu for mobile and website with fluid, responsible design (that often breaks on various platforms)? We need links that work for people - click and work.
  <br>→**Remove ul/li/nav/navbar/navbar-content mobile/laptop menu matrix and use simple a links instead**.

<br>

* Do we need three font styles for titles? With bold, thin, size, margin, padding differences?
  <br>**→Use one font and different by size.**

<br>

* Do we need jQuery? No.
  <br>**→Drop it.**

<br>

## Website is Dead, Long Live the Website

And the results? Simple, airy, with no clutter and content that is a joy to read on a laptop or in the tram.

<br>

<div class="row text-center">
    <div class="col-12 col-sm-6">
        <img src="/assets/images/posts/2021/my_website_2020.png" class="img-thumbnail rounded">
        2016&ndash;2020
    </div>
    <div class="col-12 col-sm-6">
        <img src="/assets/images/posts/2021/my_website_2021.png" class="img-thumbnail rounded">
        2021&ndash;?
    </div>
</div>


<br>

## Show Updated and Deprecated Posts

Did you know some posts get updated? Other posts are no longer valid and are deprecated to avoid spreading lousy practice as a way to go.

<div class="row text-center">
    <div class="col-12 col-sm-6">
        <img src="/assets/images/posts/2021/post_updated.png" class="img-thumbnail rounded">
        Updated post
    </div>
    <div class="col-12 col-sm-6">
        <img src="/assets/images/posts/2021/post_removed.png" class="img-thumbnail rounded">
        Deprecated post
    </div>
</div>

<br>

I'll write about reflection on past mistakes in one of my future posts.

<br>

## 10 months of Preparation, 2 Days at Coffee House

Do you know the 80/20 rule? 20 % of a project takes 80 % of the time to finish. It's not true.

I'm not sure if the stars were right, if I accidentally took some deep-focus drugs or if that was the right thing to do. I went to a coffee house in Tenerife at 10 AM. I'm thinking... what should I do today? I worked on a work project refactoring yesterday; Rector is running and released, and I already published a post for the week. Mmm, maybe I'll check what I can do on the website design.

I turned off my phone, opened my laptop, got a cortado (it's like flat white for Spanish), and started working.

Three hours later, I was tired, my battery was almost 0 %, and my table didn't have the electrical plug. The table next to me got it, but one family occupied it. Suddenly, when I was about to leave, they stood up and went to the counter. I thought... ok, I'll charge my laptop for 20 mins first. Suddenly it's 4 PM, and I'm leaving the coffee house with **half of the website redesign done**. Most importantly, with a clear vision of what to do next and how the website will look.

Don't you believe me? See [the pull-request](https://github.com/TomasVotruba/tomasvotruba.com/pull/1200).

Do you ever have moments like this? I guess it was the right time and right place to do the right thing :)

How do you like it? Let me know and stay safe.

<br>

Happy coding!
