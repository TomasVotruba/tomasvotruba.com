---
id: 249
title: "Forget Complex&nbsp;Migrations, Use&nbsp;Cleaning&nbsp;Lady Checklist"
perex: |
    Migration of legacy code base is a complex process. If we [migrate spaghetti](/blog/2020/04/13/how-to-migrate-spaghetti-to-304-symfony-5-controllers-over-weekend/), [one framework to another](/blog/2019/08/26/how-we-migrated-54-357-lines-of-code-nette-to-symfony-in-2-people-under-80-hours/) or [remove dead-code from 120 k-lines project](/blog/2019/12/09/how-to-get-rid-of-technical-debt-or-what-we-would-have-done-differently-2-years-ago/).
    <br>
    <br>
    *It's long, it's hard, it takes an expert to do it...* that's bullshit. **It should be simple, easy to understand and clear.** Like the code we strive to write.
    <br>
    <br>
    How **could any programmer start migration today** without any daunting studying?

tweet: "New Post on #php 🐘 blog: Forget Complex Migrations, Use Cleaning Lady Checklist"

deprecated_since: "May 2021"
deprecated_message: |
    The checklist would require more work to be useful. Also every migration is a bit different and requires specific list of steps.
    <br>
    <br>
    **We work on project that will include "Cleaning Lady List" spirit, but will handle the work for you - stay tuned for *Rector Click***.
---

## Simplicity beats Unread Knowledge

I wrote few sum-up posts about migrations in general:

- [8 Steps You Can Make Before Huge Upgrade to Make it Faster, Cheaper and More Stable](/blog/2019/12/16/8-steps-you-can-make-before-huge-upgrade-to-make-it-faster-cheaper-and-more-stable/)
- [5 Things I Improve when I Get to new Repository](/blog/2019/12/23/5-things-i-improve-when-i-get-to-new-repository/)

If you have 6 and 4 minutes to read them, read them. They sum up the experience that you can apply to most PHP projects.
But most people don't have 10 minutes to spare.

<br>

I don't. **I have 30 seconds to solve my problem**.

<br>

<blockquote class="blockquote text-center">
    "If you can't explain it simply,<br>
    you don't understand it well enough."

    <footer class="blockquote-footer">Albert Einstein</footer>
</blockquote>

<br>

## Look for a Pattern

When there is a new project to migrate or upgrade, I cooperate with the in-house team to perform the migration together.

After 15-20 such projects, I've noticed a pattern:

- same steps **repeat** over and over again
- those steps can be **split into smaller steps**
- these steps can be **done me or by the team** (usually 50:50)
- they're **must have before Rector migration** I have to handle
- somebody wrote a great post about *Why* or *How* to do these steps, so even [a junior](/blog/2020/03/02/we-do-not-need-senior-developers-we-need-senior-code-bases/) can handle them
- I mention these steps in 1st step - project feedback over and over again

<br>

I was running towards sunset this Saturday, and a simple idea came to me. It felt great and made sense, so I sprint back to my home to scratch it down and [shared it with you](https://twitter.com/VotrubaT/status/1254188338581471232).

<br>

2 hours later... voilá 🎉

<img src="/assets/images/posts/2020/checklist_overview.png" alt="" class="img-thumbnail">

## How to use it?

- Fill in your project and **activate it**
- **Check what you've done** (or what you don't need)
- The name of project ~= namespace for cache storage, so items are stored per project
- It uses local storage of your browser, so no-one will steal your data


<img src="/assets/images/posts/2020/checklist_howto.gif" alt="" class="img-thumbnail">

<br>

KISS!

<br>

The checklist is my first non-PHP application in years, and I need your feedback to make it better.

**Let me know how you use it or what steps you miss**.
Keep in mind these steps should generally be relevant to most PHP projects.


<br>

Happy coding!
