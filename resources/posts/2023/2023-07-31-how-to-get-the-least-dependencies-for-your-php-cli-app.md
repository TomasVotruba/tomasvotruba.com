---
id: 388
title: "How to get the least dependencies for your PHP CLI App"

perex: |
    I have couple of open-source CLI apps like Rector, ECS, Class Leak, Lines and private ones like Cleaning Checklist, Fixai and Entropy. All of them run in command line and some of them [are downgraded to PHP 7.2](/blog/how-to-release-php-81-and-72-package-in-the-same-repository).

    In every project there is rule, the less dependencies you have, the less work to maintain them. With CLI apps distributed with scoped and downgraded /vendor included, this applies twice.

    How to achieve this goal? It's not what I thought.
---

Typical CLI PHP application needs 2 main packages:

* console to make work with command arguments and options easy,
* and dependency injection container to avoid creating services manually

E.g. ECS required these dependencies:

```json
{
    "require": {
        "symfony/console": "x",
        "symfony/dependency-injection": "x",
        "symfony/finder": "x",
        "symfony/http-kernel": "x",
    }
}
```

*The http-kernel is not needed per-se, but it [contains container factory](https://www.reddit.com/r/PHP/comments/159lc7j/comment/jtihjh7/?utm_source=share&utm_medium=web2x&context=3) that is needed to build the container.*


## The Problem

The more packages we have, the more we have to maintain them, downgrade them and scope them. Downgrading and scoping is quite challenging process, so every less package to downgrade, the better.


@todo with laravel switch



...

