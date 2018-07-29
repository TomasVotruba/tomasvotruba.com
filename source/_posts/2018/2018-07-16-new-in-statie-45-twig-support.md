---
id: 123
title: "New in Statie 4.5: Twig Support"
perex: |
    Statie supports YAML and Symfony Dependency Injection for some time. But you wanted more! **You wanted Twig**. Sculpin and all [the other PHP Static Website generators](https://www.staticgen.com/) have it.
    <br><br>
    So there you go! Enjoy
tweet: "New Post on my Blog: New in #Statie 4.5: #Twig Support #symfony #latte"
tweet_image: "/assets/images/posts/2018/statie-45/statie-45.png"
related_items: [67, 120]
---

<a href="https://github.com/Symplify/Symplify/pull/892" class="btn btn-dark btn-sm">
    <em class="fab fa-github fa-fw"></em>
    See pull-request
</a>

## 3 Steps to Your First Twig Page on Statie

### 1. Prepare Layout `_layouts/default.twig`

```twig
<!DOCTYPE html>
<html lang="en">
    {% include "_snippets/head.twig" %}
    <body>
        {% include "_snippets/menu.twig" %}

        <div>
            {% block content %}{% endblock %}
        </div>

        {% include "_snippets/footer.twig" %}
    </body>
</html>
```

### 2. Create Template `contact.twig`

```twig
---
layout: "_layouts/default.twig"
---

{% block content %}
    <h1>Call me</h1>

    <a href="tel:+420776778332">+420 <strong>776 778 332</strong></a>
{% endblock %}
```

### 3. Enable Twig

```yaml
# statie.yml
parameters:
    templating: twig
```

And you're ready to go!

## How to Upgrade to Statie 4.5?

<img src="/assets/images/posts/2018/statie-45/statie-45.png">

Update it in composer:

```php
composer require symplify/statie 4.5
```

And try:

```bash
vendor/bin/source generate
```

It will probably fail because there was one change in templating reference.

Before the paths of included files were just a file name and the full path was guessed. This practice is common in Nette and Symfony Controllers, so I used as a starting point. But it caused many WTFs and name conflicts like:

```bash
/_snippets
    /post
        detail.latte
    /lecture
        detail.latte
```

```twig
{include "detail"}
```

Which file will is used? Or is it a block import? Magic :)

How to do it better with *a principle of the least surprise*?

### Clear, Obvious File Naming

All files in `_layouts` and `_snippets` are now referenced by **they full relative path to source** (usually `/source` directory):

```diff
-layout: "default"
+layout: "_layouts/default.latte"
 ---

-{include "postMetadata"}
+{include "_snippets/postMetadata.latte"}
```

Do you use [Generators](https://www.statie.org/docs/generators/)? Don't forget to upgrade them too:

```diff
 parameters:
     generators:
         posts:
-            layout: 'post'
+            layout: '_layouts/post.latte'
```

### Already Running on Statie 4.5

Check diffs of these merged pull-requests so you have the idea **how small the change really is**:

- [statie.org](https://github.com/crazko/statie-web/pull/18/files)
- [romanvesely.com](https://github.com/crazko/romanvesely.com/pull/44/files)
- [tomasvotruba.cz](https://github.com/pehapkari/pehapkari.cz/commit/a8256817acc61a14c4adcd0f6ed06b042450bfc3#diff-f9937b27a07038e5d12db3b137e228ce)

## How to Migrate Latte to Twig as Well?

<img src="/assets/images/posts/2018/statie-45/latte-twig.png">

If you use Statie, you're probably running on Latte. In the case you prefer Twig, **I guess you're already frustrated from annoying Latte to Twig migration you have ahead of you**.

Again, check these diffs, so you have the idea **how big that change really is**:

- [TomasVotruba.cz](https://github.com/TomasVotruba/tomasvotruba.cz/pull/380/files)
- [Pehapkari.cz](https://github.com/pehapkari/pehapkari.cz/pull/486/files)

I feel you. You can stay with Latte until... just kidding. That's a lot of work, right? Well, I'm not that hardworking, don't worry. I'm a lazy bastard, so I made [a package for Latte to Twig migration](/blog/2018/07/05/how-to-convert-latte-templates-to-twig-in-27-regular-expressions/).

Enjoy!
