---
id: 302
title: "Tree Coding vs. Bush Coding"
perex: |
    How does circular reference look like? It is a point where you wait for your doctor; they wait for a state to accept the vaccine, and the state waits on people like you to come to the doctor. **Who has the responsibility?** Who can change the state?


    In the past, we used singletons and static calls to get anything anywhere instantly. But soon, we got into the circular references trap. Now **we moved to dependency injection** and this problem does not exist anymore... or does it?

---

With dependency injection, dependencies grow naturally into the form of a tree:

```php
final class VaccineRepository
{
    public function __construct(
        private Connection $connection
    ) {
    }

    public function get(int $id): Vaccine
    {
        return $this->connection->getFromTable('vaccine', $id);
    }
}
```

Then we use the repository inside a controller:

```php
final class DoctorController
{
    public function __construct(
        private VaccineRepository $vaccineRepository
    ) {
    }

    public function helpPerson(Person $person, int $vaccineId)
    {
        $vaccine = $this->vaccineRepository->get($vaccineId);
        $vaccine->injectInto($person);
        // ...
    }
}
```

The dependency tree is clear and obvious:

- `Controller` uses ↓
    - `Repository` uses ↓
        - `Connection` uses ↓
            - PostgreSQL database uses ↓
                - PostgreSQL Docker image

<br>

## Why Tree?

The significant advantage of trees is that they grow strong and tall. They're robust and can produce a lot of branches and leaves to rise to the sun. The sun gives them more energy to grow even further. A typical example of tree coding is dependency injection.

<div class="text-center">
    <img src="/assets/images/posts/2021/tree_vs_bush.jpg" class="img-thumbnail mt-4 mb-2">
    <br>
    <em>Tree Coding vs Bush Coding</em>
</div>

## Why Bush?

At first sight, a bush grows much faster than a tree. Its goal is to grow as many leaves as possible in a short time. It does not care about the future as much as a tree.
A typical example of bush coding is static method calls and static classes.

<br>

## Power of Tree Coding

Tree coding is similar to [climbing a huge mountain](/blog/2018/04/30/programming-climbing-a-huge-mountain/). Let's focus on the basic idea:

### What is The Goal of Tree Coding?

- It has a **clear hierarchy** from the bottom up, as you have guessed from dependency injection over static calls
- **Single place for single responsibility**
    - Need a new leaf? Put it on a branch with access to the Sun
    - Need a new branch? Put it next to another branch, where is enough space
    - Need a new tree trunk? Plant a new tree
