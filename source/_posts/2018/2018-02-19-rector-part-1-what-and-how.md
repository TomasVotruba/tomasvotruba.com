---
id: 77
title: "Rector: Part 1 - What and How"
perex: '''
    Rector is a PHP tool that handles 2 things: **instant upgrades** and **architecture refactorings**.
    <br><br>
    What exactly Rector does and how does it work?
'''
tweet: "New post on my blog: Rector: Part 1 - What and How #php #ast #refactoring #instantupgrades"
related_items: [63, 78]
---

*Read also:*
 
- [Part 2 - Maturity of PHP Ecosystem and Founding Fathers](/blog/2018/02/26/rector-part-2-maturity-of-php-ecocystem-and-founding-fathers/)
- [Part 3 - Why Instant Upgrades](/blog/2018/03/05/rector-part-3-why-instant-upgrades/)

<br>

## What is Rector?

Rector is a PHP CLI tool build on [Symfony Components](https://symfony.com/components) that changes your PHP code for better.
He only does what you tell him to do. You can use him to *instantly upgrade* your application or to do *architecture refactorings* once for the whole codebase.

Rector won't do your job. It's here to **do the boring stuff for you**. Its help is similar to coding standard tools' help with code reviews - move focus from spaces and commas to architecture of the code.

### Where is it?

You can [find it on Github](https://github.com/rectorphp/rector). It has now [6 contributors](https://github.com/rectorphp/rector/graphs/contributors) in total. I want to thank young talented PHP developer [Gabriel Caruso](https://github.com/carusogabriel) from Brazil for his great contributions since December 2017 that pushed Rector to a brand new level.

## What are Instant Upgrades?

*I'll show examples on [Symfony](https://symfony.com/), because that's the framework I know and love the best.*

Let's say you have a project on Symfony 2.7. And you have a huge `service.yml`. You know that Symfony 2.8/3.0 brought an awesome [autowiring](https://symfony.com/blog/new-in-symfony-2-8-service-auto-wiring) feature that evolved to pure awesomenes in Symfony 3.3 and [PSR-4 services feature](/blog/2017/05/07/how-to-refactor-to-new-dependency-injection-features-in-symfony-3-3/#4-use-psr-4-based-service-autodiscovery-and-registration).

Would you like to do this upgrade work manually? No. You can use Rector instead.
Just run it with target `level` of `symfony33` and it will change everything it knows about.

Such a command looks like this:

```bash
vendor/bin/rector process src --level symfony33
```

## What are Architecture Refactorings?

The great task Rector can handle is to architecture refactorings. Your code might use a framework, but that's just 50 % of the code. The other 50 % is up to you, how you decide to use it - I mean static calls, services locators, facades over dependency injections etc.

I've seen many applications built on Symfony that used very interesting patterns:

```php
class LoggingEventSubscriber implements EventSubscriberInterface
{
    public function setController($controller)
    {
        $this->controller = $controller;
    }

    public function process()
    {
        $logger = $this->controller->get('logger');
        $logger->log('it happened!');
    }
}
```

Let's say you'd like to remove all `$this->get('logger')` and replace them with dependency injection of `LoggerInterface` type. It's not strictly coupled to the Symfony (both [Nette](https://forum.nette.org/en/22075-context-on-presenter-is-deprecated) and [Laravel](https://laravel.com/docs/5.5/facades#facade-class-reference) allows this in some version) but you want to change this in the whole application.

From this:

```php
class LectureController extends BaseController
{
    public function listAction()
    {
        $logger = $this->get('logger');
        $logger->log('it happened!');
    }
}
```

To this:

```php
class LectureController extends BaseController
{
    /**
     * @var LoggerInterface
     */
    private $logger;

    public function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    public function listAction()
    {
        $this->logger->log('it happened!');
    }
}
```

This can Rector handle too.

Do you use Laravel and want to move from facades to constructor injection? Rector can help you.

## How does it Work?

Rector parses the code to [AST](/blog/2017/11/06/wow-to-change-php-code-with-abstract-syntax-tree/) thanks to PHP superman [nikic](https://nikic.github.io/)'s [php-parser](https://github.com/nikic/PHP-Parser).

Then it finds specific places in the code, e.g. all variables that contain `Symfony\Component\HttpFoundation\Request` type and call `isMethodSafe()` method.

Then it changes it into `isMethodCacheable()` (see [UPGRADE-4.0.md](https://github.com/symfony/symfony/blob/master/UPGRADE-4.0.md#httpfoundation)).

Such a configuration looks like this (as shown in [`README`](https://github.com/rectorphp/rector#change-a-method-name)):

```yaml
# rector.yml
rectors:
    # prepared service that handles method name changes
    Rector\Rector\Dynamic\MethodNameReplacerRector:
        # type to look for
        'Symfony\Component\HttpFoundation\Request':
            # old method name: new method name
            'isMethodSafe': 'isMethodCacheable'
```

### Member of Big AST PHP Family

Rector is not the only one who uses `nikic\php-parser` for context-aware operation on your code.

You probably heard of [PHPStan](/blog/2017/01/28/why-I-switched-scrutinizer-for-phpstan-and-you-should-too/). But unfortunately it's read-only for deterministic cases = when 1 error has exactly 1 possible solution.

A bit further is another static analysis tool - [`vimeo/psalm`](https://github.com/vimeo/psalm) by [Matthew Brown](https://github.com/muglug), which fixes such code. Great job Matthew!

## Easter Egg: Has Google Own "Rector"?

This *setup and forget* approach is so addictive, that Google must have it too, right?

And it does! I found 4-page case study *[Large-Scale Automated Refactoring Using ClangMR](https://static.googleusercontent.com/media/research.google.com/en//pubs/archive/41342.pdf)*, that was [presented by *Hyrum Wright* on CppCon2014](https://www.youtube.com/watch?v=ZpvvmvITOrk) in 57 minutes. Hyrum doesn't work at Google anymore (as he wrote me), yet I still love his detailed and practical talk.

I'm still amazed by how their approach is 90 % similar to Rector, just for C++.

<br>

In the next post, I [wrote about what needed to happen before Rector could have been born](/blog/2018/02/26/rector-part-2-maturity-of-php-ecocystem-and-founding-fathers/).

<br>

Happy coding!
