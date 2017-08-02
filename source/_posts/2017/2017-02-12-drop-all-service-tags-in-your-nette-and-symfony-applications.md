---
id: 27
layout: post
title: "Drop all Service Tags in Your Nette and Symfony Applications"
perex: '''
    What is tagging for? Why we should get rid of it?
    Today I will show you, how to do it gradually without breaking the application.
'''

updated: true
updated_since: "May 2017"
updated_message: '''
    Updated with <strong>new Symfony 3.3 features <a href="http://symfony.com/blog/new-in-symfony-3-3-simpler-service-configuration">class based service naming, <code>_instanceof</code> configuration</a>
    and <a href="http://symfony.com/blog/new-in-symfony-3-3-service-autoconfiguration">autoconfiguration</a></strong> (plus PHP 7).
    <br><br>
    It does pretty much the same, so package ServiceDefinitionDecorator for Symfony was deprecated. 
    <br><br>    
    It is still available <a href="https://github.com/DeprecatedPackages/ServiceDefinitionDecorator">here for inspiration</a> though.
'''
---

This post is follow up to *[How to Avoid Inject Thanks to Decorator feature in Nette](/blog/2016/12/24/how-to-avoid-inject-thanks-to-decorator-feature-in-nette/)*. Go read it if you missed it.


## What is Tagging For?

```yaml
# app/config/services.neon / app/config/services.yml

services:
    simple_console_command:
        class: "App\Command\SimpleConsoleCommand"
        tags:
            - "console.command"
```

It is a method to mark services, so Dependency Injection Container could find them easily.

With that, we can add all Commands to Console Application during Dependency Injection Container compilation.

```php
$commands = $this->container->findByTag('console.command');
$application->addMethod('addCommands', [$commands]);
```

## Bare Tagging is Duplicated Information

But is it really needed?

Without tags, you would probably write something like this:

```php
$commands = $this->container->findAllByType(Command::class);
$application->addMethod('addCommands', [$commands]);
```

**But it's here, because of historical reasons**. Both Nette and Symfony ecosystem support it, so many packages adopted
it without thinking twice.



### The Only Place To Consider Using Tags: Metadata

If you need to setup service priority registration or **any information, that you can't put inside the class
itself**, tagging is the only way:

```yaml
# app/config/services.neon / app/config/services.yml

services:
    simple_console_command:
    class: "App\Command\SimpleConsoleCommand"
    tags:
        name: "console.command"
        priority: 20
```

It's often used **to duplicate information about a class or interface this service extends or implements**. `Symfony\Component\Console\Command\Command` in this case.


### How to get rid of them? Decorator to the rescue!

When I see this pollution in the code, I try to explain there is no added value. Till now, there was only solution in
Nette and Symfony application were doomed to use this anti-pattern.

Now there is Decorator in Symfony as well. Let's see.


## Get Rid of Tagging in Nette

In you Nette Application, you probably already use

```yaml
# app/config/config.neon

services:
    -
        class: App\Console\FirstCommand
        tags: [kdyby.console.command]
    -
        class: App\Console\SecondCommand
        tags: [kdyby.console.command]
    -
        class: App\Console\ThirdCommand
        tags: [kdyby.console.command]
    -
        class: App\EventSubscriber\FirstEventSubscriber
        tags: [kdyby.subscriber]
    -
        class: App\EventSubscriber\SecondEventSubscriber
        tags: [kdyby.subscriber]
    -
        class: App\EventSubscriber\ThirdEventSubscriber
        tags: [kdyby.subscriber]
```

So much reading, huh? Imagine 50 more of these.

This is exactly the place to use [Nette Decorator feature](/blog/2016/12/24/how-to-avoid-inject-thanks-to-decorator-feature-in-nette/).

```yaml
# app/config/config.neon

services:
    - App\Console\FirstCommand
    - App\Console\SecondCommand
    - App\Console\ThirdCommand
    - App\EventSubscriber\FirstEventSubscriber
    - App\EventSubscriber\SecondEventSubscriber
    - App\EventSubscriber\ThirdEventSubscriber

decorator:
    Symfony\Component\Console\Command\Command:
        tags: [kdyby.console.command]
    Symfony\Component\EventDispatcher\EventSubscriberInterface:
        tags: [kdyby.subscriber]
```

The more services you have, the more cleaner and readable code this approach brings.


### Minitip

If you don't like the decorator and don't like to one service take 3 lines of config instead of 1, you can use this
shortage:

```yaml
# app/config/config.neon

services:
    - { class: App\Console\FirstCommand, tags: [kdyby.console.command] }
```

This is what I did, before I used Decorator and before I dropped tags from my coding habbits.


## Get Rid of Tagging in Symfony

Symfony [has over 40 tags](http://symfony.com/doc/current/reference/dic_tags.html) that are coupled to many internal parts. This is barely half of it:

<img src="/assets/images/posts/2017/decorator/symfony-tags-half.png" class="thumbnail" alt="Tag list">

If we use the same setup as we used in Nette above, in Symfony it would look like this:

```yaml
# app/config/config.yml

services:
    App\Console\FirstCommand: 
        tags:
            - { name: console.command }
    App\Console\SecondCommand:
        tags:
            - { name: console.command }
    App\Console\ThirdCommand:
        tags:
            - { name: console.command }
    App\EventSubscriber\FirstEventSubscriber:
        tags:
            - { name: kernel.event_subscriber }
    App\EventSubscriber\SecondEventSubscriber:
        tags:
            - { name: kernel.event_subscriber }
    App\EventSubscriber\ThirdEventSubscriber:
        tags:
            - { name: kernel.event_subscriber }
```

I want to quit this project already... but wait!

### You can use new [autoconfigure](http://symfony.com/blog/new-in-symfony-3-3-service-autoconfiguration) since Symfony 3.3

```yaml
# app/config/config.yml

services:
    _defaults:
        autowire: true # recommended
        autoconfigure: true

    App\Console\FirstCommand: ~
    App\Console\SecondCommand: ~
    App\Console\ThirdCommand: ~
    App\EventSubscriber\FirstEventSubscriber: ~
    App\EventSubscriber\SecondEventSubscriber: ~
    App\EventSubscriber\ThirdEventSubscriber: ~
```

That's it. Pretty cool, huh?


## How do you Approach This?

Again, this is my point of view on making things easy, KISS and DRY.

**How do you approach this duplication?** What do you like about it apart "it's in the docs" or "everybody does that"?
