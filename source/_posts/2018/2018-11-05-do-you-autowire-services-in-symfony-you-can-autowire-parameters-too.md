---
id: 156
title: "Do you Autowire Services in Symfony?<br>You can Autowire Parameters Too"
perex: |
    I love how Symfony caught up late autowiring integration in since Symfony 2.8. Then set a trend in Symfony 3.3 with service autoregistration.
    <br><br>
    That opens new possibilities to **almost config-less registration**, doesn't it?
tweet: "New Post on My Blog: Do you Autowire Services in #Symfony? You can Autowire Parameters Too"
---

Do you still have these old-school Symfony 2.7- configs?

```yaml
# services.yml
services:
    first_service:
        class: 'OpenProject\FirstClass'

    second_service:
        class: 'OpenProject\SecondClass'
        arguments:
            - '@first_service'
```

You have to **register** every service manually and **set service arguments** manually.

Honestly, I envy you. I can't imagine more wet PHP dream than refactoring to autowiring.

<br>

Are running on a newer Symfony? You'll be more familiar with this syntax:

```yaml
# services.yml
services:
    OpenProject\FirstClass: ~

    OpenProject\SecondClass:
        arguments:
            - '@OpenProject\FirstClass'
```

...or even...

```yaml
# services.yml
services:
    _defaults:
        autowire: true

    OpenProject\FirstClass: ~
    OpenProject\SecondClass: ~
```

...or even on the final one - [autodiscovery](/blog/2017/05/07/how-to-refactor-to-new-dependency-injection-features-in-symfony-3-3/):

```yaml
# services.yml
services:
    _defaults:
        autowire: true

    OpenProject\:
        resource: "../src"
```

This allows you to forget, that you actually have any config with services.

Why? Because **creating and using new service** = creating a PHP class. No YAML, no XML, no *whatever* [memory-locking](/blog/2018/08/27/why-and-how-to-avoid-the-memory-lock/) configuration.

<br>

Programmers used DI containers, config was getting into the shape of Miss World, autowiring and autodiscovery became status-quo and life was good.

Suddenly...

## ...You Want to Use Parameters

No problem! Since you already know [the clean-way](/blog/2018/01/22/how-to-get-parameter-in-symfony-controller-the-clean-way/), you'll use constructor:

```php
<?php

namespace OpenProject;

class FirstClass
{
    public function __construct(string $apiKey)
    {
        // ...
    }
}
```

```php
<?php

namespace OpenProject;

class SecondClass
{
    public function __construct(string $googleAnalyticsId)
    {
        // ...
    }
}
```

And add parameter values to config:

```yaml
parameters:
    api_key: "asdf"
    google_analytics_id: "ga-123456"
```

How do we get those parameters in there?

```diff
 services:
     _defaults:
         autowire: true

     OpenProject\:
         resource: "../src"
+
+    OpenProject\FirstClass:
+        arguments:
+             - "%api_key%"
+    OpenProject\SecondClass:
+        arguments:
+             - "%google_analytics_id%"
```

It's that simple! *Just kidding.*

Symfony guys realized this needs to follow innovations as service registration did.

```diff
 services:
     _defaults:
         autowire: true
+    bind:
+        $apiKey: "%api_key%"
+        $googleAnalyticsId: "%google_analytics_id%"
+
     OpenProject\:
         resource: "../src"
```

Not bad, at least compared to the previous solution.

<table class="table table-bordered table-responsive mt-4 mb-4">
    <thead class="thead-inverse">
        <tr>
            <th class="w-25">New Service?</th>
            <th class="w-50">New Parameter?</th>
        </tr>
    </thead>
    <tr>
        <td>
            <ul>
                <li>create a PHP class</li>
                <li>require it in the constructor</li>
            </ul>
        </td>
        <td>
            <ul>
                <li>create a parameter</li>
                <li>require it in the constructor</li>
                <li>bind it in config</li>
                <li>bind <strong>it in every config where it is required by service</strong></li>
                <li>also, remove it from every config where it's not used anymore - otherwise you'll get nice Symfony exception</li>
            </ul>
        </td>
    </tr>
</table>

It's that simple! *Just kidding, again.*

## Autowired Parameters in Symfony

<blockquote class="blockquote text-center mt-5 mb-5">
    If services are autowired by unique type,<br>
    parameters can be autowired by unique... name.
</blockquote>

You don't need this:

```diff
 services:
     _defaults:
         autowire: true
-    bind:
-        $apiKey: "%api_key%"
-        $googleAnalyticsId: "%google_analytics_id%"
-
     OpenProject\:
         resource: "../src"
```

All you need is... *love*... and to:

- create a parameter

```yaml
parameters:
    api_key: "asdf"
```

- require it in the constructor

```php
<?php

namespace OpenProject;

class FirstClass
{
    public function __construct(string $apiKey)
    {
        // ...
    }
}
```

### Convention over Configuration

If you can shave 2 almost identical approaches - *element autowiring* - **to just one, [do it](https://simple.wikipedia.org/wiki/Occam%27s_razor)**.

By respecting the naming `%param%` = `$param` your code is consistent with services,<br>
where `Type` = `$type` and clean and easy to read.


<br>

I got bad news - this is not part of Symfony - yet. But neither was autowiring for 8 years until it was.

**In the meantime, [register](https://github.com/symplify/packagebuilder#autobind-parameters) `Symplify\PackageBuilder\DependencyInjection\CompilerPass\AutoBindParametersCompilerPass`.**

You don't know how? [See Symfony docs](https://symfony.com/doc/current/service_container/compiler_passes.html).

<br>

## How Far Can We Push Autowiring Together?

What about array-typed autowiring?

```php
<?php

class SomeClass
{
    /**
     * @param CollectedType[] $collectedClasses
     */
    public function __construct(array $collectedClasses)
    {
        // ...
    }
}
```

<em class="fas fa-lg fa-check text-success"></em> &nbsp;Done for [Symfony 3.4+](https://github.com/Symplify/Symplify/pull/1145) and in [Nette 3.0](https://github.com/nette/di/pull/178).

<br>

Maybe convention factory using `create()` method and it's `@return Type` or `create(): Type`?

```diff
 services:
     SomeFactory: ~
-    SomeObject:
-        factory: ['@SomeFactory', 'create']
```

EDITED: <em class="fas fa-lg fa-check text-success"></em> &nbsp;Done for [Symfony 3.4+](https://github.com/Symplify/Symplify/pull/1185)

<br>

Or...?

*(Place your crazy ideas below ↓)*
