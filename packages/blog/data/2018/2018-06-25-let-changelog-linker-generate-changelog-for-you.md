---
id: 117
title: "Let Changelog Linker Generate CHANGELOG.md for You"
perex: |
    Do you have an open-source project on Github? Do you want your users to know about new features and changes without you writing posts about it?
    Do you [keep a changelog](https://keepachangelog.com/en/1.0.0)? Do you struggle with keeping it up-to-date and descriptive and with all the links to all merged pull-requests?
    <br>
    <br>
    Yes? Then you'll love *Changelog Linker*. A PHP CLI tool that does all this boring work for you.
tweet: "New Post on my Blog: Let Changelog Linker Generate CHANGELOG.md for You #github #keepachangelog #staylazy #markdown"
tweet_image: "/assets/images/posts/2018/generate-changelog/tweet.png"

deprecated_since: "March 2021"
deprecated_message: |
    ChangelogLinker is now deprecated, because it has same features as GitHub pull-request overview with no added value. See [this issue in detail](https://github.com/symplify/symplify/issues/3027). Use [lob/generate-changelog](https://github.com/lob/generate-changelog) or [github-changelog-generator](https://github.com/github-changelog-generator/github-changelog-generator) instead or better switch to **[GitHub releases](https://docs.github.com/en/github/administering-a-repository/managing-releases-in-a-repository)**.
---

Changelogs have many forms.

From standard [keepachangelog.com](https://keepachangelog.com/en/1.0.0) with *Added*, *Changed*, *Fixed* and *Removed*:

<img src="/assets/images/posts/2018/generate-changelog/keepachangelog.png" class="img-thumbnail">

Through [Symfony dump](https://raw.githubusercontent.com/symfony/symfony/master/CHANGELOG-4.1.md):

<img src="/assets/images/posts/2018/generate-changelog/symfony.png" class="img-thumbnail">

Over [PHPStan Release notes](https://github.com/phpstan/phpstan/releases/tag/0.10):

<img src="/assets/images/posts/2018/generate-changelog/phpstan.png" class="img-thumbnail">

## Why is Every Changelog Different?

There is a standard recommendation in [keepachangelog.com](https://keepachangelog.com/en/1.0.0), so it can all be the same, right?
I think it's because **there is no easy way to generate such changelog**. And by easy I mean automated = 1 click solution. And we don't even talk about monorepo changelog yet.

When I looked on Github [for inspiration](https://github.com/symplify/symplify/issues/841), I found only [github-changelog-generator](https://github.com/github-changelog-generator/github-changelog-generator) - that has over 4600 stars on Github. Yet, it still doesn't work with *Added*, *Changed* etc. categories and requires labeling issues and pull-requests and adding milestones. **I wanted to save time, not to add extra work.**

## Category + Monorepo Support?

All I wanted to do is run 1 command and update `CHANGELOG.md` with content like this:

```markdown
## [v4.4.0] - 2018-06-03

### Added

#### BetterPhpDocParser

- [#811] Add multi-types method support
- [#810] Add `AbstractPhpDocInfoDecorator`
- [#809] Allow `PhpDocInfoFactory` extension without modification
- [#807], [#808] Add `replaceTagByAnother()`
- [#806] Add `getParamTypeNodeByName()`
- [#804] Add `hasTag()` to `PhpDocInfo` and other improvements
- [#801] Add `PhpDocModifier` class

#### CodingStandard

- [#851] Add _ support to PropertyNameMatchingTypeFixer
- [#845] Extended RemoveEmptyDocBlockFixer fix
- [#836] Improve cognitive complexity error, Thanks to [@enumag]
- [#823] Add Cognitive complexity sniff
```

I aim for 80/20 rule = let 80 % of manual work, manual linking, collecting of *Added*, grouping by *CodingStandard* package etc. handle program for us. Then we can polish the rest 20 %, like adding a description to the release or moving PR that the program failed to classify.

I imagined something like:

```bash
vendor/bin/changelog-linker dump-mergers
```

That would be much better than looking on Github, going through commits and putting it all together manually, right? Or bare git dump that no-one except you orients in.

Well, 2 months of work later and after detailed feedback from [Matouš Czerner](https://github.com/MattCzerner) and [Rosťa Vítek](https://github.com/vitek-rostislav) whom I'm very thankful, a [Symplify\ChangelogLinker](https://github.com/symplify/changeloglinker) package was born.

## 5 Steps to Your Generated CHANGELOG

1. Install it

    ```bash
    composer require --dev symplify/changelog-linker
    ```

2. Add target to your `CHANGELOG.md`

    ```markdown
    # CHANGELOG

    This is a changelog, you know?

    <!-- changelog-linker -->
    ```

    There will be dumped the list of changes.

3. Run it dry

    ```bash
    vendor/bin/changelog-linker dump-merges --dry-run
    ```

    to see this preview:

    ```markdown
    ## Unreleased

    - [#868] [ChangelogLinker] Add ChangeTree to manage merge messages
    - [#867] [ChangelogLinker] Change Worker registration from implicit to explicit
    - [#864] [MonorepoBuilder] improve coverage

    [#868]: https://github.com/symplify/symplify/pull/868
    [#867]: https://github.com/symplify/symplify/pull/867
    [#864]: https://github.com/symplify/symplify/pull/864
    ```

4.  There are 2 more cool options: `--in-packages` and `--in-categories`. How to they work?

    This...

    ```bash
    vendor/bin/changelog-linker dump-merges --dry-run --in-categories
    ```

    ...will create:

    ```markdown
    ## Unreleased

    ### Added

    - [#868] [ChangelogLinker] Add ChangeTree to manage merge messages

    ### Changed

    - [#867] [ChangelogLinker] Change Worker registration from implicit to explicit
    - [#864] [MonorepoBuilder] improve coverage

    [#868]: https://github.com/symplify/symplify/pull/868
    [#867]: https://github.com/symplify/symplify/pull/867
    [#864]: https://github.com/symplify/symplify/pull/864
    ```

    And this...

    ```bash
    vendor/bin/changelog-linker dump-merges --dry-run --in-packages
    ```

    ...will create:

    ```markdown
    ## Unreleased

    ### ChangelgoLinker

    - [#868] Add ChangeTree to manage merge messages
    - [#867] Change Worker registration from implicit to explicit

    ### MonorepoBuilder

    - [#864] improve coverage

    [#868]: https://github.com/symplify/symplify/pull/868
    [#867]: https://github.com/symplify/symplify/pull/867
    [#864]: https://github.com/symplify/symplify/pull/864
    ```

    Pro tip: you can combine them and set the priority by order:

    ```bash
    vendor/bin/changelog-linker dump-merges --dry-run --in-categories --in-packages
    vendor/bin/changelog-linker dump-merges --dry-run --in-packages --in-categories
    ```

5. When you're ready, run dump to `CHANGELOG.md`:

    ```bash
    vendor/bin/changelog-linker dump-merges --in-packages --in-categories
    ```

## Do You Have Existing `CHANGELOG.md`?

The Changelog Linker doesn't work only for new changes, **it can also link your existing file**:

```bash
vendor/bin/changelog-linker linkify
```

## ProTip: Use Composer Script and Forget

Composer scripts are handy, if you use CLI command with many options and **you don't want to remember and type them over and over again**.

This is how I simplify my setup for Symplify `CHANGELOG.md`:

```json
{
    "scripts": {
        "changelog": "vendor/bin/changelog-linker dump-merges --in-categories --in-packages"
    }
}
```

So all I need to do is run...

```bash
composer changelog
```

...and the `CHANGELOG.md` file is updated. Pretty cool, huh?

Find more about Composer scripts in *[Have you tried Composer Scripts? You may not need Phing](https://blog.martinhujer.cz/have-you-tried-composer-scripts)* post by amazing Martin Hujer.

<br>

That's it.

<br>

**For more features like linked *thanks*, package aliases or linked words see [README](https://github.com/symplify/changeloglinker).**

<br>

Enjoy your new free time!
