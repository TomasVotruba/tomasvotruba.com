---
layout: post
title: "Statie 2: How add Contact Page With Data"
perex: '''
    <a href="/blog/2017/02/20/statie-how-to-run-it-locally">In first post about Statie</a> you generated simple index with layout. Today we look on first semi-dynamic feature: <strong>data structures</strong>.
'''

updated: true
updated_since: "Agust 2017"
updated_message: '''
    Updated with <a href="https://github.com/Symplify/Symplify/pull/197">Statie 2.2</a> and <code>parameters</code> section in <code>statie.neon</code> config for loading global data. 
'''
---

## Contact Page with Socials Accounts Data Separated

First, **create a file** in the `/source` directory called `contact.latte`.

The file name is relevant to the url - this file will be accessible at `/contact`.


```html
<!-- source/contact.latte -->
---
layout: default
---

{block content}
    <h1>First Hour is on me - Call me now!</h1>

    <ul>
        <li>Phone: <a href="tel:123456789">123 456 789</a>a></li>
        <li>Email: <a href="mailto:hi@gmail.com">hi@gmail.com</a></li>
        <li>Twitter: <a href="https://twitter.com/wise-programmer">@wiseProgrammer</a></li>
        <li>Facebook: <a href="https://facebook.com/wise-programmer">Wise Programmer</a></li>
        <li>LinkedIn: <a href="https://linkedin.com/wise-programmer">Wise Programmer</a></li>
        <li>Github: <a href="https://github.com/wise-programmer">@WiseProgrammer</a></li>
    </ul>
{/block}
```

<br>

If you use [smart Gulp script](/blog/2017/02/20/statie-how-to-run-it-locally#minitip-use-gulp-work-for-you), you can already check the page at [localhost:8000/contact](http://localhost:8000/contact).

<div class="text-center">
    <img src="/../../../../assets/images/posts/2017/statie-2/statie-contact.png" class="thumbnail">
</div>


## How to decouple Information to Data Structures

We are programmers and we don't like data coupled to the code. You wouldn't put your repository class to your `Homepage.latte` template, would you?

What if...

- we want to **add new contact**,
- **change it**
- or **use in multiple parts** of website.

There are 2 basic ways to data structures. It depends, whether you need it locally or globally.

In both cases, we modify the template the same way:



```html
<!-- source/contact.latte -->
---
layout: default
---


{block content}
    <h1>First Hour is on me - Call me now!</h1>

    <ul>
        {foreach $contactMethods as $contactMethod}
            <li>
                {$contactMethod['type']}:
                <a href="{$contactMethod['link']}">{$contactMethod['name']}</a>
            </li>
        {/foreach}

        <!-- or shorter -->
        <!-- <li n:foreach="$contactMethods as $contactMethod"> -->
    </ul>
{/block}
```

## 1. Local Values in between `---`

### How Does it Work?

Everything located between two "triple-hyphens" (`---`) will be accessible **only in the one template as variables**.

In *code words*:

```html
---
key: "value"
---

{$key} <!-- shows "value" -->
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

Now we put this data to <a href="https://ne-on.org">NEON format</a> and place them to our `contact.latte`.


```yaml
<!-- source/contact.latte -->
---
layout: default
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

{block content}
    <h1>First Hour is on me - Call me now!</h1>

    <ul>
        {foreach $contactMethods as $contactMethod}
            <li>
                {$contactMethod['type']}:
                <a href="{$contactMethod['link']}">{$contactMethod['name']}</a>
            </li>
        {/foreach}
    </ul>
{/block}
```

Save file and [look on the contact page](http://localhost:8000/contact).

<div class="text-center">
    <img src="/../../../../assets/images/posts/2017/statie-2/statie-contact.png" class="thumbnail">
</div>


## 2. Global or Bigger Amount of Data

I use this option in 2 cases:

- **I need those data globally** (e.g. Google Analytics Code)
- **those data are 5+ lines big and they make template less readable**

I would use this option in this case.

### How does it Work?

Statie uses `statie.neon` in the root directory and its `parameters` section.

As convention, I put global data to `/source/_data` directory. But it's up to you, where you put it.

### How to Use it?

We simple move whole `contactMethods` to this file:

```yaml
# /source/_data/contacts.neon
parameters:
    contactMethods:
        ...
```

And include it in `statie.neon`:

```yaml
# /statie.neon
includes:
    - source/_data/contacts.neon
```

And that's it!


Save file, [look on the contact page](http://localhost:8000/contact) and it still works!

<div class="text-center">
    <img src="/../../../../assets/images/posts/2017/statie-2/statie-contact.png" class="thumbnail">
</div>


## Now You Know

- How to add data to your Statie page.
- **Where to put them for local and global access**.
- **That its convention** to use `/source/_data/<some-data>.neon` naming.


Happy coding!
