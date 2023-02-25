---
id: 363
title: "Twig Smoke Rendering - Journey&nbsp;of&nbsp;Fails"
perex: |
    In previous post, we explored [the "whys" for Twig Smoke Rendering](/blog/twig-smoke-rendering-why-do-we-even-need-it).


    Today we will set on the journey towards this tool and mainly. Get ready for failure, demotivation, and despair. As with every new invention, the fuel can make us faster or burn us to death.

---

How did we define the goal of twig smoke rendering? We want to render any template and validate the code, and its context works. To start, let's look at this first simple `homepage.twig` template:

```twig
{% include "snippet/menu.twig" %}

{% for item in items %}
    {{ item }}
{% endfor %}
```

<br>

That's the goal. What is our starting point? The typical render we know is from a Symfony controller will process the template like this:

* include the `snippet/menu.twig` loaded in TWIG loaders
* render it to HTML
* iterates the `$items` array and renders each item to string

## 1. Naive Render First

This journey will be very long, so we have to save as much energy as possible. Before we use the brain for thinking, let's approach the code naively. Maybe [the most straightforward solution will work](https://fourweekmba.com/occams-razor/) right from the start.

<br>

First, we prepare a minimal setup of the TWIG environment with a template loader:

```php
use Twig\Environment;
use Twig\Loader\ArrayLoader;

// here we load the "homepage.twig" template and all the TWIG files in our project
$loader = new ArrayLoader(['homepage.twig']);

$twigEnvironment = new Environment($loader);
$twigEnvironment->render('homepage.twig');
```

We run the code... any guess what happens?

<br>

## 2. There is No Variable

<img src="/assets/images/posts/2022/there_is_no_spoon.jpg" class="img-thumbnail" style="max-width: 18em">

First, we get an error on the non-existing `$items` variable 🚫

<br>

Did we forget to provide it? There is an easy fix for that. We see the template is foreaching an array of strings. Let's pass some made-up value as 2nd parameter:

```php
$twigEnvironment->render('homepage.twig', [
    'items' => ['first', 'second'],
]);
```

We re-run... and it works!

<br>

## Lure of Manual Thinking

We made a single little template to render correctly. At the same time, we also made a massive step back from any attempt to automate the process. We've just **used our brain for static analysis**:

* we looked into the code with our eyes,
* we deduce from the `for` tag called on `$items` that the value is an `array`
* we deduced from writing the `item` to the output that it is an array of strings.

It is correct, but how long will it take us for all 3214 variables in all our templates? 🚫

<br>

This solution is not generic, and without us, the CI would fail. The CI has to run without any intervention, **the same way we raise an adult from our child**. First, we can feed them manually, but in the long term, we should teach them how to use their hands, what food is, and how to get and eat it.

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
{{ login_name|length }}
```

The `$login_name` is not there, but the filter/function still needs an argument 🚫.

Ironically, if we care about code quality and strict type declaration, it is even worse. Filter **needs an argument of specific type**. The filter expects a `string` argument but gets `null`—a fatal error 🚫

<br>

What can we do about it?

* Remove all filters and functions?
* Use regex to strip them away from the template, then render it?

That will turn into crazy regex depression, or we will remove too many templates from the analysis. Nothing will work.

<br>

**At this moment, I'm seriously doomed**. This *great excellent idea* to make an automated command is falling apart. We still have to provide all the variables in the templates.

<br>

There is this moment in every journey towards automation that hasn't been done before. The moment you stop and think - "Is this worth it? Is this even possible? Should I turn to manual work and accept the risk of a bug? Should I lick my wounds and give up?"

<img src="/assets/images/posts/2022/frodo_give_up.jpg" class="img-thumbnail" style="max-width: 30em">

<br>

### Let's Take a Break and Think Different

Hm, what if we could emulate something like the `'strict_variables'` option, just on another level. No idea how to do that.

<blockquote class="blockquote text-center">
    "A big win is a summary of many small improvements."
</blockquote>

Let's list what we already know and work with:

* We accept the filter/function must exist, and that's ok.
* We know it has to accept any number and types of arguments.
* We know they're just simple callbacks:

```php
new TwigFunction('form_label', function ($value) {
    // ...
});
```

<br>

## 4. Faking Tolerant Functions/Filters

Those callbacks are defined and tight to a filter/function name. If we know the filter name, we can override and make it **tolerant to any input**:

```diff
-return new TwigFunction('form_label', function ($value) {
+return new TwigFunction('form_label', function () {
     // ...
 });
```

<br>

Let's give it a try:

```php
$environment->addFunction(new TwigFunction('form_label', function () {
    return '';
}));
```

<br>

Hm, it has already defined the `form_label` function... and crashes 🚫

<br>

Twig has an immutable extension design. Once it loads functions/filters, we cannot override it. I&nbsp;love this design because we know the `join` function will be the same and never change. But **how do we change an immutable object**? 🚫

<img src="/assets/images/posts/2022/frodo_and_troll.jpg" class="img-thumbnail" style="max-width: 30em">

**More despair** is coming... is this all waste of time? Should we give up?

<br>

### We got Beaten...

<img src="/assets/images/posts/2022/gandalf_beaten.jpg" class="img-thumbnail">

...but we're not dead.

<br>

Let's step back. What else can we do? The filter/function cannot be changed once loaded. Maybe we could fake custom twig extensions that would get loaded instead of the core ones?

But we would have to **be responsible for manual work listing all the extensions**, functions, and filters from the core - e.g., CoreExtension, FormExtension, etc. 🚫

<br>

There must be some better way.

The environment is locked and protected from change, but it must have been writable at the start. Otherwise, the TWIG would not have the core functions and filters. That means **there must be some lock mechanism**. Like entity manager from Doctrine has. If we can unlock entity manager, we can un this.... new plan is getting shape:

* we have to open the lock
* detect core filter/function names
* add them with tolerant closures
* that's it!

<br>

That's the basic plan. We tried to apply it in one project... and it worked! After 2 more days of struggle, we polished it to a working state. Now we can render a TWIG file with variables, functions, and filters, and it will pass!

## ✅

<br>

## 5. Check for Existing Filters and Functions out of the Box

<blockquote class="blockquote text-center">
    "When we find ourselves in times of troubles,
    <br>
    it is time to always look on the bright side of life."
</blockquote>

This achievement moves us light years ahead. The rendering checks filters/functions by default. Variables don't have to exist, but filters are still run on them. That way, we will know 3 invalid states that can happen to filter/function:

* if **template uses filter/function that does not exist** we will know about it ✅
* if the filter exists in PHP code, but **extensions are not loaded for missing tag**, we will know about it ✅
* if the filter exists, the extension is loaded, but the **array closure is missing**, we will know about it ✅

```php
return [
    new TwigFunction('some_function', [$this, 'some_method']);
];

// ... no "some_method" found here
```

## ✅

<br>

We're getting close, but it still does not run in CI 🚫

<br>

Will we make it to the glory, or will we give up and walk in shame? Stay tuned for the next episode to find out.

<br>

Happy coding!
