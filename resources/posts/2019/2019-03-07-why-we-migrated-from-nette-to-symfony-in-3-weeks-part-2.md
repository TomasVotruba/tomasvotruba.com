---
id: 192
title: "Why we Migrated from Nette to Symfony in 3 Weeks - Part 2 - Escaping Semantic Hell"
perex: |
    Do you want to **migrate your project from Nette to Symfony**? In [part 1](/blog/2019/02/21/how-we-migrated-from-nette-to-symfony-in-3-weeks-part-1/) we showed you how to get your project ready, why it's important to make team commitment and what you can automate.


    Today we'll look on one of the core reasons for this migration - **escaping to semantic hell**.
---

[Semantic versioning](https://semver.org) aka *semver* tells you there is a BC break between `nette/di` in version 2.5 and 3.0. That's all you need to know as a web developer.

If you're an open-source developer, you'll probably learn more about semver. If you do it right, your user will be happy and never have to learn anything more 2.5 and 3.0.

## From Nette...

When you run `composer update` Nette project, you'll find this normal:

<img src="/assets/images/posts/2019/nette-to-symfony2/version-hell.png" style="max-width:30em">

You can see there 3 variants:

- `nette/utils` is on 2.5.3
- `nette/tokenizer` is on 2.3.0
- `nette/mail` is on 2.4.6

Today, there are also some Nette packages on 3, but some not. Who cares, right?

If you don't know Nette, you might think "why would somebody use the different versions in their `composer.json`"?


```json
{
   "require": {
       "nette/utils": "^2.5",
       "nette/tokenizer": "^2.3",
       "nette/mail": "^2.4"
   }
}
```

Actually, `composer.json` looks like this:

```json
{
   "require": {
       "nette/utils": "^2.3",
       "nette/tokenizer": "^2.3",
       "nette/mail": "^2.3"
   }
}
```

And that's a huge overhead with Nette versioning. Each package requires different versions, even v2, and 3 at the same time. If you upgrade to the newer version, the last thing you want to solve is conflicts in a package that are not yours:

<img src="/assets/images/posts/2019/nette-to-symfony2/install-fail.png">

## What's different in Symfony?

When you install Symfony 4.0, you know that:

- **all your packages** are using Symfony 4.0
- all Symfony packages **are compatible** with each other
- **when you find a bug**, it will be fixed in Symfony 4.0.x
- you can look forward to next version 4.1 in 6 months :)

This approach is super stable since Symfony 3.4 and 2017. You can really on [Symfony promise](https://symfony.com/doc/current/contributing/code/bc.html) it more than of stability airplanes (I'm writing this post on one, I hope this statement is true :D).

Imagine you're looking for a bug across `symfony/console` 3.4, `symfony/dependency-injection` 4.1 and `symfony/finder` 4.2. Or just read the Symfony documentation in 3 different versions.

## Don't Bother the User with Bad Package Design

Since I was raised in Nette, I used **per-package tagging** in my open-source projects (*Zenify*, *Symnedi* and now *Symplify*). It allowed me to release new versions when the package needed it.

- Does`zenify/doctrine-filters` has a BC break? Let's release 3.
- Are there no changes `zenify/coding-standard` in last 1 year? Stick it with 1.

That's *nice to have* for the maintainer. But what about the developers who use your packages? **It's extra maintenance with 0-benefits.**

<blockquote class="blockquote mt-5 mb-5 text-center">
    There should be less and academic thinking in programming
    <br>and more <strong>pragmatic common sense</strong>. We were born with it.
</blockquote>

Maintainers should read books like [The Design of Everyday Things](https://www.amazon.com/Design-Everyday-Things-Revised-Expanded-ebook/dp/B06XCCZJ4L), [Don't Make me Think](https://www.amazon.com/Dont-Make-Think-Revisited-Usability-ebook-dp-B00HJUBRPG/dp/B00HJUBRPG) and [The Pragmatic Programmer](https://www.amazon.com/Pragmatic-Programmer-Journeyman-Master-ebook-dp-B003GCTQAE/dp/B003GCTQAE) to understand how others see our code.

## Symfony KISS = Per-Vendor Tagging

So I decided **to try [per-vendor tagging](/blog/2017/10/30/what-can-you-learn-from-menstruation-and-symfony-releases/)** over per-package tagging. Now I have to answer questions like "Why did you release a new version of your package, but no change between version 3.2.0 and 3.3.0?" But I know **bothering developers with semver-hell** is not a better choice, so I answer patiently (with a link to this post :)).

After all:

<blockquote class="blockquote text-center">
   There are no best solutions, just trade-offs.
</blockquote>

<br>

**This was one of our reasons we switched from Nette to Symfony**. Now we can upgrade all ~35 Symfony packages at once knowing they all work together.

<br>

Do you think it's impossible change for your project? Drop us a [message at Rector](https://getrector.com). We'll help you.
