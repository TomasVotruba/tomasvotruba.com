---
id: 248
title: "How to Hydrate Arrays to Objects via Constructor"
perex: |
    One technology evolution sparks naturally another one. When electricity became accessible to masses, a huge industry of home-electric tools became possible. Like this tool, I currently write on.
    <br><br>
    The same thing happens in software, just exponentially faster. Like tokens and AST sparked [tools that change your code](/blog/2018/10/22/brief-history-of-tools-watching-and-changing-your-php-code/).
    <br><br>
    Recently, I introduced [Symfony Static Dumper](/blog/2020/03/16/statie-is-dead-long-live-symfony-static-dumper/) that uses YAML to store data in your Symfony application. You where this goes... how can **we turn this YAML into objects**?

tweet: "New Post on #php 🐘 blog: How to Hydrate Arrays to Objects via Constructor"
tweet_image: "/assets/images/posts/2020/easy_hydrator_twig.png"
---

*Disclaimer: this post is not about array vs. object performance. If you still prefer arrays, check this [talk by Nikita Popov](https://www.slideshare.net/nikita_ppv/php-performance-trivia/31) that changed my mind.*

<br>

This post is about the luxury of **object IDE autocompletion** everywhere in your code. And how to make it happen, **when all you have in the start are arrays** (JSON, YAML...).

<br>

Do you work with Doctrine entities? Then you're probably used to [use Repository service](/blog/2017/10/16/how-to-use-repository-with-doctrine-as-service-in-symfony/) and Entity object:

```php
<?php

declare(strict_types=1);

namespace Pehapkari\Blog\Repository;

use App\Entity\Post;
use Doctrine\Persistence\ObjectRepository;
use Doctrine\ORM\EntityManagerInterface;

final class PostRepository
{
    private ObjectRepository $repository;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->repository = $entityManager->getRepository(Post::class);
    }

    /**
     * @return Post[]
     */
    public function fetchAll(): array
    {
        return $this->repository->fetchAll();
    }
}
```

Then we can **use reliable objects** everywhere in application, like controllers:

```php
final class BlogController
{
    // ...

    public function __invoke(): Response
    {
        return $this->render('blog/post.twig', [
            'posts' => $this->postRepository->fetchAll(),
        ]);
    }
}
```

And we also have **autocomplete in TWIG templates** (thanks to [amazing](/blog/2018/08/23/9-features-of-symfony-plugin-you-should-not-miss-in-gifs/) [Symfony plugin](/blog/2019/01/28/2-files-that-your-symfony-application-misses)/):

<img src="/assets/images/posts/2020/easy_hydrator_twig.png" class="img-thumbnail">


## What now with all the Arrays?

Each local PHP community produces videos, livestreams, or talk recordings. We make such videos too, and we store [them in YAML](https://github.com/pehapkari/pehapkari.cz/blob/master/config/_data/youtube_videos.yaml). How can we get objects from that?

Let's use the most straightforward example possible.

### 1. The Data

```yaml
parameters:
    videos:
        -
            title: 'How to Hydrate objects to Arrays'
            created_at: '2020-04-20'
```

### 2. The Value Object

In the application, we want to use an object:

- with strict and reliable types,
- autocompletion of methods
- and clear API (e.g., values of this object should no be changed).

```php
<?php

declare(strict_types=1);

namespace App\ValueObject;

use DateTimeInterface;

final class Video
{
    private string $name;

    private DateTimeInterface $createdAt;

    public function __construct(string $name, DateTimeInterface $createdAt)
    {
        $this->name = $name;
        $this->createdAt = $createdAt;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getCreatedAt(): DateTimeInterface
    {
        return $this->createdAt;
    }
}
```

### 3. The Goal

We want our application to be:

- open **to future database switch**
- **and have readable code** for new developers - [less patterns is better](/blog/2020/03/09/art-of-letting-go/)

Saying that, the code should look like **1:1 to repositories** we know from Doctrine:

```php
final class VideoController
{
    // ...

    public function __invoke(): Response
    {
        return $this->render('video/videos.twig', [
            'videos' => $this->videoRepository->fetchAll(),
        ]);
    }
}
```

## YAML, JSON... an array?

You can hydrate input of guzzle, arrays from YAML *database*, or just local data in your PHP code.

This is how we solved our use case:

```php
<?php

declare(strict_types=1);

namespace App\Repository;

use App\ValueObject\Video;
use Symplify\EasyHydrator\ArrayToValueObjectHydrator;

final class VideoRepository
{

    /**
     * @var Video[]
     */
    private $videos = [];

    public function __construct(
        array $videos,
        ArrayToValueObjectHydrator $arrayToValueObjectHydrator
    ) {
        $this->videos = $arrayToValueObjectHydrator->hydrateArrays($videos, Video::class);
    }

    /**
     * @return Video[]
     */
    public function fetchAll(): array
    {
        return $this->videos;
    }
}
```

What is `Symplify\EasyHydrator\ArrayToValueObjectHydrator`? Its from new package [symplify/easy-hydrator](https://github.com/symplify/easy-hydrator) that hydrates arrays to object. Easy.

<br>

## 1 Step to Use Easy Hydrator

```bash
composer require symplify/easy-hydrator
```

With Symfony Flex and [type in `composer.json`](https://github.com/symplify/symplify/blob/b41034f21c52105d9bb27160fdc189eaac140b98/packages/easy-hydrator/composer.json#L5), this section became boring.

Then require `Symplify\EasyHydrator\ArrayToValueObjectHydrator` in the constructor and use it anywhere.


## 4 Features of Easy Hydrator

### 1. Handles `DateTime` and `int` retypes

```php
<?php

declare(strict_types=1);

final class Person
{
    // ...
    public function __construct(string $name, int $age, DateTimeInterface $metAt)
    {
        // ...
    }
}

$person = $this->arrayToValueObjectHydrator->hydrateArray([
    'name' => 'Tom',
    // will be retyped to int
    'age' => '30',
    // will be retyped to DateTimeInterface
    'metAt' => '2020-02-02',
], Person::class);
```

### 2. PHP 7.4 support

Typed properties + initialized are must-have. PHP 7.4 is around for ~6 months now, and people use this feature.

I also looked at [Ocramius/GeneratedHydrator](https://github.com/Ocramius/GeneratedHydrator) and tried to use it, but it doesn't work with PHP 7.4 objects correctly.

### 3. Constructor Injection Only

This package tries to be 1:1 with the rest of the clean code, so **hydrated object must use constructor injection**. Private property reflection magic won't work here.

### 4. Easy

This package hydrates arrays to object via constructor. Nothing more, nothing less.
It's for easy and clear use.

I use it in 3 PHP projects now and works great. Also we could get rid of *fake object* that only autocomplete twig and stopped relying on `$video['title']` or `$video['name']` guessing all over the code. Win win :)


<br>

Happy coding!
