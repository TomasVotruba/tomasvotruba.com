---
id: 294
title: "5 New Combos opened by Symfony&nbsp;5.2&nbsp;and PHP&nbsp;8.0"
perex: |
    Conjunction of 2 releases came in December 2020, Symfony 5.2 and [PHP 8.0](https://getrector.org/blog/2020/11/30/smooth-upgrade-to-php-8-in-diffs).
    I wanted to give them a fresh try, so I've updated `composer.json` in 3 projects, run the Rector upgrade set, and this happened...

---

For code-screeners who understand diffs better than words like me, here are direct links to pull-requests to get the real deal of migration porn:

- [tomasvotruba.com upgrade](https://github.com/TomasVotruba/tomasvotruba.com/pull/1107/files)
- [getrector.org upgrade](https://github.com/rectorphp/getrector.org/pull/190/files)
- [friendsofphp.org upgrade](https://github.com/TomasVotruba/friendsofphp.org/pull/176/files)

I haven't done such a smooth upgrade in years. The work started on December 1st and finished with the last merge on December 2nd. That's two days - **that's only possible thanks to the amazing work of PHP contributors and Symfony team**. Thank you! Do you want to upgrade too? [Do it with PHP 8 Rector set](https://getrector.org/blog/2020/11/30/smooth-upgrade-to-php-8-in-diffs).


## Open the Next Door

Now that we have this off the table, let's talk about the "open the next door" technique. An open door is one of the kaizen approaches to coding, life, and everything.

**We don't know what is behind the door until we open them and enter the room**. There might be another door that we might open. Don't think about what is after 2nd potential door in a room we haven't seen yet. Just enter the room and see.

<img src="/assets/images/posts/2020/combo-door.jpg" class="img-thumbnail">

The same way PHP 5.3 helped with service vs. value object directory structure in times nobody think of it:

- PHP 5.3 opened the door to namespaces
- then PSR-4 opened the door to unique file-class names
- then Symfony autodiscovery opened the door to namespace-based service loading
- then namespace-based service loading opened the door to services vs. value objects directory structure

*Kaizen* is a Japanese technique about daily continuous little improvements. Today **we open the door to PHP 8, then another to Symfony 5.2**. Only then can we see what is in the room we've never been to.

<br>

Here is what I saw:

## 1. Switch `@Route` Annotations to `#[Route]` Attributes

PHP 8 brings attributes and Symfony 5.2 brings `#[Route]` attribute. Now we can finally get rid of stringy annotations and get robust reliable native PHP attributes code:

```diff
-/**
- * @Route(path="/archive", name="blog_archive")
- */
+#[Route(path: '/archive', name: 'blog_archive')]
 public function blogArchive(): Response
 {
     // ...
 }
```

Named properties included.

## 2. Route Names Can be Constants

Annotations have kind-of autocomplete support, but lack of docblock standard makes parsing problematic. One parser supports syntax with trailing `,`, the other does not.

With attributes, we can forget this bag of problems and welcome features we use in standard PHP code. E.g., **using constants for repeated strings across many PHP files**.

<br>

During refactoring, I used `"archive"` as route name, and the project crashed. Why? The correct value was `"blog_archive"`. What a dumb [memory locker](/blog/2018/08/27/why-and-how-to-avoid-the-memory-lock/). We don't want to store and search strings in my brain. We want to code as safely as possible with IDE having our back.

<br>

Here is the deal. What if we have a `RouteName` object with constants of route names?

<img src="/assets/images/posts/2020/combo-route-value-object.png" class="img-thumbnail">

No we have **one place** to manage route names ✅

<br>

What else extra can we get out of this...

<br>

**Where is route used?** Just click on it ✅

<img src="/assets/images/posts/2020/combo-route-use-cases.png" class="img-thumbnail">

<br>

**What routes can we use?** Ask your IDE ✅

<img src="/assets/images/posts/2020/combo-route-value-object.png" class="img-thumbnail">

Attribute and redirect autocomplete ✅

```php
return $this->redirectToRoute(RouteName::CONTACT);
```

Twig autocomplete - kind of crappy now...

```twig
<a href="{{ path(constant('Rector\\Website\\ValueObject\\RouteName::CONTACT')) }}">Dare us</a>
```

<br>

**How can we rename a route?** In 1 line ✅

Wait, why should we ever rename a route? That's a good question. But a better question is: why should we think about route names at all? We'll get into that.

## 3. Property Promotion empowers Invokable Controllers

What is property promotion? It's a PHP 8 feature that does this:

```diff
-    private PostRepository $postRepository;

-    private ClusterRepository $clusterRepository;

     public function __construct(
-        PostRepository $postRepository,
+        private PostRepository $postRepository,
-        ClusterRepository $clusterRepository
+        private ClusterRepository $clusterRepository
    ) {
-        $this->postRepository = $postRepository;
-        $this->clusterRepository = $clusterRepository;
    }
```

2/3 less lines, 100 % of duplicated code removed ✅

Note: Are you using action injection? Migrate to promoted properties and [stop now](/blog/2018/04/23/how-to-slowly-turn-your-symfony-project-to-legacy-with-action-injection/).

<br>

What is an invokable controller? It's a form of Symfony controller that is impossible to turn into a huge legacy controller with 50 actions.

**Invokable controller has exactly 1 action called `__invoke()`**. We only think about the controller name and what it should do. It's like CQRS on the controller level.

<br>

So we separate each controller action method into its controller class with a descriptive name:

```diff
-final class HomepageController
+final class BlogArchiveController
 {
     public function __construct(
         private PostRepository $postRepository,
-        private ClusterRepository $clusterRepository
     ) {
     }

     #[Route(path: '/archive', name: RouteName::BLOG_ARCHIVE)]
-    public function blogArchive): Response
+    public function __invoke(): Response
     {
         // ...
     }

-    #[Route(path: '/clusters', name: RouteName::CLUSTERS)]
-    public function clusters(): Response
-    {
-        // ...
-    }
 }
```



This opens another door... now, we'll get to the route naming...

## 4. Door for New PHPStan Rules

What do you think about this controller?

```php
 final class BlogArchiveController
 {
     public function __construct(private PostRepository $postRepository)
     {
     }

     #[Route(path: '/archive', name: RouteName::CONTACT)]
     public function __invoke(): Response
     {
        // ...
     }
 }
```

What about this controller?

```php
 final class ContactController
 {
     public function __construct(private PostRepository $postRepository)
     {
     }

     #[Route(path: '/post-detail', name: RouteName::POST_DETAIL)]
     public function __invoke(): Response
     {
        // ...
     }
 }
```

<br>

If you have OCD or you're a programmer, you have noticed the names doesn't quite add up:

- `BlogArchiveController` - route name: `CONTACT`
- `ContactController` - route name: `POST_DETAIL`

This could have been spotted during code-review... or not.
**Why bother yourself**, when PHPStan can handle it?

<br>

Abdul is [just now working on a PHPStan rule](https://github.com/symplify/symplify/pull/2740), which makes sure **the class name matches the route name**.

- `BlogArchiveController` - route name: `CONTACT` ❌
-  `ContactController` - route name: `CONTACT` ✅

## 5. Move from YAML to PHP to get REP

REP works best with pure PHP code. Not YAML, not TWIG, not NEON nor Latte. Pure PHP.
<br>**R**ector, **E**CS and **P**HPStan.
If you're still on old YAML configs, switch now - [there is a tool for that](/blog/2020/07/27/how-to-switch-from-yaml-xml-configs-to-php-today-with-migrify/).

<br>

Then, you can add PHPStan rules, e.g. [one](https://github.com/symplify/phpstan-rules/blob/master/docs/rules_overview.md#checkrequiredautowireautoconfigurepublicusedinconfigservicerule) that make sure you don't forget to add saint `defaults()` to your configs:

```php
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

return function (ContainerConfigurator $containerConfigurator): void {
    $services = $containerConfigurator->services();

    $services->defaults()
        ->public();
};
```

❌ `autoconfigure()` and `autowire()` is missing

<br>

You can avoid [parameter override](https://github.com/symplify/phpstan-rules/blob/master/docs/rules_overview.md#preventdoublesetparameterrule):

```php
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

return function (ContainerConfigurator $containerConfigurator): void {
    $parameters = $containerConfigurator->parameters();

    $parameters->set('some_param', [1]);
    $parameters->set('some_param', [2]);
};
```

❌ `"some_param"` is overridden

<br>

Or [require constant](https://github.com/symplify/phpstan-rules/blob/master/docs/rules_overview.md#requireconstantinmethodcallpositionrule) to configure parameter name:

```php
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

return function (ContainerConfigurator $containerConfigurator): void {
    $parameters = $containerConfigurator->parameters();
    $parameters->set('some_param', [1]);
};
```

❌ string `"some_param"` can create typeos, use constant instead

<br>

Another side effect is that you can finally drop [YAML linting](https://symfony.com/blog/new-in-symfony-4-4-service-container-linter) from your CI:

```diff
         steps:
-            - run: bin/console lint:YAML config
```

<br>

That's it. We've just opened 5 doors to a new view on PHP programming in Symfony. That's just a start. I bet you can see the 6th door already...

I'm curious, **What new way have you opened in your projects?** Share with us in the comments. I'm eager to try it out.

<br>

Happy coding!
