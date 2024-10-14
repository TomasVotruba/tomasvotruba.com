---
id: 417
title: "How to Upgrade deprecated PHPUnit withConsecutive()"
perex: |
    The `withConsecutive()` method [was deprecated in PHPUnit 9](https://github.com/sebastianbergmann/phpunit/issues/4255#issuecomment-636422439) and removed in PHPUnit 10. It sparked many [questions](https://stackoverflow.com/questions/75389000/replace-phpunit-method-withconsecutive-abandoned-in-phpunit-10), [on StackOverflow](https://stackoverflow.com/questions/77865216/phpunit-withconsecutive-is-gone-what-is-the-recommended-approach), in [various projets](https://www.drupal.org/project/drupal/issues/3306554) and [GitHub](https://github.com/search?q=repo%3Asebastianbergmann%2Fphpunit+withConsecutive&type=issues).

    It was not a very popular BC break. There is no 1:1 replacement. It can be combined with `willReturn*()` methods, which can make it even more tricky to merge with.

    PHPUnit upgrades take 95 % of the time to upgrade this single method and 5 % for everything else.
    In recent months, we've done a couple of project upgrades with Rector and learned a lot.

    Today, I want to share some knowledge with you and explain why it's a change for better code.
---

What does `withConsecutive()` method actually do?

```php
$mock = $this->createMock(MyClass::class);
$mock->expects($this->exactly(2))
    ->method('someMethod')
    ->withConsecutive(
        ['first'],
        ['second']
    );
```

It defines what arguments are on the input once the method mock is called. E.g. here:

* on 1st call, it expects `['first']`
* on 2nd call, it expects `['second']`

To be honest, I've never written such code myself, but so far, we've found it in every code base we've upgraded.
It's been available since 2006 and only removed after 16 years in 2022.

<br>

So how can we replace it? It would be very convenient if there would some kind of `withNthCall()` method:

```php
$mock = $this->createMock(MyClass::class);
$mock->expects($this->exactly(2))
    ->method('someMethod')
    ->withNthCall(1, ['first'])
    ->withNthCall(2, ['second']);
```

But it's not.


## `willReturnCallback()` to the Rescue

Instead, we use the `willReturnCallback()` trick. This method accepts the called parameters, which we can assert inside.

```php
$mock = $this->createMock(MyClass::class);
$mock->expects($this->exactly(2))
    ->method('someMethod')
    ->willReturnCallback(function ($parameters) {
        // check the parameters here
    });
 ```

But how do we detect if it's the first or second call? The `$this->exactly(2)` expression actually returns a value object `PHPUnit\Framework\MockObject\Rule\InvokedCount` that we can work with.

```php
$invokedCount = $this->exactly(2);

$mock = $this->createMock(MyClass::class);
$mock->expects($invokedCount)
    ->method('someMethod')
    ->willReturnCallback(function ($parameters) use ($invokedCount) {
        // check the parameters here
    });
```

On every mock invoke method, the number of invokes in `$invokedCount` will increase.

<br>

We can use it to detect the 1st or 2nd call:

```php
$invokedCount = $this->exactly(2);

$mock = $this->createMock(MyClass::class);
$mock->expects($invokedCount)
    ->method('someMethod')
    ->willReturnCallback(function ($parameters) use ($invokedCount) {
        if ($invokedCount->getInvocationCount() === 1) {
            // check the 1st round here
        }

        if ($invokedCount->getInvocationCount() === 2) {
            // check the 2nd round here
        }
    });
```

Now we include the original parameters we needed:

```php
// ...

    ->willReturnCallback(function ($parameters) use ($invokedCount) {
        if ($invokedCount->getInvocationCount() === 1) {
            $this->assertSame(['first'], $parameters);
        }

        if ($invokedCount->getInvocationCount() === 2) {
            $this->assertSame(['second'], $parameters);
        }
    });
```

Now, this is where this deprecation becomes useful. What if one of the parameters is a product object?

We could create a `$product` object and do `assertSame()`. But what if we only care about its price?

```php
// ...
    ->willReturnCallback(function ($parameters) use ($invokedCount) {
        if ($invokedCount->getInvocationCount() === 1) {
            $product = $parameter[0];
            $this->assertInstanceof(Product::class, $product);
            $this->assertSame(100, $product->getPrice());
        }

        // ...
    });
```

Using `withConsecutive()` would turn this into a single-line mess. Now, it's more readable and flexible.

<br>

## Why `if` over `match`?

Originally, we used the `match()` expression over `ifs()` in the Rector rule, but it created a couple of new problems:

* PHPUnit 9.x requires PHP 7.3+. Using `match()` would mean you have to do the upgrade to PHP 8 and to PHPUnit 10 at the same time. This is not always possible and can be risky
* The call count is already checked by `$this->exactly(2)`. There is no need to add another layer of complexity to check the same thing again
* With `match()`, there is only a single line of expression. Assert above would be a single line:

```php
=> $product = $parameters[0] && $this->assertInstanceof(Product::class, $product) && $this->assertSame(100, $product->getPrice())
 ```

This code is not readable and maintainable. There is also one more reason why `if()` is the king.


## Return value

More often than not, the method not only accepts parameters but also returns some value. That's where `willReturn*()` methods come into play:

```php
$mock = $this->createMock(MyClass::class);
$mock->expects($this->exactly(2))
    ->method('someMethod')
    ->withConsecutive(
        ['first'],
        ['second']
    )
    ->willReturnOnConsecutiveCalls([1, 2]);
```

How do we upgrade any returned value? We just return it:

```php
// ...

    ->willReturnCallback(function ($parameters) use ($invokedCount) {
        if ($invokedCount->getInvocationCount() === 1) {
            $this->assertSame(['first'], $parameters);
            return 1;
        }

        if ($invokedCount->getInvocationCount() === 2) {
            $this->assertSame(['second'], $parameters);
            return 2;
        }
    });
```

That's it! How about other return methods?

```php
->willReturnArgument(0);
// or
->willReturnSelf();
// or
->willThrowException(new \Exception('Never happens'));
```

We can just write plain PHP code:

```php
// ...

    ->willReturnCallback(function ($parameters) use ($invokedCount) {
        if ($invokedCount->getInvocationCount() === 1) {
            return $parameters[0];
            // or
            return $this->userServiceMock;
            // or
            throw new Exception('Never happens');
        }
    });
```


## More Readable and Easier to Maintain

* We don't have to learn special PHPUnit mock method naming and can understand the code.
* This vanilla PHP also opens up the next step - refactoring [away from mocks to anonymous typed classes](/blog/2018/06/11/how-to-turn-mocks-from-nightmare-to-solid-kiss-tests)
* We can easily add a new assertion line
* We can return values we need

<br>

What if upcoming PHPUnit 12, 13... versions, removes or changes more mocking methods? This code will work, as it's just plain PHP.

<br>

This is how we can upgrade the `withConsecutive()` method in PHPUnit 9 or earlier. I hope it's clearer now why this change was needed and how it can help you write better tests.

<br>

Last but not least, here is [the Rector rule](https://getrector.com/rule-detail/with-consecutive-rector) that automated this process.

<br>


## Next Upgrade in PHPUnit 10

In PHPUnit 10, the `getInvocationCount()` got renamed to `numberOfInvocations()`. Make sure you upgrade the method name, once you go to PHPUnit 10:

```diff
-if ($invokedCount->getInvocationCount() === 1) {
+if ($invokedCount->numberOfInvocations() === 1) {
     $this->assertSame(['first'], $parameters);
     return 1;
 }
```

<br>


Happy coding!
