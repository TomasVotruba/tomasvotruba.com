---
id: 114
title: "Collector Pattern for Dummies"
perex: |
    I wrote *[Why is Collector Pattern so Awesome](/blog/2018/03/08/why-is-collector-pattern-so-awesome/)* a while ago, but I got feeling and feedback that it's way to complicated.
    <br><br>
    The pattern itself is simple, but put in framework context, it might be too confusing to understand.
    <br><br>
    That's why we look on collector pattern in minimalistic plain PHP way today. 
tweet: "New Post on my Blog: Collector Pattern for Dummies"
---

Let's say you have a simple `PriceCalculator` class that calculates price for a product with a VAT:

```php
class PriceCalculcator
{
    public function calculate(Product $product): float
    {
        // compute vat
        $price $product->getPrice() * (1 + $vat);
        
        return $price;
    }
} 
```

Then we decide to have 50 % discount for admins:

```diff
 class PriceCalculcator
 {
     public function calculate(Product $product): float
     {
         // compute vat
         $price = $product->getPrice() * (1 + $vat);
       
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
 class PriceCalculcator
 {
     public function calculate(Product $product): float
     {
         // compute vat
         $price = $product->getPrice() * 1.21;
       
         // discount for admin
         if ($this->currentUser->getRole() === 'admin') {
             $price *= 0.5;
         }

         // discount for students
         if ($this->currentUser->getOccupation() === 'student') {
             $price *= 0.8;
         }
        
         return $price;
     }
 } 
```

Our `PriceCalculcator` grows and grows, our e-commerce plaform expands all over the Europe and we found out they have different strategy to calculate price with VAT. How do we solve it?

"Override the whole class and implements `calculate()` method for yourself."

```diff
 class UnitedKindomPriceCalculcator extends PriceCalculator
 {
     public function calculate(Product $product): float
     {
         // compute vat
         $price = $product->getPrice() * 1.15;

         return $price;
     }
 } 
```

That's easy solution for the end-user. But it also means 0 reusable code - imagine there will be 20 websites in UK and **each of them will have their own code to calculate price with VAT**. 100 % similar code (if written correctly), because it applies to whole country.

## Sharing is Caring 

Instead, such UK solution can be one of many, that is openly shared. 

- Do you need a UK price calculator? Plug it in.
- Do you need a configurable discount based on role? Plug it in.
- Do you need to decrease all price by 100 € if possible? Plug it in.

No need to write it more than once for all of e-commerce sites.

## How Does That Look in the Code?

### 1. Turn You Main Class to Collector

```php
class PriceCalculcatorCollector
{
    /**
     * @var PriceCalculatorInterface[]
     */
    private $priceCalculators = [];
    
    public funciton addPriceCalculator(PriceCalculatorInterface $priceCalculator)
    {
        $this->priceCalculators[] = $priceCalculator;
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

final class UnitedKindomPriceCalculcator implements PriceCalculatorInterface
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
$priceCalculcatorCollector = class PriceCalculcatorCollector;
$priceCalculcatorCollector->addPriceCalculator(new AdminDiscountPriceCalculator());
$priceCalculcatorCollector->addPriceCalculator(new UnitedKindomPriceCalculcator());

$price = $priceCalculcatorCollector->calculatePrice($product);
```

<em class="fa fa-fw fa-lg fa-check text-success"></em> single entry point for `Collector`

<em class="fa fa-fw fa-lg fa-check text-success"></em> each solution that implements `PriceCalculatorInterface` is reusable 

<em class="fa fa-fw fa-lg fa-check text-success"></em> to extend `PriceCalculcatorCollector` with another feature, e.g. have a discount for Lenovo  laptops from now till the end of June 2018, we don't have to modify it - just register a new `PriceCalculator`

That's all there is. Just kidding, there is much more, but that's out of scope of this simple tutorial.

<br><br>

Happy collecting!
