---
id: 435
title: "Alice, Nelmio, Hautelook, Faker - How to Upgrade Doctrine Fixtures - Part 3"
perex: |
    In previous parts ([part 1](/blog/alice-nelmio-hautelook-faker-and-how-to-upgrade-doctrine-fixtures-part-1) and [part 2](/blog/alice-nelmio-hautelook-faker-and-how-to-upgrade-doctrine-fixtures-part-2)) we looked a situation, where we are stuck with Alice, Nelmio, Hautelook, Faker legacy mess and need a way out to modern maintainable Doctrine.

    This is the final part. **Upgrade is finished and closed at time being**. In a unexpected way, that was fast, easy to migrate to, and will be even easier to maintain. It took me couple of month of deep thinking and experiemnt, because there was a trap solution that would take us 6-8 months of hard work and would create even more legacy code. Lesson learned: don't fall for marketing and GPT answers.

    Let's look into it.
---

In [part 1](/blog/alice-nelmio-hautelook-faker-and-how-to-upgrade-doctrine-fixtures-part-1) we looked at current situation of our legacy project. In [part 2](/blog/alice-nelmio-hautelook-faker-and-how-to-upgrade-doctrine-fixtures-part-2) we tooked low hanging fruit first:

* reduce dependency on Faker fixtures - use static PHP helper functions
* make use of PHPStan and Rector feedback - flip Alice fixtures from YAML to PHP
* explore problem of references - native Doctrine fixture and Alice fixture references are not shared

What is left ahead of us?

* ~~upgrade Alice to latest version, flip to Foundry~~
* fix references, so we have one way to reference objects in fixtures
* remove Nelmio, Hautelook, Alice and Faker dependencies
* make Doctrine fixture much more maintainable

## Migrate package A to package B Trap

**The first task was a trap.** We have planned this upgrading, using GPT to get there faster, but it felt something was wrong. I've postponed this single task for many months. Was it out of fear? Was it because it didn't feel like a better solution? After all, we'll have the safe fixture mess, just instead of *Alice* we'll call it *Foundry*.

In my experience, its good to listen to these feeling and **not rush into the solution** to "make client happy". Intuition is trying to tell me there is some better solution, I just don't see it yet.  If we'd start this upgrade, it would cost our client 2-3 months of work. It would look like better solution because "it took more and and effort", but in reality it would only create more mess for the dev team to work with from now on.

> Best code is no code.
> Best dependency is no dependency.


## 1. One way to work with Fixtures

I felt somethign is wrong, but I didn't know way out yet. So I posponed the task as long as I could. I start working on another issue: how to use object references in both hative fixtures (`doctrine/data-fixtures`) and Alice fixtures (`nelmio/alice`)? If we crack this goal, maybe the rest of upgrade will reveal itself.

After couple of days of back and forth debugging, I found very nice and tidy... dead end. We have 2 commands to load fixtures:

```bash
bin/console doctrine:fixtures:load
bin/console hautelook:fixtures:load
```

Both are run separatelly... in PHP, every process starts and dies with its own memory. That's why all the data we create and store in memory in 1st command, are never available in the 2nd command. We came up with obvious solution: use single command to load all fixtures:

```bash
bin/console app:load-doctrine-fixtures
```

That's it! We created our own simple command to load Doctrine fixtures. That way also accidentally solved [patching from part 1](/blog/alice-nelmio-hautelook-faker-and-how-to-upgrade-doctrine-fixtures-part-1).

What is loading of fixtures after all?

```php
<?php

// 1. find all fixture classes
$fixtureClasses = $this->getFixtureClasses();

// 2. create objects and store them in memory
foreach ($fixtureClasses as $fixtureClass) {
    $fixture = new $fixtureClass();
    $fixture->load($objectManager);

    // 3. persist if use references
}

// 4. flush to database
$objectManager->flush();
```

That's the command it its gist. We flipped our custom command, dropped the Doctrine fixtures one. Side effect? We can drop `doctrine/doctrine-fixtures-bundle` package! One less dependency to worry about.

Now we just add loading of Alice fixtures ot this command, and we have single command - that uses one `ReferenceRepository` with all the referencdes. It takes bit of hacking, back and forth, using `LoaderInterface`, but in a gist:

```php
// ...

// 1. find all alice fixture files (already in PHP)
// sort them using GPT algorithm to load dependencies first
$aliceFixtureFiles = $this->getAliceFixtureFiles();

// 2. load alice fixtures and store references in memory
foreach ($aliceFixtureFiles as $aliceFixtureFile) {
    $loader = new Loader();
    $loader->loadFile($aliceFixtureFile);
    $objects = $loader->getObjects();
    foreach ($objects as $object) {
        $referenceRepository->addReference($object);
    }
}

// 3. persist and flush to database
foreach ($referenceRepository->getReferences() as $reference) {
    $objectManager->persist($reference);
}

$objectManager->flush();
```

We combined both snippets to single command, and boom - now we have single command to load fixtures, and Alice files can start using references to native fixtures!

