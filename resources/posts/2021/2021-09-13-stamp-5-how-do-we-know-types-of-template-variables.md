---
id: 345
title: "STAMP #5: How do we Know Types of Template Variables"
perex: |
    In the previous post, we finished the conversion of [TWIG to PHP, run PHPStan on temporary PHP file and got list of found errors](/blog/stamp-4-how-to-run-phpstan-rules-on-temporary-php-file). We've done a full circle, and PHPStan analyses our TWIG templates.


    I've shared the intro post [on Reddit](https://www.reddit.com/r/PHP/comments/qbwudj/stamp_0_static_analysis_of_templates), that sparked many exciting questions.<br>
    Today we'll answer one of them.

---

<div class="pl-4">
    <iframe id="reddit-embed" src="https://www.redditmedia.com/r/PHP/comments/qbwudj/stamp_0_static_analysis_of_templates/hhcrdlr/?depth=1&amp;showmore=false&amp;embed=true&amp;showmedia=false" sandbox="allow-scripts allow-same-origin allow-popups" style="border: none;" height="309" width="860" scrolling="no"></iframe>
</div>

This is a great question! We skipped this piece to complete the TWIG rendering itself first and keep your cognitive load focused. Now we have time and space to bring the answer.

---

## Context First

First, let's define what PHP and TWIG files we work with. We render a template with 1 parameter:

```php
use App\Meal;

return $this->render('meal.twig', [
    'meal' => new Meal()
]);
```

<br>

The TWIG template contains one method call:

```html
{{ meal.title }}
```

<br>

Using TWIG and php-parser [we compile TWIG](/blog/stamp-1-how-to-compile-twig-to-php) into [nice and clean PHP](/blog/stamp-2-how-to-turn-messy-twig-php-to-something-useful):

```php
echo $meal->getTitle();
```

<br>

Is this clear? Now we can level up.

## Where does the Variable `$meal` come From?

Looking at the code, the `$meal` looks like coming out of nowhere. Where is it defined? In TWIG, these variables are created from a `$context` array, passed as a parameter of the main `doDisplay()` method in the compiled template class.

This `$context` is an array made mostly of 2nd argument in `$this->render()` method.

<br>

In our compiled PHP, we get the `$meal` variable from the `$context` array:

```php
public function doDisplay(array $context)
{
    $meal = $context['meal'];
    echo $meal->getTitle();
}
```

And that's how TWIG variables are born in compiled PHP.

## What is the Type of `$meal` Variable?

Now PHPStan and we know **the variable `$meal` exists** and comes from some `$context` array. But the type is still `mixed`. How do we know `$meal` is `App\Meal`?

<br>

Let's back up a little bit and look into first place the type appeared - in our controller:

```php
use App\Meal;

return $this->render('meal.twig', [
    'meal' => new Meal()
]);
```

Here we see that the `meal` string is the type of `App\Meal`. Could we add this type somehow to `meal.twig`?

<br>

Not so fast! The templates are reusable so that another developer can render them in the following way:

```php
use App\Meal;

return $this->render('meal.twig', [
    'meal' => 'Dinner'
]);
```

Here the `$meal` variable is a type of `string` and the template `{{ meal.title }}` will crash.

<br>

So to answer the question - the **type depends on the place it's rendered in PHP**. The same way [PHPStan does not analyze traits standalone, but only in the context of specific class](https://phpstan.org/blog/how-phpstan-analyses-traits).

## Template Context and Variables Types

We see that rendering the same template in different places can result in different types. Also, we should not forget, the **PHPStan sees only the final compiled PHP file** - it has no idea about the controller the template is rendered in.

<br>

Now that we know the rules of the game, the plan is pretty straightforward:

1. collect known variable types in the exact place in PHP
2. compile the TWIG to PHP
3. decorate the compiled PHP with variable types

Let's take step by step in our practical example.

### 1. Collect Variable Types

We collect types at the exact moment we see the `$twig->render()` method in PHP code.

```php
use App\Meal;

return $this->render('meal.twig', [
    'meal' => new Meal()
]);
```

The 2nd argument is an array, and we can traverse that array and detect the type with PHPStan's `$scope`.

<br>

At the end of this step, we'll have an array with a variable name and its type:

* `meal` => `PHPStan\Type\ObjectType` of `App\Meal`

### 2. Compile the TWIG to PHP

We've already covered this process in previous parts:

* [STAMP #1: How to Compile Twig to PHP](/blog/stamp-1-how-to-compile-twig-to-php)
* [STAMP #2: How to Turn Messy TWIG PHP to Something Useful](/blog/stamp-2-how-to-turn-messy-twig-php-to-something-useful)
* [STAMP #3: How to Turn TWIG Helper Functions to Origin Object](/blog/stamp-3-how-to-turn-twig-helper-functions-to-origin-object)

### 3. Decorate the Compiled PHP

The TWIG is now compiled to PHP, and we know the types of variables. But the PHPStan has still no idea about types:

```php
public function doDisplay(array $context)
{
    $meal = $context['meal'];
    echo $meal->getTitle();
}
```

<br>

How do we help PHPStan in standard PHP code if we know about some types that are not clear from the code?

Like this:

```diff
 public function doDisplay(array $context)
 {
+    /** @var \App\Meal $meal */
     $meal = $context['meal'];
     echo $meal->getTitle();
 }
```

We'll automate this process again with `php-parser`. Now the **PHPStan can analyze the code with all the known types**.

<br>

Happy coding!
