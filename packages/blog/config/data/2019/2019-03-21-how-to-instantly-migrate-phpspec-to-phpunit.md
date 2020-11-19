---
id: 196
title: 'How to Instantly&nbsp;Migrate PhpSpec&nbsp;to&nbsp;PHPUnit'
perex: |
    I'm happy that more and more people try to use Rector upgrade and migrate their code-bases to the ones they really want for a long time.
    <br>
    <br>
    Last week I was approached by 2 different people with single need - **migrate their tests to PHPUnit**.
tweet: 'New Post on #php 🐘 blog: How to Instantly Migrate #PhpSpec to #phpunit'
tweet_image: '/assets/images/posts/2019/unit-mig/phpunit.png'

updated_since: "November 2020"
updated_message: |
    Switch from deprecated `--set` option to `rector.php` config.
---

<blockquote class="blockquote text-center">
    "Nobody believes in anything without an inner feeling that it can be realized.
    <br>
    This is the only source of dreamlike powers."
    <footer class="blockquote-footer">Peter Altenberg</a>
</blockquote>

*Disclaimer: I never saw PhpSpec code before this mentoring session. All I learned was from my client and their needs (and bit of reading the documentation)*.

## "Why do you Have 2 Unit-Testing Frameworks?"

That was my question to one of my clients when I saw both PhpSpec and PHPUnit tests in its code base.

- "So what is added value of PhpSpec over PHPUnit?"
- "Well, I use it for unit testing."
- "How is that different to PHPUnit?"
- "It tests the behavior."
- "Well, what is the difference between `assertSame()` and `shouldBeEqualTo()`"?
- "None really."

But before I noticed PhpSpec and asked about it, we had another chat:

- "Why there is this static class when the rest of your code uses Symfony Dependency Injection?"
- "It's needed for my test."
- "Why don't you use `KernelTestCase`"?
- "I can't."
- "Well, you have Symfony and you run your whole application on it, you have PHPUnit, so you can. Why not?"
- "It's not PHPUnit, it's PhpSpec."
- "So?"
- "It doesn't use any container, there must be static in my code."
- "So what is added value of PhpSpec over PHPUnit?"
- "..."

Then I explored PhpSpec and found out, it's **basically PHPUnit with different naming**.

<blockquote class="blockquote text-center">
    "It looks like Y, a variant of X, could be done in
    <br>
    about half the time, and you lose only one feature."
    <footer class="blockquote-footer">The Pragmatic Programmer book</a>
</blockquote>

## Trends over Long-Tail Effect

Why did we choose to migrate PhpSpec tests to PHPUnit? Well, it's better obviously... Do you agree with me just because I wrote that? **Don't do that, it's my personal opinionated opinion (= feeling, emotion). Ask me for some data instead**.

Let's look at downloads:

- 1 mil. downloads
- [14 mils. downloads](https://packagist.org/packages/phpspec/phpspec/stats)
- [117 mils. downloads](https://packagist.org/packages/phpunit/phpunit/stats)

But 117 mil. downloads can be like "You should use Windows XP because it's the most used Windows version ever!" That's classic manipulation of dying dinosaur.

**Let's see the trends!** In the same order:

<div class="row">
    <div class="col-md-4 col-sm-4">
        <img src="/assets/images/posts/2019/unit-mig/tester.png">
    </div>
    <div class="col-md-4 col-sm-4">
        <a href="https://packagist.org/packages/phpspec/phpspec/stats">
            <img src="/assets/images/posts/2019/unit-mig/spec.png">
        </a>
    </div>
    <div class="col-md-4 col-sm-4">
        <a href="https://packagist.org/packages/phpunit/phpunit/stats">
            <img src="/assets/images/posts/2019/unit-mig/phpunit.png">
        </a>
    </div>
</div>

Which one would you pick from this 2 information? I'd go for the last one, so did my client. **So that's why we agreed to migrate PhpSpec** (the middle one) **to PHPUnit**.

<br>

This is how 1 spec migration might look like:

```diff
 <?php

-namespace spec\App\Product;
+namespace Tests\App\Product;

-use PhpSpec\ObjectBehavior;

-final class CategorySpec extends ObjectBehavior
+final class CategoryTest extends \PHPUnit\Framework\TestCase
 {
+    /**
+     * @var \App\Product\Category
+     */
+    private $createMe;

-    public function let()
+    protected function setUp()
     {
-        $this->beConstructedWith(5);
+        $this->createMe = new \App\Product\Category(5);
     }

-    public function it_returns_id()
+    public function testReturnsId()
     {
-        $this->id()->shouldReturn(5);
+        $this->assertSame(5, $this->createMe->id());
     }

-    public function it_blows()
+    public function testBlows()
     {
-        $this->shouldThrow('SomeException')->during('item', [5]);
+        $this->expectException('SomeException');
+        $this->createMe->item(5);
     }

-    public function it_should_be_called(Cart $cart)
+    public function testCalled()
     {
+        /** @var Cart|\PHPUnit\Framework\MockObject\MockObject $cart */
+        $cart = $this->createMock(Cart::class);
-        $cart->price()->shouldBeCalled()->willReturn(5);
+        $cart->expects($this->atLeastOnce())->method('price')->willReturn(5);
-        $cart->shippingAddress(Argument::type(Address::class))->shouldBeCalled();
+        $cart->expects($this->atLeastOnce())->method('shippingAddress')->with($this->isType(Address::class));
     }

-     public function is_bool_check()
+     public function testBoolCheck()
      {
-         $this->hasFailed()->shouldBe(false);
+         $this->assertFalse($this->createMe->hasFailed());
-         $this->hasFailed()->shouldNotBe(false);
+         $this->assertNotFalse($this->createMe->hasFailed());
      }

-     public function is_array_type()
+     public function testArrayType()
      {
-         $this->shippingAddresses()->shouldBeArray();
+         $this->assertIsIterable($this->createMe->shippingAddresses());
      }
 }
```

Pretty clear, right?

## How to Instantly Migrate from PhpSpec to PHPUnit?

First, take a 2-week paid vacation... Just kidding. Start with Rector which migrates ~95 % of code cases. It also renames `*Spec.php` to `*Test.php` and moves them from `/spec` to `/tests` directory:

1. Add Rector

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
    $parameters->set(Option::SETS, [SetList::PHPSPEC_TO_PHPUNIT]);
};
```

3. Run Rector on your tests directories

```bash
vendor/bin/rector process tests
```

<br>

Take couple of minutes to polish the rest of code and send PR to your project <em class="fas fa-fw fa-check text-success fa-lg"></em>

<br>

And what was the 2nd testing framework that Rector migrated? [Nette\Tester](/blog/2019/03/25/how-to-instantly-migrate-nette-tester-to-phpunit).

<br>

Happy coding!
