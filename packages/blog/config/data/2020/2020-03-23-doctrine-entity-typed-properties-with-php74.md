---
id: 244
title: "Doctrine Entity Typed Properties With PHP 7.4"
perex: |
    Recently we've upgraded our [Czech PHP community website](https://pehapkari.cz) to PHP 7.4. As a side effect, it broke most of our entities.
    Do you love how making language more strict reveals weak points in your code just by using it?
    <br>
    <br>
    Today we'll look at **the impact of typed properties on weak points of Doctrine entities and how to solve them**.

updated_since: "November 2020"
updated_message: |
    Switch from deprecated `--set` option to `rector.php` config.
    Updated with **PHPStorm and PHPStan friendly** `Collection` syntax and [Rector rule](https://github.com/rectorphp/rector/pull/4442) that handles the change for you.

tweet: "New Post on #php 🐘 blog: #doctrine Entity Typed Properties With PHP 7.4"
tweet_image: "/assets/images/posts/2020/typed_doctrine_properties_collection.png"
---

## The Collections

In the Czech PHP community, we have skilled trainers that share their knowledge with others on training. We help them to share the knowledge by handling the tedious organization processes for them and let them enjoy the training day itself.

Each trainer has many trainings, so the `Trainer` entity looks like this in PHP 7.3-:

```php
<?php

declare(strict_types=1);

namespace App\Entity;

use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 */
class Trainer
{
    /**
     * @ORM\OneToMany(targetEntity=Training::class, mappedBy="trainer")
     * @var Collection|Trainer[]
     */
    private $trainings = [];

    /**
     * @param Collection<int, Training>|Training[] $collection
     */
    public function setTrainings(array $collection): void
    {
        $this->trainings = $collection;
    }

    /**
     * @return Collection<int, Training>|Training[]
     */
    public function getTrainings(): iterable
    {
        return $this->trainings;
    }
}
```

What can we say about this code?

- works in PHP 7.4
- provides IDE enough information about types for autocomplete
- makes PHPStan happy with types
- ...broke with PHP 7.4 typed properties :)

How do we **add property types without breaking everything**?

## 1. The Property

In PHP 7.4, **the property is the king** - the rest of the code has to respect the type, and it's the default value. Let's start with that.


```diff
 /**
  * @ORM\OneToMany(targetEntity=Training::class, mappedBy="trainer")
  * @var Collection|Trainer[]
  */
-private $trainings = [];
+private array $trainings = [];
+private iterable $trainings = [];
+private Collection $trainings = [];
```

What is the type here?

- 1) `array $trainings = [];`
- 2) `iterable $trainings = [];`
- 3) `Collection $trainings = [];`

Pick one...

<br>
<br>
<br>

```diff
 /**
  * @ORM\OneToMany(targetEntity=Training::class, mappedBy="trainer")
  * @var Collection<int, Trainer>|Trainer[]
  */
-private $trainings = [];
+private Collection $trainings = [];
```

This one worked the best (= code worked).

But PHP 7.4 now complains that the `Collection` object cannot be `[]` by default.

```diff
 /**
  * @ORM\OneToMany(targetEntity=Training::class, mappedBy="trainer")
  * @var Collection<int, Trainer>|Trainer[]
  */
-private Collection $trainings = [];
+private Collection $trainings;
```

But PHP 7.4 now complains that it's `null` by default, so it has to nullable.

<blockquote class="blockquote text-center">
If it's not enforced, nobody cares.
</blockquote>

Here is where *best practices* become defaults. Do you know the "[initialize collections in the constructor](https://www.doctrine-project.org/projects/doctrine-orm/en/2.7/reference/best-practices.html#initialize-collections-in-the-constructor)" best practice?

<img src="/assets/images/posts/2020/typed_doctrine_properties_best_practise.png" class="img-thumbnail">

This was an optional improvement to help Doctrine work in a more realiable way. Well, it *was*.
Now we **have to use it** to make our code work:

