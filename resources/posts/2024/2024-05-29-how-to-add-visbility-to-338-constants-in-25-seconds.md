---
id: 410
title: "How to add visibility to 338 constants in 25 seconds"
perex: |
    In PHP, we have classes with methods inside them. Would making all your methods `public` be a good idea? No, because some of them should be used only by the class they're in and not anywhere else.

    What about class constants? PHP 7.1 introduced three types of a class constant visibility: `public`, `protected`, and `private`. We want some constants, such as the client hostname, to be used only in the class, but others, like parameter names, to be used anywhere.

    What if your project has 338 constants without visibility, and you don't want to do them one by one?
---

The class constant's visibility comes in handy to make your code as tight as possible and keep your design clean. These are the options we have:

```php
class SomeConstants
{
    // can be used anywhere
    const PUBLIC_CONST_A = 1;
    public const PUBLIC_CONST_B = 2;

    // current, child or parent class can use
    protected const PROTECTED_CONST = 3;

    // local class only
    private const PRIVATE_CONST = 4;
}
```

While upgrading with ~~legacy~~ successful projects, I only come across projects with all constants `public` by default. It's either a lack of PHP 7.1 feature awareness or avoiding the pain of adding `private` and `protected` manually to every single constant. If it's 10 class constants to handle, it is easy. But going through 50+ constants or even 338 constants is pain.

The downside of this default is the unclear architecture, which allows constants to be used anywhere in the project. New developers who come to the project and don't know the architecture are most likely to use constants all over the project.

Imagine the same area of problems as with public methods. It's like injecting a controller to get access to the Doctrine repository of the `User` class:

```php
$homepageController = $container->get(HomepageController::class);
$doctrine = $homepageController->get('doctrine');
$userRepository = $doctrine->getRepository(User::class);
```

<br>

## Private or Protected?

The easy question is: how to decide if the constant is `private` or `protected`? The private constant is used solely by the class it's being defined in. Not anywhere else.

The protected constant can be used by the class, the child, or the parent (not the best practice).

<br>

Once we have this straightforward algorithm to decide on, we only have to apply it to 338 constants in our project. The hard question is: How can we automate this to apply it to any project with any number of constants?

<br>

## Rule of Thumb: private by default

My approach is straightforward - battle-testing. I go to the project, make all constants `private` with PHPStorm Find & Replace, and see what goes wrong. In production? No, I take the safe path instead. I run PHPStan and see reported errors like:

```bash
Access to private constant SOME_CONSTANT of class App\SomeClass.
```

↓

Then we know we must turn visibility to `public`:

```diff
 namespace App;

 class SomeClass
 {
-    private const SOME_CONSTANT;
+    public const SOME_CONSTANT;
 }
```

<br>

We also look for these errors:

```bash
Access to undefined constant App\SomeClass::ANOTER_CONSTANT
```

This doesn't mean constant does not exist. The constant is defined, but with wrong visibility. Let's fix it to `protected`:

↓

```diff
 namespace App;

 class SomeClass
 {
-    private const ANOTER_CONSTANT;
+    protected const ANOTER_CONSTANT;
 }
```

That's it!

<br>

## Beware this False Positive

Well, there is one more case missed by PHPStan. That's using a particular keyword that allows parent classes to get their child values:

```php
abstract class AbstractRepository
{
    private const TABLE_NAME = null;

    public function getRepository()
    {
        return $this->db->table(static::TABLE_NAME);
    }
}


final class TripRepository extends AbstractRepository
{
    private const TABLE_NAME = 'trips';
}
```

Why is this missed? Because the `AbstractRepository` calls its default constant value, so it's valid - just wrong. We also have to check all the `static::<CONSTANT_NAME>` overloaded by child classes.

<br>

## 25 Seconds to Apply

Now that we have the formula for success, we look for a way to apply it at scale:

* make all constants `private` by default
* run PHPStan and fix visibility to `public` and `protected` where needed
* check `static::<CONSTANT_NAME>` separately and make these `protected`
* commit, push, and create pull-request

<br>

How can we do this with automated tools?

* We can use PHP `str_replace()` to find & replace,
* then we can run PHPStan to get the report with errors,
* we can extract there errors and use `str_replace()` back to `public` or `protected`

So that's what we did.

<br>

You can find a new command in the [swiss-knife](https://github.com/rectorphp/swiss-knife) tool:

```bash
vendor/bin/swiss-knife privatize-constants src tests
```

It performs all the steps above in 25 seconds. Give it a try to tighten your class constants instantly.

<br>

Happy coding!
