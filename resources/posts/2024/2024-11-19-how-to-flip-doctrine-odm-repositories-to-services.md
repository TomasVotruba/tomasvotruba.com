---
id: 420
title: "How to flip Doctrine ODM repositories to Services"
perex: |
    While working with legacy projects, I often encountered this anti-pattern of misusing repositories. Instead of easy-to-inject service, projects are locked into a service locator.

    This makes code hard to upgrade and locks your project heavily to the Doctrine ODM packages. And there are plenty of them. Each extra package bites off its share of upgrade costs.

    Today, we look at how to refactor the ODM service locator to independent services and separate your project from ODM. We also get a few advantages in strict type coverage.
---

What do I mean by *service locator*?

```php
final class ProductController
{
    // ...

    public function __construct(Container $container)
    {
        $this->gptClient = $container->get(GptAPIClient);
        $this->dynamicPriceResolver = $container->get(DynamicPriceResolver);
    }
}
```

A service locator is a class that contains all the services. It's an anti-pattern that leaks too much and makes the service able to do everything. It used to be *the way* to use containers in the 2010s before we discovered **dependency** injection in PHP. Unfortunately, it's [still in official docs on "First Steps"](https://www.doctrine-project.org/projects/doctrine-mongodb-bundle/en/5.0/first_steps.html#fetching-objects-from-mongodb) tutorial.

Nowadays, we use constructor injection to make service design clean, minimalistic, and neat.

<br>

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

The `documentManager` is a service locator for Doctrine ODM. If we inject this service everywhere, we can get any repository we want - existing or on-the-fly. That's why legacy projects are filled with `documentManager` in every single possible place:

<div class="text-center mt-5 mb-5">
<img src="https://dev-to-uploads.s3.amazonaws.com/i/it0fk0jbqphcgecnpbq7.jpg" class="img-thumbnail" style="width:30em">
</div>

<br>

What does the code actually do?

* `->getRepository()` runs docblock/attribute reflection on the `Product` entity
* it looks for a repository class `@Document(repositoryClass="App\Repository\ProductRepository")`
* if the repository is found, we'll get it back freshly made
* hidden catch: our repository has to extend `Doctrine\ODM\MongoDB\Repository\DocumentRepository` class - generic, without any types, do-it-all service

<br>

Now:

<blockquote class="blockquote text-center mt-5 mb-5">
With great power comes <strike>great responsibility</strike><br>
lot of wasted time and money to keep the code up to date
</blockquote>

We should never use `Doctrine\ODM\MongoDB\DocumentManager` outside repository services.

## The Painful Maintenance cost

The best practice nowadays is to have **single service that handles the task** we give it to - whether it's data transformation, calling external API, or sorting data based on user input filter:

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

**Why is this ideal design?**

* no parent classes from `/vendor`,
* strict types under our control
* injectable services = do you need this service? ask for it in the constructor

<br>

Rule of thumb: **Your controllers and services should not know about the database layer you use**. Only repositories should care about that.

<br>

If we upgrade ODM 1 to 2, then to 2 to 3, 3 to 4..., **we don't have to change anything**. The cost of such an upgrade is the time to change `composer.json`, run `composer update`, and fix bundle configuration here and there.

<br>

### How much does the "service locator" upgrade cost?

Let's list all the work we have to do:

* first, we have to untangle changes in our MVC framework container
* we have to remove `documentManager` from all the places it lives in
* we have to refactor repositories to services - remove their parent class, inject `DocumentManager` in the constructor
* we have to clean entity annotations from "repositoryClass"
* we have to upgrade `->getRepository()` to clean constructor injection

...to sum up: a lot!

<br>

We have to get rid of these obstacles. Only that way, our next upgrade will be close to $ 0.

The service pattern is also more adaptable in case your framework DI container changes (and it will).

## 4 steps to Independent Repositories (and cheap upgrade)

Doctrine ODM holds the service locator pattern tight [and may](https://stackoverflow.com/questions/12223176/how-to-inject-a-repository-into-a-service-in-symfony) [discourage you](https://stackoverflow.com/questions/50240596/references-class-doctrine-odm-mongodb-unitofwork-but-no-such-service-exists) from moving on. But if we apply the following steps, it will go down one by one like snowflakes in a sunbeam.

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

### 2. Separate your Repository from `/vendor`

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

### 3. Add `find*()` method you need, with type declarations

Instead of docblock + `DocumentRepository` magic, we can now use actual PHP code and native type declarations:

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

Now, we've just improved our IDE, Rector, and PHPStan support. Also, if we pass an integer where the string should be `$this->productRepository->find(1)`, we'll get an error report.

### 4. Replace `->getRepository(...)` with service injection


```diff
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

## Rule of a thumb: Handle max. 1 Repository per PR

It's tempting to do a big bang jump and refactor all the repositories at once. But it's most likely that your project has more than 5 repositories used throughout most of the codebase.

To make this change happen safely and fast, **handle only one repository per pull request**. Change it, do all 4 steps, and create a pull request. Update tests and make CI pass. Merge.

Rinse and repeat.

<br>

Finish this upgrade challenge, and you'll get a sweet reward:

* nearly zero cost upgrade of the next major Doctrine/framework
* full type coverage
* IDE, Rector, and PHPStan are working on repositories for you

<br>

Happy coding!
