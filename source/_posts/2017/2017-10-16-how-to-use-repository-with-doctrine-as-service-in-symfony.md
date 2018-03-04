---
id: 59
title: "How to use Repository with Doctrine as Service in Symfony"
perex: '''
    Dependency injection with autowiring is super easy [since Symfony 3.3](/blog/2017/05/07/how-to-refactor-to-new-dependency-injection-features-in-symfony-3-3/). Yet on my mentoring I still meet service locators.
    <br><br>
    Mostly due to traditional registration of Doctrine repositories.
    <br><br>
    The way out from *service locators* to *repository as service* was [described](https://matthiasnoback.nl/2014/05/inject-a-repository-instead-of-an-entity-manager/) by many [before](https://medium.com/@adamquaile/composition-over-inheritance-in-doctrine-repositories-f6a53a026f60) and **now we put it into Symfony 3.3 context**.
'''
tweet: "How to use repository with #dotrine in #symfony as #autowired service? #di"
tweet_image: "/assets/images/posts/2017/repository-as-service/autowire-fail.png"
---

This post is follow up to [StackOverflow answer](https://stackoverflow.com/questions/38346281/symfony-3-outsourcing-controller-code-into-service-layer/38349271#38349271) to clarify key points and show the sweetest version yet.

The person who kicked me to do this post was [Václav Keberdle](http://www.ucinnejsiweb.cz) - *thank you for that*.

## Clean, Reusable, Independent and SOLID Goal

**Our goal** is to have clean code using *constructor injection*, *composition over inheritance* and *dependency inversion principles*.

With as simple registration as:

```yaml
# app/config/services.yml

services:
    _defaults:
        autowire: true

    App\Repository\:
        resource: ../Repository
```

**IDE plugins an other workarounds put aside**, because this code can be written just with typehints.



## How do we Register Repositories Now


### 1. Entity Repository

```php
<?php declare(strict_types=1);

namespace App\Repository;

use App\Entity\Post;
use Doctrine\ORM\EntityRepository;

final class PostRepository extends EntityRepository
{
    /**
     * Our custom method
     *
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


### <em class="fa fa-fw fa-lg fa-check text-success"></em> Advantages

It's easy and everybody does that.

You can use prepared methods like [`findBy()`](https://github.com/doctrine/doctrine2/blob/2.5/lib/Doctrine/ORM/EntityRepository.php#L177), [`findOneBy()`](https://github.com/doctrine/doctrine2/blob/2.5/lib/Doctrine/ORM/EntityRepository.php#L192) right away.


### <em class="fa fa-fw fa-lg fa-times text-danger"></em> Disadvantages

If we try to register repository as a service, we get this error:
    <img src="/assets/images/posts/2017/repository-as-service/autowire-fail.png" class="img-thumbnail mb-4">

Why? Because parent constructor of `Doctrine\ORM\EntityRepository` is [missing `EntityManager` typehint](https://github.com/doctrine/doctrine2/blob/2.5/lib/Doctrine/ORM/EntityRepository.php#L64)

Also **we can't get another dependency**, like `PostSorter` that would manage sorting post in any way.


```php
<?php declare(strict_types=1);

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

Because parent constructor [requires `EntityManager` and `ClassMetadata` instances](https://github.com/doctrine/doctrine2/blob/2.5/lib/Doctrine/ORM/EntityRepository.php#L64).

Those prepared methods like `findBy()` **don't have argument nor return typehints**, so this would pass:

```php
$this->postRepository->find('someString');
```

And we don't know what object we get back:

```php
$post = $this->postRepository->find(1);
$post->whatMethods()!
```

<br>

### 2. Entity

```php
<?php declare(strict_types=1);

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

This reminds me of circular dependency and [active record pattern from Doctrine 4](http://www.doctrine-project.org/2017/04/01/announcing-doctrine-4.html). Why should entity know about its repository?

Do you know why we need `repositoryClass="PostRepository"`?

**It's form of service locator**, that basically works like this:

```php
$this->entityManager->getRepository(Post::class);
```

- Find `Post` entity
- Find repository in `@Entity` annotation
- [Creates repository](https://github.com/doctrine/doctrine2/blob/2.5/lib/Doctrine/ORM/Repository/DefaultRepositoryFactory.php#L61)

Instead of **registration to Symfony container like any other service, here is uses logic coupled to annotation of specific class**. Just a reminder: [Occam's razor](https://www.google.cz/search?q=occams+razor&oq=occams+razor&aqs=chrome..69i57j0l5.2630j0j7&sourceid=chrome&ie=UTF-8).



### <em class="fa fa-fw fa-lg fa-check text-success"></em> Advantages


It's in documentation.


### <em class="fa fa-fw fa-lg fa-times text-danger"></em> Disadvantages


It is very complicated to have more repositories for one entity. What if I want to have `PostRedisRepository` for Redis-related operations and `PostFrontRepository` for reading-only?

**We're loosing all features** of our framework's Dependency Injection container (events, collections, autowiring, automated registration, logging etc.).

<br>

### 3. Use in Controller

You have to use this [complicated service registration in YAML](https://matthiasnoback.nl/2014/05/inject-a-repository-instead-of-an-entity-manager/#factory-service):

```yaml
services:
    app.post_repository:
        class: Doctrine\ORM\EntityRepository
        factory: ['@doctrine.orm.default_entity_manager', getRepository]
        arguments:
            - App\Entity\Post
```

...or just pass `EntityManager`.


```php
<?php declare(strict_types=1);

namespace App\Controller;

use App\Entity\Post;
use App\Repository\PostRepository;
use Doctrine\ORM\EntityManager;

final class PostController
{
    /**
     * @var PostRepository
     */
    private $postRepository;

    public function __construct(EntityManager $entityManager)
    {
        $this->postRepository = $entityManager->getRepository(Post::class);
    }
}
```


### <em class="fa fa-fw fa-lg fa-check text-success"></em> Advantages

Again, status quo.


### <em class="fa fa-fw fa-lg fa-times text-danger"></em> Disadvantages

IDE doesn't know it's `App\Repository\PostRepository`, so **we have add extra typehint** (so [boring](https://www.boringcompany.com/) work). Example above would work because there is typehinted property , but this would fail:

```php
$postRepository = $entityManager->getRepository(Post::class);
$postRepository->help()?;
```

Or this:

```php
$post = $this->postRepository->find(1);
$post->help()?;
```

To enable autocomplete, we have to add them manually:

```php
/** @var App\Entity\Post $post */
$post = $this->postRepository->find(1);
$post->getName();
```

**This annotation helper should never be in *your* code, except this case**:

```php
/** @var SomeService $someService */
$someService = $container->get(SomeService::class);
```

<br>

### 4. Registration `services.yml`


None. Repositories are created by Doctrine.


<br>


## <em class="fa fa-fw fa-lg fa-check text-success"></em> Advantages Summary

It's easy to copy-paste if already present in our code.

It's spread in most of documentation, both in Doctrine and Symfony and in many posts about Doctrine.

No brain, no gain.


## <em class="fa fa-fw fa-lg fa-times text-danger"></em> Disadvantages Summary

We **cannot use autowiring**.

We **cannot inject repository to other service just via constructor**.

We have to **typehint manually** everything (IDE Plugins put aside).

**We have Doctrine in our Controller** - Controller should only delegate to model, without knowing what Database package is used.

To allow constructor injection, we have to prepare for much *config programming*.

Thus **it's coupled to the framework you use and less reusable**.

We cannot use multiple repository for single entity. **It naturally leads to huge repositories**.

We cannot use constructor injection in repositories, which **can easily lead you to creating static helper classes**.

Also, you directly depend on Doctrine's API, so if `find()` changes to `get()` in one `composer update`, your app is down.



## How to Make This Better with Symfony 3.3?

It require few steps, but **all builds on single one change**. Have you heard about *composition over inheritance*?

**Instead of *inheritance*...**

```php
<?php declare(strict_types=1);

namespace App\Repository;

use App\Entity\Post;
use Doctrine\ORM\EntityRepository;

final class PostRepository extends EntityRepository
{
}
```

**...we use *composition*:**


```php
<?php declare(strict_types=1);

namespace App\Repository;

use App\Entity\Post;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;

final class PostRepository
{
    /**
     * @var EntityRepository
     */
    private $repository;

    public function __construct(EntityManager $entityManager)
    {
        $this->repository = $entityManager->getRepository(Post::class);
    }
}
```

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
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;

final class PostRepository
{
    /**
     * @var EntityRepository
     */
    private $repository;

    /**
     * @var PostSorter
     */
    private $postSorter;

    public function __construct(EntityManager $entityManager, PostSorter $postSorter)
    {
        $this->repository = $entityManager->getRepository(Post::class);
        $this->postSorter = $postSorter;
    }

    public function find(int $id): Post
    {
        return $this->repository->find($id);
    }
}
```


### <em class="fa fa-fw fa-lg fa-check text-success"></em> Advantages


Everything is **strictly typehinted**, **no more frustration from missing autocompletion**.

**Constructor injection works** like you expect it to.

You can get another dependency if you like.

<br>

### 2. Entity

```php
<?php declare(strict_types=1);

namespace App\Entity;

use Doctrine\ORM\Entity;
use Doctrine\ORM\EntityRepository;

class Post
{
    ...
}
```

<br>


### <em class="fa fa-fw fa-lg fa-check text-success"></em> Advantages


Clean and standalone object.

No service locators smells.

**Allows multiple repositories per entity**:

```yaml
- App\Repository\ProductRepository
- App\Repository\ProductRedisRepository
- App\Repository\ProductBenchmarkRepository
```

<br>

### 3. Use in Controller

```php
<?php declare(strict_types=1);

namespace App\Controller;

use App\Repository\PostRepository;

final class PostController
{
    /**
     * @var PostRepository
     */
    private $postRepository;

    public function __construct(PostRepository $postRepository)
    {
        $this->postRepository = $postRepository;
    }
}
```


### <em class="fa fa-fw fa-lg fa-check text-success"></em> Advantages


**IDE knows the type and autocomplete 100% works.**

There is no sign of Doctrine.

**Easier to maintain and extend.**

Also space to decoupling to [local packages](/blog/2017/02/07/how-to-decouple-monolith-like-a-boss-with-composer-local-packages/) is now opened.


<br>


### 4. Registration `services.yml`


Final 3rd appearance for it's great success:

```yaml
# app/config/services.yml

services:
    _defaults:
        autowire: true

    App\Repository\:
        resource: ../Repository
```

<br>

All we needed is to apply *composition over inheritance* pattern in this specific case.

If you don't use Doctrine or you already do this approach, **try to think where else you `extends` 3rd party package instead of `__construct`**.



## How to add new repository?

The main goal of all this was to make work with repositories typehinted, safe and reliable for you tu use and easy to extends.

**It also minimized space for error**, because **strict types and constructor injection now validates** much of your code for you.

The answer is now simple: **just create repository it in `App\Repository`**.


Try the same example with your current approach and let me know in the comments.



Happy coding!
