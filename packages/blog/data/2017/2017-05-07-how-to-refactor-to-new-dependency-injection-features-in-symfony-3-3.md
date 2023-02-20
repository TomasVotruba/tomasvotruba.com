---
id: 38
title: "How to refactor to new Dependency Injection features in Symfony 3.3"
perex: |
    This May will be released Symfony 3.3 with many DependencyInjection improvements.
    Each of them is quite nice, <strong>but combined together - they are huge jump</strong> compare to what we have now.


    Today I will show you what code can you drop and how to migrate it.
tweet: "Learn new  🐘 hack: How to refactor to new #symfony 3.3 DI features? #examples #php #yaml #tool"
---

## What is New?

Symfony 3.3+ brings new that will completely **change they we register services**:

- [`autoconfigure`](https://symfony.com/blog/new-in-symfony-3-3-service-autoconfiguration)
- [`_defaults` and `instanceof`](https://symfony.com/blog/new-in-symfony-3-3-simpler-service-configuration)
- [named services → class services](https://symfony.com/blog/new-in-symfony-3-3-optional-class-for-named-services)
- [PSR-4-based service autodiscovery](https://github.com/symfony/symfony/pull/21289)

You can click read post/PR in detail, but you take **this shortcut to learn them**...

<br>

<blockquote class="blockquote text-center">
    "A full code example is worth ten thousand words of explanation."
    <footer class="blockquote-footer">Stephen P. Thomas</footer>
</blockquote>

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

    first_command:
        class: App\Command\FirstCommand
        tags:
            - { name: console.command }
    second_command:
        class: App\Command\SecondCommand
        tags:
            - { name: console.command }
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

    first_command:
        class: App\Command\FirstCommand
    second_command:
        class: App\Command\SecondCommand
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

    App\Command\FirstCommand: ~
    App\Command\SecondCommand: ~
```

### 4. Use PSR-4 based service autodiscovery and registration

```yaml
# app/config/services.yml
services:
    _defaults:
        autowire: true
        autoconfigure: true

    App\: # no more manual registration of similar groups of services
        resource: '../'

    App\Repository\FirstRepository:
        calls:
            - ["setEntityManager", ["@entity_manager"]]
    App\Repository\SecondRepository:
        calls:
            - ["setEntityManager", ["@entity_manager"]]
```


### 5. Use `_instanceof`...

```yaml
# app/config/services.yml
services:
    _defaults:
        autowire: true
        autoconfigure: true

    App\:
        resource: '../'

    _instanceof: # clean and explicit dependency injection to abstract services
        App\Repository\AbstractRepository:
            calls:
                - ["setEntityManager", ["@entity_manager"]]
```

### 5. ...or Setter Injection

You can even remove the `_instanceof` with setter injection. First, modify the abstract repository to add the `@required` annotation to `setEntityManager`:

```php
<?php

namespace App\Repository;

use Doctrine\ORM\EntityManagerInterface;

abstract class AbstractRepository
{
    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

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
        resource: '../'
```

You're awesome! Now you're using all the shiny new Symfony 3.3 Dependency Injection features.

<br>

**What is your favorite change?**
