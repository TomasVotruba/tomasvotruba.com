---
id: 425
title: "Alice, Nelmio, Hautelook, Faker - How to upgrade Doctrine Fixtures - Part 2"
perex: |
    In the first part, [we've kicked off the plan](/blog/alice-nelmio-hautelook-faker-and-how-to-upgrade-doctrine-fixtures-part-1) to upgrade all these packages to their latest version, like a blind map into unknown territory. Since then, we've put in a couple of months of hard work and climbed the terrain.

    Today, we look at the practical steps we've taken and the new challenges we discovered [after the first hill](/blog/mountain-climbing).
---

## 1. Flip YAML fixtures to PHP

Alice 2 supports both YAML and [PHP fixtures](https://github.com/nelmio/alice/blob/v2.3.0/doc/complete-reference.md#php). The YAML fixtures are more popular, but PHP fixtures are more practical. There we can use PHP code to generate dynamic data, use constants, or call simple typed functions.

We have 100+ files... how do we flip them? If we load a YAML, it's parsed into a bare PHP array. But how do we convert the other direction, to a PHP syntax?

<br>

[PHPParser](https://github.com/nikic/PHP-Parser/) to the rescue:

```php
use PhpParser\BuilderHelpers;
use PhpParser\PrettyPrinter\Standard;
use Symfony\Component\Yaml\Yaml;

$yaml = Yaml::load($yamlContent);
$expr = BuilderHelpers::normalizeValue($yaml);

$return = new Return_($expr);
$standard = new Standard();
$phpFileContent = $standard->prettyPrintFile([$return]);
```

Now we feed all YAML files to this script and we're done:

```diff
-/data-fixtures/users.yml
+/data-fixtures/users.php
```

<br>

We've wrapped this script into a command, and put the command into the [swiss-knife](https://github.com/rectorphp/swiss-knife/#9-convert-alice-fixtures-from-yaml-to-php) package, so anyone can run it in CLI:

```bash
vendor/bin/swiss-knife convert-alice-yaml-to-php fixtures
```

Done!

<br>

## 2. Make PHP Fixtures useful again

We have all Alice fixtures in PHP, that's great. But they still all look like dumb strings:

```php
return [
    'App\Entity\User' => [
        'user1' => [
            'name' => 'Tom',
            'role' => '<(App\Enum\Role::ADMIN)>',
            'created' => '<timestampNow()>',
        ]
        // ...
    ],
];
```

What if we move the `User` entity to a different namespace? Or rename the `ADMIN` constant into `ADMINISTRATOR`? IDE will most likely forget to change these strings and our fixtures will fail.

#### Three steps to empower PHP fixtures

* use `::class` references over strings with [`StringClassNameToClassConstantRector`](https://getrector.com/rule-detail/string-class-name-to-class-constant-rector) Rector rule
* change "string constants" to real `constant::REFERENCES` using regex replacement in PhpStorm
* change "method references" to real `functions()` via regex - more on that in next step

<br>

How does our fixture file after we apply these 3 automated changes?

```diff
 return [
-    'App\Entity\User' => [
+    \App\Entity\User::class => [
         'user1' => [
             'name' => 'Tom',
-            'role' => '<(App\Enum\Role::ADMIN)>',
+            'role' => \App\Enum\Role::ADMIN,
-            'created' => '<timestampNow()>',
+            'created' => timestampNow(),
         ]
         // ...
     ],
 ];
```

We're now using native PHP, our fixtures are more fun to work with, and we can easily refactor them in the future.

// We can also use comments in PHP fixtures now.

<br>

But why did we change the `'<timestampNow()>'`? Let's look into the next step.


## 3. From static method in Faker provider to straightforward `functions()`

What does `'<timestampNow()>'` mean in Alice's fixture context?

* It's a reference to a method in a Faker provider, that's being loaded and interpreted by Alice
* A → B → C

It takes a while to figure out this complex relationship. What happens in the background? We have to register a service into a test container. This service has some public methods. Then we have to mark these services (no interface marker, so no change to use autoconfigure) with a tag, so Alice can find them. Then Alice finds them and tries to match strings in `<(maybeMethodOnOneOfProviders(100))>` into one of the public methods.

<blockquote class="blockquote text-center">
Magic... Magic everywhere.
</blockquote>

Don't ask me what happens when 2 providers have the same-named methods from 2 different classes or if one of them is `private`.

<br>

Such a Faker provider can look like this:

```php
final class SomeProvider
{
    public function timestampNow(): string
    {
        return \Carbon\Carbon::now()->timestamp;
    }
}
```

This method is not defined as `static`, but **it doesn't require any other service to work**. It's a static method or pure function without any dependencies.

<br>

We can extract this code to a more straightforward form:

```php
declare(strict_types=1);

function timestampNow(): string
{
    return \Carbon\Carbon::now()->timestamp;
}
```

Then we place this function into `tests/alice-functions.php` and load with `composer.json`:

```json
{
    "autoload-dev": {
        "files": [
            "test/alice-functions.php"
        ]
    }
}
```

Now we use native PHP that we IDE can **click-through right to the file and line it's defined in**!

<br>

## 4. But wait, there is more

Since we flipped strings to native PHP, we've also **enabled PHPStan to check type declarations** without running the code:

```diff
 return [
     \App\Entity\User::class => [
         'user1' => [
-            'age' => '<randomNumber("10", "50")>',
+            'age' => randomNumber("10", "50"),
         ]
         // ...
     ],
 ];
```

With the following function, we get an early report of `string` passed into an `int` error:

```php
function randomNumber(int $low, int $high): int
{
    // ..
}
```

Flip only **really static methods** to functions. What do they look like? A couple of lines, calling only native PHP functions, simple. Do not flip methods that require another service for now.

<br>

## 5. Teach Faker to Autoconfigure

As mentioned before, there is an extra layer of complexity with the Alice Faker loader. The latest Alice 3+ might have figured this out, but in Alice 2, we still have to tag every single Faker provider:

```yaml
services:
    Tests\Faker\Provider\FirstProvider:
        tags: [ { name: hautelook_alice.faker.provider } ]

    Tests\Faker\Provider\SecondProvider:
        tags: [ { name: hautelook_alice.faker.provider } ]

    Tests\Faker\Provider\ThirdProvider:
        tags: [ { name: hautelook_alice.faker.provider } ]

    Tests\Faker\Provider\FourthProvider:
```

Ups, we've missed tagging the last one and now the fixture loading fails with an unclear error message. That's annoying, right?

<br>

We should be able to load them all in one go, like this:

```yaml
services:
    Tests\Faker\Provider:
        ../../tests/Faker/provider
```

But how? There is no marker interface, like e.g. `EventSubscriberInterace` has.

<br>

If there is none, we make one:

```php
declare(strict_types=1);

namespace Tests\Contract;

interface FakerProviderInterface
{
}
```

We make all providers to implement this interface:

```diff
+use Tests\Contract\FakerProviderInterface;

-final class FirstProvider
+final class FirstProvider implements FakerProviderInterface
 {
     // ...
 }
```

Then we update the config with auto-tagging:

```diff
 services:
+    _instanceof:
+        Tests\Contract\FakerProviderInterface:
+            tags:
+                - { name: "hautelook_alice.faker.provider" }

     Tests\Faker\Provider:
         ../../tests/Faker/Provider
```

And we don't have to worry about missed Faker providers being registered correctly. We only create it, place it into the `/tests/Faker/Provider` directory and it's automatically registered. No more "don't forget to update a test config with new Fkaer provider class and also don't forget to tag it" errors.


## 6. From `(local)` entities to honest clear schema

Alice fixture has this "effective" feature that allows you to create entities without persisting. They're only used in the file they're defined in. All we need to do is add the magic string " (local)" after the entity class.

This sounds like memory optimizing process, but guess what code we wrote:

```php
return [
    'App\Entity\Role (local)' => [
        'admin1' => [
            'role' => 'admin'
        ]
    ],
    \App\Entity\User::class => [
        'user1' => [
            'role' => '@admin1'
        ]
    ],
];
```

Then in another file:

```php
return [
    'App\Entity\Role (local)' => [
        'admin1' => [
            'role' => 'supervisor'
        ]
    ],
    \App\Entity\User::class => [
        'user2' => [
            'role' => '@admin1'
        ]
    ],
];
```

Now we have 2 references to `@admin1` - 2 different references. To add more injury to the insult, we've lost PHP features of `::class` reference.

### Fixtures !== real world?

The `(local)` appendix creates an unrealistic database structure. In real database, we always have only a single unique role with id of `1`. Let's fix that.

* Remove the `(local)` keyword and extract those entities to their own fixture file (e.g. `tests/alice-fixtures/roles.php`)
* Fix differences if necessary, e.g. here would probably create `admin2` with a different role.

<br>

As a result, all roles are unique. If we want to add a new one, we use the `tests/alice-fixtures/roles.php` file. Clear, simple, and honest.

As a bonus, we get to use native PHP again:

```diff
 return [
-    'App\Entity\Role (local)' => [
+    \App\Entity\Role::class => [
         'admin1' => [
-             'role' => 'supervisor'
+             'role' => \App\Enum\Role::SUPERVISOR,
         ]
     ],
 ]
```

<br>

## 7. Question: How to share Doctrine Native and Alice References?

Let's say we use native Doctrine PHP fixtures to create a user under `@user1` reference:

```php
$this->addReference('user1', $user1);
```

Then in the Alice fixture, we want to link it:

```php
return [
    \App\Entity\Post::class => [
        'post1' => [
            'author' => '@user1'
        ],
    ],
]
```

But it fails with an error:

```bash
"@user1" reference not found
```

<br>

It seems like an obvious way to use references, right? But there is no `@user1` in Alice's context because **it's completely isolated** from native PHP Doctrine fixtures.

<br>

### How do we share references between those two?

I asked on [Github](https://github.com/nelmio/alice/issues/1237), X, and [Mastodon](https://mastodon.social/@votrubat/113827419907411822), but nobody seems to know:

<blockquote class="twitter-tweet"><p lang="en" dir="ltr">Looking for a simple way to link Doctrine and Alice fixtures. Maybe it&#39;s obvious but newbie in this area 😅<br> <br>Anyone knows?<a href="https://t.co/NDi0MbRhbY">https://t.co/NDi0MbRhbY</a></p>&mdash; Tomas Votruba (@VotrubaT) <a href="https://twitter.com/VotrubaT/status/1878390280979628227?ref_src=twsrc%5Etfw">January 12, 2025</a></blockquote>


<br>

Clues so far: there is a manual instance of `new ReferenceRepository` in an `AbstractExecutor` with a getter ([see Github](https://github.com/doctrine/data-fixtures/blob/ddda1ea852c30eef97f17439cffa528644480fe4/src/Executor/AbstractExecutor.php#L39-L46)):

```php
abstract class AbstractExecutor
{
    public function __construct(ObjectManager $manager)
    {
        $this->referenceRepository = new ReferenceRepository($manager);
    }

    public function getReferenceRepository()
    {
        return $this->referenceRepository;
    }

    // ..
}
```

But haven't succeeded to inject it to custom `AliceLooader` yet.

<br>

If you've been there and know the answer, share a clue in [the Github issue](https://github.com/nelmio/alice/issues/1237).

<br>

Until the next part, stay tuned, stay safe.

<br>

Happy coding!
