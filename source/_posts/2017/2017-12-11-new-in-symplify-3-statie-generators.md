---
id: 67
title: "New in Symplify 3: Statie Generators"
perex: |
    Statie missed one important feature. **Posts were the only elements** that you could render as standlone page.
    But what if you want a web porfolio, not with posts but with features projects? Or lectures pages?
    <br><br>
    **Statie 3 to the rescue!**
tweet: "Statie 3 on #symfony 4 is out with many little improvements + 1 big feature - Generators! Having own page is not a luxury for posts anymore!"
tweet_image: "/assets/images/posts/2017/statie-generators/generators.png"
related_items: [29, 32, 33, 34]

updated_since: "September 2018"
updated_message: |
    Updated with <strong>Statie 5</strong>, NEON → YAML, Twig and <code>statie.yml</code> config.
---

## Coupled Approach in old Statie

Posts in Statie 2 were enabled by defaults with following hard coded logic:

- find all `*.md` files in `_posts` directory
- create `PostFile` objects from them
- save all of them to global variable `posts`
- use route defined in `parameters > post_route` to render it
- render it with `_layouts/post.twig` layout

You could change a `post_route` or `layout` in specific post file, but that was it. No flexibility, no extendability and modification of `PostFile` class was not possible.

In [Pehapkari.cz](https://pehapkari.cz/) we're staring community [lectures](https://pehapkari.cz/vzdelavej-se/) and we need not only posts, but **also lectures to be on stand-alone page**, e.g.

 - https://pehapkari.cz/course/doctrine-from-basics,
 - https://pehapkari.cz/course/phpstorm-hacks-and-tips etc.


If this could be only configurable, it would benefit many websites that are more than just a list of posts.


## Configurable Approach in Statie 3 with Generators


**So here comes Statie 3 Generators**. Same posts approach is now set by default, but also configurable in `parameters > generators` section:

```yaml
parameters:
    generators:
        posts: # key useful for exception reports
            # name of variable inside single such item
            variable: post
            # name of variable that contains all items
            varbiale_global: posts
            # directory, where to look for them
            path: '_posts'
            # which layout to use, will be converted to "_layout/post.twig"
            layout: 'post'
            # and url prefix, e.g. /blog/some-post.md
            route_prefix: 'blog'
            # an object that will wrap it's logic, you can add helper methods into it and use it in templates
            object: 'Symplify\Statie\Renderable\File\PostFile'
```

The whole old posts logic is now configurable.

Do you need to change path to `_articles`?

```yaml
parameters:
    generators:
        posts:
            path: '_articles'
```

Or do you want to use own simpler object?

```yaml
parameters:
    generators:
        posts:
            object: 'MyWebsite\Statie\SimplePostFile'
```


## How to Add Own Generator?

So how did we solved lectures in Pehapkari.cz website?

Adding new Generator Element is now easy:

```yaml
# statie.yml
parameters:
    generators:
        lectures:
            variable: 'lecture'
            variable_global: 'lectures'
            path: '_lectures'
            layout: 'lecture'
            route_prefix: 'kurz'
            object: 'Pehapkari\Website\Statie\Generator\LectureFile'
```

Do you want **real code**? See [this commit](https://github.com/pehapkari/pehapkari.cz/pull/358/commits/e68d8f98172b2a04e4cf80e635c036c3f2a7bef2) that has it all.

The configuration is as simple as possible, so `object` is optional. You can read **how to set own file** in [documentation](https://www.statie.org/docs/generators/).


### How to add new Lecture?

1. Create [new `*.md` file](https://github.com/pehapkari/pehapkari.cz/pull/358/commits/e68d8f98172b2a04e4cf80e635c036c3f2a7bef2#diff-f5b8e6c24f5a089810b255d7d0757105) in `source/_lectures/` directory
2. Have [`_layout/lecture.twig`](https://github.com/pehapkari/pehapkari.cz/pull/358/commits/e68d8f98172b2a04e4cf80e635c036c3f2a7bef2#diff-63d6418d873273aad1011eb0c40b5f3b) ready
3. Create [`LectureFile`](https://github.com/pehapkari/pehapkari.cz/pull/358/commits/e68d8f98172b2a04e4cf80e635c036c3f2a7bef2#diff-34b7c0f32f7935ef12a8b2f732c8a9d6) with extra `getTitle()` method


That's all!

These lectures will be available **in every template under `{$lectures}` variable**, as configured in `variable_global` option.

So you can create page `source/all-lectures.twig` with all lectures (ordered by filename):

```twig
{% for lecture in lectures %}
    <h2>{{ lecture.getTitle() }}</h2>
{% endfor %}
```

## Try Generators Out

Just upgrade Statie:

```bash
composer update symplify/statie
```

and add lectures, talks with details, docs... anything you need.

### Date Me!

One more surprise to prevent. In case you use `route_prefix` with date in it:

```yaml
# statie.yaml
parameters:
    generators:
        posts:
            route_prefix: 'blog/:year/:month/:day'
```

remember, **the date has to be in start of the filename**:

```bash
_lectures/2018-01-30-use-open-source-statie-for-open-blogging.md
```

When setup properly, you get this file in generated code:

```bash
2018/01/30/use-open-source-statie-for-open-blogging
```


## How to Upgrade from Statie 2 to Statie 3?

<blockquote class="blockquote text-center mt-lg-5 mb-5">
    "Real code is worth of thousand words describing it."
    <footer class="blockquote-footer">Anonymous Lazy Programmer</footer>
</blockquote>

See these PRs:

- [upgrading TomasVotruba.cz](https://github.com/TomasVotruba/tomasvotruba.cz/pull/204)
- and [Pehapkari.cz](https://github.com/pehapkari/pehapkari.cz/pull/358)

<br>

Happy Generating!
