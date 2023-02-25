---
id: 303
title: "How Dangerous is Your Nette Template&nbsp;Assign"
perex: |
    Symfony documentation contains a few ["best practices" that teach people to create bad code](https://matthiasnoback.nl/2014/10/unnecessary-contrapositions-in-the-new-symfony-best-practices/). It's important to talk about them so the framework can improve and thus its community can improve. Do you remember GitHub discussions about autowiring before it became part of Symfony?


    Nette documentation is no different. **Today, we'll look at configuring templates and how to use them better and safer**.

---

## What is the Main Purpose of Template?

What should a template engine do? Render some content to an end-user. Sometimes, it can be a static page; sometimes, there can be parameters. But fundamentally, that's it. Anything else is just fancy syntax sugar.

Let's look at a simple template example:

```html
I'll give 10 Bitcoins to people in need
```

Hmm, maybe the number will change. Let's make a parameter out of it:

```php
$bitcoinCount = 10;
```

```html
<!-- templates/giveaway.latte -->
I'll give {$bitcoinCount} Bitcoins to people in need
```

That was easy!

<br>

Are you curious about how other frameworks handle simple rendering? Me too!

<br>

[**CakePHP 4**](https://book.cakephp.org/4/en/controllers.html#rendering-a-view)

```php
public function giveaway()
{
    $this->set('bitcoinCount', 100);
    return $this->render('templates/giveaway');
}
```

<br>

[**Yii 2**](https://www.yiiframework.com/doc/guide/2.0/en/structure-views#rendering-views)

```php
public function giveaway()
{
    return $this->render('giveaway', [
        'bitcoinCount' => 100,
    ]);
}
```

<br>

[**Laravel 8**](https://laravel.com/docs/8.x/controllers#basic-controllers)

```php
public function giveaway()
{
    return view('giveaway', [
        'bitcoinCount' => 100
    ]);
}
```

<br>

[**Symfony 5**](https://symfony.com/doc/current/templates.html#creating-templates)

```php
public function giveaway()
{
    return $this->render('templates/giveaway.twig', [
        'bitcoinCount' => 100
    ]);
}
```

<br>

And [**Nette 3.1**](https://doc.nette.org/en/3.1/components#toc-rendering)

```php
public function render(): void
{
    $this->template->bitcoinCount = 100;
    $this->template->render(__DIR__ . '/templates/giveaway.latte');
}
```

<br>

What do they have in common?

- every approach is using some name or absolute path as **template identifier**
- the template identifier is passed **first argument of "render" method**

But how are parameters handled? There are 2 groups:

- parameter can be set **anywhere above** the "render" method
- parameters are passed **exactly once** into the "render" method

The 2nd approach is far better than 1st one. Could you guess **9 reasons why**?

## 1. Variable Override Bug

Why don't we use public properties? Because they can be overridden anywhere without us knowing:

```php
final class Product
{
    public $price;
}
```

We have at least setters that we can somehow monitor:

```php
final class Product
{
    private $price;

    public function changePrice($price)
    {
        $this->price = $price;
    }
}
```

You might be thinking - why would anyone make such a rookie mistake? I assure you, **this is one the most hated bug in legacy code**. Single variable change can take days or weeks to find out... if you have to go through 2 500 000 lines of code, you see for the first time.

Private property and change methods are still crappy though:

```php
$product = new Product();

$product->changePrice(100);

// 1000 lines bellow

$product->changePrice(10);
```

So after many years of tears we learned to **use a constructor, where we require the values that cannot be changed**:

```php
final class Product
{
    public function __construct(
        private $price
    ) {
    }
}
```

How is this related to `$this->template`? The same way we can break `Product` price, we can break `$this->template` parameters:

```php
public function render(): void
{
    $this->template->bitcoinCount = 100;

    // 1000 lines bellow

    $this->template->bitcoinCount = 10;

    $this->template->render(__DIR__ . '/templates/giveaway.latte');
}
```

Or more evil example:

```php
public function render(): void
{
    $this->template->bitcoinCount = 100;

    $this->sendNotification();

    $this->template->render(__DIR__ . '/templates/giveaway.latte');
}

// 1000 lines bellow

private function sendNotification(): void
{
    // oh, someone needs to do little unrelated change here
    $this->template->bitcoinCount = 10;
}
```

What is the `$bitcoinCount`? `10` or `100`? It depends on the `$this->template` override mechanism, I guess.

❌

## 2. Call Me... Maybe

With property access like this, we can define parameters... just sometimes:

```php
public function render(): void
{
    if ($this->inGoodMood) {
        $this->template->bitcoinCount = 100;
    }

    $this->template->render(__DIR__ . '/templates/giveaway.latte');
}
```

Quick quiz: **what is the `$bitcoinCount` value in the template?**

- `false`
- `null`
- nothing, use `isset()` to check if exists
- nothing, use `empty()` to check if exists

❌

## 3. Unset or Nullable Variable?

This bug build on previous weakness. Let's say we have a [null-hell](https://afilina.com/null-hell) nullable return:

```php
private function getBitcoinCount(): ?int
{
    // ...
}
```

How can we differentiate between variable set to `null`...

```php
$this->template->bitcoinCount = $this->getBitcoinCount();
```

...and variable that is missing?

```php
if ($this->inGoodMood) {
    $this->template->bitcoinCount = $this->getBitcoinCount();
}
```

❌

## 4. Forgotten Require Variable

The example above would also fail, because the `{$bitcoinCount}` is required in template:

```html
<!-- templates/giveaway.latte -->
I'll give {$bitcoinCount} Bitcoins to people in need
```

How can you **define what is required variable**? In objects and services, we have `__construct()` for that, but here?

❌

## 5. Two Ways to do One Thing

<img src="/assets/images/posts/2021/two_thing_one.jpg" class="img-thumbnail mt-4 mb-2">

Let's say we manage to set variable once in the template. Many months later, a new developer learns about 2nd `render()` parameters and uses them:

```diff
 public function render(): void
 {
     $this->template->bitcoinCount = 100;

-    $this->template->render(__DIR__ . '/templates/giveaway.latte');
+    $this->template->render(__DIR__ . '/templates/giveaway.latte', [
+        'bitcoinCount' => 10
+    ]);
 }
```

What is the final template we'll see?

- A. `I'll give 100 Bitcoins to people in need`
- B. `I'll give 10 Bitcoins to people in need`

❌

## 6. Read It!

As with any other public property, we can read it and modify it:

```php
public function render(): void
{
    $this->template->bitcoinCount = 100;

    if ($this->template->bitcoinCount > 100) {
        $this->template->bitcoinCount += 10;
    }
}
```

Soon we're using `$this->template` for any variable operation.

❌

## 7. Killing Known Types

We know that type of `100` is `int`, so do PHPStan, PHPStorm, and Rector. But once we set it to magical `$this->template`, everybody becomes blind:

```php
public function render(): void
{
    $this->template->bitcoinCount = 100;

    // condition is always true... but who knows?
    if (is_int($this->template->bitcoinCount)) {
        // ...
    }
}
```

❌

## 8. Making Magic more Magical

With Nette 3.0, there has been an attempt to fix the magic with [typed template objects](https://blog.nette.org/en/latte-how-to-use-type-system). The intention to have a typed template is good, but it adds more magic to rendering.

The `$this->template` is not a service anymore, but a value object. That can hold parameters. And also render itself somehow. That's typical example of **[active record anti-pattern](https://en.wikipedia.org/wiki/Active_record_pattern)**. Are you able to give birth yourself? **Not a way to go.**

❌

There is a better way to have both **typed template** and **pass parameters just once**. I'll write about it soon - stay tuned.

## 9. Typos Everywhere

Could you guess, what will happen here?

```php
public function render(): void
{
    $this->template->bitcoinCount = 100;

    if ($this->template->bitcointCount > 10) {
        $this->template->bitcoinCount -= 10;
    }

    // ...
}
```

The condition is obviously a `true`, right?

Well, the second variable *bitcoin**t**Count* does not exists, so we're now `-10` bitcoints.

❌

## Back to Basics

There is one more surprise. How does [Latte documentation](https://latte.nette.org/en/guide#toc-installation-and-usage) look like? **It promotes the clear approach!**

```php
$latte = new Latte\Engine;

$latte->render('template.latte', [
    'items' => ['one', 'two', 'three']
]);
```

So why is every presenter and component using it wrong? Because Occam's razor - people **tend to pick the first solution they see and use it everywhere**.

## The Correct Way to Render Templates With Parameters

What if there is a way to avoid all 7 possible bugs above? Like we don't have enough bug potential in our code-bases.

I know you've already guessed it, but just to Google can find it too. The correct way to render parameters is to **pass them exactly once** as 2nd parameter of `render()` method:

```php
public function render(): void
{
    $this->template->render(__DIR__ . '/templates/giveaway.latte', [
        'bitcoinCount' => 10
    ]);
}
```

Do we work with a value above the render? **Use a variable** to separate templating system from business logic:

```php
public function render(): void
{
    $bitcoinCount = $this->getBitcoinCount();
    if ($bitcoinCount === null) {
        $bitcoinCount = self::MINIMUM_REQUIRED_AMOUNT;
    }

    $this->template->render(__DIR__ . '/templates/giveaway.latte', [
        'bitcoinCount' => $bitcoinCount,
    ]);
}
```

Do we need an optional parameter? **Such code smell** suggests our template architecture is wrong:

- Maybe there should be an extra component?
- Maybe the parameter should be required and have a value of `0`?

<br>

Do you have more magical objects like this one? Please get rid of them before they get rid of your sanity. Now you have nine reasons why :)

<br>

Happy coding!
