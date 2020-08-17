---
id: 234
title: "Why is First Instant Feedback Crucial to Developers?"
perex: |
    Do you *open-source*? Then you now that instant feedback is crucial to your contributors. The same applies to private companies.
    <br>
    <br>
    There are **2 types of feedback**: from human and machine.
    <br>
    <br>
    Which and how can we improve?

tweet: "New Post on #php 🐘 blog: Why is First Instant Feedback Crucial to Developers?"
---

## <Faster|Slow> Reaction → <T>Result

When they send PR to your project, and you let them wait for a week, they won't be so excited to process your code-review.

But when you react **the same day or same hour**, there is a significant change the PR will be updated and merged on the same day.

Honestly, **maintainers are not human routers for Github notifications**, and you have to be lucky to hit them at the same time you're online.

**How can we make feedback instant anyway?**

## More than 5 minutes? We've Lost a Contributor

Do you know [TTFB](https://en.wikipedia.org/wiki/Time_to_first_byte) - *time to first byte* - from front-end world?

There could be **TTFF** for pull-request: *time to first feedback*.

In my experience, when someone creates a PR to an open-source, **they have carefully made time for it**. In today's [attention economy](https://www.calnewport.com/blog/2017/11/30/on-the-complicated-economics-of-attention-capital), its the most expensive resource people have.

## Example: How an Hour for Open-Source is used?

Let's say you have started your dedicated hour to open-source a week.

**You send PR** with your feature, the maintainer is not online, so you wait:

- 5 minutes...
- 10 minutes...
- 13 minutes...
- ...and CI failed on test case

**You fix the test**, send new commit to the PR branch and wait:

- 5 minutes...
- 10 minutes...
- 13 minutes...
- ...and CI fails on invalid coding standards


Now you've spend **40 minutes** of your 60 minutes by:

- 10 minutes making feature
- 4 fixing one test case
- **26 minutes by waiting**

That's **65 % percent time wasted**.

This makes me want to throw the computer out of the window, procrastinate, check my messages or another shallow work that ruins all my focus.

**True story**, this was the situation for CI in Rector in 2019. It was soo frustrating. And I don't talk about [code coverage with Xdebug that took us 33 minutes](/blog/2019/09/02/how-to-speedup-test-coverage-on-travis-by-95-percent).

<br>

## Have you Met... Github Actions?

A miracle came to my life. Exactly week ago [Markus Staab](https://github.com/staabm) talked about the idea of trying Github Actions in Rector repository to ease work to Travis CI.

### What are Github Actions?

I've heard about Github Actions, but I didn't get the idea. Is it for deploy or a bot?

Now I know it's basically *Github CI* (honestly, they should rename it).

How does it work? Like in any other CI (Travis CI, Gitlab CI, Bitbucket CI, Circle CI, Jenkins...), we basically:

- configure some YAML file
- push some code change to your git
- and wait for a green or red light

That's it! I used obvious choice for open-source - Travis CI - for the last 8 years, but **the speed was bugging me**.

When I saw [first pull-request by Markus](https://github.com/rectorphp/rector/pull/2589/files), I had no idea where this will end.

## Travis CI and Github Actions in Numbers

From all the metrics out there, these tell the main story:

**Travis CI**

- [3 concurrent jobs](https://travis-ci.com/plans)
- waits 1-4 minutes after commit, before it even starts

**Github Actions**

- [20 concurrent jobs](https://help.github.com/en/actions/automating-your-workflow-with-github-actions/about-github-actions#usage-limits)
- starts all jobs in 20-30 seconds after the commit

<br>

And how does the switch affected the Rector repository?

<img src="/assets/images/posts/instant_feedback_travis_ci.jpg" class="img-thumbnail" style="max-width: 40em">

↓

<img src="/assets/images/posts/instant_feedback_github_actions.jpg" class="img-thumbnail" style="max-width: 40em">

<blockquote class="blockquote text-center">
    From ~15 minutes to just 3 minutes.
</blockquote>

**I was amazed! Thank you, Mark, for making this happen.**


<br>

In the next post, we'll look at **practical migration** of common and less-common open-source features. We'll look at Github Actions' weaknesses and migrate a massive pipeline with 15 jobs.

<br>

Happy feedback looping!
