---
id: 419
title: "Alice, Nelmio, Hautelook, Faker - How to upgrade Doctrine Fixtures - Part 1"
perex: |
    Upgrading Doctrine Fixtures can be challenging work. It requires the coordination of four different package groups on Github. One cannot be upgraded without the other.
    <br>
    <br>
    We first prepare for the jump, then take the leap with confidence. I'm writing this post as I handle the upgrade, so my thoughts are fresh, and the process is ongoing. It may evolve based on new information. Let's see where the rabbit hole goes.
---

What is the upgrade plan?

```diff
-hautelook/alice-bundle:^1.*
+hautelook/alice-bundle:^2.*

-nelmio/alice:^2.*
+nelmio/alice:^3.*

-fzaninotto/faker
+fakerphp/faker
```

```diff
-doctrine/data-fixtures:^1.5
+doctrine/data-fixtures:^1.7

-doctrine/mongodb-odm-bundle:^3.*
+doctrine/mongodb-odm-bundle:^5.*
```

<br>

But first, we must prepare to make the upgrade smooth a walk in the park.


<br>

## 1. Prepare: Teach Data Fixtures to Give Feedback

Before we start the upgrade itself, we have to [setup a fast feedback loop](/blog/2020/01/13/why-is-first-instant-feedback-crucial-to-developers) so we can fix broken code quickly.

To load PHP data fixtures, we run the native ODM/ORM bundle command:

```bash
// in ORM
bin/console doctrine:fixtures:load

// in ODM
bin/console doctrine:mongodb:fixtures:load
```

These commands find all PHP fixtures, put them in order, and load them to the database.