```diff
 <?php

 declare(strict_types=1);

 namespace App\Entity;

 use Doctrine\Common\Collections\ArrayCollection;
+use Doctrine\Common\Collections\Collection;
 use Doctrine\ORM\Mapping as ORM;

 /**
  * @ORM\Entity
  */
 class Trainer
 {
     // ...

+    public function __construct()
+    {
+        $this->trainings = new ArrayCollection();
+    }

     // ...
 }
```

All right, we have the correct type, it's initialized in the constructor.

Our property is ready!

<br>

## 2. Getter Method?

```php
<?php

// ...

/**
 * @return Collection<int, Training>|Training[]
 */
public function getTrainings(): iterable
{
    return $this->trainings;
}
```

What about the return type? Look at the property type:

```diff
 <?php

 /**
  * @return Collection<int, Training>|Training[]
  */
-public function getTrainings(): iterable
+public function getTrainings(): Collection
 {
     return $this->trainings;
 }
```

And we're ready to go!

## 3. Setter Method?

I bet you handled this already from the top of your head, so let's compare:

```diff
 <?php

 /**
  * @param Collection<int, Training>|Training[] $trainings
  */
-public function setTrainings(array $trainings): void
+public function setTrainings(Collection $trainings): void
 {
     $this->trainings = $trainings;
 }
```

<br>

Do you want to see real code? Look at the full pull request:

<a href="https://github.com/pehapkari/pehapkari.cz/pull/297/files">
<img src="/assets/images/posts/2020/typed_doctrine_properties_collection.png" class="img-thumbnail">
</a>

## EasyAdminBundle?

Have you tried [EasyAdminBundle](https://github.com/EasyCorp/EasyAdminBundle) to delegate your full administration? If not, give it go.

**It creates data grids, forms, edit/update/add/delete controllers. All this with simple and beautiful UX. All you need to do is define your entities and register them in YAML config.** I love it!

<br>

Huge thanks to [Javier Eguiluz](https://github.com/javiereguiluz), creator and maintainer of this bundle, who's also behind amazing **690&nbsp;issues** of [Weeks of Symfony](https://symfony.com/blog/category/a-week-of-symfony).

<br>

To make this all happen, the design choice is to allow every property to be nullable. Some people disagree and try to make EasyAdminBundle work with value objects. But they fail by killing the simplicity and re-inventing admin again.

<blockquote class="blockquote text-center">
There are no best solutions, there are just trade offs.
</blockquote>

So what does *every property is nullable* mean for PHP 7.4 typed properties?

```diff
<?php

declare(strict_types=1);

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 */
class Training
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
-    * @var int
     */
-   private $id;
+   private ?int $id = null;

    // ...
}
```

Do you prefer code over text? [Here is the PHP 7.4 upgrade pull-request](https://github.com/pehapkari/pehapkari.cz/pull/297/files).

## Upgrade Instantly with Rector

I did not do the upgrade pull-request myself (I'm way too lazy for that), [Rector](https://getrector.org) did. Thanks to testing out Rector on Doctrine entities, its PHP 7.4 set got much more precise.

**Do you want to see how Rector can upgrade your code?**

1. Install Rector
```bash
composer require rector/rector --dev
```

2. Create `rector.php` config

```php
// rector.php
use Rector\Core\Configuration\Option;
use Rector\Set\ValueObject\SetList;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

return static function (ContainerConfigurator $containerConfigurator): void {
    $parameters = $containerConfigurator->parameters();
    $parameters->set(Option::SETS, [
        SetList::PHP_74,
        // Protip: Do you want to update your `Collection` syntax for PHPStorm and PHPStan friendly?
        SetList::DOCTRINE_CODE_QUALITY,
    ]);
};
```

If you got any troubles, [let us know on GitHub](https://github.com/rectorphp/rector/issues/new/choose). That's all, folks.

<br>

Happy coding!
