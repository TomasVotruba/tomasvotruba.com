---
id: 310
title: "Monorepo Split GitHub Action 2.0 with Gitlab split is Out!"
perex: |
    [The version 1.0 was released 6 months ago](/blog/2020/11/09/new-in-symplify-9-monorepo-split-with-github-action). Compared to its ancestors, it speed up the split from **2 minutes to 10 seconds**.


    After 6 months of collecting feedback and adding new features, version 2.0 is here!
    <br>
    <br>
    **How to use it with GitLab split repositories and custom git hosting?**

tweet: "New Post on #php 🐘 blog: Monorepo Split GitHub Action 2.0 with Gitlab split is Out!"
tweet_image: "/assets/images/posts/2021/split_news.png"
---

<img src="/assets/images/posts/2021/split_news.png" class="img-thumbnail">

I have 3 news today that make Monorepo Split more accessible to private packages.

<br>

## 1. Private Repositories Support

[Monorepo split](https://github.com/symplify/monorepo-split-github-action) was built on open-source principles and thus used only for public repositories on GitHub. As time went by, this GitHub Action got into private repositories.

Do you have a private monorepo repository on GitHub? **Monorepo Split 2.0 now supports it!**

## 2. Split to Private Gitlab Repository

GitHub Action can be used only on GitHub, obviously, but the target repository should not matter. Version 1.x had some troubles with splitting without the correct token. In version 2.0, we've **added support for Gitlab as target repositories**.

How to enable it? Just add the `GITLAB_TOKEN` env variable to your workflow YAML file:

```yaml
env:
    GITLAB_TOKEN: ${{ secrets.GITLAB_TOKEN }}
```

The token must include `read_repository` + `write_repository` rights. Where can you create such a token? Here https://gitlab.com/profile/personal_access_tokens.

Now you can build monorepo on GitHub and split it into repositories on Gitlab.

## 3. Split to custom Git Hosting

Do you use GitLab? If so, there is a big chance you host it on your server because the [saas is getting more and more expensive](/blog/best-time-to-switch-gitlab-to-github/).

That means your target hosting is not `github.com`, neither `gitlab.com`. That's why we've added a new parameter to set the host:

```yaml
with:
    split-repository-host: git.private.com:1234
```

## Upgrade Today

That's 3 news for today. Try it and upgrade your workflows:

```diff
-                uses: symplify/github-action-monorepo-split@1.1
+                uses: symplify/github-action-monorepo-split@2.0
```

Happy coding!
