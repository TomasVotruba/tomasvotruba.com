---
id: 315
title: "Write GitHub&nbsp;Actions for Gitlab&nbsp;Too"
perex: |
    In a recent post [How can We use GitHub Actions in Gitlab?](/blog/how-can-we-use-github-actions-in-gitlab), we looked at the idea, how both services could **the use same CI recipe**. As a Gitlab CI user, you can use some GitHub Actions to do the work for you.
    <br>
    <br>
    Today we look at **how to write such action** to provide an excellent developer experience for both.

tweet: "New Post on #php 🐘 blog: How to Write @gitHub Actions for @gitlab Too"
---

<div class="text-center">
    <blockquote class="blockquote" class="mt-3 mb-3">
        "If you want to go fast, go alone.
        <br>
        If you want to go far, go together."
    </blockquote>
</div>

## Let's Go Together

5 years ago [I gave a talk](https://www.youtube.com/watch?v=D827D5ILfh8) [Czech only] about 2 frameworks. These 2 frameworks were two different groups that didn't like each other. Both frameworks were written in PHP, both MVC and both were used by PHP developers. I was wondering, why not go together?

My talk was about how to **convert this aversion into a healthy competition, friendship, and mutual learning**.

<br>

What movie does that resemble?

<a href="https://www.slideshare.net/phplive/tom-votruba-jako-vinnetou-a-old-shatterhand-refaktoruj-nenvist-v-ptelstv">
    <img src="https://user-images.githubusercontent.com/924196/117082040-74439e00-ad41-11eb-9547-01a91a5dc6ad.jpg" class="img-thumbnail">
</a>

I would love to see a similar story between "Old Gitlabhand" and "GitHubtou" :). While researching Gitlab and GitHub in this theme, I found a post from 2018 called [GitLab CI/CD for GitHub](https://blog.anoff.io/2018-03-30-gitlab-ci-for-github/) by Andreas Offenhauser. The idea in the post is amazingly simple.

Back in 2018, GitHub didn't have a proper CI yet. We used more matured Travis, Circle CI, etc. Andreas suggests, **we can use the best of both worlds** - hook in GitHub to Gitlab CI pipelines and let GitHub collect Gitlab CI feedback.

<img src="https://blog.anoff.io/assets/gitlab-ci/feature.png" class="img-thumbnail" style="max-width: 25em">

The rest is history, but the underlying philosophy goes beyond time.

## How they Place the Input?

We already know [how to write `.gitlab-ci.yml`](/blog/how-can-we-use-github-actions-in-gitlab#2-from-pseudo-syntax-to-gitlab-ci-syntax) so we can use GitHub Action. But how can we write GitHub Action without writing two scripts - one for GitHub and another for Gitlab?

<br>

First, we need to use the Docker approach. **The GitHub docs suggest using Docker with arguments**:

```bash
docker run some-image $ARGUMENTS
```

Then work with arguments **by their order** - $1, $2 etc. In practise the GitHub workflow might look like this:

```yaml
# .github/workflows/monorepo_split.yaml
# ...
with:
    from-package: 'packages/easy-coding-standard'
    to-repository: 'https://github.com/symplify/easy-coding-standard'
```

<br>

The philosophy of **Gitlab is a bit different**. They are closer to Docker ideology, so instead of argument order, they promote ENV variables.

```yaml
# .gitlab-ci.yml
env:
    FROM_PACKAGE: "packages/easy-coding-standard"
    TO_REPOSITORY: "https://github.com/symplify/easy-coding-standard"
```

You can find this approach in [Postgres Docker](https://hub.docker.com/_/postgres), [Mysql Docker](https://hub.docker.com/_/mysql), and many others.

<br>

So Github script works with arguments order and Gitlab with ENV variables. That's a pickle. What now? Do we have to write two scripts to make our GitHub Action work for both?

## What is the Shared Way?

When I spoke about Nette and Symfony to both communities, I **focused on values they share** - active community, simple controller architecture, creative solutions in small packages. This way, we could find understanding from each other.

<br>

What would be the **shared** path here?

The default way of doing things suggests there are also alternative ways. You can use magic facades in Laravel by default, or you can use [constructor injection](/blog/2019/03/04/how-to-turn-laravel-from-static-to-dependency-injection-in-one-day/) alternative. I was looking for such an alternative for a while in GitHub and Gitlab, so they **can bridge together**.

## Research, Explore, Doubt

After a couple of days of frustration, I came across [Create custom Github Action in 4 steps](https://www.philschmid.de/create-custom-github-action-in-4-steps) post.

There was one **very important sentence**:

- `inputs`: defines the input parameters you can pass into your bash script.
    - You can access them with `$INPUT_{Variable}` in our example `$INPUT_POKEMON_ID`

So does that mean that this GitHub Action input:

```yaml
with:
    from-package: 'packages/easy-coding-standard'
    to-repository: 'https://github.com/symplify/easy-coding-standard'
```

is also:

```yaml
env:
    INPUT_FROM_PACKAGE: 'packages/easy-coding-standard'
    INPUT_TO_REPOSITORY: 'https://github.com/symplify/easy-coding-standard'
```

Can you see the shared pattern? GitHub is using ENV, Gitlab is using ENV...

<br>

**Heureka! We've found it!**

✅


## How to Write it Once?

The final solution is straightforward:

- we name the input variables with the single name
- on GitHub, we prefixed them with `INPUT_`

```php
$env = getenv();

$ciPlatform = '...'; // detect via known ci-based ENV variables

$envPrefix = $ciPlatform === 'GITHUB' ? 'INPUT_' : '';

// shared input
$packageDirectory = $env[$envPrefix . 'FROM_DIRECTORY'];
$toRepository = $env[$envPrefix . 'TO_REPOSITORY'];
```

In the end, the Docker image [has one script for both](https://github.com/symplify/monorepo-split-github-action/pull/10). You would not even notice what CI service it was written for originally.

<br>

So next time you'll be writing a GitHub Action, **think of your friends in Gitlab and write a Docker image for them too**. Thank you.

<br>

Happy coding!
