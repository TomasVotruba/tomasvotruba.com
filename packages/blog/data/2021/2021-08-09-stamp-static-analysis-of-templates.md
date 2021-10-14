---
id: 344
title: "STAMP: Static Analysis of Templates"
perex: |
    Today we have static analysis checking every line of our PHP code - with PHPStan, Psalm, and PHPStorm. With php-parser and abstract syntax tree, we can do **instant changes across hundreds of files** in a second, with a precision of human hair.
    <br><br>
    With all this power and utils having our back, we can see templates as the next low-hanging fruit that needs our attention.
tweet: "New Post on the 🐘 blog: STAMP - Static Analysis of Templates     #phpstan #twig #nettefw #latte"
---

Are you hungry? I hope so. Let's cook some dinner for our whole family.

What are we having? Let's say we have a template with the title of the meal for tonight:

```html
<!-- TWIG syntax -->
{{ meal.title }}

<!-- Latte syntax -->
{$meal->getTitle()}
```

The meal is an elementary object:

```php
namespace App;

final class Meal
{
    public function getTitle(): string
    {
        return 'Potato Salad and Schnitzel';
    }

    // ...
}
```

Then we run our website to show our whole family the meal for tonight:

```html
Potato Salad and Schnitzel
```

Pretty clear, right?

## Renamed a Method

When you look at the `App\Meal` object, what does feel itchy about it? The name of `getTitle()` method. When we first created this `App\Meal` class, we probably thought more about *post* or *news*. There it makes more sense.

In this case, maybe the *name* would be a better choice. Do you agree? Let's rename it.

We rename it using PHPStorm and "Rename Method" action:

```diff
 namespace App;

 final class Meal
 {
-    public function getTitle(): string
+    public function getName(): string
     {
         return 'Potato Salad and Schnitzel';
     }

     // ...
 }
```

Now it feels better to read the code. We'll reload the webpage to show our changes to our family...

<br>

...and it crashes with *Error 500*.

## We Forgot Something, What Was it?... Kevin!

We missed one crucial spot. PHPStorm is an excellent tool to handle PHP code. But it's PHPStorm, not *TemplateStorm*, so it missed rename in the template:

```html
<!-- TWIG syntax -->
{{ meal.title }}

<!-- Latte syntax -->
{$meal->getTitle()}
```

Once we see the error, we know what to do to make it go away:

```diff
 <!-- TWIG syntax -->
-{{ meal.title }}
+{{ meal.name }}

 <!-- Latte syntax -->
-{$meal->getTitle()}
+{$meal->getName()}
```

That's it! We refresh the website, and it works as before:

```html
Potato Salad and Schnitzel
```

👍

## Manual Fix Does not Scale

The focused and simple use case above might work in a demo article like this one, but in real projects, it's pretty rare to see a template with 1 line of code.

We do such renames in projects that make money. There might be some tests, but not every single template render path is tested. We can't afford to wait for "user testing" and error 500 in our logs.

Let's see what would happen in a PHP-only world. We can do method rename either manually, with PHPStorm, or Rector. What happens if we forget a single spot?

```php
$meal = new Meal();
echo $meal->getTitle();
```

The pull-request CI pipeline fails red with a message:

```bash
Calling unknown method "getTitle" on "App\Meal" object
```

Yes, PHPStan protects us. If such a bug got into the code, it would be idiotic.

Should we treat templates with lower expectations? No!
**The same rules that apply for our PHP code must apply for templates as well.**

## Starting Post Series

There are a couple of interesting topics in the area of static analysis in templates.  I consider them highly joyful and worth sharing with you. **It's a brand new topic, so I would like to invite you to shape the future of it**.

What do you think about it? Let me know in the comments below or on [Twitter](https://twitter.com/votrubat).

<br>

Happy coding!
