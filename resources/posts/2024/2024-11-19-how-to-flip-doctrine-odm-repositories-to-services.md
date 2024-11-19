---
id: 420
title: "How to flip Doctrine ODM repositories to Services"
perex: |
    While working with legacy projects, I often come across this anti-pattern of using repositories in wrong way. Instead of easy-to-inject service, projects are locked into service locator.

    This makes code hard to upgrade and locks your project heavily to the Doctrine ODM packages. And there is plenty of them. Each extra package bites off its share of upgrade costs.

    Today we look on how to refactor ODM service locator to independent services and separate your project from ODM. There is quite a few advantages we get in strict type coverage as well.
---

What I mean by *service locator*?

```php
final class ProductController
{
    // ...

    public function __construct(Container $container)
    {
        $this->gptClient = $container->get(GptAPIClient $gptClient);
        $this->dynamicPriceResolver = $container->get(DynamicPriceResolver $dynamicPriceResolver);
    }
}
```

A service locator is a class that contains all the services. It's any anti-pattern, that leaks too much and makes service able to do everything. It used to be *the way* to use container in 2010s, before we discovered **dependency** injection in PHP. Unfortunatelly, it's [still in official docs on "First Steps"](https://www.doctrine-project.org/projects/doctrine-mongodb-bundle/en/5.0/first_steps.html#fetching-objects-from-mongodb) tutorial.

Nowadays we use constructor injection to make service design clean, minimalistic and neat.

Yet, ODM ships with such a service locator out of the box:

```php
final class ProductController
{
    // ..


    public function productDetail()
    {
        $productRepository = $this->documentManager->getRepository(Product::class);
        $categoryRepository = $this->documentManager->getRepository(Category::class);

        // ...
    }
}
```

The `documentManager` is service locator for Doctrine ODM. If we inject this service everywhere, we can get any repository we want - existing or on-the-fly. That's why legacy projects are filled with `documentManager` in every single possible place:

@todo meme, I'll ask for container ot fetch container

It's like putting container into your container to get a container.

<br>

What does the code actually do?

* `->getRepository()` runs docblock/attribute reflection on `Product` entity
* it looks for a repository class `@Document(repositoryClass="App\Repository\ProductRepository")`
* if the repository found, we'll get it back freshly made
* hidden catch: our repository has to extend `Doctrine\ODM\MongoDB\Repository\DocumentRepository` class - generic, without any types, do-it-all service

<br>

Now:

<blockquote class="blockquote text-center mt-5 mb-5">
With great power comes ~~great responsibility~~...<br>
lot of wasted time and money just to maintain the code up to date.
</blockquote>

We should never use `Doctrine\ODM\MongoDB\DocumentManager` outside repository service. We use it typicall for `persist()` and `flush()` calls.

## The Painful Maintenance cost

The best practise nowadays is to have **single service that handle the task** we give it to - whether it's data transformation, calling external API or sorting data based on users input filter:

```php
final class ProductRepository
{
    private $repository;

    public function __construct(private DocumentManager $documentManager)
    {
        // the only allowed call of getRepository()
        $this->repository = $documentManager->getRepository(Product::class);
    }

    public function findByName(string $name): ?Product
    {
        $this->repository->findOneBy(['name' => $name]);
    }
}
```

**This is ideal design**:

* no parent classes from `/vendor`,
* full type coverage under our control
* and injectable services - do you need this service? ask for it in the constructor

<br>

Rule of a thumb: **Your controllers and services should not know about the database layer you use**. Only repositories should care about that.

<br>

If we upgrade ODM 1 to 2, then to 2 to 3, 3 to 4..., **we don't have to change anything**. The cost of such upgrade is literally the time to change `composer.json`, running `composer update` and fixing bundle configuration here and there.

<br>

### How much does the "service locator" upgrade cost?

A lot. Let's list all the work we have to do:

* first, we have to untangle changes in our MVC framework container
* we have to remove `documentManager` from all the places it lives in
* we have to refactor repositories to services - remove their parent class, inject `DocumentManager` in constructor
* we have to cleanup entity annotations from "repositoryClass"
* we have to upgrade `->getRepository()` to clean constructor injection

Only that way, our next upgrade will be close to $ 0. It's also more robust code in case your framework DI container will change (and it will).

## 4 steps to Independent Repositories (and cheap upgrade)

Doctrine ODM holds the service locator pattern tight [and may](https://stackoverflow.com/questions/12223176/how-to-inject-a-repository-into-a-service-in-symfony) [discourage you](https://stackoverflow.com/questions/50240596/references-class-doctrine-odm-mongodb-unitofwork-but-no-such-service-exists) from moving on. But if we apply following steps, it will go down, one by one.

### 1. Cleanup entity

```diff
 use Doctrine\ODM\MongoDB\Mapping\Annotations\Document;

 /**
  * @Document(
- *     repositoryClass="App\Repository\ProductRepository"
  * )
  */
 class Product
```

### 2. Disjoint your repository and `/vendor`

```diff
+use App\Entity\Product;
 use Doctrine\ODM\MongoDB\Repository\DocumentRepository;
+use Doctrine\ODM\MongoDB\DocumentManager;

-final class ProductRepository extends DocumentRepository
+final class ProductRepository
 {
+    private DocumentRepository $repository;
+
+    public function __construct(private DocumentManager $documentManager)
+    {
+        $this->repository = $documentManager->getRepository(Product::class);
+    }

     // ...
 }
```

### 3. Add `find*()`` method you need, with strict types

Instead of docblock + `DocumentRepository` magic, we can now use real PHP code and native type declarations:

```php
-/**
- * @method Product|null find(string $id)
- */
 final class ProductRepository
 {
     // ...

+    public function find(string $id): ?Product
+    {
+        return $this->repository->find($id);
+    }
 }
```

Now we've just improved our IDE, Rector and PHPStan support. Also, if we pass an integer where string should be `$this->productRepository->find(1)`, we'll get an error report.

### 4. Replace `->getRepository(...)` with service injection


```dif
-use Doctrine\ODM\MongoDB\DocumentManager;

 final class ProductController
 {
     public function __construct(
-       private DocumentManager $documentManager,
+       private ProductRepository $productRepository,
     ) {
     }

     public function productDetail(string $id)
     {
-        $productRepository = $this->documentManager->getRepository(Product::class);
-        $product = $productRepository->get($id);
+        $product = $this->productRepository->get($id);

        // ...
     }
 }
```

That's it!


<br>

## Rule of a thumb: Handle only Repository per PR

It's tempting to do big bang jump and refactor all the repositories at once. But it's most likely the case your project has more than 5 such repositories and they're literary in majority of the codebase.

To make this change happen safe and fast, **handle only one repository per pull-request**. Change it, do all 4 steps and create a pull-requests. Update tests and make CI pass. Merge.

Rinse and repeat.

<br>

Finish this upgrade challenge, and you'll get a sweet reward:

* nearly zero cost upgrade of next major Doctrine/framework
* full type coverage
* IDE, Rector and PHPStan working on repositories for you

<br>

Happy coding!
