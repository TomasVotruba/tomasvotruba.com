---
id: 59
title: "How to use Repository with Doctrine as Service in Symfony"
perex: |
    Dependency injection with autowiring is super easy [since Symfony 3.3](/blog/2017/05/07/how-to-refactor-to-new-dependency-injection-features-in-symfony-3-3/). Yet on my mentoring I still meet service locators.


    Mostly due to traditional registration of Doctrine repositories.


    The way out from *service locators* to *repository as service* was [described](https://matthiasnoback.nl/2014/05/inject-a-repository-instead-of-an-entity-manager) by many [before](https://medium.com/@adamquaile/composition-over-inheritance-in-doctrine-repositories-f6a53a026f60) and **now we put it into Symfony 3.3 context**.

tweet_image: "/assets/images/posts/2017/repository-as-service/autowire-fail.png"

updated_since: "February 2021"
updated_message: "Update YAML configs to PHP and PHP 7.4 syntax."
---

This post is follow up to [StackOverflow answer](https://stackoverflow.com/questions/38346281/symfony-3-outsourcing-controller-code-into-service-layer/38349271#38349271) to clarify key points and show the sweetest version yet.

The person who kicked me to do this post was [Václav Keberdle](http://www.ucinnejsiweb.cz) - *thank you for that*.

## Clean, Reusable, Independent and SOLID Goal

Our goal is to have clean code using *constructor injection*, *composition over inheritance* and *dependency inversion principles*.

With as simple registration as:

```php
// app/config/services.php
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

return function (ContainerConfigurator $containerConfigurator): void {
    $services = $containerConfigurator->services();

    $services->defaults()
        ->autowire();

    $services->load('App\\Repository\\', __DIR__ . '/../src/Repository');
};
```

Nothing more, nothing less. Today, we'll try to get there.

## How do we Register Repositories Now?

### 1. Entity Repository

```php
namespace App\Repository;

use App\Entity\Post;
use Doctrine\ORM\EntityRepository;

final class PostRepository extends EntityRepository
{
    /**
     * Our custom method
     * @return Post[]
     */
    public function findPostsByAuthor(int $authorId): array
    {
        return $this->findBy([
            'author' => $authorId
        ]);
    }
}
```

### Advantages

- It's easy and everybody does that ✅
- You can use prepared methods like [`findBy()`](https://github.com/doctrine/doctrine2/blob/2.5/lib/Doctrine/ORM/EntityRepository.php#L177), [`findOneBy()`](https://github.com/doctrine/doctrine2/blob/2.5/lib/Doctrine/ORM/EntityRepository.php#L192) right away ✅

### Disadvantages

- What if we try to register repository as a service? ❌

<img src="/assets/images/posts/2017/repository-as-service/autowire-fail.png" class="img-thumbnail mb-4">

- Why? Because parent constructor of `Doctrine\ORM\EntityRepository` is [missing `EntityManager` typehint](https://github.com/doctrine/doctrine2/blob/2.5/lib/Doctrine/ORM/EntityRepository.php#L64) (this is fixed in doctrine/orm 2.7+)

- **We can't get another dependency**, because parent constructor [requires `EntityManager` and `ClassMetadata` instances](https://github.com/doctrine/doctrine2/blob/2.5/lib/Doctrine/ORM/EntityRepository.php#L64) ❌

```php
namespace App\Repository;

use App\Sorter\PostSorter;
use Doctrine\ORM\EntityRepository;

final class PostRepository extends EntityRepository
{
    public function __construct(PostSorter $postSorter)
    {
        $this->postSorter = $postSorter;
    }
}
```

- Prepared methods like `findBy()` **don't have param and return type declarations** ❌

```php
// param should be "int", but whatever passes
$this->postRepository->find('someString');
```

- We don't know what object we get back ❌

```php
$post = $this->postRepository->find(1);
// some object?
$post->whatMethods()!
```

<br>

### 2. Entity

```php
namespace App\Entity;

use Doctrine\ORM\Entity;
use Doctrine\ORM\EntityRepository;

/**
 * @Entity(repositoryClass="App\Repository\PostRepository")
 */
final class Post
{
    ...
}
```

This is a code smell of circular dependency. Why should entity know about its repository?

### Static Service Locator Code Smell

Do you know why we need `repositoryClass="PostRepository"`? **It's form of static service locator** inside Doctrine:

```php
$this->entityManager->getRepository(Post::class);
```

It basically works like this:

- Find `Post` entity
- Find repository in `@Entity` annotation
- [Creates repository](https://github.com/doctrine/doctrine2/blob/2.5/lib/Doctrine/ORM/Repository/DefaultRepositoryFactory.php#L61)

Instead of **registration to Symfony container like any other service, here is uses logic coupled to annotation of specific class**. Just a reminder: [Occam's razor](https://www.google.cz/search?q=occams+razor&oq=occams+razor&aqs=chrome..69i57j0l5.2630j0j7&sourceid=chrome&ie=UTF-8).

<br>

### Advantages

- It's in documentation ✅

### Disadvantages

- What if I want to have `PostRedisRepository` for Redis-related operations and `PostFrontRepository` for reading-only? It is **not possible to have more repositories** for one entity ❌

- Would you have one Controller for every operation related to `Product` entity?

- **We're losing all features** of our framework's Dependency Injection container (events, autowiring, automated registration, logging etc.). ❌

<br>

### 3. Use in Controller

You have to use this [complicated service registration in YAML](https://matthiasnoback.nl/2014/05/inject-a-repository-instead-of-an-entity-manager/#factory-service):

```php
// app/config/services.php
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use function Symfony\Component\DependencyInjection\Loader\Configurator\service;

return function (ContainerConfigurator $containerConfigurator): void {
    $services = $containerConfigurator->services();

    $services->defaults()
        ->autowire();

    $services->set('app.post_repository', \Doctrine\ORM\EntityRepository::class)
        ->factory([service('@doctrine.orm.default_entity_manager'), 'getRepository'])
        ->args(['App\Entity\Post']);
};
```

...or just pass `EntityManager`.

```php
namespace App\Controller;

use App\Entity\Post;
use App\Repository\PostRepository;
use Doctrine\ORM\EntityManagerInterface;

final class PostController
{
    private PostRepository $postRepository;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->postRepository = $entityManager->getRepository(Post::class);
    }
}
```

### Advantages

- Again, status quo = that's how Doctrine and Symfony Documentation promotes it ✅

### Disadvantages

- IDE doesn't know it's `App\Repository\PostRepository`, so **we have add extra typehint for every single method** ❌

- Example above would work because there is typehinted property, but these would fail ❌

```php
$postRepository = $entityManager->getRepository(Post::class);
// object?
$postRepository->...?;

$post = $this->postRepository->find(1);
// object?
$post->...?;
```

- To enable autocomplete, we have to add them manually ❌

```php
/** @var App\Entity\Post $post */
$post = $this->postRepository->find(1);
$post->getName();
```

<br>

## Advantages Summary

- It's easy to copy-paste if already present in our code ✅
- It's spread in most of documentation, both in Doctrine and Symfony and in many posts about Doctrine ✅
- No brain, no gain ✅

## Disadvantages Summary

- We **cannot use autowiring** ❌
- We **cannot inject repository to other service just via constructor** ❌
- We have to **typehint manually** everything (IDE Plugins put aside) ❌
- **We have Doctrine in our Controller** - Controller should only delegate to model, without knowing what Database package is used. ❌
- To allow constructor injection, we have to prepare for much *config programming* ❌
- Thus **it's coupled to the framework you use and less reusable** ❌
- We cannot use multiple repository for single entity. **It naturally leads to huge repositories** ❌
- We cannot use constructor injection in repositories, which **can easily lead you to creating static helper classes** ❌
- Also, you directly depend on Doctrine's or Symfony's API, so if `find()` changes to `get()` in one `composer update`, your app is down ❌

## How to make this Better with Symfony 3.3+ and Composition?

It require few steps, but **all builds on single one change**. Have you heard about *composition over inheritance*?

```diff
 namespace App\Repository;

 use App\Entity\Post;
+use Doctrine\ORM\EntityManagerInterface;
 use Doctrine\ORM\EntityRepository;

-final class PostRepository extends EntityRepository
+final class PostRepository
 {
+    private EntityRepository $repository;
+
+    public function __construct(EntityManagerInterface $entityManager)
+    {
+        $this->repository = $entityManager->getRepository(Post::class);
+    }
 }
```

**Update entity that is now independent** on specific repository:

```diff
 <?php declare(strict_types=1);

 namespace App\Entity;

 use Doctrine\ORM\Entity;

 /**
- * @Entity(repositoryClass="App\Repository\PostRepository")
+ * @Entity
  */
 final class Post
 {
     ...
 }
```

Without this, you'd get a segfault error due to circular reference.

That's all! Now you can program the way *which is used in the rest of your application*:

- *class*,
- *service*
- and *constructor injection*


**And how it influenced our 4 steps?**

<br>

### 1. Entity Repository

```php
<?php declare(strict_types=1);

namespace App\Repository;

use App\Entity\Post;
use App\Sorter\PostSorter;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;

final class PostRepository
{
    private EntityRepository $repository;

    private PostSorter $postSorter;

    public function __construct(EntityManagerInterface $entityManager, PostSorter $postSorter)
    {
        $this->repository = $entityManager->getRepository(Post::class);
        $this->postSorter = $postSorter;
    }

    public function find(int $id): ?Post
    {
        return $this->repository->find($id);
    }
}
```

### Advantages

- Everything is **strictly typehinted**, **no more frustration from missing autocompletion** ✅
- **Constructor injection works** like you expect it to ✅
- You can get another dependency if you like ✅

<br>

### 2. Entity

```php
namespace App\Entity;

use Doctrine\ORM\Entity;

/**
 * @Entity
 */
class Post
{
    ...
}
```

###  Advantages

- Clean and standalone object ✅
- No service locators smells ✅
- **Allows multiple repositories per entity** ✅

```php
// app/config/services.php
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

return function (ContainerConfigurator $containerConfigurator): void {
    $services = $containerConfigurator->services();

    $services->defaults()
        ->autowire();

    $services->set(App\Repository\ProductRepository::class);
    $services->set(App\Repository\ProductRedisRepository::class);
    $services->set(App\Repository\ProductBenchmarkRepository::class);
};
```

<br>

### 3. Use in Controller

```php
namespace App\Controller;

use App\Repository\PostRepository;

final class PostController
{
    private PostRepository $postRepository;

    public function __construct(PostRepository $postRepository)
    {
        $this->postRepository = $postRepository;
    }
}
```

### Advantages

- **IDE knows** the type and autocomplete 100% works ✅
- PHPStan and Rector knows types too ✅
- There is no sign of Doctrine, the code is cleanly decoupled ✅
- **The code easier to maintain and extend, thanks to composition over inheritance** ✅
- The possibility to decouple to [local packages](/blog/2017/02/07/how-to-decouple-monolith-like-a-boss-with-composer-local-packages/) is now opened ✅

<br>

### 4. Registration `services.php`

We have a new extra step - registration of services in application container:

```php
// app/config/services.php
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

return function (ContainerConfigurator $containerConfigurator): void {
    $services = $containerConfigurator->services();

    $services->defaults()
        ->autowire();

    $services->load('App\\Repository\\', __DIR__ . '/../src/Repository');
};
```

<br>

All we needed is to apply **composition over inheritance** pattern.

## Quality Test: How to Add new Repository?

The main goal of all this was to make work with repositories typehinted, safe and reliable for you to use and easy to extend. **It also minimized space for error**, because **strict types and constructor injection now validates** much of your code for you.

The answer is now simple: **just create repository in `App\Repository`**.

Try the same example with your current approach and let me know in the comments.

<br>

Happy coding!