But there is one ["Volkswagen issue"](https://github.com/hugues-m/phpunit-vw) - what happens if Doctrine fixture throws an error? Command continues **without failing nor any report**.

<br>

Why is that? Both commands run [`AbstractExecutor`](https://github.com/doctrine/data-fixtures/blob/5ee102b61742edd60fb0b80412f38c71761441b1/src/Executor/AbstractExecutor.php#L122) class under the hood, that only report about fixture file loading *has started*:

```php
public function load(ObjectManager $manager, FixtureInterface $fixture)
{
    if ($this->logger) {
        $this->log('loading ' . $prefix . get_class($fixture));
    }

    // ...

    $fixture->load($manager);
    $manager->clear();
}
```

How do we get an error output on fail? The **logger should output something on error**.

<br>

We can verify this with a simple unit test or [a 3-line vendor patch](/blog/2020/07/02/how-to-patch-package-in-vendor-yet-allow-its-updates) applied on `AbstractExecutor`:

```diff
--- /dev/null
+++ ../lib/Doctrine/Common/DataFixtures/Executor/AbstractExecutor.php
@@ -116,7 +116,12 @@
             $fixture->setReferenceRepository($this->referenceRepository);
         }

-        $fixture->load($manager);
+        try {
+            $fixture->load($manager);
+        } catch (\Throwable $throwable) {
+            $this->log(sprintf(
+                'Error executing "%s" with error: "%s"',
+                get_class($fixture),
+                $throwable->getMessage()
+            );
+        }
+
         $manager->clear();
     }
```

We could print one error and stop the run. But if we have 20 fixture files, we want to run all of them and get all errors at once.

<br>

Great! **Now we get feedback in the output if the fixture fails**.
Ideally, this should be in the command itself. It should return a non-zero code on the failure of any called fixture.


<br>

What's the next step to get ready?

<br>

## 2. Prepare: Make Data Fixtures Write-only

Typically, the database fixtures are artificial data that we put into an empty database. Then we run a bunch of tests on it and can throw it away. Data fixtures should not interact with the database in any way. If we need to connect to the database, we [use references](https://symfony.com/bundles/DoctrineFixturesBundle/current/index.html#sharing-objects-between-fixtures).

<br>

References are located in a simple array with the following:

* *made up string* => *specific fixture entity*

<br>

<em style="font-size: 2em; color: red;">❌</em> &nbsp;  Saying that there should not be any calls in fixtures that *read* from the database:

```php
$promptRepository = $this->entityManager->getRepository(Prompt::class);
$prompt = $promptRepository->get(5);

$this->entityManager->find(Prompt::class, 5);
```

Instead, set the object by reference and get it back:

```php
// set in one fixture class
$this->setReference('prompt-5', $prompt);

// get in another
$prompt = $this->getReference('prompt-5');
```

**Make sure there are no read calls in your fixtures, otherwise we tangle dependencies together.**
Once we have write-only fixtures, we can run a separate CI job that tests that fixtures are correctly loaded.

Again, we get **instant feedback** and can iterate quickly.

<br>

## Check fixtures are read-only in CI

But how do we ensure that fixtures are also appropriately loaded in the future? What if the new developer joins the team in a year and starts to fetch data from the database?

PHPStan to the rescue!

<br>

Create a custom `NoRepositoryCallInDataFixtureRule` rule and register in `phpstan.neon`:

```php
use Doctrine\Common\DataFixtures\FixtureInterface;
use PhpParser\Node;
use PhpParser\Node\Expr\MethodCall;
use PhpParser\Node\Identifier;
use PHPStan\Analyser\Scope;
use PHPStan\Rules\Rule;

final class NoRepositoryCallInDataFixtureRule implements Rule
{
    public function getNodeType(): string
    {
        return MethodCall::class;
    }

    /**
     * @param MethodCall $node
     * @return string[]
     */
    public function processNode(Node $node, Scope $scope): array
    {
        if (! $scope->isInClass()) {
            return [];
        }

        $classReflection = $scope->getClassReflection();
        if (! $classReflection->isSubclassOf(FixtureInterface::class)) {
            return [];
        }

        if (! $node->name instanceof Identifier) {
            return [];
        }

        $methodName = $node->name->toString();
        if (! in_array($methodName, ['getRepository', 'find', 'findAll', 'findBy', 'findOneBy'])) {
            return [];
        }

        return [
            'Data fixtures cannot use repository calls, as they are written only.'
        ];
    }
}
```

The knowledge is encoded in the codebase, and PHPStan has our back.

<br>

## 3. Prepare: There is PHP, and there is YAML

### The PHP Fixtures

I assumed there was a single type of test fixture, but that's not the case. There are native Doctrine Fixtures, which are PHP classes that extend `Doctrine\Common\DataFixtures\AbstractFixture`.

To run them, we need a native package:

```bash
composer require doctrine/data-fixtures --dev
```

And one of the bundles:

```bash
composer require doctrine/mongodb-odm-bundle --dev
composer require doctrine/doctrine-bundle --dev
```

We load them with a single CLI command:

```bash
// in ORM
bin/console doctrine:fixtures:load

// in ODM
bin/console doctrine:mongodb:fixtures:load
```

### The YAML fixtures

Now, the next is YAML fixtures, that make use of [nelmio/alice](https://github.com/nelmio/alice):

```bash
composer require nelmio/alice --dev
composer require fzaninotto/faker --dev
```

I never understood this package's value, as we can achieve the same result with native PHP fixtures, but they're sometimes used together.

A separate bundle handles YAML fixtures:

```bash
composer require hautelook/alice-bundle --dev
```

And loaded by a **separate command**:

```bash
bin/console hautelook_alice:doctrine:fixtures:load
```

That means instead of 2 Doctrine-maintained packages, we have 5 to upgrade.

Using these YAML fixtures increases the upgrade price by 4-5 fold without adding much value.


<br>

These YAML files are missed by  IDE class rename and IDE method rename, and there is no support by PHPStan and Rector. This creates bugs, surprises, and unnecessary maintenance. To get all the benefits above, we migrate them [from YAML to PHP](/blog/2020/07/16/10-cool-features-you-get-after-switching-from-yaml-to-php-configs/).

<br>

Let's say we work with PHP and YAML fixtures now.

## 4. Execute: Bump Alice Bundle 1 to 2

The original hautelook/AliceBundle is no longer maintained and [Github repository even returns 404](https://github.com/nelmio/alice/issues/1089). Fortunately, there is a replacement - [theofidry/AliceDataFixtures](https://github.com/theofidry/AliceBundle), but the package name remains the same.

What should be done then? Change version in `composer.json`:

```diff
 {
     "require-dev": {
-        "hautelook/alice-bundle": "^1.0"
+        "hautelook/alice-bundle": "^2.0"
     }
 }
```

The fixture command for YAML fixtures can be dropped, as both fixtures anore now handled by the first command:

```diff
 bin/console doctrine:fixtures:load
-bin/console hautelook_alice:doctrine:fixtures:load
```

## 5. Execute: Bump Nelmio/Alice 2 to 3

Nelmio uses Faker under the hood. But **the Faker package was [sunset in 2020](https://marmelab.com/blog/2020/10/21/sunsetting-faker.html)**. Fortunately - we have a replacement package, `fakerphp/faker`.

First, let's update the `composer.json`:

```diff
 {
     "require-dev": {
-        "nelmio/alice": "^2.0",
+        "nelmio/alice": "^3.0",
-        "fzaninotto/faker": "^1.0"
+        "fakerphp/faker": "^1.23"
     }
 }
```

<br>

Great! Now we have read-only test fixtures and bumped the version for 2 out of 5 packages. We've added a custom PHPStan rule to have our back and run tests on fixtures to make them loadable.

It's enough work for one run, so we'll continue next time.

<br>

## Follow-up and Reflection

Looking at all the work, is this the best way to add test fixtures? How can we make the fixtures **easy to maintain in the next 5 years**?

Instead of having 5 packages to handle, there could be just one. Instead of a PHP + YAML mix, there should be just PHP so PHPStan, IDE, and Rector can do the hard work for us.

There is also [foundry](https://github.com/zenstruck/foundry) package, that handles fixtures in PHP exclusively way. The team behind it released [Foundry 2 just this summer](https://les-tilleuls.coop/en/blog/foundry-2-is-now-available-new-features-and-migration-path). The syntax is also Laravel-like, so test fixtures are easier to use for more PHP devs.

There is some food for thought. Let's see how the upgrade goes.

<br>

Happy coding!
