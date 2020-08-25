---
id: 32
title: "Statie - part 2: How to add Contact Page With Data"
perex: |
    [In previous post about Statie](/blog/2017/02/20/statie-how-to-run-it-locally/) you generated simple index with layout. Today we look on first semi-dynamic feature: **data structures**.

deprecated_since: "January 2019"
deprecated_message: |
    This post was moved to [statie.org](https://www.statie.org). Also, **it's better to [use `vendor/bin/statie init` command](/blog/2019/01/07/how-to-create-your-first-php-twig-static-website-under-2-minutes-with-statie/) now**.

updated_since: "September 2018"
updated_message: |
    Updated with **Statie 5**, NEON → YAML, Twig and `parameters` section in `statie.yml` config.

tweet: "#Statie 2: Data sets #php #static #github"
---

## Contact Page with Socials Accounts Data Separated

First, **create a file** in the `/source` directory called `contact.twig`.

The file name is relevant to the url - this file will be accessible at `/contact`.

```twig
<!-- source/contact.twig -->
---
layout: "_layouts/default.twig"
---

{% block content %}
    <h1>First Hour is on me - Call me now!</h1>

    <ul>
        <li>Phone: <a href="tel:123456789">123 456 789</a>a></li>
        <li>Email: <a href="mailto:hi@gmail.com">hi@gmail.com</a></li>
        <li>Twitter: <a href="https://twitter.com/wise-programmer">@wiseProgrammer</a></li>
        <li>Facebook: <a href="https://facebook.com/wise-programmer">Wise Programmer</a></li>
        <li>LinkedIn: <a href="https://linkedin.com/wise-programmer">Wise Programmer</a></li>
        <li>Github: <a href="https://github.com/wise-programmer">@WiseProgrammer</a></li>
    </ul>
{% endblock %}
```

<br>

If you use [smart Gulp script](/blog/2017/02/20/statie-how-to-run-it-locally#minitip-use-gulp-work-for-you), you can already check the page at [localhost:8000/contact](http://localhost:8000/contact).

<div class="text-center">
    <img src="/assets/images/posts/2017/statie-2/statie-contact.png" class="img-thumbnail">
</div>


## How to decouple Information to Data Structures

We are programmers and we don't like data coupled to the code. You wouldn't put your repository class to your `Homepage.twig` template, would you?

What if...

- we want to **add new contact**,
- **change it**
- or **use in multiple parts** of website.

There are 2 basic ways to data structures. It depends, whether you need it locally or globally.

In both cases, we modify the template the same way:



```twig
<!-- source/contact.twig -->
---
layout: "_layouts/default.twig"
---


{% block content %}
    <h1>First Hour is on me - Call me now!</h1>

    <ul>
        {% for contactMethod in contactMethods %}
            <li>
                {{ contactMethod.type }}:
                <a href="{{ contactMethod.link }}">{{ contactMethod.name }}</a>
            </li>
        {% endfor %}
    </ul>
{% endblock %}
```

## 1. Local Values in between `---`

### How Does it Work?

Everything located between two "triple-hyphens" (`---`) will be accessible **only in the one template as variables**.

In *code words*:

```html
---
key: "value"
---

{{ key }} <!-- shows "value" -->
```

### How to Use it?

All we need for our contact page is simple array. Putting data above to an array in PHP would look like this:

```php
$contactMethods = [
    [
        'type' => 'Phone',
        'link' => 'tel:123456789',
        'name' => '123 456 789'
    ], [
        'type' => 'Email',
        'link' => 'mailto:hi@gmail.com',
        'name' => 'hi@gmail.com'
    ], [
        'type' => 'Twitter',
        'link' => 'https://twitter.com/wise-programmer',
        'name' => '@wiseProgrammer'
    ], [
        'type' => 'Facebook',
        'link' => 'https://facebook.com/wise-programmer',
        'name' => 'Wise Programmer'
    ], [
        'type' => 'LinkedIn',
        'link' => 'https://linkedin.com/wise-programmer',
        'name' => 'Wise Programmer'
    ], [
        'type' => 'Github',
        'link' => 'https://github.com/wise-programmer',
        'name' => '@WiseProgrammer'
    ]
];
```

Now we put this data to Yaml format and place them to our `contact.twig`.


```twig
<!-- source/contact.twig -->
---
layout: "_layouts/default.twig"
contactMethods:
    -
        type: Phone
        link: tel:123456789
        name: 123 456 789
    -
        type: Email
        link: mailto:hi@gmail.com
        name: hi@gmail.com
    -
        type: Twitter
        link: https://twitter.com/wise-programmer
        name: @wiseProgrammer
    -
        type: Facebook
        link: https://facebook.com/wise-programmer
        name: Wise Programmer
    -
        type: LinkedIn
        link: https://linkedin.com/wise-programmer
        name: Wise Programmer
    -
        type: Github
        link: https://github.com/wise-programmer
        name: @WiseProgrammer
---

{% block content %}
    <h1>First Hour is on me - Call me now!</h1>

    <ul>
        {% for contactMethod in contactMethods %}
            <li>
                {{ contactMethod.type }}:
                <a href="{{ contactMethod.link }}">{{ contactMethod.name }}</a>
            </li>
        {% endfor %}
    </ul>
{% endblock %}
```

Save file and [look on the contact page](http://localhost:8000/contact).

<div class="text-center">
    <img src="/assets/images/posts/2017/statie-2/statie-contact.png" class="img-thumbnail">
</div>


## 2. Global or Bigger Amount of Data

I use this option in 2 cases:

- **I need those data globally** (e.g. Google Analytics Code)
- **those data are 5+ lines big and they make template less readable**

I would use this option in this case.

### How does it Work?

Statie uses `statie.yml` in the root directory and its `parameters` section.

As convention, I put global data to `/source/_data` directory. But it's up to you, where you put it.

### How to Use it?

We simple move whole `contactMethods` to this file:

```yaml
# /source/_data/contacts.yml
parameters:
    contactMethods:
        ...
```

And import it in `statie.yml`:

```yaml
# statie.yml
imports:
    - { resource: 'source/_data/contacts.yml' }
```

And that's it!

Save file, [look on the contact page](http://localhost:8000/contact) and it still works!

<div class="text-center">
    <img src="/assets/images/posts/2017/statie-2/statie-contact.png" class="img-thumbnail">
</div>

## Now You Know

- How to add data to your Statie page.
- **Where to put them for local and global access**.
- **That its convention** to use `/source/_data/<some-data>.yml` naming.

Happy coding!
