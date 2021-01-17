---
id: 240
title: "How Many Days of Technical Debt Has your PHP Project"
perex: |
    Every project has technical dept. But how would you measure it? With [cognitive complexity](/blog/2018/05/21/is-your-code-readable-by-humans-cognitive-complexity-tells-you/)? The age of the framework? Any other guess?
    <br>
    <br>
    This year I've started to use CI service, which tells you the number in days. And it works pretty well... how many days Rector has? Keep on learning.
tweet: "New Post on #php 🐘 blog: How Many Days of Technical Debt Has your PHP Project   #sonarcube #githubactions"
tweet_image: "/assets/images/posts/2020/sonar_rector.png"

deprecated_since: "December 2021"
deprecated_message: |
    Sonarcube is an external tool that must be checked manually. It's an extra obstacle and we barely checked it every month or two.
    <br>
    <br>
    Much better approach is [CI-based feedback loop](/blog/2020/01/13/why-is-first-instant-feedback-crucial-to-developers/). **Use PHPStan and [cognitive complexity](/blog/2018/05/21/is-your-code-readable-by-humans-cognitive-complexity-tells-you/) instead**.
---

<a href="https://sonarcloud.io/dashboard?id=rectorphp_rector">
    <img src="/assets/images/posts/2020/sonar_rector.png" class="img-thumbnail">
</a>

## How Reliable is Technical Dept Metric?

When I first analyzed Rector, **top 5 worst classes had these in common**:

- "instanceof programming", e.g., 10-15 cases of `instanceof`, then do return some logic
- 30-50 lines long methods within 1 class
- classes of length 200-500 lines
- pain points I knew were there, but I was afraid to do something about it

As the first experiment, I picked a class that had **5 hours and 40 minutes** of technical debt, and I gradually converted it to [collector pattern](/blog/2018/06/14/collector-pattern-for-dummies/).

