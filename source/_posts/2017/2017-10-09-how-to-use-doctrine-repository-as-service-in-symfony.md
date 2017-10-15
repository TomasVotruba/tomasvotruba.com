---
id: 59
layout: post
title: "How to use Doctrine Repository as Service in Symfony"
perex: '''
    Dependency injection with autowiring is super easy [since Symfony 3.3](@todo). Yet on my  mentorings I still meet service locators.
    <br><br>
    Mostly due to traditional registration of Doctrine repositories.
    <br><br>
    The way out from *service locators* to *repository as service* was [described](https://matthiasnoback.nl/2014/05/inject-a-repository-instead-of-an-entity-manager/) by many [before](https://medium.com/@adamquaile/composition-over-inheritance-in-doctrine-repositories-f6a53a026f60) and **now we put it into Symfony 3.3 context**.  
'''
tweet_todo: "..."
---

This post is follow up to [my StackOverflow answer](https://stackoverflow.com/questions/38346281/symfony-3-outsourcing-controller-code-into-service-layer/38349271#38349271) to clarify key points and show the most simple version yet.

The person who kicked me to do this post was Vašek (@todo surname+link)from Příbram, *thank you for that*.


**tl;dr;** Our goal is to have clean code using constructor injection and composition over inheritance:

```yaml
# app/config/services.yml

services:
    _defaults:
        autowire: true

    App\Repository\:
        source: ../Repository
```




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


:+1:

it's easy
everybody does that
default methods like findBy(), findOneBy() are at your service

:-1:

- if we register is as a service, we get this error (@todo-screee)
- why? because parent constructor is missing typehint
- also we can't get another dependency, like `PostSorter`, well we can but instead of
- 
 
 ...
 
- we need to do this


    ...


- default methods of EntityRepository don't have argument typehints, so this would pass `$this->postRepository->get('someString');` even if it shouldn't.


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

This reminds me of circular dependency and [active record pattern](@todo april fool post on Doctrine). Why should entity know about its repository?

Do you know why we need `repositoryClass="PostRepository"`?

Yep, it's service locator. It basically works like this:

    ```yaml
    $this->entityManager->getRepository(Post::class);
    ```
    
    - Find `Post` entity
    - Find repository in `@Entity` annotation 
    - Create repository (@link-to-code) 
    - and return it


Instead of registration to Symfony container like any other service, the Doctrine has its own with on logic coupled to annotation. [Pharetto reminder]()!


:+1:

- everybody does that 
- it's in documentation


:-1:

- It is complicated to have more repositories for one entity. What if I want to have `PostRedisRepository` for Redis-related operations and `PostFrontRepository` for reading-only? 


### 3. Use in Controller

You have to use this [complicated service registration in YAML](https://matthiasnoback.nl/2014/05/inject-a-repository-instead-of-an-entity-manager/#factory-service) or just pass `EntityManager`.

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

:+1:

didnt! find any

:-1:

-  IDE doesn't know it's `App\Repository\PostRepository`, so we have to typehint extra everywhere we need that. Example above would work because there is typehinted property, but this would fail:
    
    ```php
    $postRepository = $entityManager->getRepository(Post::class);
    $postRepository->getPostsByAuthor(); // method not found, or was it found*()?
    ```

- When we use Doctrine's EntityRepository method, like `get($id)`, we will face same problem:
 
    ```php
    $post = $this->postRepository->get(1);
    $post->getName(); # or was it "$post->getTitle()";
    ```
    
To enable typehtins, we have to add them manually.

    ```php
    /** @var App\Entity\Post $post */
    $post = $this->postRepository->get(1);
    $post->getName(); # or was it "$post->getTitle()";
    ```

This annotation helper should never be in *your* code, except this case:
 
    ```php
    /** @var SomeService $someService */
    $someService = $container->get(SomeService::class);
    ```


### 4. Registration `services.yml`


None. Everything is handled by Doctrine Repository locator.



### Advantages Summary

- Everybody does that.
- It's easy to copy-paste if already present in our code.
- It's spread in most of documentation, both in Doctrine and Symfony and in many posts about Doctrine.
- No brain, no gain.


### Problems Summary

- We cannot use autowiring.
- We cannot inject repository to other service just via constructor.
- We have to **typehint manually** everything (plugins an other workarounds put asside).
- **We have Doctrine in our Controller** - Controller should only delegate to model, without knowing what Database package is used.  
- To allow constructor injection, we have to prepare for *config programming*.
- Thus it's coupled to the framework you use and less reusable.
- We cannot use multiple repository for single entity. It naturally leads to huge repositories.
- We cannot use construtor injection in repositoreis, which can easily lead you to creating static helper classes.
- Also, you directly depend on Doctrine's API, so if `get()` changes to `find()` in one `composer update`, your app is down.
 


#1## How to Make This Better with Symfony 3.3?

It require few steps, but all leads to single one SOLID change. Have you heard about *composition over inheritance*?

Instead of *inheritance*...

```php
<?php declare(strict_types=1);

namespace App\Repository;

use App\Entity\Post;
use Doctrine\ORM\EntityRepository; 

final class PostRepository extends EntityRepository
{
}
```

...we will use *composition*:


```php
<?php declare(strict_types=1);

namespace App\Repository;

use App\Entity\Post;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;

final class PostRepository
{
    /**
     * @var Repository  
     */
    private $repository;
    
    public function __construct(EntityManager $entityManager)
    {
        $this->repository = $entityManager->getRepository(Post::class);
    }
}
```

That's all! Now you can program *the way you know the best* and *which is used in the rest of your application*: 

- *class*,
- *service*
- and *constructor injection* 


And how it influenced our 4 steps?








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
     * @var Repository  
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
    
    public function get(int $id): Post
    {
        $this->repository->get($id);
    }
}
```


:+1:

- everything is stricly typehinted, no more frustration from missing autocompletion
- constructor injetion works like you expect it too
- you can get another dependency if you like


### 2. Entity

```php
<?php declare(strict_types=1);

namespace App\Entity;

use Doctrine\ORM\Entity;
use Doctrine\ORM\EntityRepository;

final class Post
{
    ...
}
```

:+1:

- Clean and standalone object
- No service locators smells
- Allows multiple custom  repositories per entity.

    ```yaml
    - App\Repository\ProductRepository
    - App\Repository\ProductRedisRepository
    - App\Repository\ProductBenchmarkRepository
    ```


### 3. Use in Controller

*Just constructor injection, bro*:

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


:+1:

- IDE know the type and autocomplete works 100%.
- There is no sign of Doctrine. Easier to maintain and extend. Also speace to decoupling to [local packages](@todo link) is now opened.



### 4. Registration `services.yml`


Final 3rd apperance for it's great success: 

```yaml
# app/config/services.yml

services:
    _defaults:
        autowire: true

    App\Repository\:
        source: ../Repository
```


All we needed is to apply *composition over inheritance* pattern in this specific case. 



## How to add new repository?

The main goal of all this was to make work with repositories typehinted, safe and reliable for you tu use and easy to extends. 
**It also minimized space for error**, because **strict types and constructor injection now validates** much of your code for you. 

The answer is now simple: **just create repository it in `App\Repository`**. 


Try the same example with your current approach and let me know in the commentds.



Happy coding!
