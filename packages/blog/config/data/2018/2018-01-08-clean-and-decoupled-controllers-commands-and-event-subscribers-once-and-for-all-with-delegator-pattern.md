---
id: 71
title: "Clean and Decoupled Controllers, Commands and Event Subscribers Once and for All with Delegator Pattern"
perex: |
    Do you write your application for **better future sustainability** or just to get paid for it today?
    If you're the first one, you care about design patterns. I'm happy to see you!
    <br>
    <br>
    Today I will show you **why and how to use *delegator pattern*** in your application so it makes it to the pension.
tweet: "New post on my blog: Clean and Decoupled Controllers, Commands and Event Subscribers Once and for All with Delegator Pattern #php #cleancode #symfony #icology"
tweet_image: "/assets/images/posts/2018/delegator/trash-everywhere.jpg"
---

<br>

<blockquote class="blockquote text-center">
    "Every code is trash!"
</blockquote>

<br>

You'll see. But before we dig into code... what are the reasons to write sustainable code and how it looks like?

## Why Should You Care About Future Sustainability

There are 3 levels of developers by the time-frame focus they work on. Every group has it's advantages and disadvantages. You'll soon see which one you fit in.

### 1. Developers who **Code for NOW**

This project. Single site for 2018 elections. Microsite for new product release in 2019. Include anything that is hype in socials last year.

**If the code would be a trash** (literally!), they'd throw everything to 1 bag or maybe right in the city streets or nature. **Someone
else will handle cleaning up the city** #yolo

<img src="/assets/images/posts/2018/delegator/trash-everywhere.jpg" class="img-thumbnail">

### 2. Developers who Code for next 1-2 YEARS

The project has tests, continuous integration, uses stable packages with 1.0 release. It's startup or a project with profit. The team is fine and slowly growing. It's their first or second project and they try to take good care about it, with experiences they have.

They don't make any mess around the city and **put all trash to 1 trash bin**. Take them out regularly once a week. They're nice to the world. Well, at least at first sight.

<img src="/assets/images/posts/2018/delegator/orbit-junk.jpg" class="img-thumbnail">


### 3. Developer who Code for next 5-10 YEARS - Future Sustainability

...or at least with that mindset in their minds. The code won't probably work with PHP 9.0, but they do their best to make it as easy as possible to do so.

They have great experience with handful of project described in previous group. They already worked on 5 open-source projects **they need to last as long as possible without as little maintenance as possible**.

**To the trash again...**

It's like recycling plastic bags, glass bottler and papers.

You put effort to it:

- create space in your home to keep 3 separated trash bins,
- explain everyone to use them and split every product to own bin
- and when it's full you take these bags out for 5 minutes walk to their destination.

<img src="/assets/images/posts/2018/delegator/manage-it-right.jpg" class="img-thumbnail">

**Though you never see the trash again, you believe it's good for your future self and for your children**, to keep planet clean and away from trash lands. Economists would call it *positive externality*.


<br>

Now you know **why it's good to separate waste** (= code), let's get to real code.

## 4 years in life of New Web Application

Let's have a project that was born in 2015 and see how it slowly grew. It will eventually use all patterns we described in the beginning - except *delegator*, which is unfortunate for the investors of this project.

### 2015 - Start with Controllers

Project start with few controllers that contain most of logic. It's fast and easy to add new controller with new logic.

By the end of the year there are 50 controllers like this:

```php
class ProductController extends Controller
{
    public function allAction()
    {
        $allProducts = $this->getEntityManager()->getRepository(Product::class)
            ->fetchAll();

        return new TemplateResponse('all.twig', [
            'allProducts' => $allProducts
        ]);
    }
}
```

Also, it's in the documentation of the framework, so it must be [the best practise](https://matthiasnoback.nl/2014/10/unnecessary-contrapositions-in-the-new-symfony-best-practices).

