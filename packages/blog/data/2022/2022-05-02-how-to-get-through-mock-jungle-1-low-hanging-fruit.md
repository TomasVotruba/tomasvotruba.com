---
id: 354
title: "How to get through Mock Jungle #1 - Low Hanging Fruit"
perex: |
    ...

---

@todo quote about cleaning :)

...
You're going to a differnet country, by cheapest plane ticket. Let's say from UK to Czechia or from Germany to Turkey. It's eahcper there, the bachelor party is the name of the game. They go around the city, drunk, the groom wear piggy or baby clothes, the beer is ehap, so is the noise in the nigh ohours. It's fun and excitement, you've never been abroad before and friends together are "sad" to say goodbye to one more wingman. That's last time they see you single. The Sunday mornign is flight back and it's over.
From one side, this was the best party of this year!

Yet, next morning the cleaning serivdces of the hotel has to deal with the dirty puked bathroom, the public clenaing service that wakes up at 5 in the AM is picking up the glass from beer you broke and people who were so lucky to hear you partying till 3 AM have to go to work the next morning. For them, it's a tired day, cleaning another peoples mess and dealing with consequences of "fun" time.



That's how mocking seems to me used these days. On one side, there is an excited team that wants to applie this new testing frmeaork to add new test to their super legacy project that is hard to tacle. Finally there is a tool that makes testing of anyhting possible, because they do not have to deal with dependencies, types or versnionig. Eveything can be mocked and we only test the resutl.

On the otehr han ther is a devleoepr who comes to this project 2 years later, their job is to upgrade fmraeowkr or PHP version and they're trying their best. The upgrade itself goes well, but they have to deal with test too. It does not go as seasy as they expected, because test needs to be updated too. Not just test but the renamed methods, changed dependnecies etc. Even worse, in some csaes the test are still passing, becuase all the beraing parts were mocked. They're not testing the production cod,e but the mocking framework itself. They give you an impression of well tested code... untill it falis on production on the real class.


--------


# Tackling Mock Jungle #1 - Smoke Celaning

# post remove mocks
# how to get out of mock jungle?


There are many posts about how to not to use mocks, but normal readable PHP code. If you still wonder if use or not to use, here they are:

* by ...
* by ...
* by ...
* by ...

Saying that, this post will not be another argue against mocks. For such discussion pick one of the comments under post above. Today we'll look into practical way how to deal with a messy situation.

---

Personally, I prefer to avoid them unless really needed. But what if you come to a new project... well, 10 years old project that is successful and has many tests. Great test covered, running CI, PHPStan, ECS and Rector. All you can dream of.

There is just one catch: almost ~30 % of the tests are bare mocks. Have you read at least on of the posts above? Then you know that:

* when mocked method name, parameter or return value is changed, mocks have to be udpated manually
* the PHPStan and Rector has no idea, if you've used correct mock return type or not
* if the class even exists

We want to have tests that are to easy to maintain. We want to move fast and safe with PHP/framework upgrade, and the last thing we want to deal with is upgrading strings in tests. How can we get out of the mock jungle?



## Low Hanging Fruit - Lower compleixty

Currently with Rector we're upgrading 2 such projects, that are full of artificial mocks.

@todo picture of messy home
messy-and-clean.jpg - "I mean, we just want to have a cup of coffee... but where do we sit?"


@see https://twitter.com/VotrubaT/status/1519762672895635456



What is our goal:

* reduce useless mocks to reduce their maintancne cost to 0 €
* keep only those mocks that bring value
* improve the whole code base at once, with pattern refactoring, instead of file by file for yeras



** 1) smoke cleaning (redurect mock calls)

Do you know smoke testing? Then you'll grasp the idea of smoke cleaning quickly.


Even if you finish just this in 1st week, it will be more than enough.

When there is a big pile of mess in front of our eyes, it's normal to get a cognitive overload and get stuck.
First, we need to reduce initial complexity in big fold and make next step easier to tidy. We'll need our brian power there, so the more focus we have, the better. Saying that, what would you clean in a messy room first?


(todo maybe use 3 pictures)

* sligtly dirty windows
* broken lamp light bulb
* dirty dishes on the table, chair, bad and floor

Intuitively, we'd go with the last one. But why?

* it's a simple task that we can do right now
* we know where to put all dirty dishes - into the kitchen sink
* in a copule minutes, we'll feel like we've already cleaned half of the room
* the other 2 options will take lot of work, looking for the correct light bulb (do we have any extra?), or getting warm water with propper chemical detergent (do we have mr propper)
* we'll get initial dopamine kick reward


So what is the smoke cleanign in here?

createMock()
willReturn()
(this->any())
->setMethods()






@todo use Rector here https://github.com/rectorphp/rector-phpunit/blob/main/config/sets/remove-mocks.php


Result?

In one project we have createMock in X cases
this set itself redurect work by X

@todo run in a standdlaone PR on be5 to get the data







copy from notes dump brain :)
