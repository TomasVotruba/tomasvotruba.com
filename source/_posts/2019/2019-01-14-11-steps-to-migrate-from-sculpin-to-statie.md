---
id: 177
title: "11 Steps to Migrate From Sculpin to Statie"
perex: |
    In previous post we migrated [Jekyll to Statie](/blog/2019/01/10/9-steps-to-migrate-from-jekyll-to-statie/).
    If you need to add a feature to the static website, like creating preview images for Instagram, **you need PHP**.
    <br><br>
    Sculpin is the older brother of Statie but is mostly retired last 3 years. Do you want to get on track with modern PHP on your static website? Here is how.
tweet: "New Post on #php 🐘 blog: 11 Steps to Migrate from #Sculpin to #Statie"
---

### Statie beats Sculpin in Simplicity

Do you know that feeling when you look for *that post* someone wrote on their blog, you're on page 10 with 5 posts per page and you click *Next page* one more time hopefully being the last page you have to scan?

Sculpin uses paginator and tagging. Since static websites are not huge e-commerce sites, they're small in transferred data amount. **It's all HTML, no database, no PHP, so even if you display 200 posts in one page it's blazing fast**.

Statie doesn't create these problems - there is no paginator and tags/categories complexity that only bothers you and your readers. One page with all items → CTRL + F "dorctr" → that the one.

## 1. Create Basic Statie Structure

Statie 5.3 brings [new `init` command](/blog/2019/01/07/how-to-create-your-first-php-twig-static-website-under-2-minutes-with-statie/), that creates basic structure in `/source` directory, `statie.yml`, `.travis.yml` and metafiles.

Before we start any moving, create a basic structure to save many copy-pasting steps:

```bash
composer require symplify/statie
vendor/bin/statie init
```

Then, clean `/source` directory from generated files and...

## 2. Move Parameters Files Into `statie.yaml`

From `app/config/sculpin_site.yml` to `statie.yaml`:

```diff
-google_analytics_tracking_id: UA-33922165-1
+parameters:
+    google_analytics_tracking_id: UA-33922165-1
```

## 3. Keep Post Route

From `app/config/sculpin_kernel.yml` to `statie.yaml`:

**Before** - Sculpin

```yaml
sculpin_content_types:
    posts:
        permalink: blog/:filename/
```

**After** - Statie

```yaml
parmameters:
    generators:
        posts:
            route_prefix: blog/ #filename is completed by default
```

Look at [generators docs](https://www.statie.org/docs/generators/) to see other options to configure them.

## 4. Make File Names Explicit about `*.twig`

And also move `_views` to `_layouts`. That's where Statie looks for global layouts.

```diff
-_views/default.html
+_layouts/default.twig
```

## 5. Drop Old Files

```diff
-app/*
-build.sh # see step 6 for deploy replacement
```

## 6. Setup Github Pages deploy in Travis

Thanks to `vendor/bin/statie init` you have correctly configured `.travis.yml` in your repository.

To finish deploy, you need to:

- create `gh-pages` branch
- pick it as a source for Github Pages
- generate Github Token
- put it to Travis configuration of your repository

How you do this? Just **follow [Statie.org documentation](https://www.statie.org/docs/github-pages/)** step by step.

## 7. Complete IDs to Your Posts

Post:

```diff
 ---
+id: 1
 layout: post
 title: My new blog
 ---
```

Ids are used to internally interlink items, e.g to display [related posts under the post](https://www.statie.org/docs/related-items/):

```yaml
---
id: 1
title: My first post
related_items: [2]
---
```

```twig
{% set relatedPosts = post|relatedItems %}

{% if relatedPosts|length %}
    <div>
        <strong>Continue Reading</strong>
        <ul>
            {% for relatedPost in relatedPosts %}
                <li>
                    <a href="/{{ relatedPost.relativeUrl }}">{{ relatedPost.title }}</a>
                </li>
            {% endfor %}
        </ul>
    </div>
{% endif %}
```

## 8. Forget repeating Layout and Generator from Posts

Since the layout is [defined in the config for all posts](https://www.statie.org/docs/generators/) by default...

```yaml
parameters:
    generators:
        posts:
            layout: "_layouts/post.twig"
```

...no need to repeat it in every file:

```diff
 ---
 id: 1
-layout: post
-generator: [anything]
 title: My new blog
 ---
```

All variables from parameters are available in templates, no need to pick explicitly each one of them.

## 9. Update Permalinks

Sometimes you need to generate different output filename than Sculpin chooses. It's the same in Statie, just with more clear name:

```diff
 ---
-permalink: atom.xml
+outputPath: atom.xml
 ---
```

## 10. Drop Parameter Prefixes

Parameters are prefixed in Sculpin - sometimes not, sometimes with page, sometimes with site.

```yaml
parameters:
    title: "The Best Bugs"
```

Statie doesn't make you think: names in parameters = variables is in templates.

```diff
-{{ site.title }}
+{{ title }}
```

## 11. Run Project Locally

Do you want live updates streamed to your browser on every change of the code?

```bash
npm install
gulp
```

- Then open `localhost:8000` to see your generated HTML.

<br>

That's it! You can enjoy Markdown, Twig, Latte, and PHP directly from your local machine and still on Github Pages.

Happy coding!
