---
id: 164
title: "IT Bloggers, Deprecate Your Posts to Stop Spreading Legacy"
perex: |
    Do you blog about IT? Do you blog for more than a year? There is a big chance **you're spreading already outdated information**.
    <br>
    <br>
    The problem is, your readers see that as *the best practise*... did you know the Earth is flat?
    <br>
    <br>
    How to prevent that and keep your content quality high?
tweet: "New post on my #php blog: IT Bloggers, Version Your Posts to Stop Spreading Legacy #mattstauffer #stackoverflow #symfony"
tweet_image: "/assets/images/posts/2018/version-blog/symfony-warning.png"
---

## The *Long-Tail* Effect...

...that's spreads fake news.

If you answered "yes" twice to the perex, your posts are probably under long-tail effect.

<img src="/assets/images/posts/2018/version-blog/long-tail.gif" class="img-thumbnail">

They're frequently shared in the start, but the older they're, their total popularity rises. You might ask, **what's the problem with that?**

Let's say you want to know how to use parameters in Symfony controller. What will you do? Google to [StackOverflow](https://stackoverflow.com/search?q=symfony+get+parameter+in+controller): 

<img src="/assets/images/posts/2018/version-blog/answer.png" class="img-thumbnail">

Click [the first answer](https://stackoverflow.com/questions/13901256/how-do-i-read-from-parameters-yml-in-a-controller-in-symfony2/13901273#13901273) and copy-paste the solution:

<img src="/assets/images/posts/2018/version-blog/popular.png" class="img-thumbnail">

We know it's outdated for years, but **reader has no idea**. He or she googled "parameter Symfony Controller" and just go with the most voted solution. I'd do it too.

The **second answer for Symfony 3+** that was released in 2016 has much less attention: 

<img src="/assets/images/posts/2018/version-blog/second.png" class="img-thumbnail">

When the *long-tail effect* kicks in for this answer, it will be outdated. There will be also new answer for Symfony 6 with only 13 votes that no-one will read. 

Legacy is spreading, Dark Legacy Lord is happy. *That's why [Don't Read books](/blog/2018/06/28/dont-read-books/).*  

### Old !== Outdated

If time was the only issue, you could just limit Google results to last year. But it's not that easy.

**The old post doesn't mean outdated**. One example for all - Matthias Noback, my favorite PHP writer, wrote about [decopuled controllers as services](https://matthiasnoback.nl/2014/06/how-to-create-framework-independent-controllers/) in 2014. It's still valid and we're just almost getting there now. 

## How to Make it Right?

How can I even write a post about PHP when I know they'll be useless in a few years? Well, you can [tests your posts](https://pehapkari.cz/blog/2017/01/12/why-articles-with-code-examples-should-be-CI-tested/) to automate this, but it takes time and setup.
 
Right now, **I feel it's our responsibility as writers to inform our readers** what is good and tasty to eat and what id rotten old trash.

<p class="text-danger"><strong>Inform people clearly in the start of the page.</strong></p>

<br>

[Symfony blog](https://symfony.com/doc/3.1/components/console.html) &nbsp;<em class="fas fa-lg fa-check text-success"></em>

<img src="/assets/images/posts/2018/version-blog/symfony-warning.png">


[Matt Stauffer blog](https://mattstauffer.com/blog/how-to-organize-class-namespaces/) &nbsp;<em class="fas fa-lg fa-check text-success"></em>

<img src="/assets/images/posts/2018/version-blog/update.png" class="img-thumbnail">


It seems like a small detail, but it really helps people who are new in the fields to navigate differentiate between useless and good content.

## When is the Best Time? Now!

It's not an urgent call, December is really the best month to do this.

Why? A [new Symfony and PHP is released]([/blog/2017/10/30/what-can-you-learn-from-menstruation-and-symfony-releases/]) - Symfony 4.2 is out now (this blogs is running on it already) and PHP 7.3 is around corner.

It can be as easy as upgrade `composer.json`:  

```diff
 {
     "require": {
-        "symfony/symfony": "^4.1"  
+        "symfony/symfony": "^4.2"
     }
 }
```

## Practical Example

"Code, not words!" I hear you.

I'm working on such PR right now for this blog - [see it on Github]( https://github.com/TomasVotruba/tomasvotruba.cz/pull/582).

### What Changes are Included?

- Symfony <strike>4.1</strike> → 4.2
- Statie posts → official documentation [Statie.org](https://www.statie.org/)
- [Object Calisthenics post](/blog/2017/06/26/php-object-calisthenics-rules-made-simple-version-3-0-is-out-now/) → [Cognitive Complexity](/blog/2018/05/21/is-your-code-readable-by-humans-cognitive-complexity-tells-you/)
- [Nette\CodingStandard post](/blog/2017/08/14/how-to-apply-nette-coding-standard-in-your-project/) - <strike>0.5</strike> → 2.0
- deprecated [How to Require Minimal Code Coverage for Github Pull-Requests with Coveralls](/blog/2017/06/12/how-to-require-minimal-code-coverage-for-github-pull-requests-with-coveralls/) - there is no added value
- news in Symplify 2, 3 and 4 + reflect deprecations and removed classes
- and few more...

Notice, the depreciation is not removing. **I always try suggests a link to go or at least keywords to Google or provide "why" you should not do that at all**. That way people know where to go, instead of just bumping to a wall.

## Step-Up and Help Spreading Up-To-Date Information

**It's ok to make mistake and be wrong**. It's also normal to change your opinion based on a change in the worlds - that's what happened to me with [Why Doctrine is Dying](/blog/2017/03/27/why-is-doctrine-dying/) post. 

When I wrote this post, the situation was really frustrating, no vision, no new features, and passive community. That's change during the following year and even though I didn't use Doctrine, it felt wrong having this post still up, even though it was not in sync with reality.

<img src="/assets/images/posts/2018/version-blog/deprecated.png">

That's why I deprecated original post and released a new one [6 Reasons Why Doctrine is Alive and Kicking](/blog/2018/07/09/6-reasons-why-doctrine-is-alive-and-kicking/).

<br>

Happy upgrading and thank you for doing this for PHP community!
