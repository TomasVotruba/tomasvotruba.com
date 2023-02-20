---
id: 114
title: "Collector Pattern for Dummies"
perex: |
    I wrote *[Why is Collector Pattern so Awesome](/blog/2018/03/08/why-is-collector-pattern-so-awesome/)* a while ago, but I got feeling and feedback that it's way too complicated.


    The pattern itself is simple, but put in framework context, it might be too confusing to understand.


    That's why we look on collector pattern in minimalistic plain PHP way today.
tweet: "New Post on my Blog: Collector Pattern for Dummies"
---

Let's say you have a simple `PriceCalculator` class that calculates a price for a product with a VAT:

```php
class PriceCalculator
{
    public function calculate(Product $product): float
    {
        // compute vat
        $price = $product->getPrice() * 1.15;

        return $price;
    }
}
```

Then we decide to have 50 % discount for admins:

```diff
 class PriceCalculator
 {
     public function calculate(Product $product): float
     {
         // compute vat
         $price = $product->getPrice() * 1.15;

+        // discount for admin
+        if ($this->currentUser->getRole() === 'admin') {
+            $price *= 0.5;
+        }
+
         return $price;
     }
 }
```

And another 20 % discount for students:

```diff
 class PriceCalculator
 {
     public function calculate(Product $product): float
     {
         // compute vat
         $price = $product->getPrice() * 1.15;

         // discount for admin
         if ($this->currentUser->getRole() === 'admin') {
             $price *= 0.5;
         }

+        // discount for students
+        if ($this->currentUser->getOccupation() === 'student') {
+            $price *= 0.8;
+        }
+
         return $price;
     }
 }
```

Our `PriceCalculator` grows and grows, our e-commerce platform expands all over Europe and we found out they have a different strategy to calculate price with VAT. How do we solve it?

"Override the whole class and implements `calculate()` method for yourself."

```php
 class UnitedKingdomPriceCalculator extends PriceCalculator
 {
     public function calculate(Product $product): float
     {
         // compute vat
         $price = $product->getPrice() * 1.15;

         return $price;
     }
 }
```

That's an easy solution for the end-user. But it also means zero reusable code that leads to duplicated work. Imagine there will be 20 websites in the UK and **each of them will have their own code to calculate price with VAT**. 100 % similar code (if written correctly), because it applies to the whole country.

## Sharing is Caring

Instead, such UK solution can be one of many, that is openly shared.

- Do you need a UK price calculator? Plug it in.
- Do you need a configurable discount based on role? Plug it in.
- Do you need to decrease all price by 100 € if possible? Plug it in.

No need to write it more than once for all of the e-commerce sites.

## How Does That Look in the Code?

### 1. Turn Your Main Class to A Collector

```php
class PriceCalculatorCollector
{
    /**
     * @var PriceCalculatorInterface[]
     */
    private $priceCalculators = [];

    /**
     * @param PriceCalculatorInterface[] $priceCalculators
     */
    public function __construct(array $priceCalculators)
    {
        $this->priceCalculators = $priceCalculators;
    }

    public function calculate(Product $product): float
    {
        $price = $product->getPrice();

        foreach ($this->priceCalculators as $priceCalculator) {
            $price = $priceCalculator->calculate($price);
        }

        return $price;
    }
}
```

with interface decoupling:

```php
interface PriceCalculatorInterface
{
    public function calculate(float $price): float;
}
```

### 2. Converts Each Case to Collected

```php
final class CzechVatPriceCalculator implements PriceCalculatorInterface
{
    public function calculate(float $price): float
    {
        return $price * 1.21;
    }
}
```

```php
final class AdminDiscountPriceCalculator implements PriceCalculatorInterface
{
    public function calculate(float $price): float
    {
        if (! $this->currentUser->getRole() === 'admin') {
            return $price;
        }

        return $price *= 0.5;
    }
}
```

```php
final class UnitedKingdomPriceCalculator implements PriceCalculatorInterface
{
   public function calculate(float $price): float
   {
       return $price * 1.15;
   }
}
```

### 3. Let Collector Collect What You Need

Based on your needs, collect this or that service.

```php
$priceCalculatorCollector = new PriceCalculatorCollector([
    new AdminDiscountPriceCalculator(),
    new UnitedKingdomPriceCalculator(),
]);

$price = $priceCalculatorCollector->calculatePrice($product);
```

✅ single entry point for `Collector`

✅ each solution that implements `PriceCalculatorInterface` **is reusable**

✅ **to extend** `PriceCalculatorCollector` with another feature, e.g. have a discount for Lenovo laptops from now till the end of June 2018, **we don't have to modify** it - just register a new `PriceCalculator`

✅ **to reflect 1 change in reality**, e.g. from 15 % to 20 % VAT, all we need to do it **change 1 class for everyone**

<br>

**Win for the end-user, win for your project and win for the code.**

<br>

*And that's all there is.* Just kidding, there is much more, but that's out of the scope of this simple tutorial.
Why the collector class doesn't implement the interface and other questions will be answered in following posts.

<br><br>

Happy collecting!
