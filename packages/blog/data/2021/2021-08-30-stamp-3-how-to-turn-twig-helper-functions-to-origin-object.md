 ---
id: 341
next_post_id: 342
title: "STAMP #3: How to Turn TWIG Helper Functions to Origin Object"
perex: |
    In the previous post, we looked at [*how* to turn Messy TWIG PHP to something useful](/blog/stamp-2-how-to-turn-messy-twig-php-to-something-useful) in general.

    Today we'll look at how to **change TWIG helper functions to their original object form**.

---

Have you joined our "STAMP" series just in this post? We're trying to convert the TWIG file. In the TWIG template, we use the single variable `meal`, an `App\Meal` type object.

```twig
{{ meal.title }}
```

What do we want as an output? A PHP code that PHPStan can understand and analyze. All steps must happen automatically, without any manual effort, on a typical PHPStan run:

```bash
vendor/bin/phpstan
```

In the previous post, we managed to narrow the TWIG mess to a single proper method, `doDisplay()`:

```php
use Twig\Template;

/* templates/meal.twig */
class __TwigTemplate_8a9d1381e8329967... extends Template
{
    protected function doDisplay(array $context, array $blocks = [])
    {
        // line 1
        echo twig_escape_filter(
            $this->env,
            twig_get_attribute(
                $this->env,
                ($context["meal"] ?? null),
                "title",
                "any",
                false,
                false,
                false,
                1
            ),
            "html",
            null,
            true
        );
    }
}
```

Would it be enough to run PHPStan on an analyze `App\Meal` object? There is no single mention about `App\Meal` type, not even an object - we can see: functions, `$this->env` property, and some `$context` array.

## Keep it Simple

This looks like a desperate situation in a new job when they tell you, "we have strict clean code standards".

Time to quit? Not so fast.

<br>

Let's get back to basics, our TWIG template:

```twig
{{ meal.title }}
```

Now forget everything we know about TWIG PHP and complex `php-parser`.

## Back to the Facts

What are the known axioms we can work with?

* the `meal` is an object of `App\Meal` type
* the `.title` is TWIG magic syntax
    * for something like `getTitle()` method call
    * or `->title` property fetch in case of public property
* in [the first post](/blog/stamp-static-analysis-of-templates) we mentioned how `App\Meal` class looks like

```php
namespace App;

final class Meal
{
    public function getTitle(): string
    {
        return 'Potato Salad and Schnitzel';
    }
}
```

<br>

Knowing only this, how **could we write this code in pure PHP**?

```php
$meal->title
```

Probably not, there is no public property `$title`, so it would fail on undefined pubic property.

<br>

```php
$meal->getTitle()
```

Better, but what does this method do? `void`. It does not show anything. How can we improve it?

<br>

```php
echo $meal->getTitle()
```

Wow, almost like the original!
But seeing only this line of code, the `$meal` could be just an empty string for PHPStan. What about that?

<br>

```php
/** @var \App\Meal $meal */
echo $meal->getTitle()
```

Great! It would be better to define the type in parameter type of some function, but we don't see any function there. The important one is that **PHPStan now sees an object of a specific type and method call of a specific name on it**.

## Stripping down `twig_get_attribute()`

It's important to know what one wants. Now we can modify the original `echo` statement in `doDisplay()` method. First, we can drop the `twig_escape_filter()` function call, which is TWIG internal, unrelated to our template code.

```diff
// line 1
-echo twig_escape_filter(
-    $this->env,
-    twig_get_attribute(
+echo twig_get_attribute(
         $this->env,
         ($context["meal"] ?? null),
         "title",
         "any",
          false,
          false,
          false,
          1
-    ),
+    );
-    "html",
-    null,
-    true
-);
```

<br>

We now have just the `twig_get_attribute()` function call:

```php
// line 1
echo twig_get_attribute(
    $this->env,
    ($context["meal"] ?? null),
    "title",
    "any",
    false,
    false,
    false,
    1
);
```

<br>

## Drop the Boolean and Repeated Values

Let's take it further. What does `twig_get_attribute()` probably do?

* It checks if the variable `meal` is defined.
* Then tries to call `getTitle()` or fetch `title` property on it.

Now, we could drop always-present arguments unrelated to our original TWIG template:

```diff
 // line 1
 echo twig_get_attribute(
-    $this->env,
     ($context["meal"] ?? null),
     "title",
-    "any",
-    false,
-    false,
-    false,
-    1
 );
```

## Still, Keep it Simple

What do we already know about our new code snippet?

```php
// line 1
echo twig_get_attribute(
    ($context["meal"] ?? null),
    "title",
);
```

* the `$meal` variable always exists
* the `$meal` is an object of `App\Meal` type
* the `getTitle()` is the existing public method

<br>

Let's apply this knowledge:

```diff
 // line 1
 echo twig_get_attribute(
-    ($context["meal"] ?? null),
+    /** @var \App\Meal $meal */
+    $meal,
-    "title",
+    "getTitle",
 );
```

<br>

↓

<br>

What do we know about this code snippet?

```php
// line 1
echo twig_get_attribute(
    /** @var \App\Meal $meal */
    $meal,
    "getTitle",
);
```

* the `twig_get_attribute` is not needed, as its internal TWIG function
* the `"getTitle"` will be called directly on `$meal`

<br>

```diff
 // line 1
-echo twig_get_attribute(
-    /** @var \App\Meal $meal */
-    $meal,
-    "getTitle",
- );
+/** @var \App\Meal $meal */
+echo $meal->getTitle();
```

<br>

Great! It's a **method call we've been waiting for:**

```php
/** @var \App\Meal $meal */
echo $meal->getTitle();
```

Now PHPStan can check the file and tell us if the `getTitle` method exists on `App\Meal` or not.

PHPStan now knows that `$meal->getTitle()` returns a `string`, and can report type errors:

```twig
10 * {{ meal.title }}
```

## PHP Line vs. TWIG Line

You've probably noticed we kept `// line 1` comment in every snippet. What is it for?

**Every proper PHPStan error** has:

* an error message,
* file of origin,
* and exact line.

<br>

Here we analyze a PHP file that is much bigger than the original TWIG file. Our 1 line in TWIG template was compiled to **~80 lines of PHP.**

So why is the `// line 1` important? The metadata from the native TWIG compiler tells us that **code under this comment belongs to line `X` in the TWIG template**.

## Smart PHP => TWIG Line Mapping

How can we use line mapping? Let's say we change the method name in our template:

```diff
-{{ meal.title }}
+{{ meal.name }}
````

PHPStan then reports non-existing `getName()` method error. Without mapping, it would report line that does not even exist:

```bash
Error in /templates/meal.twig file on line 54:
```

With mapping, we get **correct TWIG line**:

```bash
Error in /templates/meal.twig file on line 1:
```

<br>

## To Sum Up

All right, we have removed clutter from the compiled PHP template file and used `NodeVisitor` from `php-parser` to convert magical TWIG functions to explicit PHP code.

We're now ready use PHPStan for analysis:

```bash
vendor/bin/phpstan analyze /temp/twig/__TwigTemplate_8a9d1381e8329967...php
```

<br>

Or is there a better way to run all PHPStan rules on a single file? 🤔

You'll find the answer in the next post.

<br>

Happy coding!
