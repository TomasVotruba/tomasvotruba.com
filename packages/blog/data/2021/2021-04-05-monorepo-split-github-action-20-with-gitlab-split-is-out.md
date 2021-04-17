---
id: 310
title: "Monorepo Split GitHub Action 2.0 with Gitlab split is Out!"
perex: |
    [The version 1.0 was released 6 months ago](/blog/2020/11/09/new-in-symplify-9-monorepo-split-with-github-action). Compared to its ancestors, it speed up the split from **2 minutes to 10 seconds**.
    <br><br>
    After 6 months of collecting feedback and adding new features, the version 2.0 is here!
    <br>
    <br>
    **How to use it with GitLab split repositories and custom git hosting?**

tweet: "New Post on #php 🐘 blog: Monorepo Split GitHub Action 2.0 with Gitlab split is Out!"
---

## Private Repositories Support

[Monorepo split](https://github.com/symplify/monorepo-split-github-action) was build on principles of open-source, and thus used only for public repositories on GitHub. As the time went by, this GitHub Action got into private repositories.

Do you have private monorepo repository on GitHub? **Monorepo Split 2.0 now support it!**

## Split to Private Gitlab Repository

GitHub Action can be used only on GitHub obviously, but the target repository should not matter. The version 1.x had some troubles with splitting without the right token. In version 2.0 we've **added support for Gitlab as target repositories**.

How to enable it? Just add `GITLAB_TOKEN` env variable to your workflow yaml file:

```yaml
env:
    GITLAB_TOKEN: ${{ secrets.GITLAB_TOKEN }}
```

The token must include `read_repository` + `write_repository` rights. Where you can create such token? Here https://gitlab.com/profile/personal_access_tokens.

Now you can build monorepo on GitHub and split into repositories on Gitlab.

## Split to custom Git Hosting

Do you use GitLab? If so, there is a big chance you host it on own server, because the [saas is getting more and more expensive](/blog/best-time-to-switch-gitlab-to-github/).

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
