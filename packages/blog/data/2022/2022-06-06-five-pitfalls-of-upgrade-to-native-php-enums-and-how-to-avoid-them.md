---
id: 359
title: "5 Pitfalls of Upgrade to Native PHP&nbsp;Enums and How&nbsp;to&nbsp;Avoid&nbsp;Them"
perex: |
    Native PHP Enums came almost a year ago in PHP 8.1. It's pretty easy to add a new enum. But how do we handle the old hacks we used to use before enums were legal in PHP land? MyCLabs, Spatie, or constants list.
    <br><br>
    Today we'll upgrade them all. Is it a simple constant to enum replace? Surprisingly, there are **few blind paths we have to be aware of**.
---

## The MyClabs or Spatie Early Access

Before PHP 8.1, the best way to get enums to a code base was to use [Spatie](https://github.com/spatie/enum) or [MyClabs](https://github.com/myclabs/php-enum) packages. They provided an abstract class, `Enum`, that provided a set of methods to validate used constants. These constants could be public or faked as static methods:

```php
use Spatie\Enum\Enum;
// or
// use MyCLabs\Enum\Enum;

/**
 * @method static self draft()
 * @method static self published()
 */
final class PostStatusEnum extends Enum
{
}
```

We can also use a different approach for refactoring from "enums" from entities called constant list. It's "enums for poor people" that do not depend on any 3rd party package:

```php
final class PostStatusEnum
{
    public const DRAFT = 'draft';

    public const PUBLISHED = 'published';
}
```

The class has different parent classes, but the way we use them is identical.
Let's dive into 5 pitfalls of upgrade to native enums. It is basically just 2 patterns to be careful about, but 5 places they can go wrong. If we miss just one of them, the whole upgrade can collapse. Let's dive in and get through.

<br>

Let's say we have a `Post` class with `getStatus()` method. In the project, we use the "constant static call" fake to compare the value with post status:

```php
if ($post->getStatus() === Post::DRAFT()) {
   echo 'Not yet published';
}
```

<br>

### Pre-enum to Enum?

How should we upgrade this comparison to native PHP enums?

```diff
-($post->getStatus() === Post::DRAFT()) {
+($post->getStatus() === Post::DRAFT) {
```

Is it correct? From this code of view, it is. But what does the `getStatus()` method return?

## Pitfall #1: Method Return Types

We upgraded the comparison above; now it's time to look at the `getStatus()` method.

It's a common standard that the "pre-enum" value is just a lowercased enum value. Method and constant can be written only as string, so the type is usually a `string`. If we use this convention, we could use it as a return type, right?

```php
final class Post
{
    // ...

    public function getStatus(): string
    {
        return $this->status;
    }
}
```

This is technically correct but far from being helpful or domain correct. To define post status, we use an enum with exactly 2 values - "draft" and "published". But all we require here is any `string` type. Imagine all the words you could put into Google.

<br>

Instead, we should make it explicit, **this method only returns these 2 constants**.

How? Fortunately, we have static analysis tools that solve precisely this case:

```php
    /**
     * @return Status::DRAFT|Status::PUBLISHED
     */
    public function getStatus(): string
    {
        return $this->status;
    }
```

<br>

**Now the PHPStan and Rector know**, the return type of `getStatus()` has only 2 exact values. If we add new status in the future, we'd have to extend all docblocks everywhere. That's wasted time and space for bugs.

<br>

Instead, we can use the shortcut that stands for all "pre-enum" values available:

```php
    /**
     * @return Status::*
     */
    public function getStatus(): string
    {
        return $this->status;
    }
```

Now the PHPStan knows the exact type of this return is one of the enums.

### Pre-enum to Enum?

If PHPStan knowns it, now it's easy to upgrade:

```diff
-    /**
-     * @return Status::*
-     */
-    public function getStatus(): string
+    public function getStatus(): Status
     {
         return $this->status;
     }
```

👍

This way, you can use PHPStan and Rector rules to automate your upgrade.

<br>

But not only that, but it also **gives you code narrow context that can propagate to other calls**. Even if you stay on PHP 8.0 and won't use enums, your code base still benefits from enum-like::* types:

```php
$copyPost->changeStatus($post->getStatus());
```

<br>

What is the param of the `changeStatus()` method?

## Pitfall #2: Method Param Types

This is similar to the previous case, just in param types:

```php
final class Post
{
    public function changeStatus(string $status)
    {
        $this->status = $status;
    }
}
```

<br>

Here, we again know the status is not just a `string` but one of the provided constants. Let's put this knowledge into the code:

```php
    /**
     * @param Status::* $status
     */
    public function changeStatus(string $status)
    {
        $this->status = $status;
    }
}
```

👍

### Pre-enum to Enum?

To upgrade param, just inline the param docblock to param type declaration:

```diff
-   /**
-    * @param Status::* $status
-    */
-   public function changeStatus(string $status)
+   public function changeStatus(Status $status)
    {
        $this->status = $status;
    }
}
```

<br>

## Pitfall #3: Property Type

Last but not least, the getter and changer methods have one thing in common. They access a property:

```php
final class Post
{
    /**
     * @var string
     */
    private $status;
}
```

<br>

We know the status is not a string but one of the enum-like values:

```php
final class Post
{
    /**
     * @var Status::*|null
     */
    private string|null $status = null;
}
```

👍

### Pre-enum to Enum?

By now, you probably know the upgrade path by heart:

```diff
 final class Post
 {
-    /**
-     * @var Status::*|null
-     */
-    private string|null $status = null;
+    private Status|null $status = null;
}
```

<br>

## Pitfall #4: Input Boundary

The post has status value, and we can now use it safely in the whole PHP code base. Setters, getters, and property types are covered. Are we finished with the upgrade? Depends. How can we upgrade this method?

```php
final class PostController
{
    /**
     * @param Status::* $status
     */
    public function actionPostList(string $status)
    {
        // ...
    }
}
```

<br>

We've upgraded param types before. Let's apply the same approach here:

```diff
-   /**
-    * @param Status::* $status
-    */
-   public function actionPostList(string $status)
+   public function actionPostList(Status $status)
    {
        // ...
    }
```

<br>

We refresh the `/post/post-list/?status=draft` page and... the controller action crashes. The param takes the "draft" string value, but the method expects a `Status` enum.

### Pre-enum to Enum?

It depends on the framework you use, of course. In the future, we expect Symfony param converter, which will similarly handle these cases [it handled myclabs/enums](https://github.com/Ex3v/MyCLabsEnumParamConverter).

But for now, we have to **convert the scalar value** to enum:

```php
    /**
     * @param Status::* $status
     */
    public function actionPostList(string $status)
    {
        $status = Status::from($status);
        // ...
    }
```

👍

<br>

We must approach input values **with the same care as REST API calls**.
In the same way, we convert API scalar values from scalar to value objects; we have to convert string/integer values to enums.

<br>

## Pitfall #5: Output Boundary

Before I publish this post, I prepare a draft to review and save it (to a GitHub pull request):

```php
$post = new Post('Five Pitfalls of Upgrade to native PHP Enums', Status::DRAFT);
$this->postRepository->save($post);
```

<br>

It will be published only after review and Grammarly checks.

We have a title, enum, and strictly typed param, property, and return types. Everything work on our side... but **how do
our external storage handle enums**? We talk about databases.

<br>

### Pre-enum to Enum?

Doctrine entities support enums [since `doctrine/orm` 2.11](https://www.doctrine-project.org/2022/01/11/orm-2.11.html). All we have to do is to upgrade the type to `enumType`:

```diff
 use Doctrine\ORM\Mapping\Entity;
 use Doctrine\ORM\Mapping\Column;

 #[Entity]
 final class Post
 {
-    #[Column(type: 'string')]
+    #[Column(type: 'string', enumType: Status::class)]
     private $status;
 }
```

👍

<br>

In case of different ORM or database layers, check "enum support" in the package documentation.

<br>

## Automate with Rector and Prepare for Future with PHPStan

Check Rector rules, that handles [Spatie enum class](https://github.com/rectorphp/rector/blob/main/docs/rector_rules_overview.md#spatieenumclasstoenumrector) and [MyClabs enum class](https://github.com/rectorphp/rector/blob/main/docs/rector_rules_overview.md#myclabsclasstoenumrector). Do you use constant lists? [Rector covers them too](https://github.com/rectorphp/rector/blob/main/docs/rector_rules_overview.md#constantlistclasstoenumrector).

<br>

**Are you stuck on PHP 8.0** for a while? Get your code-base strict and ready [with PHPStan enum-like `type::*`](https://phpstan.org/writing-php-code/phpdoc-types#literals-and-constants). Your future upgrade will be a piece of cake, and the types you pass around will shine bright with **narrow context**.

---

Have you come across different problems? Let me know in the comments so we can learn together.

<br>

Happy coding!
