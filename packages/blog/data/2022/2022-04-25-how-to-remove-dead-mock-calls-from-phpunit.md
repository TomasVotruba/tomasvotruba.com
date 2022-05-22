---
id: 358
title: "How to Remove Dead Mock Calls from PHPUnit"
perex: |
    ...

---

You're going to visit a different country where living costs are 1/3 of yours country. Let's say from the UK to Czechia or from Germany to Turkey. It's cheaper there; the bachelor party is the game's name. They go around the city drunk, the groom wear piggy or baby clothes, the beer is cheap, and so is the noise in the night hours. It's fun and exciting, you've never been abroad before, and friends together are "sad" to say goodbye to one more wingman. That's the last time they see you single. The Sunday morning is a flight back, and it's over.
From one side, this was the best party of this year!

The cleaning services of the hotel have to deal with the dirty puked bathroom the following day. The public cleaning service that wakes up at 5 AM is picking up the glass from beer you broke, and people who were so lucky to hear you partying till 3 AM have to go to work the following day. For them, it's a tiring day, cleaning other people's mess and dealing with the consequences of one "fun" time.



That's how mocking seems to be used these days. On one side, an excited team wants to apply this new testing framework to add a new test to their super legacy project that is hard to tackle. Finally, there is a tool that makes testing of anything possible because they do not have to deal with dependencies, types, or versioning. Everything can be mocked, and we only test the result.

On the other hand, a developer comes to this project 2 years later, their job is to upgrade the framework or PHP version, and they're trying their best. The upgrade itself goes well, but they have to deal with tests. It does not go as easy as expected because the test needs to be updated. Not just test but the renamed methods changed dependencies, etc. Even worse, the tests are still passing in some cases because all the bearing parts were mocked. They're not testing the production cod,e but the mocking framework itself. They give you an impression of well-tested code... until it fails on production on the actual class.



There are many posts about how not to use mocks but standard readable PHP code. If you still wonder if use or not to use, here they are:

*
* @see https://blog.frankdejonge.nl/testing-without-mocking-frameworks/
* @see https://maksimivanov.com/posts/dont-mock-what-you-dont-own/
* @see https://dev.to/mguinea/stop-using-mocking-libraries-2f2k
* @see https://mnapoli.fr/anonymous-classes-in-tests/
* @see https://steemit.com/php/@crell/don-t-use-mocking-libraries
* @see https://davegebler.com/post/php/better-php-unit-testing-avoiding-mocks


Saying that, this post will not be another argument against mocks. Pick one of the comments under the post above for such a discussion. Today we'll look into practical ways to deal with a messy situation.

---

Would you use 3 constructor to pass your dependency to class? No, we would use exact amount needed - just 1. The same way I prefer to avoid mocks unless they brings value. But what if you come to an existing project... well, a 10-year-old project that is successful and has many tests. The great test covered running CI, PHPStan, ECS, and Rector. All you can dream of.

There is just one catch: almost ~30 % of the tests are bare mocks. Have you read at least one of the posts above? Then you know that:

* when mocked method name, parameter, or return value is changed, mocks have to be updated manually
* the PHPStan and Rector have no idea if you've used the correct mock return type or not
* if the class even exists

We want to have tests that are too easy to maintain. We want to move fast and safely with PHP/framework upgrades, and the last thing we want to deal with is upgrading strings in tests. How can we get out of the mock jungle?



## Low Hanging Fruit - Lower complexity

Currently, with Rector, we're upgrading 2 such projects full of artificial mocks.

@see https://twitter.com/VotrubaT/status/1519762672895635456

What is our goal:

* reduce useless mocks to reduce their maintenance cost to 0 €
* keep only those mocks that bring value
* improve the whole codebase at once, with pattern refactoring, instead of file by file for years

Do you know smoke testing? Then you'll grasp the idea of smoke cleaning quickly.

Even if you finish just this in 1st week, it will be more than enough.

When there is a big pile of mess in front of our eyes, it's normal to get a cognitive overload and get stuck.
First, we need to reduce the initial complexity in the big fold and make the next step easier to tidy. We'll need our brian power there, so the more focus we have, the better. Saying that, what would you clean in a messy room first?


(todo maybe use 3 pictures)

* slightly dirty windows
* broken lamp light bulb
* dirty dishes on the table, chair, bad and floor

Intuitively, we'd go with the last one. But why?

* it's a simple task that we can do right now
* we know where to put all dirty dishes - into the kitchen sink
* in a couple of minutes, we'll feel like we've already cleaned half of the room
* the other 2 options will take a lot of work, looking for the correct light bulb (do we have any extra?) or getting warm water with propper chemical detergent (do we have Mr propper)
* we'll get an initial dopamine kick reward


So what is the smoke cleaning in here?

createMock()
willReturn()
(this->any())
->setMethods()


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

Result?

In one project, we have createMock in X cases
this set itself reduced work by X
