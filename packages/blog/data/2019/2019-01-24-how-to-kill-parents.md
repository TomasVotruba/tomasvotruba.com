---
id: 180
title: "How to Kill Parents"
perex: |
    I see too many skilled developers missing `final` in every class they use. So I reposed [When to declare classes final](http://ocramius.github.io/blog/when-to-declare-classes-final) - 4 years old post that shows you *why*. If you should learn just one skill this year, read and learn this one.
    <br>
    <br>
    It's easier said than done, but the more parents you kill, the better you get at it. Today, we look on 3 effective ways to kill them.
tweet: "New Post on #php 🐘 blog: How to Kill Parents    #phpstan #solid #final"
---

<blockquote class="blockquote text-center mt-5 mb-5" markdown=1>
**tl;dr**

Always declare your classes `final` and learn ways how to code with them.

It's not an easy path, but it will teach you SOLID better than anything else.
</blockquote>

S**O**L**ID** - 3 letters from [famous coding principles](https://en.wikipedia.org/wiki/SOLID) are related to `final` classes, classes that cannot have children. The `final` topic is very popular:

<img src="/assets/images/posts/2019/final/repost.png" class="img-thumbnail">

But have you seen them in your favorite package?

## No Parents = Happy Family

There are few cases the when parent class is **required** by 3rd party package or PHP code:

```php
use PHPUnit\Framework\TestCase;

final class PrivatesCallerTest extends TestCase
{
}
```


```php
<?php

use Symfony\Component\Console\Command\Command;

final class PropagateCommand extends Command
{
}
```

```php
<?php

use Exception;

final class OutputFormatterNotFoundException extends Exception
{
}
```

These cases are valid - after all, if they shouldn't be extended, they would have been marked them `final`, right?

But in other cases, it is **optional**. One of the most spread terribly wrong use of parent class is Doctrine repository.

```php
<?php declare(strict_types=1);

namespace App\Repository;

use Doctrine\ORM\EntityRepository;

final class PostRepository extends EntityRepository
{
}
```

Symfony upgrades this problem to [one more layer](https://github.com/doctrine/DoctrineBundle/blob/a6ab041f33a0af379314ad5dbe17006903fd9fb6/Repository/ServiceEntityRepository.php) of vendor lock:

```php
<?php declare(strict_types=1);

namespace App\Repository;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;

final class PostRepository extends ServiceEntityRepository
{
}
```

**Switching 3rd party dependency from one class to another doesn't solve your issue**. You might switch heroin for meth, but you're still an addict.

Doctrine and Symfony documentation is full of this nonsense and it gives developers an idea that inheritance is a good thing.
 That's why migration of database layer is so difficult. Read about [How to use Repository with Doctrine as Service in Symfony](/blog/2017/10/16/how-to-use-repository-with-doctrine-as-service-in-symfony/) if you still have `extends` in your repository.

## Why is this Such A Big Deal?

In the end, the code without limits look like:

```diff
namespace App\Repository;

namespace Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;

-final class PostRepository extends ServiceEntityRepository
+class PostRepository extends ServiceEntityRepository
 {
 }
```

Do you need a homepage post? Just extend, it's the *Symfony* way:

```php
<?php declare(strict_types=1);

namespace App\Repository;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;

class HomepagePostRepository extends PostRepository
{
}
```

```php
<?php declare(strict_types=1);

namespace App\Repository;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;

class CachedHomepagePostRepository extends HomepagePostRepository
{
}
```

This code is not made up, but the common sense of applying *inheritance over composition* approach on everything that can be extended. And **everything that is not `final`, can be extended.**

<br>

## Vendor-Lock Payback

Overusing `extends` is similar to overuse of static methods in Laravel. Everyone with [bad expensive experience](/blog/2018/04/26/how-i-got-into-static-trap-and-made-fool-of-myself/) knows why it's bad, but they're not able to pass this experience who are in "the zone" of using.

Then comes the day when 3rd party code changes:

```diff
 <?php declare(strict_types=1);

 namespace Doctrine\ORM;

 class EntityRepository
 {
-    public function createQueryBuilder($alias, $indexBy = null)
+    public function createQueryBuilder(string $alias, ?string $indexBy = null)
     {
     }

-     public function findAll()
+     public function findAll(): array
      }
 }
```

Have you overridden this method? Your code is broken. If this example doesn't cover your method, maybe you've changed one of 15 methods in `EntityRepository` you can override now. And what is not `final` can...

And if you inherit `Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository`, you'll have to wait for them to fix the code first. It's like waiting on Android upgrades with Samsung.

## Your Parents are Good Parents

**You can avoid this completely** by playing with your own parents. Do you need a common method for all your repositories? You can:

```php
<?php

abstract class AbstractDoctrineRepository
{
    // @inject EntityManager here

    // your common methods
}
```

```php
<?php

final class ProductRepository extends AbstractDoctrineRepository
{
}
```

This way

- **you own the code**
- you **have no troubles** when `EntityRepository` changes the way above
- migration of a database is a matter of weekend work

## Make Children in Factory Instead

This week we started a migration of Nette application to Symfony with Rector. One of the changes is `Nette\...\Response` to `Symfony\...\Response` change. It's easy:

```diff
 class SomePresenter
 {
-    public function someAction(): \Nette\...\Response
+    public function someAction(): \Symfony\...\Response
     {
     }
 }
```

There are over 50 classes like this, but still do-able even without Rector.

But how would you approach cases like this?

```php
<?php

class SomeResponse extends \Nette\...\Response
{
}
```

```php
<?php

class SomePresenter
{
    public function someAction()
    {
        return new SomeResponse($values, $code);
    }
}
```

Again, there **are over 50 classes in this format**.

Oh, and the arguments are in different order and there is one extra:

```diff
 <?php

 class SomePresenter
 {
     public function someAction()
     {
-        return new SomeResponse($values, $code);
+        return new Symfony\...\Response($values, $headers, $code);
     }
 }
 ```

Now we have to go through all these cases and change them. To add more salt to the wound, once there is `OkResponse` or `DeniedResponse`, all children of `Nette\...\Response`. This got us by shock. **Our big plan to refactor application in one afternoon went to dust.**

And it doesn't have to be such a big change as a framework, but argument swap or just new type declaration - **there will be so many BC breaks just for [type declarations](/blog/2019/01/03/how-to-complete-type-declarations-without-docblocks-with-rector/) in next 2 years**.

"What if instead, we'd have a factory."

```diff
 <?php

 class SomePresenter
 {
     public function someAction()
     {
-        return new SomeResponse($values, $code);
+        return $this->responseFactory->createSuccess($values);
     }
 }
```

```php
<?php

class ResponseFactory
{
     public function createSuccess($values)
     {
         return new Nette\...\Response($values, 'OK');
     }
}
```

The change here for **every such call would be in 1 place** instead of 50:

```diff
-return new Nette\...\Response($values, 'OK');
+return new Symfony\..\Response($values, $headers, 'OK');
```

Luckily, Honza [added support for `new Instance($args)` → `$this->instanceFactory->create($args)`](https://github.com/rectorphp/rector/pull/982) to Rector, so it won't be a stopper for us. But if the original parent would be `final`, this would never happen.

## Static Anal to the Rescue

We don't have to wait for changes in all packages. There is a static analysis to help us. I just learned about [localheinz/phpstan-rules](https://github.com/localheinz/phpstan-rules). I can't wait to try these nice rules:

<img src="/assets/images/posts/2019/final/rules.png" class="img-thumbnail">

<br>

Next time you'll try to do coding, try using `final`. Do you know what will happen?