As a side effect, we can drop `hautelook/alice-bundle` package too! Its job it only to integrate Alice fixtures with Doctrine, but we already have our own minimalistic integration so we don't need it anymore.

<br>

Let's see our list of goals now:

* fix references, so we have one way to reference objects in fixtures - **done!**
* remove Nelmio, Hautelook, Alice and Faker dependencies - **Hautelook done!**
* make Doctrine fixture much more maintainable

## 2. Make References the King

References in both Native and Alice fixtures are often bare string. Yes, we already have all Alice fixtures flipped from YAML to PHP (see [part 2](/blog/alice-nelmio-hautelook-faker-and-how-to-upgrade-doctrine-fixtures-part-2)), but we still have string references. That is not ideal, because we can easily make typo and break the fixtures loading.

```php
return [
    \App\Entity\Post::class => [
        'post1' => [
            'author' => '@user1'
        ],
    ],
]
```

How to fix that? We created couple Rector rules, to flip all references in anywhere in the project to be clear and safe - using class constant lists (you may call them "enums"):

```diff
 return [
     \App\Entity\Post::class => [
-        'post1' => [
+        PostReference::POST_1 => [
-           'author' => '@user1'
+           'author' => '@' . UserReference::USER_1
         ],
     ],
 ]
```

This way, one can easily use IDE to click through the reference origin and see how its used. Add couple custom PHPStan rules that to enforce reference usage in all places, and the fixture files are much more maintainable: we can instantly see, what is a string value as a value, and what is a reference.

Apply this approach not only to fixtures, but all places where `ReferenceRepository` is used:

```diff
-$this->addReference('user1', $user);
+$this->addReference(UserReference::USER_1, $user);

-$this->getReference('user1');
+$this->getReference(UserReference::USER_1);
```

## 3. Remove Nelmio, Hautelook, Alice and Faker dependencies

So what now? We use references everywhere we can, we have single command to load fixture files, we can use references in both native and Alice fixtures, but we still have 4 dependencies to maintain. Question to ask is: do we *really* need them?

Faker dependency is a collection of couple basic functions - in our case, related to time, name and couple emails. Everything else were our data. So we replaced with couple classes with static method calls. Bye bye `fzaninotto/faker`!

> We cannot solve our problems
> with the same thinking we used when we created them.

Using Alice fixture is quite complicated approach to create objects. Lets take a simple reason to use Alice fixtures:

```php
return [
    \App\Entity\Post::class . '{1..25}' => [
        'post1' => [
            'author' => '@user{{ current }}'
        ],
    ],
]
```

This will create 25 posts with various authors. But why we need it in exactly this syntax?

I mean YAML is cool is most answers, but YAML is also #1 reason Symfony 2 legacy projects are nearly impossible to upgrade.

What if... we use plain PHP?

```php
$posts = [];
foreach ($i < 25; $i++) {
    $currentPost = new Post();
    $currentPost->setAuthor($this->getReference('user' . $i, User::class));

    $entityManager->persist($currentPost);
    $posts[] = $currentPost;
}

$entityManager->flush();
```

Does this code look familiar? Yes, it's native Doctrine fixture! All we need to run this code is `doctrine/data-fixtures` and `doctrine/orm` or `doctrine/mongodb-odm` packages. No external dependency, no weird magic syntax.

As a sign in bonus, we also got:

* PHPStan type check on passing correct type via reference:

```php
$currentPost->setAuthor($this->getReference('user' . $i, User::class));
```

* much better maintainability - if we rename `setAuthor()` to `setWriter()`, require strict types in `Post` or change them, IDE, Rector and PHPStan are now able to help - this was not possible when we used magic Alice setter via `"author"` string.

<br>

Of course, there are some edge cases that use deep Alice magic features, but we can easily fix them by using simple PHP. In our case, it was like 5 % of fixtures, nothing weird we could not solve rings a bell.

Now we have ~100 PHP Alice fixture files to convert to native Doctrine fixtures. With GPTs and Rector its doable work under fulltime month. The trick is to start with Alice fixtures with least amount of references (dependencies) and convert those first.

In the end, we're left with 3-4 files that need extra care, but we had enough experience and courage to deal with those as well.

<br>

Let's see our list of goals now:

* fix references, so we have one way to reference objects in fixtures - **done!**
* remove Nelmio, Hautelook, Alice and Faker dependencies - **done!**
* make Doctrine fixture much more maintainable - **done!**

<br>

## 4. Final Solution and Retrospective

I'm glad we waited and took it step by step, because every legacy upgrade is like a [climbing huge mountain](/blog/mountain-climbing). We must be careful, or we'll pick a path that will be deadly for both of us.

In there end, we got rid of all these packages and their legacy custom forks:

- `nelmio/alice`
- `hautelook/alice-bundle`
- `theofidry/alice-data-fixtures`
- `fzaninotto/faker`
- `doctrine/doctrine-fixtures-bundle`

<br>

All we need now are 3 custom PHP classes that load our fixtures and:

- `doctrine/data-fixtures` (last major release in 2024, actively maintained, 3.x behind corner)

I have never dreamed so slim upgrade, our initial Intro Analysis missed this nice and clear path.

