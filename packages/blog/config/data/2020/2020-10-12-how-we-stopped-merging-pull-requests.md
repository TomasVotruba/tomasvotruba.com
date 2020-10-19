---
id: 282
title: "How We Stopped Merging Pull&nbsp;Requests"
perex: |
    What comes before merging a pull request? Code-review, feedback from developers, and fixes to make the reviewer happy. After that, we only need the tests, coding standard, PHPStan, and Rector to pass in the CI.
    <br><br>
    Here is an idea - **don't merge any pull-request from now on**...

tweet: "New Post on #php 🐘 blog: How We Stopped Merging Pull Requests    #cireview #kodiak"
tweet_image: "/assets/images/posts/2020/kodiak/kodiak_waiting.png"
---

...and let them opened for ages... no, just kidding.

## Don't forget to Merge

But if you already accepted the pull-request, the issues are resolved, **you still have to wait for CI to finish with green**. If you're lucky, it's under 3 minutes, if you're open source 5-8 minutes and with private project 5-30 minutes.

How to kill the waiting time? Go for a coffee, toilet break, or a social leak (Facebook, Twitter, or your favorite PHP blog), get back, see the green checkbox, and click on the merge button. **Or even worse** - you jump to another issue, [remember to merge](/blog/2018/08/27/why-and-how-to-avoid-the-memory-lock/), then switch your focus back and forth...

<img src="/assets/images/posts/2020/kodiak/kodiak_focus.png" alt="" class="img-thumbnail mt-5">

[The instant feedback](/blog/2020/01/13/why-is-first-instant-feedback-crucial-to-developers/) is killed, and so is the flow.


<blockquote class="blockquote text-center mt-5 mb-5" markdown="1">
"**Can you automate** responsibility developers have to think about?
<br>
Please do it!
<br>
They will be able to focus more and produce better quality code."
</blockquote>

<br>

## Wait for 240 pull-requests a Month? No, Thanks!

In [Rector](https://github.com/rectorphp/rector/pulse/monthly), [Symplify](https://github.com/symplify/symplify/pulse/monthly) and [Migrify](https://github.com/migrify/migrify/pulse/monthly) [mono-repositories](/clusters/#monorepo-from-zero-to-hero) we has 240 merge-request for just last month.

That's **240 distractions with ~5 minutes upkeep** = 2O hours wasted by brain-waiting and much more work ruined.


## Delegate and Automate Merge Request

What if I told you just a few percent of these manually? The rest is done by [Kodiak](https://github.com/marketplace/kodiakhq). That's not my full-time on-demand coworker, but Github Application that **handles automated merging**.

How does *Kodiak* work? You mark the pull-request with the "automerge" tag, then - if CI passes - the pull-request is merged. So instead of waiting 240 times for CI feedback, you'll **add the tag when you finish the review**. Then the pull-request is closed, and you can focus on the next work in the peace.

## 4 Steps to Setup Kodiak

### 1. Go to Setting of your GitHub Repository

<img src="/assets/images/posts/2020/kodiak/kodiak_branches_1.png" alt="" class="img-thumbnail mt-3">

### 2. Add Branch Checks for `master`

<img src="/assets/images/posts/2020/kodiak/kodiak_branches_2.png" alt="" class="img-thumbnail mt-3">

### 3. Select Jobs that are Required to Pass

<img src="/assets/images/posts/2020/kodiak/kodiak_require.png" alt="" class="img-thumbnail mt-3">

### 4. Enable Kodiak

Go to <a href="https://github.com/marketplace/kodiakhq">marketplace</a> and enable it.

### 5. Add `.kodiak.toml` Setup To the Repository code

```yaml
# .kodiak.toml
version = 1

# this saves deleting merged branches manually
merge.delete_branch_on_merge = true
```

<br>

Now Kodiak is enabled and waiting for your work!

## 1 Step to Automerge Pull-Request with Kodiak

### 1. After You Decide the PR is Ready, add the "automerge" tag

<img src="/assets/images/posts/2020/kodiak/koidak_tag.png" alt="" class="img-thumbnail mt-3">

Then you're finished. The Kodiak will handle the rest...

<br>

Kodiak waits for the CI to pass...

<img src="/assets/images/posts/2020/kodiak/kodiak_waiting.png" alt="" class="img-thumbnail mt-3">

...then merges and deletes branch:

<img src="/assets/images/posts/2020/kodiak/kodiak_merge.png" alt="" class="img-thumbnail mt-3">

<br>

Now you've one less to think about for the rest of your life.

<br>

Happy coding!
