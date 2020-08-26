---
id: 144
title: "New in Symplify 5: Generate Rich, Precise and Smart Changelog in Seconds"
perex: |
    ChangelogLinker started as a small tool to complete links to PRs, authors, and versions in `CHANGELOG.md`. Then it started to [generate](/blog/2018/06/25/let-changelog-linker-generate-changelog-for-you/) the `CHANGELOG.md`.
     <br><br>
     **Where is now and how to start using it?**
tweet: "New Post on my Blog: New in #Symplify 5: #ChangelogLinker - Generate Rich, Precise and Smart #Changelog in Seconds    #git #github #api #regex"
---

### Tested on Humans ✅

<a href="https://github.com/shopsys/shopsys/pull/446/files" class="btn btn-dark btn-sm mt-2">
    <em class="fab fa-github fa-fw"></em>
    See pull-request #446 on Shopsys
</a>

<blockquote class="blockquote text-center">
    "If you're not embarrassed by the first version of your product,<br>
    you've launched too late."
    <footer class="blockquote-footer"><a href="https://www.linkedin.com/pulse/arent-any-typos-essay-we-launched-too-late-reid-hoffman">Reid Hoffman</a>, Founder of Linkedin</footer>
</blockquote>

The first version of any software is how the author(s) think people will use it. It's like trying to see the future of people you've never met. That's why the first version is **better done than perfect**. The important part is to **collect feedback as soon as possible and improve based** on it.

Saying that I recommended ChangelogLinker to Shopsys to manage news in their [monorepo](https://github.com/shopsys/shopsys). In exchange, I got informative and clear feedback with creative ideas on how to solve it from [Rostislav Vitek](https://github.com/vitek-rostislav) and [Petr Heinz](https://github.com/petrheinz). **Huge thanks for making this tool better belongs to you guys**.

<br>

First, install this package:

```bash
composer require symplify/changelog-linker --dev
```

## 1. Multiple `CHANGELOG.md` for Smaller Versions

<a href="https://github.com/symplify/symplify/pull/1047/files#diff-3b69acbe6b33a88158b373e6e96de097" class="btn btn-dark btn-sm">
    <em class="fab fa-github fa-fw"></em>
    See pull-request #1047
</a>

If you have more than 2 major versions, your changelog can be really long and hard to orientate. Some people want to see the news in your version 3, some are still using version 1 and need to upgrade gradually.

I got this inspiration from Symfony, where they have **own CHANGELOG per minor versions*:

```bash
CHANGELOG-4.0.md
CHANGELOG-4.1.md
```

Good idea to keep files not huge and clear. So **Now** it's possible to work with each CHANGELOG file separately - just use file path as the first argument of any command:

```php
vendor/bin/changelog-linker link # "CHANGELOG.md" is used
vendor/bin/changelog-linker link CHANGELOG-2.md
vendor/bin/changelog-linker link CHANGELOG-3.md
```

## 2. Smarter Last Change Detection

<a href="https://github.com/symplify/symplify/commit/05d91b9412ebec49a66a4717d856a5a2c6718232" class="btn btn-dark btn-sm">
    <em class="fab fa-github fa-fw"></em>
    See commit
</a>

This package generates changelog from merged PRs, that are not mentioned in your `CHANGELOG.md` yet.

```bash
vendor/bin/changelog-linker dump-merges
```

**Before** it looks for the highest merged PR ID and added only PRs with higher id. But what if you merge PR with number 1000, but number 990 is still opened due to longer code review?

**Now** it works with the `merged_at` instead, so no merged PR is left behind.

## 3. Remove Dead Links

<a href="https://github.com/symplify/symplify/pull/1045/files" class="btn btn-dark btn-sm">
    <em class="fab fa-github fa-fw"></em>
    See pull-request #1045
</a>

This is the best command to start with when you install this package for the first time.

`CHANGELOG.md` can be edited, items removes, shifter above or links duplicated. To keep it fit and slim, you'd have to check this manually with every link. And believe me, if you have 500+ PRs, 50+ contributors and 30+ versions, it's not as fun as you imagine.

That's where "cleanup" rocks:

```bash
vendor/bin/changelog-linker cleanup

# again, you can use the file as argument
vendor/bin/changelog-linker cleanup CHANGELOG-2.md
```

In Symplify `CHANGELOG.md` itself it [removed 50 dead lines](https://github.com/symplify/symplify/pull/1045/files#diff-4ac32a78649ca5bdd8e0ba38b7006a1e).

## 4. Improved Category Detection

<a href="https://github.com/symplify/symplify/pull/1064/files#diff-2ee93fc74523d03ea046d5419ae75a9a" class="btn btn-dark btn-sm">
    <em class="fab fa-github fa-fw"></em>
    See pull-request #1064
</a>

<small>
Thanks to <a href="http://github.com/petrheinz">Petr Heinz</a> ❤️️
</small>

<br>

When you generate a `CHANGELOG.md` you can use `--in-categories` option:

```bash
vendor/bin/changelog-linker dump-merges --in-categories
```

It will assign PRs to one of 4 categories: *Added*, *Fixed*, *Changed* and *Removed*. I didn't make them up, it's a standard taken from [keepachangelog.com](https://keepachangelog.com/en/1.0.0).

**How it works?** It uses regex to detect keywords in the pull-request title, e.g.

`Added "cleanup" command` → Added
`Remove dead links` → Removed
`Fix ID detection` → Fixed

You can see all the regexes in [`CategoryResolver`](https://github.com/symplify/symplify/blob/v5.0.0/packages/ChangelogLinker/src/ChangeTree/Resolver/CategoryResolver.php).

Peter added many keywords, but also showed me a new trick : **the [`\b` wrapper](https://www.regular-expressions.info/wordboundaries.html)**:

```bash
-private const ADDED_PATTERN = '#(add|added|adds) #i';
+private const ADDED_PATTERN = '#\b(add(s|ed|ing)?)\b#i';
```

It means, that words need to be standalone. Not part of any other string.
Regex `\b(is)\b` applied to `"This island is beautiful"` returns `["is"]`.

A very nice trick that made detection much more precise. Thanks, Peter!

<br>

Happy changing!
