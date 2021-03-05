---
id: 305
title: "The Best Time to Switch Gitlab&nbsp;to&nbsp;GitHub"
perex: |
    I'm known for using GitHub, a true paradise for any open-source project. But I don't have much experience with private projects pricing for this and other services like GitLab or Bitbucket.
    <br>
    <br>
    I assumed they all were at a similar price layer. After today's call with one of my clients, I've learned about one benefit of GitHub Actions for private projects I didn't consider before. **So much it's worth the switch.**

tweet: "New Post on #php 🐘 blog: The Best Time to Switch @gitlab to @github"
---

Let's take typical arguments why Gitlab CI is not better than GitHub, one by one.

## 1. GitLab has Much more Features than GitHub

When we compare CI services, this is one of the typical arguments for Gitlab. I find it as useful as this: "This manual has 100x more pages. It must be better".

Honestly, I have trouble finding simple settings like total CI minutes, so I have to call Honza and get mentoring on navigation. That's not how a product should work. GitHub is growing on the features too. They also put effort into improving DX/UX, so these features are easy to use.

## 2. GitLab has More Advanced CI

This statement used to be true when the only CI GitHub had was Travis - an external service that got popular mainly thanks to the free tier for open-source projects.

GitHub introduced a new CI service [in late 2018](https://github.blog/changelog/2018-10-16-github-actions-limited-beta/). I recall it did not seems useful to me because most of the configuration happened in Javascript. Can you imagine configuring your Symfony services config in Javascript? I don't.

But they switched to YAML as well and, as a bonus, added open-source storage for "Workflows". Think of it **as a composer package for your CI**. Do you want to publish your project via SSH? No need to learn how it works in detail and risk security breaches by invalid configuration. Just use SSH Workflow and complete needed ENV variables.

That was a breaking point for me to give GitHub Actions a try and switch all my open-source projects [from Travis to GitHub](/blog/2020/01/27/switch-travis-to-github-actions-to-reduce-stress/).

## 3. GitLab has Faster CI

I'm not sure how about 2019 and before because I was not using GitHub.
Actions yet. We run GetRector.org on Gitlab in 2018 because Gitlab had better Docker support in CI... and it had a CI :)

Since the switch from Travis/Gitlab to GitHub Actions, we've experienced much faster CI reactions. When a new commit was pushed on the former, it took 20-40 seconds until the CI server noticed. But on GitHub Actions, it's a matter of 5-10 seconds. That's blazing fast.

