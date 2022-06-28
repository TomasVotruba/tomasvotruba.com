---
id: 363
title: "Twig Smoke Rendering - Journey&nbsp;of&nbsp;Fails"
perex: |
    In previous post, we explored [the "whys" for Twig Smoke Rendering](/blog/twig-smoke-rendering-why-do-we-even-need-it).
    <br><br>
    Today we will set on the journey towards this tool and mainly the beauty of failing on every single step.

twitter_image: ""
---

We need to be able to render any template and validate the code and its context work. To start, let's look at this first simple `homepage.twig` template:

```twig
{% include "snippet/menu.twig" %}

{% for item in items %}
    {{ item }}
{% endfor %}
```

<br>

What will happen if a native TWIG we use in controllers should render this?

* it will include the `snippet/menu.twig` loaded in TWIG loaders
* it will render it to HTML
* it iterates the `$items` array and renders each of them to string

## 1. Naive Render First

Before using my brain for thinking, I try to approach the code natively. Maybe the most straightforward solution will work right from the standard, and we can use willpower for the next step, right?

Let's render it with TWIG and see what happens:

```php
// here we load the "homepage.twig" template and all the TWIG files in our project
$loader = new ArrayLoader(['homepage.twig']);

$twigEnvironment = new Twig\Environment($loader);
$twigEnvironment->render('homepage.twig');
```

We run the code... any guess what happens?

<br>

## 2. There is No Variable

<img src="/assets/images/posts/2022/there_is_no_spoon.jpg" class="img-thumbnail" style="max-width: 18em">

First, we get an error on the non-existing `$items` variable 🚫

<br>

There is an easy fix for that, right? We can see the template is foreaching an array of strings:

```php
$twigEnvironment->render('homepage.twig', [
    'items' => ['first', 'second'],
]);
```

We re-run... and it works!

<br>

## Lure of Manual Thinking

We've made a massive step back for any attempt to automate the process. We've just **used our brain for static analysis**:

* we looked into the code with our eyes,
* and we assumed from `for` called on `$items`
* that the value is an `array` of strings.

It is correct that our brain works, but how long this process takes for all 3214 variables in all our templates? 🚫

<br>

This solution is not generic, and without us, the CI would fail. The CI has to run without us, **the same way we raise an adult from our child**. First, we can feed them manually, but in the long term, we teach them how to use their hands, what food is and how to put it in their mouth.

<br>

The `render()` has to **run generically without variables** . How? Thanks to [Alexandr for the rescue](https://twitter.com/alex_s_/status/1537030374651572225). Twig has an option to disable check for variable existence:

```php
$twigEnvironment = new Twig\Environment($loader, [
    'strict_variables' => false
]);
$twigEnvironment->render('homepage.twig');
```

Now we re-run the test... and **it works** precisely as we need to!

## ✅

<br>

## 3. Without Variables, There is no Type

<img src="/assets/images/posts/2022/there_is_no_spoon.jpg" class="img-thumbnail" style="max-width: 18em">

<br>

The variables are missing, but we can still render the file. That's fantastic!

Well, until we use a filter or function:

```twig
{{ login_name|size }}
```

The `$login_name` is not there, but the filter/function still needs an argument 🚫.

Ironically, if we care about code quality and strict type declaration, it is even worse. Filter **needs an argument of specific type**. The filter expects a `string` argument but gets `null`—fatal error 🚫

<br>

What can we do about it?

* Remove all filters and functions?
* Use regex to strip them away from the template, then render it?

That will turn into crazy regex depression, or we will remove too many templates from the analysis. Nothing will work.

<br>

**At this moment, I'm seriously doomed**. This *great excellent idea* to make an automated command is falling apart. We still have to provide all the variables in the templates.

<br>

There is this moment in every journey towards automation that hasn't done before. The moment you stop and think - "Is this worth it? Is this even possible? Should I turn to manual work and accept the risk of bug? Should I lick my wounds and give up?"

<img src="/assets/images/posts/2022/frodo_give_up.jpg" class="img-thumbnail" style="max-width: 30em">

<br>

### Let's Take a Break

Hm, what if we could emulate something like the `'strict_variables'` option, just on another level.

<br>

A big win is a summary of many small wins. Let's list what we already know and work with:

* We accept the filter/function must exist, and that's ok.
* We know it has to accept any number and types of arguments.
* We know they're just simple callbacks:

```php
public function getFunctions(): array
{
    return [
        new TwigFunction('form_label', function (...) {
            // ...
        }),
    ];
}
```

<br>

* Those callbacks are defined and tight to a filter/function name. If we know the filter name, we can override and make it **tolerant to any input**:

```php
public function getFunctions(): array
{
    return [
        new TwigFunction('form_label', function () {
            return '';
        }),
    ];
}
```

<br>

## 4. Faking Tolerant Functions/Filters

Let's give it a try:

```php
$environment->addFunction(new TwigFunction('form_label', function () {
    return '';
}));
```

<br>

Hm, it has already defined the `form_label` function... and crashes. That's a pity.

<br>

Twig has an immutable extension design. Once it loads functions/filters, you cannot override it. I&nbsp;love this design because we know the `join` function will be the same and never change. But **how do we change an immutable object**? 🚫

<img src="/assets/images/posts/2022/frodo_and_troll.jpg" class="img-thumbnail" style="max-width: 30em">

**More despair** is coming... is this all waste of time? Should we give up?

<br>

### We got Beaten...

<img src="/assets/images/posts/2022/gandalf_beaten.jpg" class="img-thumbnail">

...but we're not dead.

<br>

Let's step back. What else can we do? The filter/function cannot be changed once loaded. Maybe we could fake custom twig extensions that would get loaded instead of the core ones?

<br>

But we would have to **be responsible for manual work listing all the extensions**, functions, and filters from the core - e.g., CoreExtension, FormExtension, etc.

<br>

**There must be a better way**. We don't want our children to starve when they're 10 years old, do we?

The environment is protected from change, but it was writable before... it means **there must be some lock mechanism**. Like entity manager has. If entity manager can be unlocked, so can this. New plan is getting shape:

* we have to open the lock
* detect core filter/function names
* add them with tolerant closures
* that's it!

<br>

That's the basic recipe, at least. We tried this path in one project... and it worked! After 2 more days of struggle we polished it to a working state. Now we can render any TWIG file, and it will pass!

## ✅

<br>

## 5. Check for Existing Filters and Functions out of the Box

<blockquote class="blockquote text-center">
    "When we find ourselves in times of troubles,
    <br>
    it is time to always look on the bright side of life."
</blockquote>

This rendering approach gives us control of filters and functions by default. The variables don't have to exist, but filters are still run on them:

* If **template uses filter/function that does not exist** we will know about it.
* If the filter exists in PHP code, but extensions are loaded for missing tag, we will know about it.
* If the filter exists, the extension is loaded, but the array object closure is missing, we will know about it:

```php
return [
    new TwigFunction('some_function', [$this, 'some_function']);
];

// ...
```

## ✅

<br>

See you in the last episode of this series!

<br>

Happy coding!
