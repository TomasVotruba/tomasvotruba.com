---
id: 158
title: "Will Autowired Arrays Finally Deprecate Tags in Symfony and Nette?"
perex: |
    To be clear: we talk about those tags that only have a name. No priority, no level, no event name, nothing, **just the name**. If you're not sure why these tags are bad, read *[Drop all Service Tags in Your Nette and Symfony Applications](/blog/2017/02/12/drop-all-service-tags-in-your-nette-and-symfony-applications/)* first.
    <br>
    <br>
    I'm very happy to see that [collectors](/clusters/#collector-pattern-the-shortcut-hack-to-solid-code) are finally getting to the core of DI components of PHP frameworks. Tags, extensions, compiler passes and `autoconfigure` now became workarounds. Collectors are now in the best place they can... **the PHP code**. 
       
tweet: "New Post on My Blog: Can Autowired Arrays Finally Deprecate Tags in #Symfony and #NetteFw?    #collector #nettefw30 #php #simplestupid"
---

Let's say we need to build a tool for releasing a new version of the open-source package. Something like what I use for 
[Symplify and Rector releases](https://github.com/symplify/monorepobuilder), **but better**.

You want it to be *open for extension and closed for modification*. How do we do that?  
 
You introduce and a `ReleaseWorkerInterface`:

```php
<?php declare(strict_types=1);

namespace Moses\ReleaseWorker;

interface ReleaseWorkerInterface
{
    public function work(string $version): void;
}
```

Good, now if anyone wants to extend it, they' just create a new service:

```php
<?php declare(strict_types=1);

namespace Moses\ReleaseWorker;

use Nette\Utils\Strings;

final class CheckBlogHasReleasePostReleaseWorker implements ReleaseWorkerInterface
{
    public function work(string $version): void 
    {
        $blogContent = file_get_contents('https://tomasvotruba.cz');
        
        // is there a post with this title?
        if (Strings::match($blogContent, '#Release of ' . $version . '#')) {
            // good
            echo 'Good job! The blog post was released.';
            // early return
            return;
        } 

        // bad
        throw new DoThisFirstException(sprintf('Write release post about "%s" version first', $version));
    }
}
```

and register it

```yaml
# moses.yml
services:
    Moses\ReleaseWorker\CheckBlogHasReleasePostReleaseWorker: ~
```

## Find all the `ReleaseWorkerInterface`?

Note: I'll be mixing Nette | Symfony syntax now, but they're almost identical in DI component, so just imagine it's your favorite framework.

How can we get all the services that implement `ReleaseWorkerInterface`? 

### 1. Tags!

```yaml
services:
    Moses\ReleaseWorker\CheckBlogHasReleasePostReleaseWorker:
        tags:
            - "release_worker"
```

In extension/compiler pass:

```php
<?php declare(strict_types=1);

foreach ($containerBuilder->findByTags('release_worker') as $workerDefinition) {
   $mosesDefinition->addCall('addWorker', [$workerDefinition->getName()]);   
}
```

This is what we would do in 2010. **This brings [memory-lock](/blog/2018/08/27/why-and-how-to-avoid-the-memory-lock/) on tag name and disables common sense**. And we need common sense to create usable code.

What's the next option we have?

### 2. `byType()` methods 

In extension/compiler pass:

```php
<?php declare(strict_types=1);

foreach ($containerBuilder->findByType(ReleaseWorkerInterface::class) as $workerDefinition) {
   $mosesDefinition->addCall('addWorker', [$workerDefinition->getName()]);   
}
```

This drops memory-lock, good. But **we still have to go to extension/compiler-pass**, lands that are visited by fractions of framework-users.

What about something "2018"?

### 3. Autowired Arrays

All options above hides a contract. Which one? The `Moses` class looks like this:

```php
<?php declare(strict_types=1);

final class Moses
{
    // property + setter
    
    public function release(string $version)
    {
        foreach ($this->releaseWorkers as $releaseWorker) {
            $releaseWorker->work($version);
        } 
    }
}
```

What is wrong with this contract? Have you noticed the constructor? Me neither, **it's not there!** It needs at least some release workers, it's useless without it, but we lie about this contract:

```php
<?php declare(strict_types=1);

$moses = new Moses\Moses;
$moses->release('v5.0.0');

// nothing
// ...
// WTF?
```

We already know that **public properties, setters, and drugs are bad**. **Missing constructor contract and sniffing dependency somewhere else by setters - not good either**. Moreover when your other classes keep that contract. What's the point of rules in your code then?

### Success is Given to Reliable People

We should make a design that is reliable. 

- Do you need these services? Tell us in the constructor.
- Do you need this parameter to work? [Tell us constructor](/blog/2018/11/05/do-you-autowire-services-in-symfony-you-can-autowire-parameters-too/). 
- Do you need all `ReleaseWorkerInterface`s? **Tell us in the constructor.**

```php
<?php declare(strict_types=1);

$releaseWorkers = [
    new Moses\ReleaseWorker\CheckBlogHasReleasePostReleaseWorker,
];

$moses = new Moses\Moses($releaseWorkers);
```

Now when we call the service, **we can actually see some output**:

```php
<?php declare(strict_types=1);

$moses->release('v5.0.0');

// "Good job! The blog post was released."
// ...
// Thanks!
```

Sound nice, right? Is that even possible? Without that, we could drop tags, the compiler passes, YAML/Neon stringly-typed configuration, anti-conception... The world would finally make sense again!

<blockquote class="blockquote text-center">
    "Vision over Expectations."
</blockquote>

It sounds really nice. But how would that work in PHP? How does container now what we need in the constructor. Yes, Mr. Potter?

```php
<?php declare(strict_types=1);

namespace Moses;

use Moses\ReleaseWorker\ReleaseWorkerInterface;

final class Moses
{
    /**
     * @param ReleaseWorkerInterface[] $releaseWorkers 
     */
    public function __construct(array $releaseWorkers)
    {
    }
} 
```

No need for magic. **Just use typehint in annotation**.

<br>

Typehint in the annotation. It's that simple.

## When Can I use That <my-favorite-framework>?
   
I have no idea.

But you can **install it today**:
 
 - with `"nette/di": "v3.0.0-beta1"` with [this feature enabled in the core](https://github.com/nette/di/pull/178)
 - and `"symplify/package-builder": "^5.2"` and [`AutowireArrayParameterCompilerPass`](https://github.com/Symplify/PackageBuilder#autowire-array-parameters)

## Does it Work?

Yes, for the cases above it's 1:1 substitution with 0-configuration. It's part of [Symplify since 5.1](https://github.com/Symplify/Symplify/pull/1145/files) (released 1,5 month ago) and **it works flawlessly**.

<br>

And why *Moses*? Well, he *released* the most of people from slavery :)
