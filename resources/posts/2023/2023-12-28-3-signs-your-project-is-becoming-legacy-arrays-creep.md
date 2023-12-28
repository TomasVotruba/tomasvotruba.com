---
id: 399
title: "3 Signs Your Project is Becoming Legacy - Arrays Creep"
perex: |
    In [the first post](/blog/3-signs-your-project-is-becoming-legacy-and-how-to-avoid-them), we looked at the long-term effects of our decisions. Turning a legacy project into a fresh one is a matter of the "just do it" approach.

    But there are 3 things we should take with care even if our project seems outside the legacy project category. First of those are arrays.
---

Imagine you're visiting friends for Christmas in a distant country. It's 5 hour's drive from the airport, and they live in a small village with a population of less than 10 000 people. Public transport is complicated, so you borrow a car is a convenient tool for such a travel.

You head on the road; half is highway and a straight trip. It's going well. It's crowded around holidays but still a driveable trip. The second half is an entirely different experience.

There is a left or right turn every 3 minutes. You have to stop to give away to other cars and check if you are on the main road and have a way. You have to speed from 0 to 50 and back to 0 most of the way. The fluency of the highway is gone, and so is your energy and joy from the ride.

<br>

To add a tiny grain of salt: **There are no signs on the road.** It's not clear who goes first on the crossing. It's getting dark, and you're getting tired and frustrated.

Finally, you arrive at the destination. In the end, it took you 3 hours. Wow, what a long drive, "it must be far away," you think. You check the map and see the highway only took 50 % of the distance, **but the other 50 % on small roads took 2,5 hours**.

<br>
<br>

<div class="text-center">
    <img src="https://betterexplained.com/wp-content/uploads/2016/08/Interstate_Highway_plan_October_1_1970.jpg" style="max-width: 40em" class="img-thumbnail">

<br>
<em>Going from Los Angeles to New York starts easy but gets more and more complex as you go.</em>
</div>

<br>
<br>


This is how working with an array in the code feels like and affects the cognitive system. You're trying to figure out this controller method; it accepts an array, and it's passing an array to another service method call... 3 hours later, you figure out you are not sure what the other service method call really accepts, and you're tired.

**That's not how working code has to look like**. I've seen a couple of hundred codebases and various areas from internal CRM over huge e-commerce, open-source projects, and tiny pet projects. The code complexity has nothing to do with the project area or size. It's about making the decision and holding it.

<br>

## Rule of Single Place

Working with no arrays, only typed objects and collections, would be beautiful. Yet, internet API is built on string transfers - whether JSON, XML, or bare URLs.

