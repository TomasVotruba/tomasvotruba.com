---
id: 187
title: "How we Automated Shopsys Packages Release from 2 days to 1&nbsp;Console Command"
perex: |
    Do you **release open-source**? Do you have **monorepo**? Do you release over **10 monorepo packages at once**?
    Do you still do it manually per package just to be sure?
    <br><br>
    Good news, it's not a single day-task, but this whole process can be automated. Let's me show how we did it in Shopsys.

tweet: "New Post on #php 🐘 blog: How we Automated @shopsysfw Packages Release from 2 days to 1 Console Command     #monorepo #symfony #git #semver #composer #symplify"
tweet_image: '/assets/images/posts/2019/release/pr.png'
---

Monorepo release management has ~~few~~ many gotchas. The one that requires most of your attention I described in [Monorepo Composer Magic](/blog/2019/01/31/monorepo-composer-magic/) post. **1 missed commit or forgotten version change** in `composer.json` of your package and **you've just released a broken package**!

I mean the ugly kind of errors you'll find out only after someone tries to install the package. Of course, you can improve this by [3-layer monorepo tests](/blog/2018/11/22/how-to-test-monorepo-in-3-layers/), but there is still a 50 % chance for human error.

Let's get practical!

## The *Manual* Shopsys Release Process

[Shopsys](https://github.com/shopsys/shopsys) is an open-source e-commerce build on Symfony. I helped them with monorepo, Symfony and open-source standards setup through 2018.

When we first looked at release todo-document, it had roughly 17 steps. After a bit deeper look we realized some steps have actually 5-10 more steps in it. In the end, we found over **40 steps in 3 various stages**.

Just to give you the idea...

### Before Release Stage

- check if Travis passes for all packages
- bump package interdependency for tagged version
- bump Docker image version to tagged version
- [validate `composer.json` dependencies](/blog/2018/10/08/new-in-symplify-5-create-merge-and-split-monorepo-with-1-command/#2-validate-it) of each package
- [dump `CHANGELOG.md`](/blog/2018/06/25/let-changelog-linker-generate-changelog-for-you/) since the previous release
- check `UPGRADE.md`
- ...

### Release Stage

- check changelog has today's date
- push the tag (and let CI service do the split)
- ...

### After Release Stage

- open branch alias for `next-version-dev`
- bump package interdependency for `next-version-dev`
- bump Docker image version to `dev`
- check packages are pushed on Packagist
- ...

<p class="text-muted">
Do you want to check them all? Just see <a href="https://github.com/shopsys/shopsys/tree/master/utils/releaser/src/ReleaseWorker">this directory on Github</a>.
</p>

<br>

Shopsys [releases new version every month](https://github.com/shopsys/shopsys/releases) and they had to do all these steps manually. Until now. Automation of this process **saves time and attention of 3 developers** (2 for code-review), that could be used for new features.

No surprise here, that the final [pull-request](https://github.com/shopsys/shopsys/pull/623) got a bit out of hand...

<img src="/assets/images/posts/2019/release/pr.png" class="img-thumbnail text-center">

It **took 49 days** and **3 900 new lines** to get the PR merged. Why? Well, when you introduce simple automatization of a complex process, **people start to see how easy is to convert manual daunting work to a new PHP class that does the work for them**. So more and more ideas came.

## From Human Check-list to Command with Workers

To automate the process, we used MonorepoBuilder, resp. it's [release-flow feature](https://github.com/symplify/monorepobuilder#6-release-flow).

```bash
composer require symplify/monorepo-builder
```

The implements a worker for each step described above. Workers are grouped by stage and ordered by priority, so the whole process is under control.

```php
<?php declare(strict_types=1);

namespace Utils\Release\ReleaseWorker;

use Nette\Utils\DateTime;
use Nette\Utils\FileSystem;
use Nette\Utils\Strings;
use Symplify\MonorepoBuilder\Release\Contract\ReleaseWorker;
use PharIo\Version\Version;

final class UpdateChangelogToDateReleaseWorker implements ReleaseWorkerInterface
{
    /**
     * 1 line description of what this worker does, in a commanding form! e.g.:
     * - "Add new tag"
     * - "Dump new items to CHANGELOG.md"
     * - "Run coding standards"
     */
    public function getDescription(Version $version): string
    {
        return 'Update CHANGELOG.md "Unreleased" to version and today date';
    }

    /**
     * Higher first
     */
    public function getPriority(): int
    {
        return 1000;
    }

    public function work(Version $version): void
    {
        $changelogPath = getcwd() . '/CHANGELOG.md';
        $content = FileSystem::read($changelogPath);

        // before: ## Unreleased
        // after: ## v7.0.0-beta6 - 2019-02-18
        $newContent = Strings::replace(
            $content,
            '#^\#\#Unreleased$#',
            '## ' . $version->getVersionString() . ' - ' . DateTime::from('today')->format('Y-m-d')
        );

        FileSystem::write($changelogPath, $newContent);
    }
}
```

Each step is written this way.

<p class="text-muted">
Do you want to include stages? We did, so the worker implemented <code>Symplify\MonorepoBuilder\Release\Contract\ReleaseWorker\StageAwareInterface</code>.
</p>

In these workers, you can trigger Travis CI with API, use Packagist API to check new version are released... sky it the limit.

## Shopsys Release Process *Now*?

```bash
vendor/bin/monorepo-builder release v7.0.0-beta6 --stage release-candidate

# → review...

vendor/bin/monorepo-builder release v7.0.0-beta6 --stage release

# → CI work + 2nd review...

vendor/bin/monorepo-builder release v7.0.0-beta6 --stage after-release
```

Complex process made simple with PHP! <em class="fas fa-lg fa-check text-success"></em>

<br>

Btw, do you know how Symplify 14-package monorepo release process looks like?

```bash
vendor/bin/monorepo-builder release v5.4.1
```

Just with bare [MonorepoBuilder](https://github.com/Symplify/MonorepoBuilder) install.

<br>

**How does your package release process look like?**
