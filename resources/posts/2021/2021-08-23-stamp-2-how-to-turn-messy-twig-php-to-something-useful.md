 ---
id: 340
title: "STAMP #2: How to Turn Messy TWIG PHP to Something Useful"
perex: |
    In the previous post, we looked [at *how* to compile TWIG to raw PHP](/blog/stamp-1-how-to-compile-twig-to-php). It's one step forward, but it's not enough.


    Today we look at *how* we turn **the raw PHP code to the code PHPStan understands**.
---

In the previous post, we successfully ~~rendered~~ compiled `templates/meal.twig` template:

```twig
{{ meal.title }}
```

<br>

## Rendered or Compiled?

What is the difference between *rendered* and *compiled* in our context?

* When we *render* a TWIG template, the result will be the **final HTML that users see** when they open the website.
* When we *compile* a TWIG template, we'll get a **PHP code with class**, that TWIG uses for the cache.

<br>

After compilation `templates/meal.twig` to PHP, we'll get a child of `Twig\Template` class:

```php
use Twig\Environment;
use Twig\Source;
use Twig\Template;

/* templates/meal.twig */
class __TwigTemplate_8a9d1381e8329967... extends Template
{
    private $source;
    private $macros = [];

    public function __construct(Environment $env)
    {
        parent::__construct($env);
        $this->source = $this->getSourceContext();
        $this->parent = false;
        $this->blocks = [];
    }

    protected function doDisplay(array $context, array $blocks = [])
    {
        $macros = $this->macros;
        // line 1
        echo twig_escape_filter(
            $this->env,
            twig_get_attribute(
                $this->env,
                $this->source,
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

    public function getTemplateName()
    {
        return "templates/meal.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (37 => 1);
    }

    public function getSourceContext()
    {
        return new Source("", "templates/meal.twig", "");
    }
}
```

<br>

What a mess, right? Don't worry; you don't have to understand a single line of it.

What is our plan for today? Somehow "transform" this code so **PHPStan can analyze it**.

<blockquote class="blockquote mt-5 mb-5 text-center">
    "Perfection is achieved not when there is nothing more to add,<br>
    but when there is nothing left to take away"
    <footer class="blockquote-footer">Antoine de Saint-Exupery</footer>
</blockquote>

## Keep only the Necessary Code

The first step is to **remove the clutter** helpful only for TWIG internals. These methods do not provide any information about the original TWIG code. In other words: remove the PHP content that is always the same, regardless of the TWIG input file we use.

<br>

## "Why we don't Run PHPStan on This PHP File?"

Great question! What would happen if we did? The PHPStan would analyze the content based on the TWIG template, but it would also ** analyze the TWIG generator for template classes**. This way, we would get dozens of always the same errors repeated for every single TWIG file.

We don't want to run PHPStan on TWIG itself; that's a job for Symfony maintainers. We want to only know about possible bugs coming from our TWIG template code:

```twig
{{ meal.title }}
```

## Which Methods are Useful?

How do we define if the class method is proper? Let's use common sense to drop class methods that look like "metadata". If we drop a method that proves helpful in the future, we'll return it.

Look for the keywords mentioned in TWIG template: "meal" and "title". They are mentioned in `doDisplay()` class method, let's keep that.

```diff
-use Twig\Environment;
-use Twig\Source;
 use Twig\Template;

 /* templates/meal.twig */
 class __TwigTemplate_8a9d1381e8329967... extends Template
 {
-    private $source;
-    private $macros = [];
-
-    public function __construct(Environment $env)
-    {
-        parent::__construct($env);
-        $this->source = $this->getSourceContext();
-        $this->parent = false;
-        $this->blocks = [];
-    }
-
    protected function doDisplay(array $context, array $blocks = [])
    {
-       $macros = $this->macros;
        // line 1
        echo twig_escape_filter(
            $this->env,
            twig_get_attribute(
                $this->env,
-               $this->source,
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
-
-    public function getTemplateName()
-    {
-        return "templates/meal.twig";
-    }
-
-    public function isTraitable()
-    {
-        return false;
-    }
-
-    public function getDebugInfo()
-    {
-        return array (37 => 1);
-    }
-
-    public function getSourceContext()
-    {
-        return new Source("", "templates/meal.twig", "");
-    }
 }
```

<br>

In the end we keep only `__construct` and `doDisplay()` methods:

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

<br>

That looks better. 75 % less code!

But wait...

## How do we Remove these Red Lines?

We're editing a cache PHP code that TWIG compiles. Do we open this file in PHPStorm, edit it, save it and feed it PHPStan?

```bash
vendor/bin/phpstan analyse temp/twig/__TwigTemplate_8a9d1381e8329967...php
```

That might give us the PHPStan analysis we aim for, but would you like to edit cache files for every single TWIG file manually? I thought so.

So how do we automate PHP code modifications based on specific rules? Yes, we could use [Rector](http://github.com/rectorphp/rector), but that's a far too heavy tool to include just for a single PHPStan rule.

Instead, we use bare `nikic/php-parser` and custom `NodeVisitor`. We don't want to focus on AST modifications, but to give you an idea:

```php
use PhpParser\Node\Stmt\ClassMethod;
use PhpParser\NodeVisitorAbstract;
use PhpParser\NodeTraverser;

final class TwigCleaningNodeVisitor extends NodeVisitorAbstract
{
    public function enterNode(Node $node)
    {
        // not a class method? skip it
        if (! $node instanceof ClassMethod) {
            return null;
        }

        // is one of these class method names? skip it
        if ($node->name->toString() === 'doDisplay') {
            return null;
        }

        // remove the class method
        return NodeTraverser::REMOVE_NODE;
    }
}
```

This node visitor will remove all but `doDisplay()` method.

*Would you like to know more about this? Read [Programmatically Modifying PHP Code chapter](https://leanpub.com/rector-the-power-of-automated-refactoring) of the Rector book. Matthias describes behavior in nice short examples*.

<br>

That's all for today, to keep the reading light. In the next post, we'll try to give `doDisplay()` a more transparent form.

<br>

Happy coding!
