---
id: 38
title: "How to refactor to new Dependency Injection features in Symfony 3.3"
perex: |
    This May will be released Symfony 3.3 with many DependencyInjection improvements.
    Each of them is quite nice, <strong>but combined together - they are huge jump</strong> compare to what we have now.
    <br><br>
    Today I will show you what code can you drop and how to migrate it.
tweet: "How to refactor to new #symfony 3.3 DI features? #examples #php"
---

## What is new?

If you follow [Symfony blog](https://symfony.com/blog/), you will already know about:

- **[autoconfigure](https://symfony.com/blog/new-in-symfony-3-3-service-autoconfiguration)**
- **[`_defaults`, `instanceof` and simpler service registration](https://symfony.com/blog/new-in-symfony-3-3-simpler-service-configuration)**
- **[from named services to class services](https://symfony.com/blog/new-in-symfony-3-3-optional-class-for-named-services)**
- [`autowire()` and name tag shortcuts](https://symfony.com/blog/new-in-symfony-3-3-added-new-shortcut-methods)

And there are some more, that **were not mentioned on the blog**:

- **[PSR-4-based services discovery and registration](https://github.com/symfony/symfony/pull/21289)**
    - Using Nette? Check tools [by F3l1x](https://github.com/contributte/di) and [Pavel Janda](https://github.com/ublaboo/directory-register)
- [action method injection](https://github.com/symfony/symfony/pull/21771) - known [from Laravel](https://mattstauffer.co/blog/laravel-5.0-method-injection#solution)
- [abstract controller](https://github.com/symfony/symfony/pull/22157)

**Those bold** will be important part of every Symfony 3.3+ application. You can click on them to get to post or PR, where they're explained in
more detailed way. But I think there is **quicker way to learn them**...

<br>

### "A full code example is worth ten thousand words of explanation."

*Stephen P. Thomas*

<br>


## Refactor Service Config in 5 Steps

This is service config in Application in Symfony 3.2 or lower.

We apply all features we can and I always add a small `# comment` to the code with explanation.

```yaml
# app/config/services.yml
services:
    some_service:
        class: App\SomeService
        autowire: true

    some_controller:
        class: App\Controller\SomeController
        autowire: true

    first_repository:
        class: App\Repository\FirstRepository
        autowire: true
        calls:
            - ["setEntityManager", ["@entity_manager"]]
    second_repository:
        class: App\Repository\SecondRepository
        autowire: true
        calls:
            - ["setEntityManager", ["@entity_manager"]]

    # console commands
    first_command:
        class: App\Command\FirstCommand
        autowire: true
        tags:
            - { name: console.command }
    second_command:
        class: App\Command\SecondCommand
        autowire: true
        tags:
            - { name: console.command }

    # event subscribers
    first_subscriber:
        class: App\EventSubscriber\FirstSubscriber
        autowire: true
        tags:
            - { name: kernel.event_subscriber }
    second_subscriber:
        class: App\EventSubscriber\SecondSubscriber
        autowire: true
        tags:
            - { name: kernel.event_subscriber }
```

### 1. Let's add `_defaults`

```yaml
# app/config/services.yml
services:
    _defaults:
        autowire: true # all services in this config are now autowired

    some_service:
        class: App\SomeService

    some_controller:
        class: App\Controller\SomeController

    first_repository:
        class: App\Repository\FirstRepository
        calls:
            - ["setEntityManager", ["@entity_manager"]]
    second_repository:
        class: App\Repository\SecondRepository
        calls:
            - ["setEntityManager", ["@entity_manager"]]

    # console commands
    first_command:
        class: App\Command\FirstCommand
        tags:
            - { name: console.command }
    second_command:
        class: App\Command\SecondCommand
        tags:
            - { name: console.command }

    # event subscribers
    first_subscriber:
        class: App\EventSubscriber\FirstSubscriber
        tags:
            - { name: kernel.event_subscriber }
    second_subscriber:
        class: App\EventSubscriber\SecondSubscriber
        tags:
            - { name: kernel.event_subscriber }
```

### 2. Use autoconfigure

```yaml
# app/config/services.yml
services:
    _defaults:
        autowire: true
        autoconfigure: true # all Symfony native tags are now added automatically

    some_service:
        class: App\SomeService

    some_controller:
        class: App\Controller\SomeController

    first_repository:
        class: App\Repository\FirstRepository
        calls:
            - ["setEntityManager", ["@entity_manager"]]
    second_repository:
        class: App\Repository\SecondRepository
        calls:
            - ["setEntityManager", ["@entity_manager"]]

    # console commands
    first_command:
        class: App\Command\FirstCommand
    second_command:
        class: App\Command\SecondCommand

    # event subscribers
    first_subscriber:
        class: App\EventSubscriber\FirstSubscriber
    second_subscriber:
        class: App\EventSubscriber\SecondSubscriber
```

### 3. Use Class-Named Services

```yaml
# app/config/services.yml
services:
    _defaults:
        autowire: true
        autoconfigure: true

    App\SomeService: ~ # no more thinking about creative and unique service name

    App\Controller\SomeController: ~

    App\Repository\FirstRepository:
        calls:
            - ["setEntityManager", ["@entity_manager"]]
    App\Repository\SecondRepository:
        calls:
            - ["setEntityManager", ["@entity_manager"]]

    # console commands
    App\Command\FirstCommand: ~
    App\Command\SecondCommand: ~

    # event subscribers
    App\EventSubscriber\FirstSubscriber: ~
    App\EventSubscriber\SecondSubscriber: ~
```

### 4. Use PSR-4 based service autodiscovery and registration

```yaml
# app/config/services.yml
services:
    _defaults:
        autowire: true
        autoconfigure: true

    App\: # no more manual registration of similar groups of services
        resource: ../{Controller,Command,Subscriber}

    App\SomeService: ~

    App\Repository\FirstRepository:
        calls:
            - ["setEntityManager", ["@entity_manager"]]
    App\Repository\SecondRepository:
        calls:
            - ["setEntityManager", ["@entity_manager"]]
```


### 5. Use `_instanceof`

```yaml
# app/config/services.yml
services:
    _defaults:
        autowire: true
        autoconfigure: true

    _instanceof: # clean and explicit dependency injection to abstract services
        App\Repository\AbstractRepository:
            calls:
                - ["setEntityManager", ["@entity_manager"]]

    App\:
        resource: ../{Controller,Command,Subscriber,Repository}

    App\SomeService: ~
```

### 6. Use Setter Injection

You can even remove the `_instanceof` with setter injection.

First, modify the abstract repository to add the `@required` annotation to `setEntityManager`:

```php
namespace App\Repository;

use Doctrine\ORM\EntityManagerInterface;

abstract class AbstractRepository
{
    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    // ...

    /**
     * @required
     */
    public function setEntityManager(EntityManagerInterface $entityManager): void
    {
        $this->entityManager = $entityManager;
    }

    // ...
}
```

Now, remove the `_instanceof` in your `services.yml`:

```yaml
# app/config/services.yml
services:
    _defaults:
        autowire: true
        autoconfigure: true

    App\:
        resource: ../{Controller,Command,Subscriber,Repository}

    App\SomeService: ~
```

That is awesome, isn't it?

Now you are using all the shiny new Symfony 3.3 Dependency Injection features.

### More in Symfony Flex

If you need more examples, check out [`manifest.json` file for FrameworkBundle by Symfony Flex](https://github.com/symfony/recipes/blob/master/symfony/framework-bundle/3.3/config/services.yaml).

**Do you like it? Do you plan to skip some of those and stick to the current syntax?**
