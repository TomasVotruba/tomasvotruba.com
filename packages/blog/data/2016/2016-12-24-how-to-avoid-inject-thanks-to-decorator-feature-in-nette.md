---
id: 19
title: "How to avoid @inject thanks to Decorator feature in Nette"
perex: |
    I often find `@inject` being overused in projects I review while mentoring. They often bring less writing, but in exchange they break [SOLID principles](https://en.wikipedia.org/wiki/SOLID_(object-oriented_design)).


    Today I will show you solution that will **keep your code both small and clean** - **Decorator feature in Nette**.

tweet: "How to avoid @inject by using Decorator in #nettefw?"
---

As [Derek Simons says](https://www.ted.com/talks/simon_sinek_how_great_leaders_inspire_action) says...

## ...Start with "Why"

Why am I writing this article? I try to improve knowledge interoperability between frameworks so it **is easier to understand and use each other**. The goal is to discourage Nette- (or any framework-) specific things **in favor of those that may be common**.

Today, I will try to agree on setter injection with you.


## `@Inject` Overuse is Spreading

This code is common to 80 % Nette applications I came across in last year:

```php
// app/Presenter/ProductPresenter.php

namespace App\Presenter;

final class ProductPresenter extends AbstractBasePresenter
{
    /**
     * @inject
     * @var ProductRepository
     */
    public $productRepository;
}
```

Using `@inject` annotations over constructor injection is **fast, short and it just works**.

Ok, why not use it everywhere:

```php
// app/Repository/ProductRepository.php

namespace App\Repository;

class ProductRepository
{
    /**
     * @inject
     * @var Doctrine
     */
    public $entityManager;
}
```

and

```yaml
# app/config/config.neon

services:
    -
        class: App\Repository\ProductRepository
        inject: on
```

## Your Code is Seen as Manual How to Write

Why? Because "what you see is what you write". New programmer joins the teams, sees this handy `@inject` feature and uses when possible and handy.

Some of you, who already talked about `@inject` method usage already there are some and only few specific places to use it.

## Where to only `@inject`?

**To prevent constructor hell**. If you meet this term first time, go read [this short explanation](https://phpfashion.com/di-a-predavani-zavislosti#toc-constructor-hell) by David Grudl.

The best use case is `AbstractBasePresenter`.
Let's say I need `Translator` service in all of my presenters.

```php
// app/Presenter/AbstractBasePresenter.php

namespace App\Presenter;

abstract class AbstractBasePresenter extends Nette\Application\UI\Presenter
{
    /**
     * @inject
     * @var Translator
     */
    public $translator;
}
```

And I can use it in `ProductPresenter` along with constructor injection

```php
// app/Presenter/ProductPresenter.php

namespace App\Presenter;

final class ProductPresenter extends AbstractBasePresenter
{
    /**
     * @var ProductRepository
     */
    private $productRepository;

    public function __construct(ProductRepository $productRepository)
    {
        $this->productRepository = $productRepository;
    }
}
```

This is quite clean and easy to use, because presenters have injects [enabled by default](https://github.com/nette/application/blob/3165d3a8dab876f4364cdcba450a33ab0182049a/src/Bridges/ApplicationDI/ApplicationExtension.php#L111-L116).


## Level up

But what if we have other objects that:

 - **inherit from abstract parent**
 - needs **1 service available everywhere**

2 common case pop to my mind:

- `AbstractBaseRepository` for all our repositories
- `AbstractBaseControl` for all our components

Let's take the first one:

```php
// app/Repository/AbstractBaseRepository.php

namespace App\Repository;

use Doctrine\ORM\EntityManagerInterface;

abstract class AbstractBaseRepository
{
    /**
     * @var EntityManagerInterface
     */
    protected $entityManager;

    public function setEntityManager(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }
}
```

And specific repository with some dependency:

```php
// app/Repository/ProductRepository.php

namespace App\Repository;

use App\Model\Product\ProductSorter;

final ProductRepository extends AbstractBaseRepository
{
    /**
     * @var ProductSorter
     */
    private $productSorter;

    public function __construct(ProductSorter $productSorter)
    {
        $this->productSorter = $productSorter;
    }
}
```

So our config would look like:

```yaml
# app/config/config.neon

services:
    -
        class: App\Repository\ProductRepository
        setup:
            - setEntityManager
    # and for other repositories
    -
        class: App\Repository\UserRepository
        setup:
            - setEntityManager
    -
        class: App\Repository\CategoryRepository
        setup:
            - setEntityManager
```

### SO much writing!

It is cleaner, but with so much writing? Thanks, but no, thanks. Let's go back to `@inject`...

Wait! Before any premature conclusion, let's set the goal first.

### What is Desired Result?

```yaml
# app/config.config.neon

services:
    - App\Repository\ProductRepository
    - App\Repository\UserRepository
    - App\Repository\CategoryRepository
```

That would be great, right? Is that possible in Nette while keeping the code clean?

## Decorator Extension to the Rescue

This feature is in Nette [since 2014](https://github.com/nette/di/commit/28fdac304b967ae43a90936069d94316ee2daca4) (<= the best documentation for it so far).

How does it work?

```yaml
# app/config/config.neon

decorator: # keyword used by Nette
    App\Repository\AbstractBaseRepository: # 1. find every service this type
        setup: # same setup as we use in service configuration
            - setEntityManager # 2. call this setter injection on it

    # or do you need to call "setTranslator" on every component?
    App\Component\AbstractBaseControl:
        setup:
            - setTranslator
```

That's it!


## What Have You Learned Today?

- It is easy to overuse `@inject` in places where it doesn't solve any problem
- The problem `@inject`/`inject<method>` method were born to solve is called *dependency hell*
- If you need to **decorate service of some type**, use *Decorator Extension*
- This will lead **to better framework understandability and usability**
- ...and world peace in time :)

In next article, we will look at other practical use cases for Decorator Extension.

<br>

How do you use `@inject`, constructor injection or Decorator Extension? Let me know in the comments, I'm curious.

<br>

Happy coding!
