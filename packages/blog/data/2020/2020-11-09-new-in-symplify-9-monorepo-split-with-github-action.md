---
id: 287
title: "New in Symplify 9: Monorepo&nbsp;Split with GitHub&nbsp;Action"
perex: |
    Until now, monorepo split in Symplify was [one big fractal of bad design](/blog/2020/11/02/symplify-monorepo-builder-split-fractal-of-bad-design/). In Symplify 9 and with technologies of 2020, we've decided to change that.

    **To simple setup in a single GitHub Action.**

---

In past years, the `split` command was one of the poorest part of whole Symplify code base. [Here is why](/blog/2020/11/02/symplify-monorepo-builder-split-fractal-of-bad-design/).

<br>

In Symplify 9, **we've finally fixed it** - with a [`symplify/monorepo-split-github-action`](https://github.com/symplify/monorepo-split-github-action) ↓

<div class="text-center mt-3 mb-5">
    <img src="/assets/images/posts/2020/split_monorepo.png" class="img-thumbnail">
    <br>
    <em>42 packages split under <strong>3,5 minutes</strong></em>
</div>


## Battle Tested

We've been testing it last 3 weeks on 3 different monorepos. Nathan, good friend of mine, was eager to test them too on [eonx-com monorepo](https://github.com/eonx-com/easy-monorepo/blob/master/.github/workflows/split_packages.yml).

This week, also [Ruud Kamphuis](https://github.com/ruudk) [pushed](https://github.com/symplify/github-actions/commits?author=ruudk) the package even further - thank you Ruud.

After dozens of successful GitHub Actions runs we're ready to go public - **to you**!

## GitHub Actions - Composer for your CI

The idea of GitHub Actions is as simple as Composer or Dependency Injection: **Do you need something? Ask for it.**

If you're not yet familiar with GitHub Actions, give them a try. [I first tried them almost a year ago](/blog/2020/01/27/switch-travis-to-github-actions-to-reduce-stress/), and every month I found something new to fell in love with.

## The Simplest GitHub Action for 1 Package Split

Do you need a monorepo split? Ask for it:

```yaml
# .github/workflows_monorepo_split.yaml
name: 'Monorepo Split'

on:
    push:
        branches:
            - main

jobs:
    monorepo_split:
        runs-on: ubuntu-latest

        steps:
            -   uses: actions/checkout@v2

            -
                uses: "symplify/monorepo-split-github-action@master"
                env:
                    GITHUB_TOKEN: ${{ secrets.ACCESS_TOKEN }}
                with:
                    # ↓ split "packages/easy-coding-standard" directory
                    package-directory: 'packages/easy-coding-standard'

                    # ↓ into https://github.com/symplify/easy-coding-standard repository
                    split-repository-organization: 'symplify'
                    split-repository-name: 'easy-coding-standard'

                    # ↓ the user signed under the split commit
                    user-name: "kaizen-ci"
                    user-email: "info@kaizen-ci.org"
```

The section `with:` is where you configure the output.

- `package-directory` is a local path to your split package, e.g. 'packages/easy-coding-standard'

- `split-repository-organization` and `split-repository-name` is name of repository on GitHub - basically `symplify/easy-coding-standard`

## How to Split with Tags?

What is splitting good for without tags, right?

We need a tag, so we ask for it [WyriHaximus/github-action-get-previous-tag](https://github.com/WyriHaximus/github-action-get-previous-tag):

```diff
 # .github/workflows_monorepo_split.yaml
 name: 'Monorepo Split'

 on:
     push:
         branches:
             - master

 jobs:
     monorepo_split:
         runs-on: ubuntu-latest

         steps:
             -
                 uses: actions/checkout@v2
+                # this is required for "WyriHaximus/github-action-get-previous-tag" workflow
+                # see https://github.com/actions/checkout#fetch-all-history-for-all-tags-and-branches
+                with:
+                    fetch-depth: 0

+            # get a tag see https://github.com/WyriHaximus/github-action-get-previous-tag
+            -
+                id: previous_tag
+                uses: "WyriHaximus/github-action-get-previous-tag@master"

            -
                uses: "symplify/monorepo-split-github-action@master"
                env:
                    GITHUB_TOKEN: ${{ secrets.ACCESS_TOKEN }}
                with:
                    package-directory: 'packages/easy-coding-standard'
                    split-repository-organization: 'symplify'
                    split-repository-name: 'easy-coding-standard'

+                   tag: ${{ steps.previous_tag.outputs.tag }}

                    # ↓ the user signed under the split commit
                    user-name: "kaizen-ci"
                    user-email: "info@kaizen-ci.org"
```

## How to Split Multiple Packages?

But wait, that's just one package. How can we do it for all 15 of them? Do we need 15 workflows files like this?

No, GitHub Actions are ready for this - add `strategy` section:

```diff
 ...
 jobs:
     monorepo_split:
         runs-on: ubuntu-latest

+        strategy:
+           fail-fast: false
+           matrix:
+               package:
+                    # list your packages here
+                    - easy-coding-standard
+                    - phpstan-rules
```

And update `with:` section with dynamic content:

```diff
                with:
-                   package-directory: 'packages/easy-coding-standard'
+                   package-directory: 'packages/${{ matrix.package }}'
                    split-repository-organization: 'symplify'
-                   split-repository-name: 'easy-coding-standard'
+                   split-repository-name: '${{ matrix.package }}'
```

**This is a vast performance improvement**, just like a parallel run.

Now GitHub Actions will run a standalone build for each package provided.


That's it!

<br>

## How to migrate Symplify 8 to Symplify 9?

The `split` command [was dropped in Symplify 9](https://github.com/symplify/symplify/pull/2490/files#diff-f4265307118be2f5d2389968d183656ebf8a0e8a7e711ed42101bd1bb179034f). Do you use it?

<br>

We've prepared a 3 step switch for you:

1. Remove `directories_to_repositories` parameter `from monorepo-builder.(yaml|php)`:

```diff
 use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
 use Symplify\MonorepoBuilder\ValueObject\Option;

 return function (ContainerConfigurator $containerConfigurator): void {
     $parameters = $containerConfigurator->parameters();
-    $parameters->set(Option::DIRECTORIES_TO_REPOSITORIES, [
-        'packages/*' => 'git@github.com:symplify/*.git',
-    ]);
};
```

2. Remove current `split` command from GitHub Action:

```bash
vendor/bin/monorepo-builder split
```

3. Add GitHub Action with a list of your packages in a `matrix`

<br>

Happy coding!
