---
id: 72
title: "Is Your CHANGELOG Useful or Just Boring Plain Text? And How to Fix That"
perex: '''
    Do you [keep a CHANGELOG](http://keepachangelog.com/)? You shoud! I [do](https://github.com/Symplify/Symplify/blob/master/CHANGELOG.md), because it's the main story about the open-source package.
    <br><br>
    And if you do, is it boring plain text or **useful rich markdown**?
'''
tweet: "New post on my blog: Keep Your CHANGELOG Useful with Links #github #changelog #dx"
tweet_image: "/assets/images/posts/2018/changelog/pkeep-a-changelog.jpg"
related_posts: 
---

This post is written in Markdown. Would you read it, if it would look like this?

<br>

*I wrote about monorepo before and - as ShopSyS and Google agrees - it's the best choice for longterm projects, like children or planet projects.*
 
*Moreover know, when Vitek showed me awesome tool called Tomono, that can merge git history from multiple repositories...*

<br>

Or this one:

<br>

*I [wrote about monorepo before](/blog/2017/01/31/how-monolithic-repository-in-open-source-saved-my-laziness/) and - as [ShopSyS](https://blog.shopsys.com/how-to-maintain-multiple-git-repositories-with-ease-61a5e17152e0) and [Google agrees](https://cacm.acm.org/magazines/2016/7/204032-why-google-stores-billions-of-lines-of-code-in-a-single-repository/fulltext) - **it's the best choice for longterm projects**, like children or planet projects.*
 
*Moreover know, when [Vitek](https://github.com/vitek-rostislav) showed me awesome tool called [Tomono](https://github.com/unravelin/tomono), that can **merge git history from multiple repositories**...*

<br>
<br>

Btw, Hypertext Markup Language (HTML) is here since [1993](https://en.wikipedia.org/wiki/HTML), so we might integrate it in its natural environment. Same for Markdown and not just because it's a markup language.

<br>
  
## What is Sad about Missing References?

Imagine you'll read my post first *plain text* version. While reading it, you might think:
 
- What is this monorepo? Isn't that *monolith* - an antipattern?
- I didn't know Google is using monorepo?
- Is that true name or made-up one?
 
You could read answer to all those questions if I'd only provide links - but **you can't, because there are no links**. You'd have to go to comments, ask them, wait for answers... So you'll probably end up closing my blog and never come again.

## What is Sad about Plain Text `CHANGELOG.md`?

It takes maintainer **a lot of effort** to [keep a changelog](http://keepachangelog.com), keep it updated, with every version and every new pull-request, refer issues, pull-request, @author references... 

<br>

<blockquote class="blockquote text-center">
    "Too many cooks spoil the broth"
</blockquote>

<br>

No surprise that most `CHANGELOG.md` files look like this:


```markdown
## v3.2.0 - 2018-01-18

### Changed

- #560 Added `PhpdocVarWithoutNameFixer` to `docblock.neon` level, thanks to @carusogabriel
- #578 Use `@doesNotPerformAssertions` in tests, thanks to @carusogabriel
```

Does your `CHANGELOG.md` look like this too? Is it just dump of [pull-requests](https://github.com/Symplify/Symplify/issues?q=is%3Apr+is%3Aclosed) combined with [releases](https://github.com/Symplify/Symplify/releases)? 

### Why do we Look to Changelog?

To find an answer:

- What has changed in new version?
- If it was `@deprecated`, what is the replacement?
- **Most often when it broke our code** and we're angry: what are the reasons for this change?
- How did it work before?
- Was there some issue?
- Who is that active person behind all pull-requests for this release?

I've asked all these questions when I was investigating bug in packages I used.

Often, release descriptions are not so detailed. In that case it is **really helpful to have comparision to previous version**, e.g. [3.1 to 3.2](https://github.com/Symplify/Symplify/compare/v3.1.0...v3.2.0).

But all of this requires time. A time that maintainer usually puts to new features or resolving bugs.

When I added [`CHANGELOG.md` to Symplify](https://github.com/Symplify/Symplify/blob/master/CHANGELOG.md) and moved all notes from [Github Releases](https://github.com/Symplify/Symplify/releases) there, I was in the same situation. Do I create new features or rather play and cuddle with my `CHANGELOG.md`?


## Can't let go? Automate!

I wanted both. Why? Because I was used to Github Released that work like I needed:

```markdown
## v3.2.0 - 2018-01-18

### Changed

- [#560](https://github.com/symplify/symplify/pull/560) Added `PhpdocVarWithoutNameFixer` to `docblock.neon` level, 
   thanks to [@carusogabriel](https://github.com/carusogabriel)
- [#578](https://github.com/symplify/symplify/pull/578) Use `@doesNotPerformAssertions` in tests,
   thanks to [@carusogabriel](https://github.com/carusogabriel)
```

I've closed myself to coffee house for 3 hours and I've came up with solution!

**A [Changelog Linker](https://github.com/Symplify/ChangelogLinker) was born**.

<br>

## 3 Steps To Add Links To Your `CHANGELOG.md`  

### 1. Install

```bash
composer require symplify/changelog-linker --dev
```

### 2. Run it

```bash
vendor/bin/changelog-linker CHANGELOG.md --repository https://github.com/symplify/symplify
```

*Option `--repository` is used for links to PRs, issues and commits.* 

It will complete links to:

- **PRs and issues**, e.g. `[#1](https://github.com/symplify/symplify/pull/1) - fix everything`
- **Commits links**, e.g. `see [#9b154d9]: https://github.com/Symplify/Symplify/commit/9b154d9b6e88075e14b6812613bce7c1a2a79daa]`
- **Version Diffs**, e.g. `# [v2.0.0](https://github.com/Symplify/Symplify/compare/v1.5.0...v2.0.0)`
- **Users**, e.g. `Thanks to [@SpacePossum](https://github.com/SpacePossum)`

### 3. Commit and Push

```bash
git add . 
git commit -m "CHANGELOG: add links to PRs, issues, version diffs and user names"
git push origin master
```

That's it

<br>

I'm sorry I didn't follow this rule from [PHP Package Checklist](http://phppackagechecklist.com/#1,2,3,4,5,6,7,8,9,10,11,12,13,14) and used Github Releases instead. But now I have no more excuses.

I hope you to...

<a href="htts://keepachangelog.com">
    <img src="/assets/images/posts/2018/changelog/keep-a-changelog.jpg" class="img-thumbnail">
</a>

Huge thanks to @olivierlacan for keepachangelog.com! It helped me a lot. 

*Oh, sorry...*
 
**Huge thanks to [@olivierlacan](https://github.com/olivierlacan)** for [keepachangelog.com](http://keepachangelog.com)!

<br>

Happy lazy linking! 