Do you want to see **real code**? Look at [this PR with `NodeTypeResolver` decoupling to 10 new classes](https://github.com/rectorphp/rector/pull/2767/files#diff-23d92ff042a5c83870af8b8d30bbdd8d).

## I Thought Removing Legacy Would be Fun...

...but removing my legacy code was rather painful. I had to [load huge methods](/blog/2018/05/21/is-your-code-readable-by-humans-cognitive-complexity-tells-you/) to my working memory, think about relations of huge monolithic class and try to split into the smallest standalone pieces.

**After 3 hours of work, I was exhausted**, but CI was passing, and the god class was gone. I pushed my work, merged the PR to the `master`, and waited for SonarCloud analysis... **from 5:40 I got into 2:40**. What?

After all this work, only such a small improvement? Don't take me wrong; the code was much improved, I'm just used to work more effectively with Rector-wave refactoring approach... no more [from months to days](https://freek.dev/1518-automatically-convert-your-code-to-php-74-syntax-using-rector)?

This process taught me a lesson:

<blockquote class="blockquote mt-5 mb-5 text-center">
    The legacy code will always be there. Observe it, measure it and remove it.
    <br>
    The later you start, the more it hurts. No matter what.
</blockquote>

Since then, **I'm adding SonarCube on every project I work on**, so I know (not just *feel*):

- what is the pain point,
- and where should we put the effort...

...**to keep code base fit for years**

<br>

Do you wonder **how many days you have on your back**? 10, 50 or over 100? Add your first SonarCube check into the CI and share with us in comments ↓

## How to add SonarCube to your Github Project in 6 Steps?

SonarCube is free for open-source and has a 1-week trial for private projects. I tried the 1-week trial on one private project, and then I saw the debt... **365**, ~~hours~~ days... you have to love it :D.

## 1. Add Project on SonarCloud

- [Create new project](https://sonarcloud.io/projects/create)

<img src="/assets/images/posts/2020/sonar_step_1.png" class="img-thumbnail">

## 2. Authorize Your Github Project

<img src="/assets/images/posts/2020/sonar_step_2.png" class="img-thumbnail">

Add the file and commit to `master`.

## 3. Add Github Action

We need to have a way to tell the Sonar that new code was pushed. That's what Github Actions are for.

Add new workflow:

```yaml
# .github/workflows/sonarcube.yaml
name: Sonar

on: push

jobs:
    sonar_cloud:
        runs-on: ubuntu-latest
        steps:
            -   uses: actions/checkout@master
                with:
                    # sonar needs non-shallow clone
                    fetch-depth: 10000

            -   uses: sonarsource/sonarcloud-github-action@master
                env:
                    ACCESS_TOKEN: ${{ secrets.ACCESS_TOKEN }}
                    SONAR_TOKEN: ${{ secrets.SONAR_TOKEN }}
```

As you can see, there are 2 tokens to authorize.

### How to get Tokens?

- https://github.com/settings/tokens → `ACCESS_TOKEN`
- https://sonarcloud.io/account/security → `SONAR_TOKEN`

### Where to place Them?

Add both tokens to your *secrets* sections in your repository: https://github.com/TomasVotruba/tomasvotruba.com/settings/secrets

## 4. Add Badge for Quick Link to SonarCloud Analysis

What is analysis good for if you can't reach it from your `README`? Be sure to add it there, so you can enter it quickly and share your excellent results with others.

* You can use the standard link: [SonarCube](https://sonarcloud.io/dashboard?id=TomasVotruba_tomasvotruba.com)
* But I went for **fancier custom badge** (where you can add your technical dept **days** number):

```markdown
[![SonarCube](https://img.shields.io/badge/SonarCube_Debt-%3C2-brightgreen.svg?style=flat-square)](https://sonarcloud.io/dashboard?id=TomasVotruba_tomasvotruba.com)
```

[![SonarCube](https://img.shields.io/badge/SonarCube_Debt-%3C2-brightgreen.svg?style=flat-square)](https://sonarcloud.io/dashboard?id=TomasVotruba_tomasvotruba.com)

Almost done...

## 5. Where is the Code?

We still have to tell SonarCube where to look for the `src` code. To do this, we need to add `sonar-project.properties`.

```bash
# sonar-project.properties
# see https://sonarcloud.io/documentation/project-administration/narrowing-the-focus/
sonar.organization=TomasVotruba
sonar.projectKey=tomasvotruba.com

# relative paths to the source, wildcards don't work
sonar.sources=src
```

To get `organization` and and `projectKey`, just split the key (`TomasVotruba_tomasvotruba.com`) by `_`.

## 6. Remove Spam Bot

**Do this AFTER the first analysis of `master` branch is completed.** If you do it earlier, the Github Action will not work.

<br>

There is a price for all the excellent features... you need to tolerate SonarCube **spam bot on every commit**.

<img src="/assets/images/posts/2020/sonar_spam.png" class="img-thumbnail">

**I hated it** and wanted to delete all this Sonar-spam from my repositories, but there is one solution out of it.

Go to [Github installations](https://github.com/settings/installations):

<img src="/assets/images/posts/2020/sonar_step_3.png" class="img-thumbnail">

And remove it:

<img src="/assets/images/posts/2020/sonar_step_4.png" class="img-thumbnail">

We only need it for the first contact. Instead of it, **you can authorize with Github Actions**.

<br>

And that should be it! (If not, let me know in comments.)

<a href="https://sonarcloud.io/dashboard?id=TomasVotruba_tomasvotruba.com">
    <img src="/assets/images/posts/2020/sonar_final.png" class="img-thumbnail">
</a>

<br>

## Trouble Shooting

### "Please add the secret GITHUB_TOKEN to the GitHub action for SonarCloud"

If you see this message, don'T try to add `GITHUB_TOKEN` to your Secrets in GitHub repository. It won't let you.
Instead, re-use already existing secret in your `.github/workflows/sonarcube.yaml`:

```diff
# .github/workflows/sonarcube.yaml
# ...
             -   uses: sonarsource/sonarcloud-github-action@master
                 env:
                     ACCESS_TOKEN: ${{ secrets.ACCESS_TOKEN }}
                     SONAR_TOKEN: ${{ secrets.SONAR_TOKEN }}
+                    GITHUB_TOKEN: ${{ secrets.SONAR_TOKEN }}
```

<br>

### Dual Analysis?

When CI fails for having both automatic and Github Action analysis, go to your project on SonarCube and disable it:

<img src="/assets/images/posts/2020/sonar_one_method.png" class="img-thumbnail">

<br>

### "Project was never analyzed. A regular analysis is required before a branch analysis."

I tried many paths, but I'm not aware of any specific solution for this. **Delete** project on Sonarcloud, **hide** local GitHub Action workflow and **star over**.

<br>

**Now you see your weakest points and [Fight the Hydra](https://joshkaufman.net/how-to-fight-a-hydra) with courage!**

<br>

Happy coding!
