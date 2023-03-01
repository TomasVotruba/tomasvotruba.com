---
id: 339
title: "STAMP #1: How to Compile Twig to PHP"
perex: |
    In the previous post, we looked [at *why* and *when* static analysis of templates matter](/blog/stamp-static-analysis-of-templates). Today we look at how to prepare starting point for Twig templates.


    How can we analyze templates with PHPStan that understand only PHP? There are 2 ways: we could teach PHPStan the language of Twig - a new "TWIGStan" tool.


    The other option is to take it from the other end - convert Twig to PHP.

---

In the previous post we worked with `templates/meal.twig` with single method call:

```twig
{{ meal.title }}
```

Today we'll try to turn this single line into PHP syntax.

## How do we Render Twig in our Projects?

The most common use case for rendering templates is in a Symfony controller. We call `$this->render()` with a template name as 1st argument:

```php
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;

final class DinnerController extends AbstractController
{
    public function __invoke(): Response
    {
        return $this->render('templates/meal.twig');
    }
}
```

This creates rendered HTML. But we don't have a tool called "HTMLStan", but PHPStan. How do we get a PHP syntax of the `templates/meal.twig` template?


## From Controller to Twig Environment

Let's take it to step by step. We don't need the whole `symfony/framework-bundle` to render Twig. What does the `render()` method of `AbstractController` do? It can be decomposed into these calls:

```php
use Symfony\Component\HttpFoundation\Response;

// ...
abstract class AbstractController
{
    protected function render(string $view): Response
    {
        /** @var string $content */
        $content = $this->environment->render($view);

        $response = new Response();
        $response->setContent($content);

        return $response;
    }
}
```

As you've expected, it is a simple request and response with some `string`. The `$content` string is what we're interested in.

But **what is this "environment" we're calling?** First association to "environment" might be "env" or development, production, and tests. But such a name suggests a value object or a variable, not a service. I will save you from confusion - it's a Twig renderer service. A name like "TwigRenderer" or "TwigApplication" would save us this paragraph, but sometimes legacy is what we have to work with.

## From Response to Twig Render

Let's strip of clutter from `render()` and keep only the relevant lines:

```php
use Twig\Environment;

$environment = new Environment();

/** @var string $content */
$content = $environment->render('templates/meal.twig');
```

We pass a TWIG `'templates/meal.twig'` and get rendered HTML `$content`. How can we use this as food for PHPStan?

## TWIG → ? → HTML

We're getting closer. Now we know how to render TWIG file path to HTML content with couple of PHP lines and the `twig/twig` package. That's great! But **how is that useful for static analysis in PHP**?

First, we need to understand TWIG rendering lifecycle. It would be costly to convert every TWIG template to PHP, then complete variables and `echo` it to HTML string.

How does TWIG make sure it's fast?

## 3-Step TWIG Lifecycle

1. find `templates/meal.twig` absolute path and load its contents
2. **check if this template was already parsed to PHP**
- NO? parse if to PHP and save it the PHP to filesystem cache
- YES? load the parsed PHP from the filesystem cache
3. complete dynamic parameters to PHP template and echo it

Now it's clear what we need to do. We have to do step 1., then step 2. parse TWIG to PHP and save the file to filesystem. PHPStan can analyze files in the filesystem, so we have a clear goal!

## Stop Rendering TWIG at PHP Step?

In the last snippet, we can see only the `render()` method that outputs HTML string:

```php
use Twig\Environment;

$environment = new Environment();

/** @var string $content */
$content = $environment->render('templates/meal.twig');
```

That's not what we need; it's too late for us. But we have all we need here. We just deep dive into the `render()` method and find out the smaller steps.

Inside the `render()` method, we'll find many nested calls, but in the end, it's just 3 methods:

* `parse()` → **`compile()`** ~~→ `render()`~~
* TWIG → PHP ~~→ HTML~~

## Finally: Compile TWIG to PHP

If we extract this `compile()` method and remove clutter code, we'll get to 3 lines that do the job:

```php
use Twig\Environment;

$environment = new Environment();

// 1. gets contents of TWIG file and parses to tokens like tokens_get_all()
$source = $environment->getLoader()->getSourceContext('templates/meal.twig');

// 2. compile TWIG tokens to the PHP as we know it
$phpContent = $environment->compileSource($source);
```

<br>

**In `$phpContent` now we have PHP string that PHPStan can analyze, yay!**

# 🎉

## What is in `$phpContent`?

Are you curious, how does the final compiled PHP code look like?

I will not lie to you. It's not nice. It's even worse. It's not readable. That's probably because in 2009 when the TWIG was released, nobody thought of creating beautiful cached PHP code for PHPStan.

<br>

Are you ready? Here is it:

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

        $this->blocks = [
        ];
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

## How is this PHP Mess Helpful?

Not much, to be honest. Yet.

We'll give it a look [in the next post](/blog/stamp-2-how-to-turn-messy-twig-php-to-something-useful). Maybe we can come up with something useful.

<br>

Happy coding!
