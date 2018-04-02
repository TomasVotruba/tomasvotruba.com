---
id: 88
title: "Rectify: Turn All Doctrine Repositories From Composition To Inheritance in Seconds"
perex: |
    Today I start new series called *Rectify*. It will be about **instant refactoring** to better code not manually, but with Rector.
    <br><br> 
    That way there is no excuse left to change your legacy application to clean code you'll love to extend. 
    <br><br>
    We'll start with very popular post - [Repository with Doctrine as Service in Symfony](/blog/2017/10/16/how-to-use-repository-with-doctrine-as-service-in-symfony/).

tweet: "..@todo"
tweet_image: "@todo"
related_items: [59, 81, 78, 77]
---

I wrote about [How to use Repository with Doctrine as Service Symfony](/blog/2017/10/16/how-to-use-repository-with-doctrine-as-service-in-symfony/) a while ago. There are many posts about this topic, but not as simple to apply as this one. At least for one repository.

## The One-to-Many Problem of The Best Practise 

It's always very simple to write 1 service, with `final`, constructor injection, design patterns and modern PHP 7.1 type hints and `strict_types`. That's why it's easy to write such posts as the one above :)

**But what if you have 50 repositories? Would I write a post about how I refactored 50 repositories to services?** Probably not, because it would take so much time and energy and you'd fell asleep while reading the first 1/10.

## Turn M-complexity to 1 with Rector

What if you could **change just 1 case and it would be promoted to the rest of your application**? From 1:M to 1:1. That's exactly what Rector help you with.

Let's see how it works. I'll use [the example from original post](/blog/2017/10/16/how-to-use-repository-with-doctrine-as-service-in-symfony/#how-to-make-this-better-with-symfony-3-3), where I write about turning [inheritance to composition](https://github.com/jupeter/clean-code-php#prefer-composition-over-inheritance) - one of SOLID princips.

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

1. First, install rector

```bash
composer install rector/rector --dev
```

 
2. Setup config



```yaml
# rector.yml
services:
    # order matters, this needs to be first to correctly detect parent repository
    
    # this will replace parent calls by "$this->repository" property
    Rector\Rector\Architecture\RepositoryAsService\ReplaceParentRepositoryCallsByRepositoryPropertyRector: ~
    
    # this will move repository from parent to constructor
    Rector\Rector\Architecture\RepositoryAsService\MoveRepositoryFromParentToConstructorRector: ~
```

3. Add entity => repository provider

Last step of configuration is letting the Rector know what class is mapped to which repository.
For that reasons there is `Rector\Contract\Bridge\EntityForDoctrineRepositoryProviderInterface`:

```php
<?php declare(strict_types=1);

namespace App\Rector;

use Rector\Contract\Bridge\EntityForDoctrineRepositoryProviderInterface;

final class EntityForDoctrineRepositoryProvider implements EntityForDoctrineRepositoryProviderInterface
{
    /**
     * @var string[]
     */
    private $map = [
        'App\Repository\PostRepository' => 'App\Entity\Post',
    ];

    public function provideEntityForRepository(string $name): ?string
    {
        return $this->map[$name] ?? null;
    }
}
```

And register it 

```diff
 # rector.yml
 services:
     # order matters, this needs to be first to correctly detect parent repository
    
     # this will replace parent calls by "$this->repository" property
     Rector\Rector\Architecture\RepositoryAsService\ReplaceParentRepositoryCallsByRepositoryPropertyRector: ~
    
     # this will move repository from parent to constructor
     Rector\Rector\Architecture\RepositoryAsService\MoveRepositoryFromParentToConstructorRector: ~

+    App\Rector\EntityForDoctrineRepositoryProvider: ~
```

4. Run on your code

```bash
vendor/bin/rector process /app --config rector.yml
```

When the Rector finishes, be sure to check your code. While it can save manage 80 % of cases for you, it's not perfect. I love to use `git diff` and *PgDown* - the best use case for this key I know.

Ready? Add, commit, send invoice for big refactoring and enjoy your coffee :)

<br><br>

Happy instant refactoring!
