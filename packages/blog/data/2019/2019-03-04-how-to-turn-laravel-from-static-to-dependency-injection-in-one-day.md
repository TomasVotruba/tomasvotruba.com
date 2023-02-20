---
id: 191
title: "How to turn Laravel from Static to Dependency Injection in one Day"
perex: |
    A framework is just a tool. Each teaches you coding habits you need to use them effectively.
    Like Laravel gives you speed at prototyping with static "facades". But the applications grows, so does the team, so does your skill and **you start to prefer constructor injection**.


    What then? Switch framework or rewrite? But what if all you need is to **switch single pattern**?
tweet: "New Post on #php 🐘 blog: How to turn #Laravel from Static to Dependency Injection in one Day"

updated_since: "November 2020"
updated_message: |
    Switched deprecated `--set` option to `ecs.php` config.
---

I don't use Laravel in my own life, but I follow the community closely. It likes the idea of Contracts from day 1 and it's also part of Rector upgrade set.

Recently I read the [Moving away from magic—or: why I don’t want to use Laravel anymore](https://www.freecodecamp.org/news/moving-away-from-magic-or-why-i-dont-want-to-use-laravel-anymore-2ce098c979bd/) on medium by *Niklas Schöllhorn*.

<a href="https://medium.freecodecamp.org/moving-away-from-magic-or-why-i-dont-want-to-use-laravel-anymore-2ce098c979bd">
    <img src="/assets/images/posts/2019/laravel/best-seller.png" class="img-thumbnail">
</a>

Read the post, it's really beautifully written with respect to the framework by somebody, who uses is for 2 years. Also, I think it's **about the natural evolution of code and how to deal with it**, not Laravel itself.

Niklas finishes the post with a reasonable statement:

<blockquote class="blockquote text-center">
"Other frameworks and tools come with better-designed defaults and less magic. So for now, I’ll say goodbye to Laravel."
</blockquote>

But is switching the framework (language, girlfriends, parents...) really the best solution?

## Do You Leave Your Kid on Puberty?

Thing is, every framework (and developer) has this step in evolution. So did [Symplify with static methods](/blog/2018/04/26/how-i-got-into-static-trap-and-made-fool-of-myself/). Each framework I used in some period of its evolution used static:

**Nette**

```php
<?php

$result = Container::get('some_service')->someMethod();
```

**Symfony**

```php
<?php

$result = $this->get('some_service')->someMethod();
```

**Laravel**

```php
<?php

$result = SomeService::someMethod();
```

## Help Your Parents Grow

I don't think the point is to leave the framework but to help it to the quality. E.g. Symfony **constructor injection autowiring wasn't always there**. It was first suggested around Symfony 2.3 but strictly rejected by Fabien as an anti-pattern.

It took many bundles like `Kutny\AutowiringBundle`, `Skrz\Autowiring` or `symplify\autowiring`, closed issues and PRs with proof of concept before the Symfony core team was convinced enough to accept next autowiring-PR, **that laid the foundation to autowiring we use today**.

This is a completely normal process of community learning.

## "So How do I get rid of Static in Laravel?"

One option is change pattern by **switching to another framework**, that already promotes the feature you want. I did this once with Nette when the development stopped around 2015. First, I tried to add a feature, make packages with integration and propose PRs and issues. But after a few years of failure, I decided to switch to Symfony.

The second option is to try the approach in your framework, regardless of what is considered *best practice* or the *framework-way*.


## How move to Dependency Injection in Laravel?

I like the way suggested in [post above](https://medium.freecodecamp.org/moving-away-from-magic-or-why-i-dont-want-to-use-laravel-anymore-2ce098c979bd).

```diff
 <?php

 namespace App\Http;

 use App\Example;
 use Request;
 use Response;

 class ExampleController extends Controller
 {
+    /**
+     * @var ResponseFactory
+     */
+    private $responseFactory;
+
+    public function __construct(ResponseFactory $responseFactory)
+    {
+        $this->responseFactory = $responseFactory;
+    }
+
     public function store()
     {
         $example = 5;
-        return Response::view('example',
+        return $this->responseFactory->view('example',
             ['new_example' => $example]
         );
     }
 }
```

That looks great! If you have ~10 cases like that, you're done instead of reading this post.

But what if your project is commercially successful and there are 100+ cases like this? You'd probably think "it would be nice for my small pet project, but my boss wouldn't pay for that".

## Rector to the Rescue

These kinds of problems are *so 2018*. Recently Rector got on board [Laravel instant upgrades](https://github.com/rectorphp/rector/pulls?utf8=%E2%9C%93&q=laravel), first with Laravel 5.8.

Next impulse was the post by Niklas, so I've **converted his idea to Rector rule**. The change from facades to constructor injection can be done with new `laravel-static-to-injection`:

1. Install Rector

```bash
composer require rector/rector --dev
```

2. Create `rector.php` config with `SetList::LARAVEL_STATIC_TO_INJECTION` set

```php
use Rector\Laravel\Set\LaravelSetList;use Rector\Set\ValueObject\SetList;
use Rector\Config\RectorConfig;

return function (RectorConfig $rectorConfig): void {
    $rectorConfig->import(LaravelSetList::LARAVEL_STATIC_TO_INJECTION);
};
```

3. Run Rector

```bash
vendor/bin/rector process /src
```

No need to switch framework and you can enjoy new constructor injection in your Laravel application matter of minutes.

<br>

Happy coding!