Little we know, here starts our [Broken Window Theory](https://blog.codinghorror.com/the-broken-window-theory), the most underestimated effect from social science in software world.


### 2016 - Add few Commands

Application grows and the size needs pre-caching handled by running commands in CRON. So you start using [Symfony\Console](/blog/2019/08/12/standalone-symfony-console-from-scratch). You get inspired by `Controller`, because `Command` looks like it and by the end of year, there are many command like this one:

```php
class CacheProductsCommand extends Command
{
    /**
     * @var EntityManager
     */
    private $entityManager;

    public function __construct(EntityManager $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function execute()
    {
        $allProducts = $this->entityManager->getRepository(Product::class)
            ->fetchAll();

        // cache them all
    }
}
```

### 2017 - Add just few more EventSubscribers

It's 2017, AI is on hype and you start thinking about product recommendation feature. You use [EventSubscribers](/blog/2019/08/05/standalone-symfony-event-dispatcher-from-the-scratch) that saves many information about user behavior and return best producs just for him.

```php
class RecommendedProductsEventSubscriber implements EventSubscriber
{
    /**
     * @var EntityManager
     */
    private $entityManager;

    public function __construct(EntityManager $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public static function subscribe()
    {
        return ['onPageVisit' => 'setRecommendedProducts'];
    }

    public function setRecommendedProducts(BehaviorPatternEvent $behaviorPatternEvent)
    {
        $productRepository = $this->entityManager->getRepository(Product::class);

        $product = $productRepository->findBestByBehavior($behaviorPatternEvent->getBehavior());
        $behaviorPatternEvent->setRecommendedProducts($product;
    }
}
```

So far so good?


### 2018 - Year of Changes

<blockquote class="blockquote text-center mt-5 mb-5">
    "Change is the only constant."
    <footer class="blockquote-footer">John Candee Dean</footer>
</blockquote>

New owner with technical skills comes the the play. And he wants to finally use `VueJs`, the company is now big enough to use Docker as standards and **there are more programmers that know [Eloquent](https://laravel.com/docs/eloquent) than [Doctrine](/blog/2017/03/27/why-is-doctrine-dying) in his country**:

*"Alibaba is catching up and we might lose the position #1 leader on market. Just switch it to Eloquent, so we can hire and on board faster.*"

Ups! Your code is coupled to the Doctrine and Symfony pretty hard. You're standing in front of important question: **Do you get extra $ 10 000 to refactor the code?**

Posing this question, now we finally understand [Broken Window Theory](https://blog.codinghorror.com/the-broken-window-theory)...

<img src="/assets/images/posts/2018/delegator/broken-window.jpg" class="img-thumbnail">

...because we have personal experience with going it the wrong way. Little to late.

## Prevention over Experience

- What could be done better?
- Could you prevent this?
- **Do you separate your trash or do you wait till your country becomes plastic land?**

<img src="/assets/images/posts/2018/delegator/plastic-land.jpg" class="img-thumbnail">

No. You think for **the future** with prevention!

<blockquote class="blockquote text-center mt-5 mb-5">
    "Plan like you will live forever, and then live like there is no tomorrow."
    <footer class="blockquote-footer"> Mahatma Gandhi</footer>
</blockquote>

Same can be applied to your code.

### Delegator Pattern to the ~~Rescue~~ Prevention

This is what we did in [Lekarna.cz](https://www.lekarna.cz) - The biggest online drugstore in the Czech Republic. It started on Nette 2.4 and Doctrine 2.5, with [monorepo approach](/blog/2017/12/25/composer-local-packages-for-dummies).

When a class pattern is marked as *delegator*, it **can't contain any direct connection to database layer** (Doctrine in this case).

Among most popular delegators belongs:

- Controller
- Command
- EventSubscriber
- Presenter or Component in [Nette](https://nette.org)
- CommandHandler from [CQRS](https://ocramius.github.io/ShittyCQRSPresentation) etc.

In Lekarna, these classes can only use own service to access products - `ProductRepository`:

```php
class ProductRepository
{
    public function __construct(EntityManager $entityManager)
    {
        $this->repository = $entityManager->getRepository(Product::class);
    }

    public function fetchAll()
    {
        return $this->repository->fetchAll();
    }
}
```

You don't want to check this in code reviews (imagine 5 years doing it), just [write a sniff for that](/blog/2017/07/17/how-to-write-custom-sniff-for-code-sniffer-3) and forget it.

This will remove any database layer reference from all our `delegators`:

```php
class CacheProductsCommand extends Command
{
    /**
     * @var ProductRepository
     */
    private $productRepository;

    public function __construct(ProductRepository $productRepository)
    {
        $this->productRepository = $productRepository;
    }

    public function execute()
    {
        $allProducts = $productRepository->fetchAll();

        // cache them all
    }
}
```

Do you need to switch database layer? Easy!

```diff
 class ProductRepository
 {
-     public function __construct(EntityManager $entityManager)
-     {
-         $this->repository = $entityManager->getRepository(Product::class);
-     }
+     public function __construct(Eloquent $eloquent)
+     {
+         $this->repository = $eloquent->getRepository(Product::class);
+     }

      public function fetchAll()
      {
          return $this->repository->fetchAll();
      }
 }
```


**1 day of work instead of hundreds of hours.** That's what delegator pattern is all about.


## Start with Best on the Knowledge Market

When you start with the best known approach possible, you'll end-up in well grown project that you'll love to contribute more the older it gets.

**Just like with children - invest in them right from the start and it will get back to you**!

<br>

Happy Children and Project Raising!
