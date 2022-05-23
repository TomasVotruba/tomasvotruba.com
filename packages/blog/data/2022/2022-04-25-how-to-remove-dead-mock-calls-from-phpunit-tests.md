---
id: 358
title: "How to Remove Dead Mock Calls from PHPUnit Tests"
perex: |
    We're going to visit a foreign country where living costs are just 1/3 of our. Let's say from UK or Germany to Brno, Czechia. We're organizing a bachelor party for our best friend.
    <br><br>
    Fast forward, the night has come and our group walks around the city, drink beers, the groom wears baby piggy  clothes, and we're happily celebrating. We're loosing one more wing man that is going to settle down with beautiful wife and soon to come child. It's fun and exciting, we've never been abroad before with so many friends together to celebrate a next step in man's life.

tweet: "How to Remove Dead Mock Calls from #PHPUnit tests"
---

It's a Sunday morning is and we fly back home. Just a little hangover, but apart that, the best party of this year!

That's one side of the story.

<blockquote class="blockquote text-center">
"A person's freedom ends<br>
where another man's freedom begins."
</blockquote>

We can see the same weekend via 3 different glasses:

* We're working for the hotel as cleaning service. We wake up at 5 AM, ready to wash yet another dirty puked bathroom.

* The public cleaning service that wakes up at 4 AM, to pick up broken glass in the streets. That way people going to through the morning always get what they expect - clean city ready for their dogs and children to walk around safely.

* We're a family living in the city has a bad weekend. We could not sleep till 3 AM. Why? The streets were so noisy, with singing drunks arguing with police. Then playing some techno music from their bluetooth speakers.

For these people, it's a tiring day, cleaning other people's mess and dealing with the consequences of one "fun" time.

<br><br>

Let's take this story to programming, city being our project. Every new feature might seems cool and very useful to implement, but it has its consequences on the side of long term use.

## Mocking is Fun... but at What Cost?

That's how mocking seems to be used these days. On one side, an excited team wants to apply this new testing framework to add a new test to their super legacy project that is hard to tackle. Finally, there is a tool that makes testing of anything possible because they do not have to deal with dependencies, types, or versioning. Everything can be mocked, and we only test the result!

On the other hand, a developer comes to this project 2 years later. Their job is to upgrade the framework or PHP version. The upgrade itself goes well, but they have to deal with tests. "Deal" with tests? Should not tests be the helpful positive assets of the project?

It is not going as easy as expected because the test have to be updated. Not just test but the renamed methods of changed dependencies, etc. Even worse, the tests are still passing in some cases because all the bearing parts were mocked. They're not testing the production cod,e but the mocking framework itself. They give you an impression of well-tested code... until it fails on production on the actual class.

<blockquote class="twitter-tweet"><p lang="en" dir="ltr">How much do you mock?<br><br>Got 2 projects overrun with mocks, that make maintaining test terribly painful.<br><br>Exploring options to get to 0 mocks in an automated fashion.<a href="https://t.co/1NCIXIWjjK">https://t.co/1NCIXIWjjK</a></p>&mdash; Tomas Votruba 🇺🇦 (@VotrubaT) <a href="https://twitter.com/VotrubaT/status/1519732183614775300?ref_src=twsrc%5Etfw">April 28, 2022</a></blockquote> <script async src="https://platform.twitter.com/widgets.js" charset="utf-8"></script>

<br>

There are handful wise posts about **why not to use mocks** and move to standard readable PHP code:

* [Testing without mocking frameworks](https://blog.frankdejonge.nl/testing-without-mocking-frameworks/)
* [Using anonymous classes to write simpler tests](https://mnapoli.fr/anonymous-classes-in-tests/)
* [Don't use Mocking libraries](https://steemit.com/php/@crell/don-t-use-mocking-libraries)
* [Better PHP unit testing: avoiding mocks](https://davegebler.com/post/php/better-php-unit-testing-avoiding-mocks)

Saying that, this post will not be about "why", but rather about **how to deal with mock flood** in 10+ year project we took over.

## The Low Hanging ~~Fruit~~ Mock

Would you use 3 constructor to pass your dependency to class? No, we would use the exact amount needed - one. The same way we treat mocks - bring value or leave.

The project we took over is old, but successful. Fortunately for us, it has great test coverage, running CI, PHPStan, ECS, and Rector. All we can dream of. There is just one catch: almost ~30 % of the tests are bare mocks. Have you read at least one of the posts above? Then you know that:

* when mocked method name, parameter, or return value is changed, **mocks have to be updated manually**
* the PHPStan and Rector **have no idea about mocked types**
* **if the class even exists**, because anything that is a string, can be mocked

We want to have tests that are too easy to maintain. We want to move fast and safely with PHP/framework upgrades, and the last thing we want to deal with is **upgrading strings in tests**. How can we get out of the mock jungle?

## Taking Dead Mocks First ot Lower Complexity

It's easier to argue with single person then group of 10 people.

Currently, [with Rector cleaning services](https://getrector.org/for-companies), we're upgrading 2 projects with overuse of mocks. Our goal at first is simple:

* remove useless mocks
* keep only those mocks that bring value
* improve the whole codebase at once, with pattern refactoring
* make sure safety is our priority

So how does the universally useless mocks look like? The idea is the same with dead code in PHP:

```diff
-$value = 100;
 $value = 150;
```

Here we know the only 2nd value will remain, as last assign wins. We can remove the 1st line without changing anything. We can also detect this pattern with Rector and automate this.

## Detecting Dead Mocks

### 1. Replace `getMockBuilder()` with `createMock()`

The `getMockBuilder()` is old method, that was used [for detailed strict testing](https://stackoverflow.com/questions/38363086/what-is-the-difference-between-createmock-and-getmockbuilder-in-phpunit). Replacing this method will remove lot of clutter and most likely will not change the test:

```diff
-$cacheServiceMock = $this->getMockBuilder(CacheInterface::class)
-    ->disableOriginalConstructor()
-    ->setMethods(['del', 'get', 'set'])
-    ->getMock();
+$cacheServiceMock = $this->createMock(CacheInterface::class);
```

We've done this refactoring on one code base, removing 59 cases and test are passing well.

<br>

### 2. Remove `expects($this->any())` as default

This method defined expected input on a following method call. The "any" type is default input, so it can be safely removed:

```diff
 $siteServiceMock
-    ->expects($this->any())
     ->method('getMetadata')
     ->willReturn(['name' => 'John']);
```

In our project, 63 lines are now gone.

<br>

### 3. Replace `will()` + `returnValue()` with `willReturn()`

Handy shortcut, that might help us in the next refactoring:

```diff
 $requestMock
     ->method('getPathInfo')
-    ->will($this->returnValue($url));
+    ->willReturn($url);
```




## Empower your CI to Work for You

After all this effort, our tests are getting slim. We're not there yet, but it's a start to spark the fire. But what if someone will send a PR or IDE/tool-generated code that will give us the clutter back?

No worries, add this set to your `rector.php`, enable Rector in CI and you're covered forever:

```php
use Rector\Config\RectorConfig;
use Rector\Set\ValueObject\SetList;
use Rector\PHPUnit\Set\PHPUnitSetList;

return function (RectorConfig $rectorConfig): void {
    $rectorConfig->sets([
        PHPUnitSetList::REMOVE_MOCKS,
    ]);
};
```

That's it for today! Do you know **another pattern refactoring to reduce mocks we should include here**? Please share with me in the comments. I'm eager to learn, as this is a wide-spread problem we can easily automate.

<br>

Happy coding!
