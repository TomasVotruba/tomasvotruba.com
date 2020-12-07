---
id: 199
title: "Removing Static - There and Back Again"
perex: |
    The more companies I meet, the more I see `static` and `new` everywhere. Not like `new Product`, but rather `new ProductRepository(new Database())`. Not just Laravel, but across all PHP frameworks. I wish frameworks could prevent antipatterns, but they don't, do they?
    <br><br>
    Instead of "refactor all the things" step by step, class by class, I'd **like share my thoughts when exploring full automated path**. I look for feedback to improve this process.
tweet: "New Post on #php 🐘 blog: Removing Static - There and Back Again"
tweet_image: "/assets/images/posts/2019/removing-static/there.jpg"
---

*This process will be (in a more practical and detailed way) part of future [Rector training](https://getrector.org), so you'll be to **solve any problem with your PHP** code regardless the size of your project. Static refactoring is just a very nice example, that happens to be very popular around me nowadays and no-one solves it.*


## 1. Show Code

I usually start with a minimal code snippet possible, that explore the problem. No comments, no types, just the code. This is the real code I'm currently refactoring:

```php
<?php

final class Product
{
    private $price;

    public function __construct(float $price)
    {
        $this->price = $price;
    }

    public function getPrice(): Price
    {
        return new Price($this->price, CurrencyProvider::get());
    }
}

final class Price
{
    private $amount;

    private $currency;

    public function __construct(float $amount, Currency $currency)
    {
        $this->amount = $amount;
        $this->currency = $currency;
    }

    public function getAmount()
    {
        return $this->currency->convertFromCzk($this->amount);
    }
}
```

## 2. Describe the Code

Then I describe the problem with a few words using your common sense. No censorship, just flow of words.

"There is a `Product` object... no, an entity since I can have multiple products. It has *active record pattern* since it creates `Price` in itself. There is also *static service locator* `CurrencyProvider::get()` to get current currency. I have no idea where the currency is set and it can be *overridden* multiple times during code run.

The goal of all this is probably to have price *always* in the same currency. Which is not true, since I can change the currency anytime I want. The price computation is input/output relations - so it should be solved by service, not an entity. I'm confused."


## 3. Break The Code

My favorite part. How can we break this code?

```php
<?php

$product = new Product(100.0);

CurrencyProvider::set('czk');
$product->getPrice(); // 100

CurrencyProvider::set('eur'); // this will be invoked in some other method, where the user needs a special price for newletter in Germany
$product->getPrice(); // ups, 2500?
```

<div class="alert alert-sm alert-danger mt-3">
   1. An entity with the same ID can return different values on different calls of the same method.
</div>

It's like my name would be "Tom" in the morning and "John" in the afternoon.

---

```php
<?php

$product = new Product(100.0);
$product->getPrice(); // Error: 2nd argument of price cannot be null

CurrencyProvider::set('czk');
```

<div class="alert alert-sm alert-danger mt-3">
    2. Due to a static design of <code>CurrencyProvider</code>, we cannot set currency at the single place of application, e.g. container creation, but we have to put it in the "right" place so it doesn't break the code. Here it broke the code because we set it too late.
</div>

---

```php
<?php

$product = new Product(100.0);

$allCurrencies = /* get from database */;
foreach ($allCurrencies as $currency) {
    CurrencyProvider::set($currency);
    echo $product->getPrice();
}

// what is the currency now?
```

<div class="alert alert-sm alert-danger mt-3">
   3. How do I show a price for all the currencies we support?
</div>

## 3. The Ideal World

Now I imagine how I want this code to be designed in an ideal world, with enough time and skills. My goal is to make code work, while minimal as possible, while readable as quickly as possible. So when I leave the company, the person who reads the code will understand it at the same speed as I did.

1. There will be a service that will take care of price computation.

2. It will accept `Product` and `Currency` as an argument.

3. That way it's easy to tests, method parameters are clearly stating dependencies, nothing to surprise.

## 4. Put Ideal World into Code

Now put that ideal into PHP code:

```php
<?php

final class Product
{
    private $price;

    public function __construct(float $price)
    {
        $this->price = $price;
    }

    public function getPrice()
    {
        return $this->price;
    }
}

final class Currency
{
    private $name;

    public function __construct(string $name)
    {
        $this->name = $name;
    }
}

final class PriceCalculator
{
    public function calculatePriceForProductAndCurrency(Product $product, Currency $currency): float
    {
        // ... computing algorithm ...
        return $price;
    }
}
```

And then use:

```php
<?php

final class ProductController
{
    public function render()
    {
        $product = new Product(1000.0);
        $currency = new Currency('czk'); // default will be configured parameters in config.yaml

        $price = $this->priceCalculator->calculatePriceForProductAndCurrency($product, $currency);

        echo $price;
    }
}
```

This is the thought process of most refactorings. It is mostly intuition until now. A small piece of code → the problem → the idea of how code should look like → the solution. Just commit and send for review, right?

<br>

[Martin Fowler](https://stackoverflow.com/a/454012/1348344) once said:

<blockquote class="blockquote">
Refactoring is a controlled technique for improving the design of an existing code base. Its essence is applying <strong>a series of small</strong> behavior-preserving transformations, each of which "too small to be worth doing".<br><br>However the cumulative effect of each of these transformations is quite significant. <strong>By doing them in small steps you reduce the risk of introducing errors</strong>. You also avoid having the system broken while you are carrying out the restructuring - which allows you to gradually refactor a system over an <strong>extended period of time</strong>.
</blockquote>

This worked well for a long period of time. Today I feel confident enough to say **this paradigm is dead** - and it's a good thing!

- "series of small"
- "small steps"
- "extended period of time"

In 2019, we can do refactoring in **[one big step in short period of time](/blog/2019/02/21/how-we-migrated-from-nette-to-symfony-in-3-weeks-part-1/)** - and still get working application in the end.

Clear proof is **PHPStorm refactorings** over the whole code base. They're still dumb compared to human and sometimes cause errors, but [they get better and better each version](https://www.jetbrains.com/phpstorm/whatsnew). Trend beats the status quo. You probably also know **[Php Inspections (EA Extended)](https://github.com/kalessil/phpinspectionsea) plugin** to PHPStorm.

<br>

It's easy to use these tools, but they still tend to solve the most generic problems. Instead of **just blindly using
rules in these tools, we'll learn how to build them** to solve your problem.

<br>

<img src="/assets/images/posts/2019/removing-static/there.jpg" class="img-thumbnail">

## 5. Extract The Journey

Back to the thinking process. This last step might seem a bit weird. We already have the clear code up and running and it's ready to ship. Why would we invest more energy into this? If I don't learn from this, I'm sentencing my future self to do it again in the future. It's funny to watch companies how they go for "a business value", try to delivery features fast, but never pause to realize, that they do mostly repetitive tasks for the same price as the first one. So by this strategy to deliver business value fast, they cut their business value in half.

### ~~Think Big~~ Think Absolute

Instead, I ask myself: "how would I describe the process step by step to a machine, so it could refactor **all PHP code** on Github, Gitlab and the whole world with the same issue to the one in the end?" There could be billions of such cases in the whole-world PHP code base.

If we're able to describe the process, **we'll turn billions of use cases to 1 pattern transformation**.

Imagine you try to fix typoes one-by-one manually. Or you could write a function, that fixes 5 most common typos for the user and hooks it on the Internet and SMS network (regardless of security) - to process every electronic message in the world. Just like that, the world became smarter thanks to you single function.

I disagree with Martin's statement: "Its essence is applying a series of small behavior-preserving transformations". It's not about the behavior of code anymore. Much more important is the pattern in the code. We don't care about `Price`, nor `Currency` (it could be also called `Name` and `Invoice`).

<br>

**Instead we look at "static call in an object".**

<br>

And what we did with that pattern it? Give your ideas in the comments or wait for the next post.

<br>

Happy coding!
