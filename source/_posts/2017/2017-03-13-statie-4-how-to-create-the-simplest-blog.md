---
id: 34
title: "Statie - part 4: How to Create The Simplest Blog"
perex: |
    Statie is very powerful tool for creating small sites. But you will use just small part of it's features, having just micro-sites. How to get to full 100%? **build a blog**.
    <br><br>
    Today I will show you, **how to publish your first post**.
tweet: "#Statie 4: How to create blog #php #static #github"

updated_since: "September 2018"
updated_message: |
    Updated with Statie 5.0, NEON → YAML, Twig and <code>parameters</code> section in <code>statie.yml</code> config.
---

## Create a Blog Page

This might be the simplest page to show all your posts:

```twig
<!-- /source/blog.twig -->

---
layout: default
---

{% block content %}
    <h2>Shouts Too Loud from My Hearth</h2>

    {% for post in posts %}
        <a href="/{{ post.relativeUrl }}/">
            <h3>{{ post.title }}</h3>
        </a>
    {% endfor %}
{% endblock %}
```

### You already see

- that all posts are in stored in `posts` variable
- that every post has `relativeUrl`
- that every post should have a `title` (optional, but recommended)


## How Does it Work?

Statie will do 3 steps:

1. **Scans `/source/_posts` for any files**
    - those files have to be in `YYYY-MM-DD-url-title.md` format
    - that's how Statie can determine the date
2. **Converts Markdown and TWIG/Latte syntax to HTML**
3. Stores them to `posts` variable.


## How does a Post Content Look Like?

```html
<!-- source/_posts/2017-03-05-my-last-post.md -->

---
title: "This my Last Post, Ever!"
---

This is my last post to all
```

### How to Show Post in Own Layout

Posts use `_layouts/post.twig` by default. It should include the common parts for all layouts - like header, menu or footer.

```twig
<!-- /source/_layouts/post.twig -->

{% include "_snippets/header.twig" %}

{% block content %}
    <h2>{{ post.title }}</h2>

    {{ post.content|raw }}
{% endblock %}

{% include "_snippets/footer.twig" %}
```

That should be it.

Save file, [look on the blog page](http://localhost:8000/blog) and see:

<div class="text-center">
    <img src="/assets/images/posts/2017/statie-4/statie-blog.png" class="img-thumbnail">
</div>

When you click a post title:

<div class="text-center">
    <img src="/assets/images/posts/2017/statie-4/statie-post.png" class="img-thumbnail">
</div>

### ProTip: Change Post Url

You see the url for the post is `blog/2017/03/05/my-last-post/`.

This **can be changed by configuration** in `statie.yml`:

```yaml
parameters:
    generators:
        posts:
            route_prefix: 'my-blog/:year' # "blog/:year/:month/:day" by default
```

That produces `my-blog/2017/my-last-post/` url.

Got it? I know you do! **You are smart.**

## Now You Know

- **That all posts are placed in `/source/_posts` directory and in `$posts` variable**.
- That post has to be in **named as `YYYY-MM-DD-title.md` format**
- That you can change the post generated url in `statie.yml` in `parameters > generators > posts > route_prefix`.

Happy coding!
