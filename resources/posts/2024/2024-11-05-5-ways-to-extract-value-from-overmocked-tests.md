---
id: 418
title: "5 Ways to Extract Value from Overmocked Tests"
perex: |
    The legacy projects I work with are often flooded with mocks and hard to upgrade. I already wrote [How to Remove Dead Mock Calls from PHPUnit Tests](/blog/how-to-remove-dead-mock-calls-from-phpunit-tests), that focuses on dealing with PHPUnit bloated syntax.

    Today we look on next wave of improvements, that make tests more valuable, easier to upgrade and read and even avoid false types.
---

Mocking comes useful, if you want to unit test a method, that depends on complex external factors. Like database response, GPT API response, AWS file storage and so on.
Mocking is also one of main factors, that make upgrade of tests very slow and expensive. During Rector upgrades, we often get to a project, that has 300+ tests classes that use mocks for its own classes.

We want to give objects in those mocks PHP 7.0 types, and have Rector and PHPStan our back in case we change those objects. How do avoid manual work and maintenance?

<br>

I'll share approach we use to get the most out of mocks, drop dead code and make tests more valuable.

## Our goals

* get as much **native PHP code** as possible, so IDE, Rector and PHPStan can check it easily
* remove any unnecessary dependency on the mocking framework, so **any PHP developer can read code**
* **make test fun to work with**, not a burden that we have to update with every change of native code

<br>

Read following examples with *your mocking tool* in mind. Not just PHPUnit, but also Prophecy, Phpspec, Mockery and so on. The syntax sugar of these tool are different, but principles are the same.

<br>

## 1. Test case, where every property is a mock

Let's start with simple question: what does this test case verify?

```php
use PHPUnit\Framework\TestCase;

final class SomeTest extends TestCase
{
    private MockObjet $apiClientMock;

    private MockObjet $responseMock;

    protected function setUp(): void
    {
        $this->apiClientMock = $this->createMock(ApiClient::class);
        $this->responseMock = $this->createMock(Response::class);
    }

    public function testApiClient(): void
    {
        $this->apiClientMock->expects($this->once())
            ->method('get')
            ->willReturn($this->responseMock);

        $returnedResponse = $this->apiClientMock->get();
        $this->assertSame($this->responseMock, $returnedResponse);
    }
}
```

Does it verify, that `ApiClient::get()` method returns `Response` object? What if we change the `get` method body? Nothing will happen, because this test only **check that our mocking framework works**.

## ❌

<br>

This test case **doesn't test our code at all**, because it overrides our code with made up behavior. It's like reading a news headline about "thousands of dead", before learning it's only a prediction if we get hit by a meteorite in a movie.

<br>

<div class="text-center">
    <h3>Rule of Thumb &nbsp; 👍</h3>
</div>

<blockquote class="blockquote mt-5 mb-5 text-center">
Every single test case should have at least<br>
1 real object that is being tested.
</blockquote>

<br>

There is [a PHPStan rule](https://github.com/TomasVotruba/handyman/blob/main/src/PHPStan/Rule/NoMockOnlyTestRule.php) to discover these.

Add PHPStan rule, spot those classes and make sure the expected service is tested.

<br>

## 2. Global Mock Property That is Never Modified

The first rule demands some manual work and care. So we'll take a rest with next one - an easy pick, to make our tests more readable.

I've just used this approach to remove 1000 lines from tests in couple seconds:

<img src="https://pbs.twimg.com/media/Gbs08uUWoA0aBQ_?format=png&name=240x240" class="img-thumbnail">

What is the use case?

```php
use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\MockObject\MockObject;

final class SmarterTest extends TestCase
{
    private TestedService $testedService;

    private MockObject $databaseMock;

    protected function setUp(): void
    {
        $this->databaseMock = $this->createMock(Database::class);
        $this->testedService = new TestedService($this->databaseMock);
    }

    public function test(): void
    {
        $result = $this->testedService->count(1, 3);
        $this->assertSame(3, $result);
    }
}
```

This has nothing to do with mocking, but it's a **visual clutter** that makes **code harder to read and more expensive to maintain**.

Can you spot it?

<br>

Yes, the `$databaseMock` **property is only written**, but never used later on. We can refactor it to direct variable in `setUp()` method:

```diff
 use PHPUnit\Framework\TestCase;
 use PHPUnit\Framework\MockObject\MockObject;

 final class SmarterTest extends TestCase
 {
-    private TestedService $testedService;

     private MockObject $databaseMock;

     protected function setUp(): void
     {
-        $this->databaseMock = $this->createMock(Database::class);
+        $databaseMock = $this->createMock(Database::class);
-        $this->testedService = new TestedService($this->databaseMock);
+        $this->testedService = new TestedService($databaseMock);
     }

     public function test(): void
     {
         $result = $this->testedService->count(1, 3);
         $this->assertSame(3, $result);
     }
 }
```

We use [a Rector rule](https://getrector.com/rule-detail/narrow-unused-set-up-defined-property-rector) to handle this for us.

<br>

## 3. Use single Mocking tool

The next step might be a bit advanced, but it's worth the work. In PHP projects that were developed during times of PHPUnit 4-9 (2014-2020), we can see **2+ different mocking frameworks**. Not rarely.

At start, PHPUnit didn't have a decent way to mock object. So PhpSpec was integrated in PHPUnit. As time went by, PHPUnit improved mocking features and eventually [removes in-house support for PhpSpec](https://github.com/sebastianbergmann/phpunit/issues/4141) in PHPUnit 9.

<br>

That's why tests upgrade can lead to exponentially expensive process. Instead of upgrading PHPUnit, we have to upgrade Prophecy and Mockery at the same time.

That is a waste of time and mental energy.

<br>

<div class="text-center">
    <h3>Rule of Thumb &nbsp; 👍</h3>
</div>

<blockquote class="blockquote mt-5 mb-5 text-center">
    Pick a single testing framework you like<br>
    and drop the rest.
</blockquote>

```diff
 {
     "require-dev": {
-        "phpspec/prophecy": "*",
-        "mockery/mockery": "*",
         "phpunit/phpunit": "*"
     }
 }
```

<br>

I wrote about [PhpSpec to PHPUnit migration in separate post](/blog/2019/03/21/how-to-instantly-migrate-phpspec-to-phpunit). You can use this [secret standalone Rector set](https://github.com/rectorphp/custom-phpspec-to-phpunit) to automate most of it.

<br>

## 4. Either mock or a Real service

What type is mocked object? That is the question.

If we mock a `Product` class, it will become:

* a) `Product|MockObject`
* b) `Product&MockObject`
* c) `MockObjet`
* d) `Product`

