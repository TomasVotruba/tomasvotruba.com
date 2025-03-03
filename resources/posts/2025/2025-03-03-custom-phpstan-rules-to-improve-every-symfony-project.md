---
id: 428
title: "Custom PHPStan Rules to Improve Every Symfony project"
perex: |
    Using PHPStan is not just about getting to level 8 with less than 100 ignored cases. Yes, there are also [official extensions](https://packagist.org/?query=phpstan%2Fphpstan-) that improve the type support of Symfony, Doctrine, and Laravel projects.

    But more rules are needed to get our PHP project into a future-proof state.

    **It takes less effort than getting to level 5 and we can use them since day one**. That's why I love them so much.
---

<blockquote class="blockquote text-center mt-5 mb-5">
"The more you sweat in training,<br>
the less you bleed in combat."
</blockquote>

## What is a "future-proof" state?

I use this term to define [project code quality](/blog/2020/03/02/we-do-not-need-senior-developers-we-need-senior-code-bases), on its own, without developers. I think you, my reader, have your own idea of what *good code quality* is. If you're with the project and have time to review pull requests, it's good.

But what happens when we leave the project? Or do we get promoted to a manager position and don't have time to review PRs anymore?

That's when the real code quality shows. The project should be able to survive without us. Not just survive, but prosper and teach others to take good care of it.

<br>

The way we can **make project future proof** is:

* good test coverage, but also easy to maintain tests ([no mocking of our own code](/blog/5-ways-to-extract-value-from-overmocked-tests))
* [99 % type coverage](/blog/how-to-measure-your-type-coverage)
* **guidelines and rules that are enforced by CI**

<br>

Today we look closely at the last item.

<br>

## Improve Codebase, one Rule at a Time

Symfony framework offers 50+ packages we can use. It's challenging to understand them all and use them correctly without many years of experience. I've seen very poorly written codebases in Symfony 7 and on the other hand, very well-written Symfony 3 codebases.

<br>

* In every Symfony project we upgrade, we add custom PHPStan rules one by one, to get it into better shape.
* We extract these rules to [shared open-source repository](https://github.com/symplify/phpstan-rules/), so more projects can re-use them.
* The package is downgraded to PHP 7.4, so even older projects can use them without effort.

<br>

The goal of these rules is to improve the codebase and **keep it that way even if we leave the project** (by leaving the company or selling it).

<br>

## A. Rules for clear Dependency Injection

### 1. NoRequiredOutsideClassRule

```yaml
rules:
    - Symplify\PHPStanRules\Rules\Symfony\NoRequiredOutsideClassRule
```

The `#[Require]`/`@required` is a great feature to prevent dependency hell. They should be used exclusively in classes.
Why? Using required in traits can lead to highly coupled and hard-to-read code:

```php
use Symfony\Component\DependencyInjection\Attribute\Required;

trait SomeTrait
{
    #[Required]
    public function autowireSomeTrait(SomeService $someService)
    {
        // ...
    }
}
```

```php
final class SomeController
{
    // these traits are autowired, and inject 10 different services
    // some of them with the same name, but a different type
    use SomeTrait;
    use AnotherTrait;
    use YetAnotherTrait;
}
```

Use clear `__construct()` injection instead.

<br>

### 2. NoAbstractControllerConstructorRule

```yaml
rules:
    - Symplify\PHPStanRules\Rules\Symfony\NoAbstractControllerConstructorRule
```

The abstract controller should not have a constructor, as it can lead to tight coupling and multi-level parent constructor parsing.

```php
abstract class AbstractController extends Controller
{
    public function __construct(
        private SomeService $someService,
        private AnotherService $anotherService,
        private YetAnotherService $yetAnotherService,
    ) {
    }
}
```

Then every single child class has to pass these services into the parent constructor:

```php
final class ProjectController extends AbstractController
{
    public function __construct(
        SomeService $someService,
        AnotherService $anotherService,
        YetAnotherService $yetAnotherService,
        private ProjectService $projectService,
        private ProjectRepository $projectRepository,
    ) {
        parent::__construct($someService, $anotherService, $yetAnotherService);
    }
}
```

What a mess, right? Actually, we have 40+ of such child controller classes. Then we add/remove a single service in the `AbstractController::__construct()` method... and **we have to update all 40+ child classes**.

<br>

Instead, the abstract class should use `#[Require]`, and `@required` autowire to promote clear and easy-to-use constructor injection in child classes with isolated dependencies:

```php
final class ProjectController extends AbstractController
{
    public function __construct(
        private ProjectService $projectService,
        private ProjectRepository $projectRepository,
    ) {
    }
}
```

<br>

### 3. NoConstructorAndRequiredTogetherRule

```yaml
rules:
    - Symplify\PHPStanRules\Rules\Symfony\NoConstructorAndRequiredTogetherRule
```

Frameworks are very open and tolerate many ways to do one thing. Like using `#[Require]` and `__construct()` in single class together. It can happen, so we have the PHPStan rule to have our back and prevent this. Use one or the other, not both.

<br>

### 4. NoFindTaggedServiceIdsCallRule

```yaml
rules:
    - Symplify\PHPStanRules\Rules\Symfony\NoFindTaggedServiceIdsCallRule
```

Using `findTaggedServiceIds()` is a historical feature to collect many services of one type and add them to another collector service. e.g. adding all `EventSubscriberInterface` to `EventDispatcher`.

For many years, we have now autowire tags and attributes to handle the same operation in configs without compiler passes. Unfortunately, it's [still promoted in docs](https://symfony.com/doc/current/service_container/tags.html).

This rule warns about this method and promotes [single-line in configs instead](https://symfony.com/doc/current/service_container/tags.html#reference-tagged-services).

<br>

### 5. NoGetInControllerRule

```yaml
rules:
    - Symplify\PHPStanRules\Rules\Symfony\NoGetInControllerRule
```

This rule prevents service locator anti-pattern `$this->get(...)` in controllers, to promote dependency injection. It's rather historical, but there are still many projects that use it.

<br>

The same rule, just for console commands:

```yaml
rules:
    - Symplify\PHPStanRules\Rules\Symfony\NoGetInCommandRule
```

<br>

## B. Unified Routing

### 6. NoRoutingPrefixRule

```yaml
rules:
    - Symplify\PHPStanRules\Rules\Symfony\NoRoutingPrefixRule
```

Avoid hiding route paths in prefixes.

```php
use Symfony\Component\Routing\Loader\Configurator\RoutingConfigurator;

return static function (RoutingConfigurator $routingConfigurator): void {
    $routingConfigurator->import(__DIR__ . '/some-path')
        ->prefix('/some-prefix');
};
```

Why? This makes it hard to find the actual route path. If we look at a controller and see:

```php
/**
 * @Route("/some-path")
 */
```

Is it really the path or is there some magic prefix elsewhere?
Use a single place for paths in `@Route`/`#[Route]`. This also opens a path to PHPStan rules that work with routes in a reliable way.

<br>

### 7. NoClassLevelRouteRule

```yaml
rules:
    - Symplify\PHPStanRules\Rules\Symfony\NoClassLevelRouteRule
```

Same as above, only in different locations. Avoid class-level route prefixing:

```php
use Symfony\Component\Routing\Attribute\Route;

#[Route('/some-prefix')]
class SomeController
{
    #[Route('/some-action')]
    public function someAction()
    {
    }
}
```

It's easy to get lost in 200+ lines controller and jump back and forth. Use a single place in `#[Route]`/`@Route` to keep a single source of truth and focus. Also helps PHPStan to work with routes in a reliable way.

To add more value to a single source of truth: if we use [FOSRestBundle](https://github.com/FriendsOfSymfony/FOSRestBundle), the global file/controller prefixes are sometimes included, and sometimes ignored.

<br>

Are you afraid of bugs in routes during this refactoring? Get covered with [route smoke testing](/blog/cost-effective-container-smoke-tests-every-symfony-project-must-have) before doing anything. We use it and it works great.

<br>

## C. Event Subscribers

### 9. NoListenerWithoutContractRule

```yaml
rules:
    - Symplify\PHPStanRules\Rules\Symfony\NoListenerWithoutContractRule
```

If we use listeners over subscribers, we have to do more config coding. Instead `EventSubscriberInterface` contract stores all metadata in the class itself. Here is [how to upgrade](/blog/2019/07/22/how-to-convert-listeners-to-subscribers-and-reduce-your-configs).

<br>

### 10. NoStringInGetSubscribedEventsRule

```yaml
rules:
    - Symplify\PHPStanRules\Rules\Symfony\NoStringInGetSubscribedEventsRule
```

Symfony `getSubscribedEvents()` method must contain only event class references, no strings. Why?

* easier upgrade from stringy event names to [event-objects](https://symfony.com/blog/new-in-symfony-4-3-simpler-event-dispatching)
* better PHPStan support for custom rule
* better IDE support for event names, as classes

<br>

That was a selection of 10 rules we use in every Symfony project. You can [the full Symfony list here](https://github.com/symplify/phpstan-rules#3-symfony-specific-rules).

<br>

## Get your Symfony project to PRO today

1. Install the rules:

```bash
composer require symplify/phpstan-rules --dev
```


<br>

2. Add the first rule and fix the spotted cases. A good first pick rule is `NoGetInControllerRule`.

It prevents using `$this->get(...)` in controllers, to promote dependency injection instead.

```yaml
rules:
    - Symplify\PHPStanRules\Rules\Symfony\NoGetInControllerRule
```

<br>

3. Once you've covered most of the rules, replace them with a single ruleset import to keep your `phpstan.neon` clean:

```yaml
# phpstan.neon
includes:
    - vendor/symplify/phpstan-rules/config/symfony-rules.neon
```

<br>

Happy coding!
