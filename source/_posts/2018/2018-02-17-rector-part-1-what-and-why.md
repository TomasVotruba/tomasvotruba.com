---
id: 77
title: "Retor: Part 1 - What and How"
perex: '''
    Rector is a PHP tool that handles 2 things: **instant upgrades** and **architecture refactorings**.
    <br>
    What exactly Rector does and how it works?  
'''
todo_tweet: "..."
todo_tweet_image: "..."
related_items: [63] 
---

# What is Rector?

Rector is CLI PHP tool build on [Symfony Components](https://symfony.com/components) that changes your PHP code for better. 
He does only what you tell him to do. You can use him to *instantly upgrade* your application or to do *architecture refactorings* once for the whole codebase.

Rector won't do your job. It's here to **do the boring stuff for you**. It's similar help like coding standard tools help with code reviews - move focus from spaces and commas to architecture of the code.

# What are Instant Upgrades?

*I'll show examples on [Symfony](http://symfony.com/), because that's the framework I know and love the best.*

Let's say you have project on Symfony 2.7. And you have huge `service.yml`. You know that Symfony 2.8/3.0 brought awesome [autowiring](https://symfony.com/blog/new-in-symfony-2-8-service-auto-wiring) feature that evolved to pure awesomenes in Symfony 3.3 and [PSR-4 services feature](/blog/2017/05/07/how-to-refactor-to-new-dependency-injection-features-in-symfony-3-3/#4-use-psr-4-based-service-autodiscovery-and-registration).

Would you like to do this upgrade work manually? No. You can use Rector instead.
 Just run it with target `level` of `symfony33` and it will all change he knows about.

# What are Architecture Refactorings?

The great task Rector can handle is to architecture refactorings. Your code might use a framework, but that just 50 % of the code. The other 50 % is up to you, how you decide to use it - I mean static calls, services locators, facades over dependency injections etc. 

I've seen many applications build on Symfony that used very interesting patterns:

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

Let's say you'd like to remove all `$this->get('logger')` and replace them with dependency injection of `LoggerInterface` type. It's not strictly coupled to the Symfony (both [Nette](https://forum.nette.org/en/22075-context-on-presenter-is-deprecated) and [Laravel](https://laravel.com/docs/5.5/facades#facade-class-reference) allows this in some version) but you want to change this in whole application.

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
    private $loggerInterface;
    
    public function __construct(LoggerInterface $loggerInterface)
    {
        $this->loggerInterface = $loggerInterface;
    }

    public function listAction()
    {
        $this->logger->log('it happened!');
    }
}
```

This can Rector handle too.



Do you use Laravel and want to move from facades to constructor injection? Rector can help you. 




## How it Works?

Rector parses the code to [AST](/blog/2017/11/06/wow-to-change-php-code-with-abstract-syntax-tree/) thanks to PHP superman [nikic](https://nikic.github.io/)'s [php-parser](https://github.com/nikic/PHP-Parser).

Then it finds specific place in the code, e.g. all variables that containt `Symfony\Component\HttpFoundation\Request` type and call 'isMethodSafe()' method.
 
Then it changes it into 'isMethodCacheable()' (see [UPGRADE-4.0.md](https://github.com/symfony/symfony/blob/master/UPGRADE-4.0.md#httpfoundation)).

### Growing AST PHP Family

Similar approach with `nikic\php-parser` uses [PHPStan](/blog/2017/01/28/why-I-switched-scrutinizer-for-phpstan-and-you-should-too/), but unfortunately it's read-only even for deterministic (1 error = exactly 1 solution) cases. A bit further is [`vimeo/psalm`](https://github.com/vimeo/psalm) by [Matthew Brown](https://github.com/muglug), that fixes such code.

# Google has Own "Rector"

