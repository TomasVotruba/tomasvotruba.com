---
id: 424
title: "Symfony @required - Avoid the Temptation and Use it Right"
perex: |
    Symfony 3 introduced a [`@required` annotation](https://symfony.com/doc/3.x/service_container/calls.html) (now also an attribute) that allows injecting more services via the setter method apart constructor. At the time, it was good.

    The goal was to solve circular dependencies: when A needs B, B needs C, and C needs A.

    But more often than not, I see PHP projects where it got completely out of hand.

    How to use it right?
---

<blockquote class="blockquote text-center mt-5 mb-5">
    "Fire is a good servant,<br>
    but a bad master."
</blockquote>

The official documentation is not very verbose about how not to use it, so this is my attempt to fill in the missing piece. Similar to static methods, it's easy to use everywhere instantly, but it's very hard to revert the change to clean constructor injection.

<br>

## 3 Temptations to Avoid

## 1. `@Required` is not "a way to get a service"

This annotation was introduced in times of static containers, where we could get a service by using a global container,e. g. `$this->get(ServiceWeNeed::class)`.

Let's replace that, shall we?

```php
final class HomepageController
{
    private ProductRepository $productRepository;

    /**
     * @required
     */
    public function setProductRepository(ProductRepository $productRepository)
    {
        $this->productRepository = $productRepository;
    }

    // ...
}
```

## 👎

<br>

This is a bit too verbose and ugly. But it's just one step from the second temptation.

<br>

## 2. `@required` is not for "Handy Traits"

If we combine 2 cool features in the way nobody expected them to use, like an ice-cream and a hamburger, we'll get the following:

```php
trait ProductRepositoryTrait
{
    private ProductRepository $productRepository;

    /**
     * @required
     */
    public function setProductRepository(ProductRepository $productRepository)
    {
        $this->productRepository = $productRepository;
    }

    // ...
}
```

Now we can finally have a 1-line solution to inject any service anywhere:

```php
final class HomepageController
{
    use ProductRepositoryTrait;

    public function home()
    {
        $products = $this->productRepository->fetchAll();
        // ...
    }
}
```

```php
final class ProductController
{
    use ProductRepositoryTrait;

    public function list()
    {
        $products = $this->productRepository->fetchAll();
        // ...
    }
}
```

So neat, right? This was especially tempting before PHP 8.0 came with promoted properties.

## 👎

<br>

I've seen this in 3 projects recently and it makes any changes very slow and sticky.

The original idea of `trait` was to **re-use shared and diverse logic in value objects/entities**, to avoid bloated abstract classes.

If we want to use a service anywhere, we inject it via the constructor.

<br>

This setter method also opens the possibility **to override service from the outside**. You thought this was the only `ProductRepository` service instance in the whole project? It could be, or maybe not. We're only one tiny step away from the next temptation.

<br>

## 3. `@required` is not to make mocking and tests easier

Last but not least, these setters allow anyone to replace service in tests on the fly:

```php
final class HomepageControllerTest extends TestCase
{
    public function test(): void
    {
        $homepageController = self::$container->get(HomepageController::class);

        $productRepositoryMock = $this->createMock(ProductRepository::class);
        $productRepositoryMock->expect('find')->willReturn('...');

        $homepageController->setProductRepository($productRepositoryMock);
    }
}
```

So easy, so tempting, right?

## 👎

<br>

This is also wrong, as we **have just turned our dependency injection paradigm into setter injection**.

Next time any other developer in our team will need to mock a service, they will create service setters everywhere.

<br>

Forget all previous examples... so how to use the `@required` correctly?

<br>

## Three Ways to Use it Right

If you can, always use constructor injection:

```php
final readonly class HomepageController
{
    public function __construct(
        private ProductRepository $productRepository
    ) {
    }
}
```
Clean and reliable. We can build trust in our codebase, as it has a single `ProductRepository` instance.

<br>

The `@required` annotation should **be the last solution**, if there is no better way to inject a service.

<br>

## 1. Prevent Circular Dependency

As stated, the original idea that sparked this feature was to prevent circular dependencies. This could happen if there are complex service structures, e.g. `PriceResolved` service that depends on 10 different `PriceModifier` implementations that depend mutually on each other.

## 👍

<br>

**Rule of thumb**: If the Symfony container gives us a "circular dependency" exception, and it's not easy to handle this in the main service by using `->set($this)` on `foreach` loop, we use `@required`.

<br>

## 2. Prevent Dependency hell with Abstract Class

Let's say we have an abstract controller with a couple of services useful in the controller itself and all its children:

```php
abstract AbstractProductController
{
    public function __construct(
        private Logger $logger,
        private Security $security,
    ) {
    }

    // ...
}
```

Then we extend this controller and add one more dependency to its own:

```php
final class RestProductController extends AbstractController
{
    public function __construct(
        private EntitySerializer $entitySerializer,
        Logger $logger,
        Security $security,
    ) {
        parent::__construct($logger, $security);
    }
}
```

All this is just to get `EntitySerializer` here. Now imagine parent `__construct()` of `AbstractController` will change. We have to update all its children.

<br>

This is where `@required` becomes useful. It is a little more verbose, but only in single parent class. The rest of the children will become cleaner:


```php
abstract AbstractProductController
{
    private Logger $logger;

    private Security $security;

    /**
     * @required
     */
    public function autowireAbstractProductController(
        Logger $logger,
        Security $security,
    ) {
        $this->logger = $logger;
        $this->security = $security;
    }

    // ...
}
```

```php
final class RestProductController extends AbstractController
{
    public function __construct(
        private EntitySerializer $entitySerializer,
    ) {
    }
}
```

## 👍

<br>

**Rule of thumb**: If we use an `abstract` class with a couple of services, and we need to add one more service to its children, we use `@required`.

<br>

## 3. Make the autowire method single and unique

Avoid using multiple `@required` methods in a single class:

```php
abstract class AbstractController
{
    // ...

    /**
     * @required
     */
    public function setLogger(Logger $logger)
    {
        $this->logger = $logger;
    }

    public function setSecurity(Security $security)
    {
        $this->security = $security;
    }
}
```

It might lead to a forgotten `@required` annotation above one of the methods (see the 2nd one), or even mutual override by a slightly different type. We've seen both bugs.

<br>

Use a single autowire method to be safe:

```php
abstract class AbstractController
{
    /**
     * @required
     */
    public function autowireAbstractController(...)
    {
        // ...
    }
}
```

## 👍

<br>

**Rule of the thumb**: Use single autowire method and name it `autowire` + name of the calls. It prevents the `autowire()` method override bugs in case of multiple inheritance.

<br>

## How to spot all these problems?

That's a lot of tiny code smells to worry about, right? Are you curious about your project `@required` health check?

PHPStan to the rescue - check these [custom PHPStan rules](https://github.com/symplify/phpstan-rules#3-symfony-specific-rules) that watch our back on our projects:

* `NoRequiredOutsideClassRule`
* `RequiredOnlyInAbstractRule`
* `SingleRequiredMethodRule`

<br>

Add them to your `phpstan.neon` and see what they found.

<br>

Happy coding!
