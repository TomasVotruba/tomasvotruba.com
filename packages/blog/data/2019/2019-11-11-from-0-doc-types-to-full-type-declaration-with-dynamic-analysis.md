---
id: 225
title: "From 0 Doc Types to Full Type Declaration with Dynamic Analysis"
perex: |
    I wrote [How we Completed Thousands of Missing @var Annotations in a Day](/blog/2019/07/29/how-we-completed-thousands-of-missing-var-annotations-in-a-day/). If you have at least some annotations, you can use Rector to do the dirty work.
    <br>
    <br>
    To be honest, open-source is the top 1 % code there is, but out **in the wild of ~~legacy~~ established PHP companies, it's a miracle to see just one `string` type declaration**.
    <br>
    <br>
    Are these projects lost? Do you have to quit them? And what if the annotations are lying?

tweet: "New Post on #php 🐘 blog: From 0 Doc Types to Full Type Declaration with Dynamic Analysis - Thank you @DaveLiddament for injecting the idea to my head"
tweet_image: "/assets/images/posts/2019/dynamic-analysis/probe.png"

updated_since: "August 2020"
updated_message: |
    Updated Rector YAML to PHP configuration, as current standard.
---

I had a great trip with a friend of mine [Dave Liddament](https://github.com/DaveLiddament) after [PHP Day 2019 in Verona](https://2019.phpday.it). During Venice sightseeing in beautiful wild rain, we had a short coffee break to talk about Rector. Dave was amazed by what Rector can do for the developer, with such a few lines of YAML config.

As a proper curious developer, **he challenged me with series** of "Can it do...?" or "How can it...?" questions.

<br>

The one we spent almost an hour on one was:

**"How can Rector help with type declarations, if there are no docblocks and no type hints?"**

```php
<?php

class SomeClass
{
    public function run($value)
    {
        return $value;
    }
}
```

First I was cold stone and said: "That's far beyond static analysis. That's a job for a human, Rector can't help here".

I vividly recall I was sure was *this is impossible* (← now this line my motto pick project that is interesting enough, lol).

<br>

But David sticks with it questioning:

- "If we know there is **always a string coming inside and nothing else**, we could do this:"

```diff
 <?php

 class SomeClass
 {
-    public function run($value)
+    public function run(string $value): string
     {
         return $value;
     }
 }
```

- "Well... yes, you're right."

## Detect Every Argument Type

I started to see a very small candle at the end of the tunnel and said:

- "We could do some kind of logging types that come to the method. Collect enough data and decide based on that."

There is a similar technique for dead-code analysis - [tombs](https://github.com/krakjoe/tombs). But the problem is, **it's not written in PHP**. And if it's not written in the language we use, we are not able to extend it or fix it. That's why PHPStorm plugins written Java take so long to catch up with framework releases.

We wanted to have the code just once in the whole application. If possible in the end and collect all the method calls and their arguments. We tried to use `register_shutdown_function` and `debug_trace` for it. But after some time spends hacking them, we gave up.

So it will have to be good old **static call under each class method**, something like:

```diff
 <?php

 class SomeClass
 {
    public function run($value)
    {
+       TypeCollector::collect($value, __METHOD__, 0);
        return $value;
    }
 }
```

## What about Performance?

`file_put_contents()` takes [~10 ms for 10 000 writes](https://www.php.net/manual/en/function.file-put-contents.php#105421) writes, so writing in filesystem might work.

Still, it's safer to use **feature toggles** or direct **small fraction of traffic** to a standalone server with these static methods.

## How Long Should we Collect Data?

This needs to be tested in the wild. It depends on many factors, for a blogging platform, it can be a week of data.
For a payment system, a month would be better, maybe more to be sure.

Also, the same way we collect data first **with feature toggle/traffic fraction**, we can test added types after they're added to the code.

## The Simple Idea

To make the idea more solid, we looked for the edge cases:

- "Wait, so we just collect types and then analyze them?"
- "Yes, if there are 10 000 calls for the method and it gets a string in 100 % cases, it's a `string`."
- "But, if 5 % of them is null... it will be nullable `?string`"
- "Exactly, and if it's 5 different types, there is nothing we can do."
- "I see, but the point is to complete everything that can be completed, based on data and experience instead of docblock that can contain anything."

The idea is pretty clear, right?

## How can we bring it to all PHP developers in Need?

It all seemed like a nice brain exercise for our brains... but we looked for **practical appliance that would help every PHP developer in the world**.

To automate this process fully, we came with 4 automated steps:

- 1. Add type collector to all public class and trait methods

    ```diff
     <?php

     class SomeClass
     {
        public function run($value)
        {
    +       TypeCollector::collect($value, __METHOD__, 0);
            return $value;
        }
     }
    ```

- 2. Collect data for a 1-4 weeks

- 3. Complete collected types that can be added

    ```diff
     <?php

     class SomeClass
     {
    -    public function run($value)
    +    public function run(string $value)
         {
             TypeCollector::collect($value, __METHOD__, 0);
             return $value;
         }
     }
    ```

- 4. Remove type collector

    ```diff
     <?php

     class SomeClass
     {
         public function run(string $value)
         {
    -        TypeCollector::collect($value, __METHOD__, 0);
             return $value;
         }
     }
    ```

This was May 2019 and it was just an idea. Now, 6 months later, I'm proud to say this **4-step process is now possible**.
I've  [merged the PR into Rector](https://github.com/rectorphp/rector/pull/2264/files) just a few minutes ago.

↓

### Step 1 - Add Type Collector

```php
// rector.php

declare(strict_types=1);

use Rector\DynamicTypeAnalysis\Rector\ClassMethod\DecorateMethodWithArgumentTypeProbeRector;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

return function (ContainerConfigurator $containerConfigurator): void {
    $services = $containerConfigurator->services();
    $services->set(DecorateMethodWithArgumentTypeProbeRector::class);
};
```

```bash
vendor/bin/rector process src
```

### Step 2 - Wait for it...

### Step 3 - Complete Collected Types

```php
// rector.php

declare(strict_types=1);

use Rector\DynamicTypeAnalysis\Rector\ClassMethod\AddArgumentTypeWithProbeDataRector;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

return function (ContainerConfigurator $containerConfigurator): void {
    $services = $containerConfigurator->services();
    $services->set(AddArgumentTypeWithProbeDataRector::class);
};
```

```bash
vendor/bin/rector process src
```

### Step 4 - Remove Type Collector

```php
// rector.php

declare(strict_types=1);

use Rector\DynamicTypeAnalysis\Rector\StaticCall\RemoveArgumentTypeProbeRector;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

return function (ContainerConfigurator $containerConfigurator): void {
    $services = $containerConfigurator->services();
    $services->set(RemoveArgumentTypeProbeRector::class);
};
```

```bash
vendor/bin/rector process src
```

<br>

## It's not Perfect, But Done

In all means, it's not perfect. There is still missing support for arrays, nested arrays, type co/ntra/variance, return types, union types, etc. **But it's ready to be tested and prototype works** (at least that's what unit tests say).

Now it's up to you. Make your code-base filled with real data it already uses. No guessing, no hoping, **just science fully-automated**.

<br>

Last but not least, thank you [Dave](https://github.com/DaveLiddament) for a great afternoon and sorry it took me so long to publish this.

<br>

Happy lazy coding!
