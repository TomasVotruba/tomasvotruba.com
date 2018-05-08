---
id: 103
title: "Why You Should Combine Symfony Console and Dependency Injection"
perex: |
    I saw 2 links to Symfony\Console in [today's Week of symfony](http://symfony.com/blog/a-week-of-symfony-592-30-april-6-may-2018) (what a time reference, huh?).
    There are plenty of such posts out there, even in Pehapkari community blog: [Best Practice for Symfony Console in Nette](https://pehapkari.cz/blog/2017/06/02/best-practice-for-symfony-console-in-nette/) or [Symfony Console from the Scratch](https://pehapkari.cz/blog/2017/01/05/symfony-console-from-scratch/).     
    <br>
    But nobody seems to write about **the greatest bottle neck of Console applications - static cancer**. Why is that? 
tweet: "A new post on my blog: ..."
---

## Current Status in PHP Console Applications

Your web application has entry point in `www/index.php`, where it loads the DI Container, gets `Application` class and calls `run()` on it (with explicit or implicit `Request`):

```php
require __DIR__ . '/vendor/autoload.php';

// Kernel or Configurator
$container = $kernel->getContainer();
$application = $container->get(Application::class);
$application->run(Request::createFromGlobals());
```

Console Applications (further as *CLI Apps*) have very similar entry point. Not in `index.php`, but usually in `bin/something` file. 
 
When we look at entry points of popular PHP Console Applications, like:

<br>

[PHP_CodeSniffer](https://github.com/squizlabs/PHP_CodeSniffer/blob/master/bin/phpcs)

```php
$runner = new PHP_CodeSniffer\Runner();
$runner->runPHPCS();
```

<br>

[PHP CS Fixer](https://github.com/FriendsOfPHP/PHP-CS-Fixer/blob/master/php-cs-fixer)

```php
$application = new PhpCsFixer\Console\Application();
$application->run();
```

<br>

[PHPStan](https://github.com/phpstan/phpstan/blob/master/bin/phpstan)

```php
$application = new Symfony\Component\Console\Application('PHPStan');
$application->add(new AnalyseCommand());
$application->run();
```

<br>

If we **mimic such approach in web apps**, how would our `www/index.php` look like?
 
```php
require __DIR__ . '/vendor/autoload.php';

$application = new Application;
$application->addController(new HomepageController);
$application->addController(new PostController);
$application->addController(new ContactController);
$application->addController(new ProducController);
// ...
$application->run();
```

How do you feel seeing such code? I feel a bit weird and [I don't get on well with statically](/blog/2018/04/26/how-i-got-into-static-trap-and-made-fool-of-myself/).


On the other hand, if we take the web app approach to cli apps:

```php
$container = $kernel->getContainer();

$application = $container->get(Application::class);
$application->run(new ArgInput);
```

### Why is That?

I wish I knew this answer :). In my opinion and experience with building cli apps, there might be few... 

   
### <em class="fa fa-fw fa-lg fa-check text-success"></em> Advantages

- CLI apps almost always starts as with simple plain PHP code:

    ```php
    $input = $argv[1];
    
    // 1st PSR-2 rule: replace tabs with spaces
    return str_replace('\t', ' ', $input);
    ```

    No container, no dependency injection, sometimes not even dependencies. Just see [the PHP-CS-Fixer v0.00001](https://gist.github.com/fabpot/3f25555dce956accd4dd).

    When the proof of concepts works, the application grows.

- It's easy, quick and simple.
- Who would use container right from the start for 1 command, right?


### <em class="fa fa-fw fa-lg fa-times text-danger"></em> Disadvantages

- If you start static, it's difficult to migrate
- the need of refactrogin shows much ealier beore it really happnes => legacy
- When the application grows, new classes are added... @todo



More code examples!



### smiecontainer

php cs fixer
phpstan


### <em class="fa fa-fw fa-lg fa-times text-danger"></em> Disadvantages
@todo
### <em class="fa fa-fw fa-lg fa-check text-success"></em> Advantages
@todo



### FrameworkBunlel

### <em class="fa fa-fw fa-lg fa-times text-danger"></em> Disadvantages
@todo
### <em class="fa fa-fw fa-lg fa-check text-success"></em> Advantages
@todo



## Mariage Boilerplate of Symfony console nad DI


### <em class="fa fa-fw fa-lg fa-times text-danger"></em> Disadvantages
@todo
### <em class="fa fa-fw fa-lg fa-check text-success"></em> Advantages
@todo



