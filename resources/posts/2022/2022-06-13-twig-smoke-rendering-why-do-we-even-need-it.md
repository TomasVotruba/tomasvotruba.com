---
id: 362
title: "Twig&nbsp;Smoke&nbsp;Rendering - Why&nbsp;do&nbsp;we&nbsp;Even&nbsp;Need&nbsp;it?"
perex: |
    Two weeks ago [our upgrade team started](https://getrector.com/hire-team) to upgrade Twig 1 to 2 and Latte 2 to 3 for two clients. There was no test that covers the templates, just a few integration ones that might have invoked a few % of happy paths render. Not good enough.


    We **need a CI test to be sure** templates are working. I had an initial idea, but knowing the value of external input, I asked on [Twitter for brainstorming](https://twitter.com/VotrubaT/status/1537029650379116544). I'm happy I did. Alexander Schranz came with [a tip](https://twitter.com/alex_s_/status/1537030374651572225) that led me on a 2-week journey, and I would love to share it with you today.

---

## Why do we need Smoke Render all Templates?

First, let me tell you about the **3 reasons** why we need smoke-render all TWIG templates:

<br>

1. Twig 1 and 2 contains a few BC breaks. Not only in Twig itself but also in related Twig Bundle. We're not interested in the complete list of BC breaks, but rather **if they break our templates** and **where exactly**.

<br>

2. We need to render templates directly, without controllers and variables. The controller renders only a specific path. Based on variables, it can include one template snippet but forget the other:

```twig
{% if value %}
    {% include 'snippet/first_option.twig' %}
{% else %}
    {% include 'snippet/second_option.twig' %}
{% endif %}
```

<br>

3. We must ensure all the functions, filters, and constants exist. What if the `join` filter was removed and replaced by `implode`? This is also useful for [static analysis of templates](/blog/stamp-static-analysis-of-templates/).

<br>

The smoke rendering means "test that everything works". Render template and expect valid HTML. Not a file by file, test by test, but all TWIG files we can find in the project in a single test run. It could look like this:

```bash
bin/console twig-smoke-render templates
```


### Why not use the TWIG Lint command?

You might think: "but Tomas, Symfony is already doing that for you":

```bash
bin/console lint:twig templates
```

That could work if we care about syntax errors like `{{ value }`. But we care about more than bare language syntax. We care about meaning and context:

* variable types,
* filter/functions/constant existence,
* include of layout and snippet templates.

That's why we don't use only `php -l`, but also PHPStan to check PHP code with context.

<br>
<br>

Now we have a Goal of Doom and a team of *why*s.

We're ready to set on the journey!

<img src="/assets/images/posts/2022/fellowship.jpg" class="img-thumbnail">

<br>

To be continued...

<br>

Happy coding!