[Honza Mikes](https://github.com/JanMikes) explained it to me: it might be related to Microsoft Azure. Since Azure and GitHub both belong to Microsoft, they probably have an internal bridge with priority speed. Corporation co-operation well done :)

## 4. GitLab is Cheaper than GitHub

This used to be true back in 2015 when GitLab allowed unlimited private repositories to an unlimited amount of users. That was a game changed, as GitHub had this as paid service. I recall that was a massive spike in developer interest on Twitter just because of this exclusive feature.

As time went by and both companies grew, GitHub introduced private repositories for free too.

[In January 2021 **Gitlab introduced new pricing**](https://techcrunch.com/2021/01/26/gitlab-reshuffles-its-paid-subscription-plans/). Before we get into that, I came across post about similar topic back from 2018 - [How #GitLab Will Lose Business](https://mlaccetti.medium.com/how-gitlab-will-lose-business-bea8fb2f0fd4). Quoting:

<blockquote class="blockquote">
    When we got the license in 2017, it was roughly $3.25/user/mo (billed annually, works out to be $39) ... this year ... it would be $19/user/mo (billed annually, works out to be $228) — a 585% increase!
</blockquote>

But today, pricing is about something different.

## 5. 2020: It's all About CI Minutes, Baby

During consulting, I teach companies to switch positions with their CI. It's not rare that in smaller companies with up to 15 devs, the CI is set up once for a particular job, but no-one knows how to use it practically. Everyone is trying to make a build pass. That's it.

Instead, **the CI should be a helpful buddy**, that you can delegate any tedious works - from updated, from finding bugs... to checking that new pricing has been released, and next month it will cost you much more :).

We had few bugs with renamed classes and constant outside PHP in one project, usually too late or after a detailed code-review. Twig, Latte, NEON of YAML always had to be checked manually. That's such a waste of human attention and energy. Today, we don't know about these bugs because CI is taking care of them.

<br>

Saying that, **the more we delegate to CI, the more minutes we spend**. The more population Earth has, the more food we eat, even if we focus on optimizations like cached Container Builds. This reflects the pricing models of both services.

Do you recall how much time do you spend in your CI per pull-request run? How much is that a day... or a month?

From my personal experience, basic CI setup with two developers took around **2000-5000 minutes a month**. Both services have a limit of 3000 minutes/month for one-tier. If you cross it, you have to pay more or stop using it.

But that's **just bare setup**. Once we add:

* monorepo split
* template checks
* config checks
* translation validation
* [custom PHPStan rules](https://tomasvotruba.com/blog/2020/12/14/new-in-symplify-9-more-than-110-phpstan-rules/)
* automated [coding standard merging](https://tomasvotruba.com/blog/2020/12/28/why-coding-standards-should-not-be-part-of-ci/)
* or Rector run...

...we can easily get over 10 000-15 000 minutes month.

<br>

Now the interesting parts comes. Let's compare compare [GitHub pricing](https://github.com/pricing) and [GitLab pricing](https://about.gitlab.com/pricing/) from today:


<img src="https://user-images.githubusercontent.com/924196/110146332-b9e3f900-7dda-11eb-99fd-9ffb500095fc.png" class="img-thumbnail mb-2">
<em>Gitlab pricing</em>

<br>

<img src="https://user-images.githubusercontent.com/924196/110146328-b94b6280-7dda-11eb-97a2-332bbc0fd8f0.png" class="img-thumbnail mb-2">
<em>GitHub pricing</em>

<br>

If we take it by the best price per minute:

- 3 000 minutes/4 $ from GitHub
- 10 000 minutes/19 $ from Gitlab
- **50 000 minutes/21 $ from GitHub**
- 50 000 minutes/99 $ from Gitlab

For only 2 $, you get 40 000 extra minutes. That's madness, and that's the last why you should switch.

<br>

I think GitHub might take advantage of this an go from *21 $* to *69 $* without loosing an advantage. But it seems their product does not focus on profit, but **on us - customers**:

<blockquote class="twitter-tweet"><p lang="en" dir="ltr">In a world where rich companies take more and more, this is an amazing move by <a href="https://twitter.com/github?ref_src=twsrc%5Etfw">@github</a> <br><br>Thank you for taking whole open-source community to a brand new level for last 5 years ❤️️<br><br>I'm very happy to be one of your customers <a href="https://t.co/RbtpdA9s9W">pic.twitter.com/RbtpdA9s9W</a></p>&mdash; Tomas Votruba (@VotrubaT) <a href="https://twitter.com/VotrubaT/status/1360353684396380171?ref_src=twsrc%5Etfw">February 12, 2021</a></blockquote>

<script async src="https://platform.twitter.com/widgets.js" charset="utf-8"></script>

<br>


## The Best of Both Worlds

These 2 services are back and forth in a fight over customers, and that's great. Healthy competition between 2 companies brings value to everyone - motivation for the company to grow, new features to the users, and innovations in the field.

I'm delighted to have experienced GitHub and Gitlab on both open-source and private projects and see their struggle for a better product.

<br>

Do you want to switch from GitLab/Travis/Bitbucket to GitHub? Are you afraid of too high learning costs? [Hire me](https://tomasvotruba.com/contact/) to get it done fast.

Are you standing behind Gitlab even with its pricing? Let me know in the comments why.

<br>

Happy coding!
