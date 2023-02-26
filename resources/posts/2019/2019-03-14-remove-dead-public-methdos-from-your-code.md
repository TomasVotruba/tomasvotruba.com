---
id: 194
title: "Remove Dead Public Methods from Your Code"
perex: |
    We already have sniffs and rectors to remove unused private methods. But what about public methods? If you're creating open-source packages, public methods might be used by anyone.


    But what if you're creating an application - you can remove unused with peace in mind.
    There is only one problem to resolve - **find public unused methods**.


deprecated_since: "2020-04"
deprecated_message: |
    `UnusedPublicMethodSniff` was added in 2017. It's been useful in those times, but now it's more and more crappy. As we have AST, dead code analysis in PHPStorm and [Rector dead code set](https://github.com/rectorphp/rector/blob/master/docs/rector_rules_overview.md#deadcode), **these tools should be used instead**.
---

*Too long to read? Look at [3:45 min practical video](https://www.youtube.com/watch?v=sKFB6XVmO_Q) by a friend of mine [Jan Kuchař](https://jankuchar.cz).*

<br>

As we code on an application for many years, some methods may be replaced by a few new-ones:

```diff
 $person = new Person('Tomas');
-$person->getFullName();
+$person->getName();
```

If the application is complex, we may not know if the old method is still in use anywhere:

```php
<?php

class Person
{
    /**
     * @var string
     */
    private $name;

    public function __construct(string $name)
    {
        $this->name = $name;
    }

    public function getName()
    {
        // ...
    }

    public function getFullName()
    {
        // ...
    }
}
```

We don't have to manually read the codebase file by file. PHPStorm can help us examine each public method and where they are being used.

**Just right-click the method call** ("provide" in the picture) **and select "Find Usages"**.

<img src="/assets/images/posts/2019/dead-public/usages.png" class="img-thumbnail">

It took us 5-10 seconds to **find out that `getFullName()` is a dead method**. Great job!

## Can we Find Them Faster?

Now **do the same for all the other public methods**.

I consider Symplify project quite small, at least compared to private web applications. Yet, there [is over 684 public methods](https://github.com/symplify/symplify/search?q=%22public+function%22&unscoped_q=%22public+function%22). Even if we remove public methods from test fixtures, there will remain ~ 500 public methods:

<div class="blockquote text-center">
    500 * 5 secs = 2500 secs ~= 41 mins
</div>

...and we don't talk about brain wasted on linear operations. This is not the way.

## How to find them in 1 Minute?

There is one little sniff in `symplify/coding-standard` only few people know about. Set it up:

```php
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

return function (ContainerConfigurator $containerConfigurator): void {
    $services = $containerConfigurator->services();
    $services->set(Symplify\CodingStandard\Sniffs\DeadCode\UnusedPublicMethodSniff::class);
};
```

And use it:

```bash
vendor/bin/ecs check src
```

## Magic?

Not really. The sniff goes through your code and:

- finds all methods: `public function someMethod()`
- then find all method calls: `$this->someMethod()`
- and simply **reports those public functions that were never called**

Then just skip false positives, that are [called in yaml configs](https://github.com/rectorphp/rector/blob/a8db80baff48eb02319963b3380f185461678815/packages/NodeTypeResolver/config/config.yaml#L15) or in strings - and that is it!

**You'll be surprised, how many methods are dead in your code :)**

<br>

Happy coding!
