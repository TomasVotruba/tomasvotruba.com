---
id: 294
title: "5 New Combos opened by Symfony&nbsp;5.2&nbsp;and PHP&nbsp;8.0"
perex: |
    Conjunction of 2 releases came in December 2020, Symfony 5.2 and [PHP 8.0](https://getrector.org/blog/2020/11/30/smooth-upgrade-to-php-8-in-diffs).
    I wanted to gave them a fresh try, so I've updated `composer.json` in 3 projects, run Rector upgrade set and this happened...
tweet: "New Post on #php 🐘 blog: 5 New Combos opened by Symfony 5.2 and PHP 8.0"
---


For code-screeners like me, here are direct links to pull-requests to get the real deal of migration porn:

- [tomasvotruba.cz upgrade](https://github.com/TomasVotruba/tomasvotruba.com/pull/1107/files)
- [getrector.org upgrade](https://github.com/rectorphp/getrector.org/pull/190/files)
- [friendsofphp.org upgrade](https://github.com/TomasVotruba/friendsofphp.org/pull/176/files)

I haven't done such a smooth upgrade in years. The work started on December 1st and finished with last merge on December 2nd. That's 2 days - **that's only possible thanks to amazing work of PHP contributors and Symfony team**. Thank you!


## Open the Next Door

Now that we have this off the table, let's talk about "open the next door" technique. Open door is one of kaizen approaches to coding, life and everything.

**We don't know what is behind the door, until we open them and enter the room**. There might be another door, that we might open. Don't think about what is after 2nd potential door in a room we haven't seen yet. Just enter the room and see.

<img src="/assets/images/posts/2020/combo-door.jpg" class="img-thumbnail">

Same way PHP 5.3 helped with service vs value object directory structure in times nobody think of it:

- PHP 5.3 opened door to namespaces
- then PSR-4 opened door to unique file-class names
- then Symfony autodiscovery opened door to namespace-based service loading
- then namespace-based service loading opened door to services vs value objects directory structure

*Kaizen* is a Japanese technique about daily continuous little improvements. Today **we open the door to PHP 8, then another to Symfony 5.2**. Only then we can see, what is in the room we've never been to.

<br>

Here is what I saw:


## 1. Switch `@Route` Annotations to `#[Route]` Attributes

PHP 8 brings attributes and Symfony 5.2 brings `#[Route]` attribute. Now we can finally get rid of stringy annotations and get robust reliable native PHP attributes code:

```diff
-/**
- * @Route(path="/archive", name="blog_archive")
- */
+#[Route(path: '/archive', name: 'blog_archive')]
 public function someMethod(): Response
 {
     // ...
 }
```

Named properties included.

## 2. Route Names Can be Constants

Annotations have somewhat autocomplete support, but lack of docblock standard make parsing problematic. One parser supports syntax with trailing `,`, the other does not.

With attributes we can forget this bag of problems and welcome features we use in normal PHP code. E.g. **using constants for repeated strings across many PHP files**.

<br>

During refactoring, I used `"archive"` as route name and project crashed. Why? The correct value was `"blog_archive"`. What a dumb [memory locker](/blog/2018/08/27/why-and-how-to-avoid-the-memory-lock/). We don't want to store and search strings in my brain, we want code as safely as possible with IDE having our back.

<br>

Here is the deal. What if we have a `RouteName` object with constants of route names?

<img src="/assets/images/posts/2020/combo-route-value-object.png" class="img-thumbnail">

No we have **one place** to manage route names <em class="fas fa-fw fa-check text-success"></em>

<br>

What else extra can we get out of this...

<br>

**Where is route used?** Just click on it <em class="fas fa-fw fa-check text-success"></em>

<img src="/assets/images/posts/2020/combo-route-use-cases.png" class="img-thumbnail">

<br>

**What routes can we use?** Ask your IDE <em class="fas fa-fw fa-check text-success"></em>

<img src="/assets/images/posts/2020/combo-route-value-object.png" class="img-thumbnail">

Attribute and redirect autocomplete <em class="fas fa-fw fa-check text-success"></em>

```php
return $this->redirectToRoute(RouteName::CONTACT);
```

Twig autocomplete - kind of crappy now <em class="fas fa-fw fa-question text-warning"></em>

```twig
<a href="{{ path(constant('Rector\\Website\\ValueObject\\RouteName::CONTACT')) }}">Dare us</a>
```

<br>

**How can we rename a route?** In 1 line <em class="fas fa-fw fa-check text-success"></em>

Wait, why should we ever rename a route? That's a good question. But better question is: why should we think about route names at all? We'll get into that.

## 3. Property Promotion empowers Invokable Controllers

What is invokable controller? A controller that you can't turn into a controller with 50 actions even if you had 20 years for that.
**Invokable controller has exactly 1 action called `__invoke()`**. We only think about the controller name and what it should do. It similar to CQRS applied on controller level.

- property promotion
- action injection bring mostly clutter PHP 8.0 promoted properties
- ...

## 4. New PHPStan Rules





## 5. Move from YAML to PHP to get REP

REP works best with pure PHP code. Not YAML, not TWIG, not NEON nor Latte. Pure PHP.
<br>**R**ector, **E**CS and **P**HPStan.
If you're still on old YAML configs, switch now - [there is a tool for that](/blog/2020/07/27/how-to-switch-from-yaml-xml-configs-to-php-today-with-migrify/).

<br>

Then, you can add PHPStan rules, e.g. [one](https://github.com/symplify/phpstan-rules/blob/master/docs/rules_overview.md#checkrequiredautowireautoconfigurepublicusedinconfigservicerule) that make sure you don't forget to add saint `defaults()` to your configs:

```php
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

return static function (ContainerConfigurator $containerConfigurator): void {
    $services = $containerConfigurator->services();

    $services->defaults()
        ->public();
};
```

<em class="fas fa-fw fa-times text-danger fa-2x" style="margin-left: 0em; float: left; padding-right: .2em;"></em> `autoconfigure()` and `autowire()` is missing

<br>

You can avoid [parameter override](https://github.com/symplify/phpstan-rules/blob/master/docs/rules_overview.md#preventdoublesetparameterrule):

```php
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

return static function (ContainerConfigurator $containerConfigurator): void {
    $parameters = $containerConfigurator->parameters();

    $parameters->set('some_param', [1]);
    $parameters->set('some_param', [2]);
};
```

<em class="fas fa-fw fa-times text-danger fa-2x" style="margin-left: 0em; float: left; padding-right: .2em;"></em> `"some_param"` is overridden

<br>

Or [require constant](https://github.com/symplify/phpstan-rules/blob/master/docs/rules_overview.md#requireconstantinmethodcallpositionrule) to configure parameter name:

```php
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

return static function (ContainerConfigurator $containerConfigurator): void {
    $parameters = $containerConfigurator->parameters();
    $parameters->set('some_param', [1]);
};
```

<em class="fas fa-fw fa-times text-danger fa-2x" style="margin-left: 0em; float: left; padding-right: .2em;"></em> string `"some_param"` can create typoes, use constant instead

<br>

Another side effect is that you can finally drop [YAML linting](https://symfony.com/blog/new-in-symfony-4-4-service-container-linter) from your CI:

```diff
         steps:
-            - run: bin/console lint:yaml config
```



