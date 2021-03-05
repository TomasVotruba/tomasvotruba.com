---
id: 305
title: "The Best Time to Switch Gitlab&nbsp;to&nbsp;GitHub"
perex: |
    I'm known for being in love with GitHub Actions, a true paradise for any open-source project. I don't have such experience with private projects pricing for this and other services like GitLab or Bitbucket.
    <br>
    <br>
    I assumed their quality is reflected in better price. But after today call with one of my client, I've learned about huge benefits in GitHub Actions for private projects too. So much it's worth the switch.

tweet: "New Post on #php 🐘 blog: The Best Time to Switch Gitlab to GitHub"
---

Let's take typical arguments why Gitlab CI is not better than GitHub, one by one.

## 1. GitLab has Much more Features than GitHub

When we compare CI services, this is one of typical arguments for Gitlab. I find it as useful as this: "this manual has 100x more content, it must be better".

Honestly, I have troubles finding simple settings like total CI minutes, so I have to call Honza and get mentoring on navigation. That's not how product should work. GitHub is growing on the features too, but there is a clear effort to improve DX/UX along with that. Less is more.

## 2. GitLab has More Advanced CI

This statement used to be true when the only CI GitHub had was Travis - an external service that got popular mainly thanks to free tier for open-source projects.

GitHub introduced a new CI service [in late 2018](https://github.blog/changelog/2018-10-16-github-actions-limited-beta/). I recall it did not seems useful to me, because most of the configuration happened in Javascript. Can you imagine configuring your Symfony services config in Javascript? I don't.

But they switched to YAML as well and as a bonus, added open-source storage for "Workflows". Thinks of it **as composer package for your CI**. Do you want to publish your project via SSH? No need to learn how it works in detail and risk security breach by inexperienced configuration. Just use SSH Workflow and complete needed ENV variables.

That was a breaking point for me to give GitHub Actions a try and switch all my open-source projects [from Travis to GitHub](/blog/2020/01/27/switch-travis-to-github-actions-to-reduce-stress/).

## 3. GitLab has Faster CI

I'm not sure how about 2019 and before, because I was not using GitHub
Actions yet. We run GetRector.org on Gitlab in 2018, because Gitlab had better Docker support in CI... and it had a CI :)

Since the switch from Travis/Gitlab to GitHub Actions, we've experience much faster CI reactions. On former, when the CI job was created, it sometimes took 40-60 seconds until the CI server noticed. But on GitHub Actions it's a matter of 5-10 seconds, that's blazing fast.

[Honza Mikes](https://github.com/JanMikes) explained me, it might be related to Microsoft Azure. Since Azure and GitHub both belong to Microsoft, they probably have internal bridge with priority in speed. Corporation co-operation well done :)

## 4. GitLab is Cheaper than GitHub

This used to be true back in 2015, when GitLab allowed unlimited private repositories to unlimited amount of users. That was a game changed, as GitHub had this as payed service. I recall that was a huge spike for developer interest on Twitter, just because of this sole feature.

As the time went by and both companies grew, GitHub introduced private repositories for free too.

[In January 2021 **Gitlab introduced new pricing**](https://techcrunch.com/2021/01/26/gitlab-reshuffles-its-paid-subscription-plans/). Before we get into that, I came across post about similar topic back from 2018 - [How #GitLab Will Lose Business](https://mlaccetti.medium.com/how-gitlab-will-lose-business-bea8fb2f0fd4). Quoting:

<blockquote class="blockquote">
    When we got the license in 2017, it was roughly $3.25/user/mo (billed annually, works out to be $39) ... this year ... it would be $19/user/mo (billed annually, works out to be $228) — a 585% increase!
</blockquote>

But today, pricing is about something different.

## 4. 2020: It's all About CI Minutes, Baby

During consulting, I teach companies to switch positions with their CI. It's not rare, that in smaller companies with up to 15 devs, the CI is setup once, for very specific job, but no-one know how to practically use it. Everyone is trying to make build pass, that's it.

Instead, **the CI should be a helpful buddy**, that you can delegate any boring works - from updated, from finding bugs... to checking that new pricing has been released and next month it will cost you much more :).

In one project, we had few bugs with renamed classes and constant outside PHP. Usually too late or after detailed code-review. Twig, Latte, NEON of YAML always had to be checked manually. That's such a waste of human attention and energy. Today, we don't know about these bugs, because CI is taking care of it.

<br>

Saying that, **the more we delegate to CI, the more minutes we spend**. The more population Earth has, the more food we eat, even if we focus on optimizations like cached Container Builds. This reflects pricing models of both services.

Do you recall how much time do you spend in your CI per pull-request run? How much is that a day... or a month?

From my personal experience, basic CI setup with 2 developers took around **2000-5000 minutes a month**. Both services have a limit of 3000 minutes/month for one tier. If you cross it, you have to pay more or stop using it.

Let's compare compare [GitHub pricing](https://github.com/pricing) and [GitLab pricing](https://about.gitlab.com/pricing/) from today:


<img src="https://user-images.githubusercontent.com/924196/110146332-b9e3f900-7dda-11eb-99fd-9ffb500095fc.png" class="img-thumbnail">

<img src="https://user-images.githubusercontent.com/924196/110146328-b94b6280-7dda-11eb-97a2-332bbc0fd8f0.png" class="img-thumbnail">

If we take it by the best price per minute:

- 3 000 minutes/4 $ from GitHub
- 10 000 minutes/19 $ from Gitlab
- **50 000 minutes/21 $ from GitHub**
- 50 000 minutes/99 $ from Gitlab

For only 2 $, you get 40 000 extra minutes. That's madness and that's the last why you should switch.

<br>

I think GitHub might take advantage of this an go from 21 $ to 69 $, but it seems their product does not focus on profit, but on us - customers:

<blockquote class="twitter-tweet"><p lang="en" dir="ltr">In a world where rich companies take more and more, this is an amazing move by <a href="https://twitter.com/github?ref_src=twsrc%5Etfw">@github</a> <br><br>Thank you for taking whole open-source community to a brand new level for last 5 years ❤️️<br><br>I&#39;m very happy to be one of your customers <a href="https://t.co/RbtpdA9s9W">pic.twitter.com/RbtpdA9s9W</a></p>&mdash; Tomas Votruba (@VotrubaT) <a href="https://twitter.com/VotrubaT/status/1360353684396380171?ref_src=twsrc%5Etfw">February 12, 2021</a></blockquote> <script async src="https://platform.twitter.com/widgets.js" charset="utf-8"></script>

<br>


## The Best of Both Worlds

These 2 services are back and forth in fight over customers, and that's great. Healthy competition between 2 companies brings value to everyone - motivation for company to grow, new features to the users and innovations in the field.

I'm very happy to have experienced GitHub and Gitlab on both open-source and private projects and see their struggle for better product.

<br>

Do you want to switch from GitLab/Travis/Bitbucket to GitHub? Are you afraid of too high learning costs? [Hire me](https://tomasvotruba.com/contact/) to get it done fast.

Are you standing behind Gitlab even with it's pricing? Let me know in comments why.

<br>

Happy coding!
