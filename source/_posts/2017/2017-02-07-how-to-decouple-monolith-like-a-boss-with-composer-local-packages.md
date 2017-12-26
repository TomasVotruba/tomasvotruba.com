---
id: 26
title: "How to Decouple Monolith like a Boss with Composer Local Packages"
perex: '''
    While building PHP applications that grows and grows, you often get to the situation when there is too much code. You get easily lost, duplicate and code smells and rottens. The cognitive upkeep to add a new class is bigger every day.
    <br><br>
There are 3 solutions: put code <code>/libs</code> subdirectories, decouple to private packages using Satis or (the best) create an open-source packages.
    <br><br>
<strong>All of them are overkill to the solution I will show you to day - composer local packages.</strong>
'''
related_posts: [45, 25, 13, 12, 69]
tweet: "Have you heard about local packages? Try them #composerPHP #monolith #php"
---

I found first [article about this topic](http://www.whitewashing.de/2015/04/11/monolithic_repositories_with_php_and_composer.html) in 2015, when Benjamin Eberlei created a tool called Fiddler. I was using similar approach in that time for a few months and I was happy I'm not alone.

Benjamin wrote about an idea, that is **now integrated in composer by default**. To my surprise, nobody knows about it!
That's similar case for great Composer tools like [prestissimo](https://github.com/hirak/prestissimo) or [versions-check](https://github.com/Soullivaneuh/composer-versions-check).

But first, we'll look at current solutions and why I don't see them scalable. We all tried them in Lekarna.cz and learned by painful experience that they don't work.


## 1. Just Put The Code to `/libs` or `/src` Directory

This is most common solution I see. Is there a login logic? Just put the code to `/libs/Login` or `/src/LoginBundle`.
Add it to `$robotLoader` (Nette) or Composer in Symfony and it works.

### Advantages

- it's simple
- it's the least you can do

### Disadvantages

- still coupled to the application, just now on 2 different places instead of 1
- service/route registration is in `app` - this is cyclic dependency (also known as "vicious circle")
- it shows others that putting code to 2 or 3 places is ok
- the "package" is coupled to the application and it's not easy to use somewhere else
- dependencies of this "package" are unknown


**It doesn't scale!**


## 2. Create Private Packages

This way is a bit pro. We used Gitlab and Satis - it took a month to set up all access rights correctly (Gitlab CI and Satis server tend to argue). It's so much pain, there is even [official paid service](https://packagist.com/) for it.

### Advantages

- all is clean, separated and leads to good practices
- all code and test are in separated extension
- package have own README.md with documentation, so it's natural to document it

### Disadvantages

- development is 4 step hell:
    - add feature to the package
    - push and tag package
    - wait
    - run `composer update` in application and see the results
- it requires lot of maintainer skills (I would say at least 1 year in open-source) to do it fine
- it's premature decoupling that doesn't bring joy

**It doesn't scale!**


## 3. Publish Open-source Packages

I love this most and it was great for some packages. That's how many [Zenify](https://github.com/Zenify/) Doctrine-related packages were born.

### Advantages

- you feel great for giving back to community
- people will help improve the package
- open-source packages tend to have longer lifespan than private packages

### Disadvantages

- even more steps to hell
- it's less flexible in experiments - somebody else is using it
- you can't money or security related packages like that

**It doesn't scale.**


During this path of packages we started to feel desperate. Nothing was good enough. I was loosing my belief in open source packages and packages themselves.


## Composer Saved my Life!

Not sure why and how, but in composer ["path" feature](https://getcomposer.org/doc/05-repositories.md#path) was added. **And it was exactly what we looked for!**

This is so simple that many people find it hard to believe. Forget private and open-sourced packages. Forget Gitlab, Satis and Github. This is **local filesystem only** solution. **All you need is composer**.

### Lets create First Local Package - Filesystem

Back to Lekarna.cz. We had application that used own *filesystem services*. It was registered in `app/config/config.neon` (Nette app) and it was used in various scenarios. Once we needed to create directory, store image, create image from database, download remote `.xml` etc. **It was growing and more and more coupled.**

People started to create new classes and methods for what was already there, because there was too much code and nobody knew about it.

I had enough!

So I started to decouple this filesytem logic. How?

### 1. Create Directory For Local Package

```bash
/packages
    /lekarna
        /filesystem
```

### 2. Add Basic Package Directories and Files

```bash
/packages
    /lekarna
        /filesystem
            /src
            /tests
            README.md
            composer.json
```

### 3. Name the Package in its `composer.json`

The file is `packages/lekarna/filesystem/composer.json`:

```javascript
{
    "name": "lekarna/filesystem",
    "require": {
        "php": "^7.1",
        "nette/utils": "^2.4",
    },
    "autoload": {
        "psr-4": {
            "Lekarna\\Filesystem\\": "src"
        }
    }
}
```

### 4. Register Package to Main `composer.json`

```javascript
{
    "require": {
        "lekarna/filesystem": "@dev"
    },
    "repositories": [
        { "type": "path", "url": "packages/Lekarna/Filesystem" }
    ],
}
```

Run

```bash
composer update
```

and your package is ready. That's it! Easy, step-by-step maturing and scalable architecture pattern.

**How do you maintain huge code bases?**
