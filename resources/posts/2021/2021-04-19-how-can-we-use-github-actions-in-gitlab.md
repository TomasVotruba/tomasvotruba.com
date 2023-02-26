---
id: 312
title: "How can We use GitHub&nbsp;Actions in&nbsp;Gitlab?"
perex: |
    One of my customers is building a monorepo, so we prepared a prototype on GitHub to test it out. It uses [Monorepo Split GitHub Action](/blog/monorepo-split-github-action-20-with-gitlab-split-is-out/) and works well.


    You know [I'm a big fan of GitHub](/blog/best-time-to-switch-gitlab-to-github), so when client asked me: **"how can we do it in Gitlab?"**
    <br>
    I was like: "that's not possible... you need to switch to GitHub".

---

...or was I?

Critical thinking can be easily disabled by [confirmation bias](https://en.wikipedia.org/wiki/Confirmation_bias). When I stop thinking critically and actually realize that, I try to get back to reality. One way to do that is to **describe the situation as it is in few simple words**.

<br>

**What we have now?**

- GitHub mono repository ✅
- working split on GitHub thanks to GitHub Action ✅
- Gitlab repositories ✅

**What we need?**

- Gitlab mono repository
- Gitlab split
- Gitlab repositories

## ~~Can We Get There?~~ How Can We Get There?

1. From GitHub mono repository to Gitlab mono repository. Basically, we need to switch the `origin` reference in git:

```bash
# remove github
git remote remove origin

# add GitLab
git remote add origin git.gitlab.com/...
git push origin
```

2. From GitHub Split Action to Gitlab?

My first idea was: *"I have no idea"*. Let's be honest here. It's not a very good solution, not a solution at all, not working, but we always have to start somewhere.

What is the next idea? Let's describe **world in simple words**.

## What is a GitHub Action?

"GitHub Action is some script that you call, and it does some work for you. Mostly in CI regularly."

Good. GitHub Actions is a CI, and Gitlab CI is a CI. **We have a mental bridge that we can use**.

"Yea, but GitHub Action is tailor-made for GitHub CI".

<br>

Ok, what exactly is GitHub CI doing when you run a GitHub Action in it?

"Well, it gets input arguments that we set and runs a script with them."

<br>

What do you mean by **a script**?

"There is Docker image that we download and run. The Docker image can call bash, PHP, or whatever other inner script and just pass the arguments in it."

<br>

Ok, so GitHub Action is **basically a Docker image that accepts arguments and runs them**?

```bash
docker run some-image $ARGUMENTS
````

"Yes!"

<br>

*Note:* there is 2nd way to build GitHub Action [via Javascript](https://docs.github.com/en/actions/creating-actions/about-actions#types-of-actions). I didn't follow that path, as I know Docker better than Javascript.

## 1. From GitHub Action Syntax to Pseudo Syntax

Let's look at CI configuration. In GitHub, it's YAML files located in the `.github/workflows` directory, in Gitlab, it's `.gitlab-ci.yml`.

<br>

This is how looks real **GitHub Action in normal syntax**:

```yaml
# .github/workflows/monorepo_split.yaml
jobs:
    monorepo_split:
        steps:
            # ...
            -
                uses: "symplify/monorepo-split-github-action@2.0"
                with:
                    package-directory: 'packages/easy-coding-standard'
                    split-repository-organization: 'symplify'
                    split-repository-name: 'easy-coding-standard'
```

How would the same action look like **in pseudo syntax** without GitHub syntax sugar?

```yaml
# use this docker image
image: "symplify/monorepo-split-github-action"

# setup input variables
env:
    FROM_PACKAGE: "packages/easy-coding-standard"
    TO_REPOSITORY: "https://github.com/symplify/easy-coding-standard"

# run docker image with input variables
script:
    - docker run symplify/monorepo-split-github-action $FROM_PACKAGE $TO_REPOSITORY
```

This is great! Now we know the exact steps that have to be reproduced in the next CI.

## 2. From Pseudo Syntax to GitLab CI Syntax

Now we take the pseudo syntax and try to fit in Gitlab conventions:

```yaml
# .gitlab-ci.yml
split_monorepo:
    # ...
    # set envs
    variables:
        FROM_PACKAGE: "packages/easy-coding-standard"
        TO_REPOSITORY: "https://github.com/symplify/easy-coding-standard"

    script:
        - docker run symplify/monorepo-split $FROM_PACKAGE $TO_REPOSITORY
```

That's it! **Now we're using GitHub Action in a Gitlab CI.**

<br>

## How to make GitHub Actions that GitLab Developers can use?

Not every GitHub Action will work out of the box. There are steps we have to think about:

1. build GitHub Action as a **Docker image** and not Javascript one
2. **publish the Docker image** to [Docker Hub](https://hub.docker.com/) (basically Packagist for Docker images), so anyone can use it
3. allow passing **Gitlab Access Token** for repository access
4. bonus: if you have many arguments, think of passing them as ENV variables to keep `.gitlab-ci.yaml` small

That's all, folks. I could not believe how simple this is.

Can't wait to give it a try? **Let me know what GitHub Actions you're using in Gitlab!**

<br>

Happy coding!
