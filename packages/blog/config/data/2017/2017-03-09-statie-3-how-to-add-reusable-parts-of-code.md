---
id: 33
title: "Statie - part 3: How to Add Reusable Parts of Code"
perex: |
    You already know [how to run Statie with layout](/blog/2017/02/20/statie-how-to-run-it-locally) and [how to add data structures](/blog/2017/03/06/statie-2-how-to-add-contact-page-with-data).
    <br><br>
    Today you'll learn how to **decouple big templates to smaller and reusable snippets**. Like Google Analytics code.
tweet: "#Statie 3: Reusable code snippets #php #static #github"

updated_since: "September 2018"
updated_message: |
    Updated with **Statie 5**, NEON → YAML and Twig.

deprecated_since: "2020-03"
deprecated_message: |
    Statie was deprecated with last version 7, because 99 % of features are covered in Symfony application.<br>
    <br>
    To create static content, migrate to Symfony app and [SymfonyStaticDumper](https://github.com/symplify/symfony-static-dumper).
---

Sometimes you need to add part of template, that you want to use on multiple pages (in the same form or with smaller changes) or that makes your template less readable.

## Google Analytics

Often you start a new web with settings up Google Analytics measure code.

It looks like this:

```javascript
<script>
    ga=function(){ ga.q.push(arguments) };
    ga.q=[];
    ga.l=+new Date;
    ga('create', 'YXZ', 'auto');
    ga('send','pageview');
</script>
<script async defer src="https://www.google-analytics.com/analytics.js"></script>
```


We put into layout:

```twig
<!-- source/_layouts/default.twig -->
<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
    </head>
    <body>
        {% block content %}{% endblock %}

        <script>
            ga=function(){ ga.q.push(arguments) };
            ga.q=[];
            ga.l=+new Date;
            ga('create', 'XYZ', 'auto');
            ga('send','pageview');
        </script>
        <script async defer src="https://www.google-analytics.com/analytics.js"></script>
    </body>
</html>
```

This layout might be small, but it will be definitely bigger in real app and **will keep growing and growing in time**.

Google Analytics code is not something we change or extend too much. Why not outsource it?

## Sexy & Small Snippet

What if you could use some "include googleAnalytics snippet" command?

With Statie you can!

```twig
{% include "_snippets/googleAnalytics.twig" %}
```

### How does it Work?

Statie scans `/_snippets` directory and turns all files to snippets. Relative path of the file in `/source` directory = key for including the snippets.

- "_snippets/googleAnalytics.twig" => `snippets/googleAnalytics.twig`

### Decoupling the Snippet

First, we create the snippet file and move the Google Analytics code there:

```html
<!-- source/_snippets/googleAnalytics.twig -->

<script>
    ga=function(){ ga.q.push(arguments) };
    ga.q=[];
    ga.l=+new Date;
    ga('create', 'XYZ', 'auto');
    ga('send','pageview');
</script>
<script async defer src="https://www.google-analytics.com/analytics.js"></script>
```

Then clean the layout:

```twig
<!-- source/_layouts/default.twig -->

<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
    </head>
    <body>
        {% block content %}{% endblock %}
        {% include "_snippets/googleAnalytics.twig" %}
    </body>
</html>
```

There is one last think that bothers me:

```javascript
ga('create', 'XYZ', 'auto');
```

Can you see it?

## ProTip #1: Why Always Put IDs to Config and Not to Code

When you start using static site generator, you'll **appreciate its power in scaling**:

- First web might take some time to setup.
- Second is mostly copy-pasting previous settings and changing only layout and content.
- And third? Way too easy and well paid job.

To allow this flow, I recommend **keeping all IDs** that change from site to site - Google Analytics, Facebook Pixel, Disqus ID... - in `_config/config.yml` file. When you create a new website, **all you have to do is change one file** to make it customized.

### How To Do It

I wrote about config in previous post - [go read it, if you missed it](/blog/2017/03/06/statie-2-how-to-add-contact-page-with-data#2-global-or-bigger-amount-of-data).

So you should end up with this:

```javascript
ga('create', '{{ $googleAnalytics }}', 'auto');
```

Nice work!

## ProTip #2: Too Many Snippets? Group them by Type

Often, I've ended up with many unrelated snippets in one directory.

```bash
/source
    /_snippets
        comments.twig
        header.twig
        footer.twig
        postMetadata.twig
        postHeadline.twig
        postRecommendations.twig
```

There is no need for that. You can group them to subdirs as you like:

```bash
/source
    /_snippets
        /layout
            header.twig
            footer.twig
        /post
            comments.twig
            postMetadata.twig
            postHeadline.twig
            postRecommendations.twig
```

## Now You Know

- That using snippets will save lot of time.
- **That snippets are named by their filename**: `{% include "_snippest/fileName.twig" %}`.
- **That snippets work the best with [global configuration](/blog/2017/03/06/statie-2-how-to-add-contact-page-with-data#2-global-or-bigger-amount-of-data)**.

Happy coding!