- **It's intuitive** - everybody knows putting a branch inside a leaf is a bad idea
- It respects **[principle of least astonishment](https://softwareengineering.stackexchange.com/a/187462/148956)**

<a href="https://www.slideshare.net/GiovanniScerra/thoughtful-software-design">
    <img src="/assets/images/posts/2021/principal_of_least_astonishment.jpg" class="img-thumbnail mt-4 mb-2">
</a>

<br>

But bushes tend to spread faster, so it's only natural to assume they're already in your code. **Slowly growing and waiting for their time to slow you down**.

<img src="/assets/images/posts/2021/bushes_everywhere.jpg" class="img-thumbnail">

I have come around some bushes in my code before:

- [How I Got into Static Trap and Made Fool of Myself](/blog/2018/04/26/how-i-got-into-static-trap-and-made-fool-of-myself)
- [Removing Static - There and Back Again](/blog/2019/04/01/removing-static-there-and-back-again)

<br>

Today we look on 3 more examples that can be easily missed while slowly poluting your code:

## 1. Service Inside Value Object

By value object, we talk about any entity, value object, or data transfer object. Anything that can be created multiple times, like vaccines.

```php
final class Vaccine
{
    public function __construct(
        private string $uuid,
        private float $volume
    ) {
    }

    public function getShotVolume(int $shotRank): float
    {
        // ...
    }
}
```

How do we create a vaccine? Let's build a factory:

```php
$vaccineFactory = new VaccineFactory();
$vaccine = $vaccineFactory->create();
```

Pretty straight-forward, right?

<br>

Later that year, there is a big pressure on pharmaceutical companies, so programmers are under huge pressure to deliver. The vaccine object is now a bit more powerful:

```php
$vaccine->orderNewOne();
$vaccine->vaccinateHuman($human);
$vaccine->vaccinateAnimal($animal);
```

Our simple vaccine with a basic method is now **powerful service locator** that can vaccinate itself and order itself. It's like **giving birth to yourself**.

❌

What happened? In a factory, someone had an idea to put the services right into vaccine itself:

```diff
 final class Vaccine
 {
     public function __construct(
         private string $uuid,
         private float $volume,
+        private VaccinatingService $vaccinatingService,
+        private OrderingService $orderingService,
    ) {
    }
 }
```

That's not a way to go.

### How to Refactor?

It can happen to anyone. I'm aware of 3 such places in Rector itself that we plan to refactor. What can we do about it?

Let's refactor service from the object...

```diff
 final class Vaccine
 {
     public function __construct(
         private string $uuid,
         private float $volume,
-        private VaccinatingService $vaccinatingService,
-        private OrderingService $orderingService,
    ) {
    }
 }
```

...to service using the object:

```diff
+$orderingService = new OrderingService();
+$vaccinatingService = new VaccinatingService();

-$vaccine->orderNewOne();
+$orderingService->order($vaccine, 100);

-$vaccine->vaccinateHuman($human);
+$vaccinatingService->vaccinate($human);

-$vaccine->vaccinateAnimal($animal);
+$vaccinatingService->vaccinate($animal);
```

✅

## 2. Trait adding Dependency

Let's stay with a vaccine from previous example. We got into the same problem:

```php
$vaccine->orderNewOne();
$vaccine->vaccinateHuman($human);
$vaccine->vaccinateAnimal($animal);
```

But the constructor seems correct:

```php
final class Vaccine
{
    public function __construct(
        private string $uuid,
        private float $volume
    ) {
    }
}
```

What is going on?

```php
final class Vaccine
{
    use SomeHelperTrait;
}
```

Hm... what is that?

```php
trait SomeHelperTrait
{
    /**
     * In Symfony
     * @required
     */
    public OrderingService $orderingService;

    /**
     * In Nette
     * @inject
     */
    public VaccinatingsService $vaccinatingsService;
}
```

Oh, so it's using frameworks (Nette/Symfony) dependency injection to add dependencies... how ~~smart~~ bush coding!

❌

To be honest, I put similar crap code into Rector. Shame on me. It took [bunch](https://github.com/rectorphp/rector/pull/5385) [of](https://github.com/rectorphp/rector/pull/5466) [pull](https://github.com/rectorphp/rector/pull/5383) [request](https://github.com/rectorphp/rector/pull/5384) to get rid of completely.

### How to Refactor?

The same way as the example above. Get rid of traits...

```diff
 final class Vaccine
 {
-    use SomeHelperTrait;
 }
```

...and use services to handle the service work:

```php
$orderingService = new OrderingService();
$vaccinatingService = new VaccinatingService();

$orderingService->order($vaccine, 100);
$vaccinatingService->vaccinate($human);
$vaccinatingService->vaccinate($animal);
```

✅

After this incident, we [forbid trait completely](https://github.com/symplify/phpstan-rules/blob/master/docs/rules_overview.md#notraitrule) with PHPStan.

## 3. Using Nette Template to Transfer Data

Last but not least, there are template objects. It about any object that magically **sets its variables on the fly**:

```php
$someObject = new stdClass;
$someObject->name = 'Maybe' + 'Some';
$someObject->age = '100';
```

It's common to use these on unknown JSON response, but here **these variable names ("name", "age") and their values are known**.

<br>

I don't think Symfony or Laravel have those, but it's a typical code from Nette controller:

```php
use Nette\Application\UI\Presenter;

final class DoctorPresenter extends Presenter
{
    public function renderInstructions()
    {
        $this->template->welcomeHere = '...';
        $this->template->waitingRoom = '...';
    }
}
```

The `$template` is a base template object that holds assigned variables magically. Instead of a service locator, we have **a variable locator** (not good).
This simple construction of 2 variables is ok, but it will grow to a dirty bug.

<br>

To give you a comparison, this is how Symfony handles the problem:

```php
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

final class DoctorController extends AbstractController
{
    public function renderInstructions()
    {
        $this->render(__DIR__ . '/template/doctor/instructions.twig', [
            'welcomeHere' => '...',
            'waitingRoom' => '...',
        ]);
    }
}
```

We pass parameters into template **exactly once**, at **the time where it's needed**. Not sooner, not later, [just in time](https://blog.codinghorror.com/the-just-in-time-theory/), also called JIT.

Not twice by accident:

```php
$this->template->welcomeHere = '...';

// 100 lines bellow or in another method called
$this->template->welcomeHere = 'no';

// which is used?
```

...and not maybe:

```php
if ($this->isOpened) {
    $this->template->welcomeHere = '...';
}
```

<br>

Still, so far, that is framework convention it works well when used correctly.

**But what is not forbidden is allowed.** We can use the object for reading the data:

```php
use Nette\Application\UI\Presenter;

final class DoctorPresenter extends Presenter
{
    public function renderInstructions()
    {
        $this->template->welcomeHere = '...';

        if ($this->template->welcomeHere === '...') {
            $this->template->welcomeHere .= PHP_EOL. 'Thank you!';
        }
    }
}
```

This is very bad idea:

- we depend on `$this->template->welcomeHere` to **be set somewhere above** - how do you even check if magic property is set?
```php
if (isset($this->template->welcomeHere) && $this->template->welcomeHere !== null) {
    $this->template->welcomeHere .= PHP_EOL . 'Thank you!';
}
```

- we knew we assigned type of `string`, but **magic made the type disappear**
- we have no idea what the type is, **static analysis and Rector does not work**
- we're promoting to use a magical object for anything in `render()` methods
- we're saying it's ok **to set a single variable twice in one method**

❌

### How to Refactor?

How can we get out of this bush? It's simpler than the two examples before. What would you do?

Use a variable, of course.

```diff
-$this->template->welcomeHere = '...';
+$welcomeHere = '...';

-if ($this->template->welcomeHere === '...') {
+if ($welcomeHere === '...') {
-    $this->template->welcomeHere .= PHP_EOL. 'Thank you!';
+    $welcomeHere .= PHP_EOL. 'Thank you!';
-}

+$this->template->welcomeHere = $welcomeHere;
```

This way we:

- use a variable for a *variable* content (a content that might change and we expect it to change)
- **we separate a templating system from the construction of parameters**
- we have a type of `string`, so does PHPStan and Rector
- and if we know the template parameters types... we can... wait, that's a topic for another post

✅

We [made a rule for PHPStan](https://github.com/symplify/phpstan-rules/blob/master/docs/rules_overview.md#nonettetemplatevariablereadrule) to keep an eye on us.

<br>

And that's it! Now we've refactored from **3 ground floor bushes to high trees**!

<br>

What was the hardest to spot bush coding you've met? How did you refactor it? Share in comments ↓

<br>

Happy coding!
