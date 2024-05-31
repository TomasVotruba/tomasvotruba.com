---
id: 410
title: "How to add visibility to 338 constants in 25 seconds"
perex: |
    In PHP we have classes with methods inside them. Would make all your methods `public`? No, because some of them should be used only by the class they're in, and not anywhere else.

    What about class constants? PHP 7.1 introduced class constant visibility - `public`, `protected` and `private`. We want some constants, e.g. client hostname, to be used only in the class, but other like parameter names to be used anywhere.

    What if your project has 338 constants without visibility, and you don't want to do one by one?
---

The class constant visibility comes handy to make your code as tight as possible and keep your design clean. These are the options we have:

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

While upgrading with ~~legacy~~ successful projects, I only come across projects that have all constants `public` by default. It's either lack of PHP 7.1 feature awareness or avoiding pain of adding `private` and `protected` manually to every single constant. If it's 10 class constants to handle, is easy. But going through 50+ constants or even 338 constants is pain.

The downside of this default is unclear architecture that allows to use of constants anywhere in the project. It's often missued by new developers that come to project and don't know the architecture well.

Imagine the same area of problems as with public methods. It's like injecting controller to get access to Doctrine repository of `User` class:

```php
$homepageController = $container->get(HomepageController::class);
$doctrine = $homepageController->get('doctrine');
$userRepository = $doctrine->getRepository(User::class);
```

<br>

## Private or Protected?

The easy question is: how to decide if constant is `private` or `protected`? The private constant is used solely by the class it's being defined in. Not anywhere else.

The protected constant can be used by the class, the child or the parent one (not the best practise).

<br>

Once we have this clear algorithm to decide, we only have to apply it to 338 constants in our project. The hard question is: how to **automate this to apply it on any project with any amount of constants**?

<br>

## Rule of Thumb: private by default

My approach is really simple - battle-testing. I go to the project, make all constants `private` with PHPStorm Find & Replace and see what goes wrong. In production? No, I take the safe path instead. I run PHPStan and see reported errors like:

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

Well, there is one more case that is missed by PHPStan. That's using a special keyword that allows parent classes to get their child values:

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

Why is this missed? Because the `AbstractRepository` calls its own default constant value, so it's valid, just wrong. We have to check all the `static::<CONSTANT_NAME>` overloaded by child classes as well.

<br>

## 25 Seconds to Apply

Now that we have the formula to success, we look for a way to apply it at scale:

* make all constants `private` by default
* run PHPStan and fix visibility to `public` and `protected` where needed
* check `static::<CONSTANT_NAME>` separately and make these `protected`
* commit, push and create pull-request

<br>

How can we to this with automated tools?

* We can use PHP `str_replace()` to find & replace,
* then we can run PHPStan to get the report with errors,
* we can extract there errors and use `str_replace()` back to `public` or `protected`

So that's what we did.

<br>

You can find a new command in [swiss-knife](https://github.com/rectorphp/swiss-knife) tool:

```bash
vendor/bin/swiss-knife privatize-constants src tests
```

It does all the steps above in 25 seconds. Give it a go to make your class constants tight in instant.

<br>

Happy coding!