The *technical answer* is *b)* because a new proxy mock object will extend `MockObject`, that will extend `Product`.

<br>

But **how does it help IDE, Rector and PHPStan**?

```php
$productMock = $this->createMock(Product::class);
```

Do we really call real methods on mock directly?

```php
$productMock->getName();
```

No, we setup mocks and pass mocks object into the real object we test:

```php
$productMock->expects($this->once())
    ->method('getName')
    ->willReturn('Wifi Router');

$productRepository = new ProductRepository([$productMock]);
```

If IDE autocompletes `$product->getName()` it might lead us astray the object is real `Product` object. We want to avoid ambiguity and always separate `MockObject` properties from real classes.

To add more fuel ot the fire, PHPStan fails to resolve type, if [tested `Product` class is `final`](/blog/2019/03/28/how-to-mock-final-classes-in-phpunit).

<br>

Separate `MockObject`s and real classes to give our tests clarity:

```diff
-private MockObject|Product $product;
+private MockObject $product;

-private MockObject|ProductRepository $productRepository;
+private ProductRepository $productRepository;

-private MockObject|Database $database;
+private MockObject $database;
```

Now we see right from the properties, which object is being tested.

<br>

There is [a Rector rule](https://getrector.com/rule-detail/single-mock-property-type-rector) we use to handle this.

<br>

## 5. Use Entity Objects Directly

Last but not least is mocking of simple objects. Let me give you an example:

```php
use PHPUnit\Framework\TestCase;

final class ProductRepositoryTest extends TestCase
{
    public function test(): void
    {
        $productRepository = new ProductRepository();

        $productMock = $this->createMock(Product::class);
        $productMock->method('getName')
            ->willReturn('Latest PHP conference talk');

        $productRepository->save($productMock);

        // ...
    }
}
```

Let's recall one of our goals defined at start of this post:

* remove any unnecessary dependency on the mocking framework, so **any PHP developer can read code**

<br>

What can be improved there? We can use the `Product` object directly:

```diff
 use PHPUnit\Framework\TestCase;

 final class ProductRepositoryTest extends TestCase
 {
     public function test(): void
     {
         $productRepository = new ProductRepository();

-        $productMock = $this->createMock(Product::class);
+        $product = new Product();
-        $productMock->method('getName')
-             ->willReturn('Latest PHP conference talk');
+        $product->setName('Latest PHP conference talk');

         $productRepository->save($productMock);

         // ...
     }
 }
```

This code is more intuitive to read and cheaper to maintain:

* we have type control on `getName()` input
* i we change the method in the future, IDE will update it here as well

<br>

But **how do we know** we can replace the mock with real object here? What if we have another mock line like:

```php
       $resolverMock = $this->createMock(ComplicatedProductNameResolver::class);
       $resolverMock->method('getName')
            ->willReturn('Latest PHP conference talk');
```

This is clearly a service, but `Product` is an entity. **How do we know that?**

It has a marker. Doctrine ORM/ODM entities have an annotation or a PHP 8.0 attribute:

```php
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity()
  */
class Product
{
}
```

<div class="text-center">
    <h3>Rule of Thumb &nbsp; 👍</h3>
</div>

<blockquote class="blockquote mt-5 mb-5 text-center">
Never mock Doctrine ORM/ODM entities.
<br>
Use them directly.
</blockquote>


We use PHPUnit Rector [rule to handle this case](/https://getrector.com/rule-detail/entity-document-create-mock-to-direct-new-rector).

<br>

That's all for today. Get PHPStan and Rector rules on board, and let them work for you. There is more than meets the eyes when it comes to mocks. We'll tackle automated refactoring of simple services in next part.

<br>

Happy coding!
