---
id: 362
title: "Twig Smoke Rendering - Why do we Even Need it?"
perex: |
    Two weeks ago [our upgrade team started](https://getrector.org/for-companies) to upgrade Twig 1 to 2 and Latte 2 to 3 for two clients. There were no test that would cover the templates, just few integration ones that might have invoked few % of happy paths render. Not good enough.
    <br><br>
    We **need a CI test to be sure** templates are fine. I had an initial idea, but knowing the value of external input, I asked on [Twitter for brainstorm](https://twitter.com/VotrubaT/status/1537029650379116544). I'm happy I did. Alexander Schranz came with [a tip](https://twitter.com/alex_s_/status/1537030374651572225) that lead me on a 2 week journey and I would love to it with you today.
---

## Why do we need Smoke Render all Templates?

First, let me tell you about the **3 reasons** why we need smoke render all TWIG templates:

1. The Twig 1 and 2 contains few BC breaks. Not only in Twig itself, but also in related Twig Bundle. We're not interested in the full list of BC breaks, but rather **if they break our templates** and **where exactly**.

<br>

2. We need to render templates directly, without controllers and variables. Controller renders only specific path. Based on variables it can be include one template snippet, but forget the other:

```twig
{% if value %}
    {% include 'snippet/first_option.twig' %}
{% else %}
    {% include 'snippet/second_option.twig' %}
{% endif %}
```


3. We need to be sure all the functions exist, filters exist and constant exist. This is also useful for general static analysis of templates.

<br>

The smoke rendering means "test that everything works". Render template and expect valid HTML. Not a file by file, but rather all TWIG files we can find in the project. It could look like this:

```bash
bin/console twig-smoke-render templates
```


### Why not use TWIG Lint command?

You might think: "but Tomas, Symfony is already doing that for you":

```bash
bin/console lint:twig templates
```

That could work, if we care about syntax errors like `{{ value }`. But we care about more than simple syntax, we care about meaning and context:

* variable types,
* filter/functions/constant existence,
* include of layout and snippet templates.

That's why we don't use only `php -l`, but also PHPStan to check PHP code with context.

<br>
<br>

Now we have a Goal of Doom and team of *why*s, we're ready to set on the journey!

<img src="/assets/images/posts/2022/fellowship.jpg" class="img-thumbnail">

<br>

To be continued...

<br>

Happy coding!
