---
id: 435
title: "Alice, Nelmio, Hautelook, Faker - How to Upgrade Doctrine Fixtures - Part 3"
perex: |
    In previous parts ([part 1](/blog/alice-nelmio-hautelook-faker-and-how-to-upgrade-doctrine-fixtures-part-1) and [part 2](/blog/alice-nelmio-hautelook-faker-and-how-to-upgrade-doctrine-fixtures-part-2)) we looked at a situation where we are stuck with Alice, Nelmio, Hautelook, Faker legacy mess and need a way out to modern maintainable Doctrine.

    This is the final part. **The upgrade is finished and closed for the time being**. In an unexpected way, it was fast, easy to migrate to, and will be even easier to maintain. It took me a couple of months of deep thinking and experiments, because there was a trap solution that would take us 6-8 months of hard work and would create even more legacy code. Lesson learned: don't fall for marketing and GPT answers.

    Let's look into it.
---

In [part 1](/blog/alice-nelmio-hautelook-faker-and-how-to-upgrade-doctrine-fixtures-part-1) we looked at the current situation of our legacy project. In [part 2](/blog/alice-nelmio-hautelook-faker-and-how-to-upgrade-doctrine-fixtures-part-2) we took the low-hanging fruit first:

* reduce dependency on Faker fixtures - use static PHP helper functions
* make use of PHPStan and Rector feedback - flip Alice fixtures from YAML to PHP
* explore problem of references - native Doctrine fixture and Alice fixture references are not shared

<br>

What is left ahead of us?

* ~~upgrade Alice to latest version, flip to Foundry~~
* fix references, so we have one way to reference objects in fixtures
* remove Nelmio, Hautelook, Alice and Faker dependencies
* make Doctrine fixtures much more maintainable

## "Migrate package A to package B" Trap

**The first task was a trap.** We had planned this upgrade, using GPT to get there faster, but it felt like something was wrong. I postponed this single task for many months. Was it out of fear? Was it because it didn't feel like a better solution? After all, we'll have the same fixture mess, just instead of *Alice* we'll call it *Foundry*.

In my experience, it's good to listen to these feelings and **not rush into the solution** to "make the client happy". Intuition is trying to tell me there is a better solution, I just don't see it yet. If we'd started this upgrade, it would have cost our client 2-3 months of work. It would look like a better solution because "it took more time and effort", but in reality it would only create more mess for the dev team to work with from now on.


<blockquote class="blockquote text-center">
    Best code = no code.<br>
    Best dependency = no dependency.
</blockquote>


## 1. One way to work with Fixtures

I felt something was wrong, but I didn't know the way out yet. So I postponed the task as long as I could. I started working on another issue: how to use object references in both native fixtures (`doctrine/data-fixtures`) and Alice fixtures (`nelmio/alice`)? If we crack this goal, maybe the rest of the upgrade will reveal itself.

After a couple of days of back and forth debugging, I found a very nice and tidy... dead end. We have 2 commands to load fixtures:

```bash
bin/console doctrine:fixtures:load
bin/console hautelook:fixtures:load
```

Both are run separately... in PHP, every process starts and dies with its own memory. That's why all the data we create and store in memory in the 1st command is never available in the 2nd command. We came up with an obvious solution: use a single command to load all fixtures:

```bash
bin/console app:load-doctrine-fixtures
```

That's it! We created our own simple command to load Doctrine fixtures. That way we also accidentally solved [patching from part 1](/blog/alice-nelmio-hautelook-faker-and-how-to-upgrade-doctrine-fixtures-part-1).

<br>

What is loading fixtures, after all?

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

That's the command in its gist. We flipped to our custom command and dropped the Doctrine fixtures one. Side effect?

* We can drop the `doctrine/doctrine-fixtures-bundle` package! One less dependency to worry about.

Now we just add loading of Alice fixtures to this command, and we have a single command - one that uses one `ReferenceRepository` with all the references. It takes a bit of hacking, back and forth, using `LoaderInterface`, but in a gist:

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

We combined both snippets into a single command, and boom - now we have a single command to load fixtures, and Alice files can start using references to native fixtures!

* As a side effect, we can drop the `hautelook/alice-bundle` package too!

Its only job is to integrate Alice fixtures with Doctrine, but we already have our own minimalistic integration, so we don't need it anymore.

<br>

### Let's see our list of goals

* fix references, so we have one way to reference objects in fixtures - **done!**
* remove Nelmio, Hautelook, Alice and Faker dependencies - **Hautelook done!**
* make Doctrine fixtures much more maintainable

## 2. References are the King

