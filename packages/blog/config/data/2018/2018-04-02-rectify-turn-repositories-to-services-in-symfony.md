---
id: 88
title: "Rectify: Turn All Doctrine Repositories From Inheritance To Composition in Seconds"
perex: |
    Today I start new series called *Rectify*. It will be about **instant refactoring** to better code not manually, but with Rector.
    <br><br>
    That way there is no excuse left to change your legacy application to clean code you'll love to extend.
    <br><br>
    We'll start with very popular post - [Repository with Doctrine as Service in Symfony](/blog/2017/10/16/how-to-use-repository-with-doctrine-as-service-in-symfony).

tweet: "New Post on my Blog: Rectify: Turn All Doctrine Repositories From Composition To Inheritance in Seconds"
---

I wrote about [How to use Repository with Doctrine as Service Symfony](/blog/2017/10/16/how-to-use-repository-with-doctrine-as-service-in-symfony) a while ago. There are many posts about this topic, but not as simple to apply as this one. At least for one repository.

## The One-to-Many Problem of The Best Practise

It's always very simple to write 1 service, with `final`, constructor injection, design patterns and modern PHP 7.1 type hints and `strict_types`. That's why it's easy to write such posts as the one above :)

**But what if you have 50 repositories? Would I write a post about how I refactored 50 repositories to services?** Probably not, because it would take so much time and energy and you'd fell asleep while reading the first 1/10.

## Turn M-complexity to 1 with Rector

What if you could **change just 1 case and it would be promoted to the rest of your application**? From 1:M to 1:1. That's exactly what Rector help you with.

Let's see how it works. I'll use [the example from the original post](/blog/2017/10/16/how-to-use-repository-with-doctrine-as-service-in-symfony/#how-to-make-this-better-with-symfony-3-3), where I write about turning [inheritance to composition](https://github.com/jupeter/clean-code-php#prefer-composition-over-inheritance) - one of SOLID principles.

<br>

**Instead of inheritance...**

```php
<?php declare(strict_types=1);

namespace App\Repository;

use App\Entity\Post;
use Doctrine\ORM\EntityRepository;

final class PostRepository extends EntityRepository
{
}
```

**...we use composition:**

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


## 4 Steps to Instant Refactoring of All Repositories

### 1. Install Rector

```bash
composer install rector/rector --dev
```

### 2. Setup `rector.php`

There you name all the changes you'd like to perform on you code:

```php
<?php

use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Rector\Architecture\Rector\MethodCall\ReplaceParentRepositoryCallsByRepositoryPropertyRector;
use Rector\Architecture\Rector\Class_\MoveRepositoryFromParentToConstructorRector;

return function (ContainerConfigurator $containerConfigurator): void {
    $services = $containerConfigurator->services();

    // order matters, this needs to be first to correctly detect parent repository

    // this will replace parent calls by "$this->repository" property
    $services->set(ReplaceParentRepositoryCallsByRepositoryPropertyRector::class);

    // this will move the repository from parent to constructor
    $services->set(MoveRepositoryFromParentToConstructorRector::class);
};
```

### 3. Add Repository → Entity Provider

But how does Rector know what entity should it add to which repository? For that reasons, there is `Rector\Bridge\Contract\DoctrineEntityAndRepositoryMapperInterface` you need to implement.

It could be as simple as:

```php
<?php declare(strict_types=1);

namespace App\Rector;

use Rector\Bridge\Contract\DoctrineEntityAndRepositoryMapperInterface;

final class DoctrineEntityAndRepositoryMapper implements DoctrineEntityAndRepositoryMapperInterface
{
    /**
     * @var string[]
     */
    private $map = [
        'App\Repository\PostRepository' => 'App\Entity\Post',
        'App\Repository\ProductRepository' => 'App\Entity\Product',
    ];
    public function mapRepositoryToEntity(string $name): ?string
    {
        return $this->map[$name] ?? null;
    }

    public function mapEntityToRepository(string $name): ?string
    {
        $inversedMap = array_flip($this->map);

        return $inversedMap[$name] ?? null;
    }
}
```

And register it:

```diff
 <?php

 use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
 use Rector\Rector\Architecture\RepositoryAsService\ReplaceParentRepositoryCallsByRepositoryPropertyRector;
 use Rector\Rector\Architecture\RepositoryAsService\MoveRepositoryFromParentToConstructorRector;

 return function (ContainerConfigurator $containerConfigurator): void {
     $services = $containerConfigurator->services();

     // order matters, this needs to be first to correctly detect parent repository

     // this will replace parent calls by "$this->repository" property
     $services->set(ReplaceParentRepositoryCallsByRepositoryPropertyRector::class);

     // this will move the repository from parent to constructor
     $services->set(MoveRepositoryFromParentToConstructorRector::class);

+    $services->set(\App\Rector\DoctrineEntityAndRepositoryMapper::class);
 };
```

### 4. Run on Your Code

Now the fun part:

```bash
vendor/bin/rector process /app --dry-run # "--config rector.php" as default
```

You should see diffs like:

```diff
 use App\Entity\Post;
 use Doctrine\ORM\EntityRepository;

-final class PostRepository extends EntityRepository
+final class PostRepository
 {
     /**
+     * @var \Doctrine\ORM\EntityRepository
+     */
+    private $repository;
+    public function __construct(\Doctrine\ORM\EntityManager $entityManager)
+    {
+        $this->repository = $entityManager->getRepository(\App\Entity\Post::class);
+    }
+    /**
      * Our custom method
      *
      * @return Post[]
@@ -14,7 +22,7 @@
      */
     public function findPostsByAuthor(int $authorId): array
     {
-        return $this->findBy([
+        return $this->repository->findBy([
             'author' => $authorId
         ]);
     }
```

Are all looking good? Run it:

```bash
vendor/bin/rector process /app
```

### Safety First

When the Rector finishes, be sure to check your code. While it can manage 80 % of cases for you, it's not perfect. I love to use `git diff` and *PgDown* - the best use case for this key I know.

Ready? Add, commit, send an invoice for big refactoring and enjoy your coffee :)


## Clean Code... Done, but What About Beautiful?

You've probably noticed that code itself is not looking too good. Rector's jobs is not to clean, but to change the code. It's not a hipster designer, but rather a thermonuclear engineer. **That's why there are coding standards. You can apply your own or if not good enough use Rector's prepared set**:

```bash
composer require symplify/easy-coding-standard --dev
vendor/bin/ecs --config vendor/rector/rector/ecs-after-rector.php --fix
```

And your code is now both **refactored and clean**. That's it!

<br><br>

Happy instant refactoring!
