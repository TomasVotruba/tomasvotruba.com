---
id: 221
title: "How we Completed Thousands of Missing @var Annotations in a Day"
perex: |
    I'm currently working as Rector consultant for [Spaceflow](https://spaceflow.io/en), Prague-based rising startup with very nice codebase. One of the pre-requisites for Rector is to have code that static analyser can work with. PHPStan that Rector uses depends on `@var` annotations and not every property had that. Well... **over 2500 of them**.
    <br><br>
    I'll show you how we completed them without any manual change of the code and how you can do the same... today.

tweet: "New Post on #php 🐘 blog: How we Completed Thousands of Missing @var Annotations in a Day    #casestudy"
tweet_image: "/assets/images/posts/2019/var/doctrine.png"

updated_since: "August 2020"
updated_message: |
    Updated Rector/ECS YAML to PHP configuration, as current standard.
---

This post has 2 parts:

- 1st is about how we use other parts of class to infer `@var` types of used properties - **good for analytical thinking and pattern-algorithms**
- 2nd is about [how to do it yourself](#do-it-yourself) - **good for your project**

<br>

The latter part increased the **`@var` annotation count from 1790 to 4418**.

Here you can see the whole process in real-time:

<img src="/assets/images/posts/2019/var/rector-final-optimized.gif" alt="How the ECS + Rector change 2500+ missing `@var` annotations" class="img-thumbnail">

<br>

So what will you do when you see property like this?

```php
<?php

class ProductController
{
    private $productRepository;
}
```

### 1. From Constructor Injection Assign

This is the most common case in most projects:

```php
<?php

class ProductController
{
    private $productRepository;

    public function __construct(ProductRepository $productRepository)
    {
        // ah, it's a "ProductRepository"
        $this->productRepository = $productRepository;
    }
}
```

### 2. From `setUp` Assign in Tests

The next biggest group of missing `@var` annotations was in tests. Instead of `__construct` we have`setUp`:

```php
<?php

class BuilderTest extends TestCase
{
    private $factory;

    protected function setUp()
    {
        // ah, it's a "Builder"
        $this->factory = new Builder;
    }
}
```

### 3. From Assign in Constructor

Sometimes constructor is for setting default values:

```php
<?php

class HomeController
{
    private $maxNumberOfGirlfriends;

    public function __construct()
    {
        // ah, it's an "int"
        $this->maxNumberOfGirlfriends = 1;
    }
}
```

So far quite simple, right? 3 files are ok, but imagine doing this for 2500+ properties 🧠🤯

### 4. From Setters and Getters

Some object don't have constructor injection, or are missing the information there, e.g.:

```php
<?php

class Product
{
    private $name;

    public function __construct($name)
    {
         $this->name = $name;
    }
}
```

How can we know, what is `$name`? Well, we don't. Statical analysis can't help here, we can only guess and maybe crash the production code. To solve cases like this **we need dynamical analysis** - data are much better and solid than "probability guessing" of static analysis. Don't worry, I'll write about it in the future with a case study.

But what about this code:

```php
<?php

class Product
{
    private $name;

    public function __construct($name)
    {
         $this->name = $name;
    }

    public function getName(): string
    {
        // now we know it's a "string"
        return $this->name;
    }
}
```

Although it could be also non-string value:

```php
<?php

$product = new Product(5); // this passes without error
```

But the working code `getName(): string` says **it must be `string`**, so we can rely on it.

Also, this is just `@var` annotation not [typed properties](/blog/2018/11/15/how-to-get-php-74-typed-properties-to-your-code-in-few-seconds), so the change cannot break anything.

### 5. From Nullables

Let's take this one step further. Also, let's be more realistic - there are barely any type declarations (PHP 7.0+) out there, so we have to take annotations into account:

```php
<?php

class Product
{
    private $name;

    /**
     * @param string $name
     */
    public function __construct($name = null)
    {
         $this->name = $name;
    }

    /**
     * @param $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }
}
```

What types can be in `$name`?

There is a type `string` everywhere, so probably `string`. But what happens in this case?

```php
<?php

$product = new Product();
```

The `$name` will be actually `null`. So this is final correct code change:

```diff
 <?php

 class Product
 {
+     /**
+      * @var string|null
+      */
      private $name;
 }
```

### 6. From Default Value

Before we get into Doctrine entities, let's get relaxed with a simple case:

```php
<?php

class Victim
{
     private $name = 'Tomas';

     public function getName()
     {
         return $this->name;
     }
}
```

I don't have to tell you the `$name` property is always `string`.


### 7. From all the Other Assigns

What if we have no constructor, no getters, no setters, no default values... are we lost?

This actually happens more often than you think. Take this as just an example, in reality it could be much better, but is often much worse:

```php
<?php

class ProductController
{
    private $activeProduct;

    /**
     * @var ProductRepository
     */
    private $productRepository;

    public function default($id = null)
    {
        if ($id) {
            $this->activeProduct = $this->productRepository->get($id);
        } else {
            $this->activeProduct = $this->productRepository->getMainProduct();
        }

        // ...
    }
}
```

This is hell to do manually. Not in 5 lines-long method, but in 30, 60 or even 100 lines in one method (don't forget you have to deliver feature and don't have paid time to play with stupid useless `@var` annotation).

The process has just 2 step:

- go through all `$this->activeProduct = X`
- resolve type for the `X` expression

And that's it:

```diff
 <?php

 class ProductController
 {
+    /**
+     * @var Product
+     */
     private $activeProduct;

     // ...
 }
```

We could argue if the `$activeProduct` could be also `null`. It can, but in our case base it never was. But if you find such a case that is invalid for your code, [please report an issue](https://github.com/rectorphp/rector/issues/new?template=1_Bug_report.md) to improve this Rector rule.

### 8. From Doctrine Column Annotations

Missing Doctrine annotations are hard to spot, because there is always *some* annotation. Just not the one we need:

```php
<?php

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 */
class Product
{
    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $content;
}
```

Pretty easy, right?

```diff
 /**
  * @ORM\Column(type="text", nullable=true)
+ * @var text|null
  */
  private $content;
```

Ups, not so fast. Database types and PHP scalar types are not 1:1 compatible. To pick a few: *longblob*, *decimal*, *set*, *time* or *year* (mini-quiz: `int` or `DateTimeInterface`)?

`string` is the correct choice here:

```diff
 /**
  * @ORM\Column(type="text", nullable=true)
+ * @var string|null
  */
  private $content;
```

### 9. From Doctrine Relation Annotations

Let's finish with the most complex case, yet brutally common. What do we mean by Doctrine Relation annotations?

```php
<?php

class Product
{
    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\Storage")
     */
    private $storage;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Category", inversedBy="products")
     * @ORM\JoinColumn(name="category_id", referencedColumnName="id", nullable=true)
     */
    private $category;
}
```

The docblock looks full and rich for useful content, so it's hard to spot the missing `@var` in there.

You probably know, that `OneToMany` and `ManyToMany` are not just an array of objects, but also `Doctrine\Common\Collections\Collection`.

So what `@var` we'd add here?

```diff
 <?php

 class Product
 {
     /**
      * @ORM\ManyToMany(targetEntity="App\Entity\Storage")
+     * @var \App\Entity\Storage[]|\Doctrine\Common\Collections\Collection
      */
     private $storage;

     /**
      * @ORM\ManyToOne(targetEntity="App\Entity\Category", inversedBy="products")
      * @ORM\JoinColumn(name="category_id", referencedColumnName="id", nullable=true)
+     * @var \App\Entity\Category|null
      */
     private $category;
 }
```

That's it! Did you find any more cases that we forgot? Share with us in the comments.

<br>

Now the 2nd practical part - **your project** ↓

## Do It Yourself

All above is not limited just to our project. You can use it too!

I'll guide you from step zero to final merge of pull-request, **so the gif will not be just a picture, but a real change in your project**.

**Install Rector + ECS**

```bash
composer require rector/rector --dev
composer require symplify/easy-coding-standard --dev
composer require slevomat/coding-standard --dev
```

**Run ECS to check missing `@var` annotations at properties**

```php
<?php

// ecs.php

declare(strict_types=1);

use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Symplify\EasyCodingStandard\Configuration\Option;

return static function (ContainerConfigurator $containerConfigurator): void {
    $services = $containerConfigurator->services();
    // every property should have @var annotation
    $services->set(SlevomatCodingStandard\Sniffs\TypeHints\TypeHintDeclarationSniff::class);

    $parameters = $containerConfigurator->parameters();
    $parameters->set(Option::SKIP, [
        //  this part is needed, because `TypeHintDeclarationSniff` is actually mix of 7 rules we don't need
        // (they also delete code, so be sure to have this section here)
        'SlevomatCodingStandard\Sniffs\TypeHints\TypeHintDeclarationSniff.UselessDocComment' => null,
        'SlevomatCodingStandard\Sniffs\TypeHints\TypeHintDeclarationSniff.MissingTraversablePropertyTypeHintSpecification' => null,
        'SlevomatCodingStandard\Sniffs\TypeHints\TypeHintDeclarationSniff.MissingTraversableReturnTypeHintSpecification' => null,
        'SlevomatCodingStandard\Sniffs\TypeHints\TypeHintDeclarationSniff.MissingTraversableParameterTypeHintSpecification' => null,
        'SlevomatCodingStandard\Sniffs\TypeHints\TypeHintDeclarationSniff.MissingParameterTypeHint' => null,
        'SlevomatCodingStandard\Sniffs\TypeHints\TypeHintDeclarationSniff.MissingReturnTypeHint' => null,
    ]);
};
```

Run coding standard on your code:

```bash
vendor/bin/ecs check src tests
```

↓

2 500 errors? Not a problem.

<br>

**1. Configure Rector**

```php
<?php

// rector.php

declare(strict_types=1);

use Rector\TypeDeclaration\Rector\Property\PropertyTypeDeclarationRector;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

return function (ContainerConfigurator $containerConfigurator): void {
    $services = $containerConfigurator->services();
    $services->set(PropertyTypeDeclarationRector::class);
};
```

**2. Run Rector to fix your code**

```bash
vendor/bin/rector process src tests
```

Then run coding standard again, to see how useful Rector was:

```bash
vendor/bin/ecs check src tests
```

- 0 errors? Congrats and enjoy your vacation :)

- 1+ errors? Create an [issue with missed PHP code snippet](https://github.com/rectorphp/rector/issues/new?template=1_Bug_report.md). We'll look at it and add support for it to Rector if possible.

<br>

<blockquote class="blockquote">
    I hope you'll find this useful as we did!
</blockquote>

<br>

Happy coding!