References in both native and Alice fixtures are often bare strings. Yes, we already have all Alice fixtures flipped from YAML to PHP (see [part 2](/blog/alice-nelmio-hautelook-faker-and-how-to-upgrade-doctrine-fixtures-part-2)), but we still have string references. That is not ideal, because we can easily make a typo and break the fixtures loading.

```php
return [
    \App\Entity\Post::class => [
        'post1' => [
            'author' => '@user1'
        ],
    ],
]
```

How to fix that? We created a couple of Rector rules to flip all references anywhere in the project to be clear and safe - using class constant lists (you may call them "enums"):

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

This way, one can easily use the IDE to click through to the reference origin and see how it's used. We add a couple of custom PHPStan rules to enforce reference usage in all places, and the fixture files are much more maintainable: we can instantly see what is a plain string value and what is a reference.

Apply this approach not only to fixtures, but to all places where `ReferenceRepository` is used:

```diff
-$this->addReference('user1', $user);
+$this->addReference(UserReference::USER_1, $user);

-$this->getReference('user1');
+$this->getReference(UserReference::USER_1);
```

## 3. Remove Nelmio, Hautelook, Alice and Faker dependencies

So what now? We use references everywhere we can, we have a single command to load fixture files, we can use references in both native and Alice fixtures, but we still have 4 dependencies to maintain. The question to ask is: do we *really* need them?

The Faker dependency is a collection of a couple of basic functions - in our case, related to time, name and a couple of emails. Everything else was our data. So we replaced it with a couple of classes with static method calls. Bye bye `fzaninotto/faker`!

<blockquote class="blockquote text-center">
    We cannot solve our problems<br>
    with the same thinking we used when we created them.
</blockquote>

Using Alice fixtures is quite a complicated approach to creating objects. Let's take a simple reason to use Alice fixtures:

```php
return [
    \App\Entity\Post::class . '{1..25}' => [
        'post1' => [
            'author' => '@user{{ current }}'
        ],
    ],
]
```

This will create 25 posts with various authors. But why do we need it in exactly this syntax?

I mean, YAML is cool in most cases, but YAML is also the #1 reason Symfony 2 legacy projects are nearly impossible to upgrade.

What if... we use plain PHP?

```php
$posts = [];
for ($i = 0; $i < 25; $i++) {
    $currentPost = new Post();
    $currentPost->setAuthor($this->getReference('user' . $i, User::class));

    $entityManager->persist($currentPost);
    $posts[] = $currentPost;
}

$entityManager->flush();
```

Does this code look familiar? Yes, it's a native Doctrine fixture! All we need to run this code are the `doctrine/data-fixtures` and `doctrine/orm` or `doctrine/mongodb-odm` packages. No external dependency, no weird magic syntax.

<br>

As an added bonus, we also got:

* PHPStan type check on passing the correct type via reference

```php
$currentPost->setAuthor($this->getReference('user' . $i, User::class));
```

* much better maintainability - if we rename `setAuthor()` to `setWriter()`, require strict types in `Post` or change them, the IDE, Rector and PHPStan are now able to help - this was not possible when we used the magic Alice setter via the `"author"` string.

<br>

Of course, there are some edge cases that use deep Alice magic features, but we can easily fix them by using simple PHP. In our case, it was about 5 % of fixtures - nothing weird that we could not solve comes to mind.

Now we have ~100 PHP Alice fixture files to convert to native Doctrine fixtures. With GPTs and Rector, it's doable in under a full-time month. **The trick is to start with the Alice fixtures with the least amount of references (dependencies) and convert those first.**

In the end, we're left with 3-4 files that need extra care, but we had enough experience and courage to deal with those as well.

<br>

### Let's see our list of goals

* fix references, so we have one way to reference objects in fixtures - **done!**
* remove Nelmio, Hautelook, Alice and Faker dependencies - **done!**
* make Doctrine fixtures much more maintainable - **done!**

<br>

## 4. Final Solution and Retrospective

I'm glad we waited and took it step by step, because every legacy upgrade is like [climbing a huge mountain](/blog/mountain-climbing). We must be careful, or we'll pick a path that will be deadly for both of us.

<br>

In the end, we got rid of all these packages and their legacy custom forks:

- `nelmio/alice`
- `hautelook/alice-bundle`
- `theofidry/alice-data-fixtures`
- `fzaninotto/faker`
- `doctrine/doctrine-fixtures-bundle`

<br>

All we need now are 3 custom PHP classes that load our fixtures and:

- `doctrine/data-fixtures` (last major release in 2024, actively maintained, 3.x around the corner)

I have never dreamed of such a slim upgrade - our initial Intro Analysis missed this nice and clear path.