The entry-level API **are mostly controllers that accept scalars**. **That's the only place where arrays have to be tolerated**. If you're lucky to run on Symfony or Laravel and you trust your external data providers, there are [request typed objects](https://stackoverflow.com/a/55632295/1348344).

**Every other place in the codebase is optional**. In other words, in every other place than controllers, **we decide** if we use strings known type or go for weak array-mixed type.

Decide well, as compound interest is coming.

<br>

## How to deal with Arrays?

Okay, we have arrays in our project and would love to have more joyful work. We want to drive on the highway every day just for 30 minutes instead of being stuck for 2,5 hours on tiny roads.

First, we make sure the array **really contains the item you expect it to contain**:

```php
public function __construct(array $codes)
{
    $this->codes = $codes;
}
```

"Really" doesn't mean your colleague tells you, "It's obviously string," or you "guess it." Really = I'm 100 % sure. It's the technical result, not a personal opinion.

<br>

After 2 hours of digging into code, we learn that:

* in some places, we pass `string[]`,
* in another `CountryCode[]` value object,
* in another `array<string|null>`
* and in another `array<string|false>`

You want to quit such a project, right? Wait, there is another a way out.

<br>

You might be thinking, "Let's refactor the array to collections." It is also a way, but it takes more energy, more studying, and most importantly - much more than 1 line.

**Our job is to deliver for a reasonable price, so I always aim for cost-effective solutions.**


## The Single Line

I learned the following approach from a [friend of mine](https://www.outofdark.com/) about 10 years ago, way before PHP 7.0 with type declarations was released. Simple but effective.

**Validate with assert**. I don't mean the native `assert()` native function that is often disabled in php config. I mean assert PHP package, like `webmozart/assert`, that fails hard with an exception not correct:

```php
use Webmozart\Assert\Assert;

public function __construct(array $codes)
{
    Assert::allString($codes);
    $this->codes = $codes;
}
```

They used this approach 10 years ago and I was thinking "lol, what an anxious approach to coding, they're so afraid they have to check everything".

Little did I know, nowadays, with similar projects, **I'm the one who checks everything because I have to manually verify the exact type of each variable**. Those guys are already working on the 20th project and keep delivering.

This takes about **half of our focus while working with projects** in [Rector upgrade team](https://getrector.com/hire-team) projects.

<br>

<div class="text-center mt-5 mb-5">
<img src="https://pbs.twimg.com/media/FH83m_aX0AIjFAX?format=jpg&name=medium" class="img-thumbnail">

<em>Your past decisions got your codebase here. But today's choices will affect its destiny.<br><a href="https://twitter.com/waitbutwhy/status/1476962460049584136/photo/1">From Waitbutwhy</a></em>
</div>


## Compound Benefits

What benefits do we get from this single line? Our IDE is now smarter and can suggest method calls if we iterate through the `$codes` property.

There is more:

* our PHPStan got smarter - it can pick up the type thanks to [webmozart extension](https://packagist.org/packages/phpstan/phpstan-webmozart-assert)
* our Rector run got **more powerful**, as it can complete further type declarations with 100 % accuracy

```diff
-public function getFirst()
+public function getFirst(): ?string
 {
    $firstCode = array_pop($this->codes);
    return $firstCode;
 }
```

What about us, developers? Now **we know now we're on the highway and go ahead to create the feature we want**.

<br>

## Rule of a Thumb

<blockquote class="blockquote mt-5 mb-5 text-center">
In house construction, when you extend a 5-floor building with 2 more floors, you must strengthen the base accordingly.
<br>
Or you risk it will all collapse on your head.
</blockquote>

**Don't be greedy and rush - be safe and robust**. Apply the same for the code.

<blockquote class="blockquote mt-5 mb-5 text-center">
As your project grows, make the basis - not the business layer - stronger to support the growth.
</blockquote>

<br>

## Real-life Examples

I'll share a few code snippets we meet every week while working with our clients, and how we turn them into safe and joyful code.

<br>

## 1. Return Array with "Results"

Sometimes, we need to calculate a result for an input. At first, it's simple return of a single `float` value:

```php
public function calculateTripPrice(array $visitors): float
{
    // ...

    return $price;
}
```

<br>

Our company grows and becomes a VAT payer. Now we need to include the price with VAT and VAT itself as well.

```php
public function calculateTripPrice(array $visitors): array
{
    // ...

    return [$price, $priceWithVat, $vat];
}
```

<br>

We've just turned a single `float` type we could 100 % trust into a `mixed[]` array we have to hope has the correct values in it.

```php
/** @var mixed, mixed, mixed ... */
[$price, $priceWithVat, $vat] = $this->calculateTripPrice($visitors);
```

<br>

### How to improve the code?

To save the hope and other wishful thinking, we can use a **result value object**:

```php
final class CalculationResult
{
    public function __construct(
        private readonly float $price,
        private readonly float $priceWithVat,
        private readonly float $vat
    ) {
    }

    // getters
}
```

↓

```php
public function calculateTripPrice(array $visitors): CalculationResult
{
    // ...

    return new CalculationResult($price, $priceWithVat, $vat);
}
```

<br>

**Now get exact data anywhere** we pass the result object into:

```php
$calculationResult = $this->calculateTripPrice($visitors);

// all of these are 100 % floats
$calculationResult->getPrice();
$calculationResult->getPriceWithVat();
$calculationResult->getPriceVat();

```

## 👍

<br>

## 2. Array has More than 1 sole Item

If we have `string` names of GPT models, it would be easy to validate it:

```php
\Webmozart\Assert\Assert::allString($gptModels);
```

<br>

As the project grows, we want to add their release date and company:

```php
return [
    'gpt' => [
        'name' => 'GPT-3',
        'release_date' => '2020-06-11',
        'company' => 'OpenAI',
    ]
];
```

<br>

We **turned a simple array we can validate into structured data** with 3 keys. We could validate those as well, right?

```php
use Webmozart\Assert\Assert;

$gptModels = $this->getGptModels();

Assert::allKeyExists($gptModels, ['name', 'release_date', 'company']);
Assert::allString($gptModels['name']);
Assert::allString($gptModels['company']);
Assert::allDate($gptModels['release_date']);
```

 <br>

**That's a mess**, and we must repeat it every time we use the `$gptModels` array. I'll do this once to make my IDE happy and then ignore errors in PHPStan.

<br>

The solution must be **reliable and simple at the same time**.

<br>

### How to improve the code?

Let's switch to a value object:

```php
final class GptModel
{
    public function __construct(
        private readonly string $name,
        private readonly DateTime $releaseDate,
        private readonly string $company
    ) {
    }

    // getters
}
```

## 👍

<br>

## 3. Array Has 2 Types

This case is the most common because, at the moment code is written, it seems like too little to worry about:

```php
final class RouteMapper
{
    public function createRouteData(array $params): array
    {
        return ['route' => 'home', 'params' => $params]
    }
}
```

<br>

What if someone creates a typo in one of the keys?

```php
return ['route' => 'home', 'param' => $params]
```

<br>

Or nests too much?

```php
return [['route' => 'home', 'params' => $params]]
```

<br>

Why not add an optional parameter?

```php
return ['route' => 'home', 'params' => $params, 'secured' => 1]]
```

<br>

We can't be sure what we get back when we call the method:

```php
$routeData = $this->routeMapper->crateRouteData();
```

We have to check it every time we use it.

**This creates a problem** for you, PHP tooling, and IDE. We work again with the `mixed[]` type in the place where the result is returned. We've just removed all the traffic signs from our codebase.

<br>

### How to improve the code?

Let's avoid these problems once and for all. It's clear that we don't work with *a list of single-type items*, e.g., route names, but **with a data structure**.

This is a great candidate to **switch to strict-typed value object**:

```php
final class RouteData
{
    public function __construct(
        private readonly string $route,
        private readonly array $params
    ) {
        // to be sure we keys are always a string
        \Webmozart\Assert\Assert::allString(array_keys($params));
    }

    // getters
}
```

## 👍


<br>

## How to spot These Cases?

It might be hard to spot these cases in your code base. Every array can be a list of single-typed items or a lazy data structure. It's time and attention-demanding. That's why these cases are frequent in the first place.

**Do you want to save time spotting these in your code base?** Us too.

There is [a PHPStan rule](https://github.com/symplify/phpstan-rules/blob/main/docs/rules_overview.md#noreturnarrayvariablelistrule) to automate this.


<br>

## One Step at a Time

Give it a try! Until we have strictly typed arrays in native PHP, this is the best state you can get your project into.
You'll see the magic IDE, Rector and PHPStan bring to your project.

<br>

Happy coding!
