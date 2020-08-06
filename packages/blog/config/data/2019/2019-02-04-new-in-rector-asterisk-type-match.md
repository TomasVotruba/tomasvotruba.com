---
id: 183
title: "New in Rector: Asterisk Type Match"
perex: |
    Rector had started just recently helping **instantly refactor private commercial projects**. Not just from legacy to modern PHP, but also **from one PHP framework to another**. I won't tell you which ones as the work is in progress, but when it's finished, you'll be the first to hear.
    <br>
    <br>
    The positive side effect of Rector helping to migrate real commercial project **are new features in its core** that is free and open-source Today with little, yet powerful *asterisk type match*.

tweet: "New Post on #php 🐘 blog: New in Rector: Asterisk Type Match"
---

MVC ([model-view-controller](https://en.wikipedia.org/wiki/Model%E2%80%93view%E2%80%93controller)) is wide-spread pattern across all PHP frameworks.
That allows migration between them pretty smooth process. What do have *presenter*, *action*, *route-target* or *controller* in common? All are various names for the same entry point to the application.

Each PHP frameworks has its conventions and conventions are the main topics during migration.

E.g. one framework has default method of controller named `run`, the other `__invoke`. How can Rector help us?

```php
<?php

declare(strict_types=1);

use Rector\Renaming\Rector\MethodCall\RenameMethodRector;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

return function (ContainerConfigurator $containerConfigurator): void {
    $services = $containerConfigurator->services();
    $services->set(RenameMethodRector::class)
        ->call('configure', [[
            RenameMethodRector::OLD_TO_NEW_METHODS_BY_CLASS => [
                // match type
                'SomeFramework\AbstractPresenter' => [
                    // old method: new method
                    'run' => '__invoke'
                ]
            ]
        ]]);
};
```

Then Rector will change the code for you:

```bash
vendor/bin/rector process src
```

↓

```diff
 <?php

 namespace App\SomeModule\Presenter;

 use SomeFramework\AbstractPresenter;

 final class SomeController extends AbstractPresenter
 {
-    public function run()
+    public function __invoke()
     {

     }
 }
```

Do you have more classes? No troubles! Just put each class one by one carefully to the config...

Wait. What if you could use [`fnmatch`](http://php.net/manual/en/function.fnmatch.php) pattern?

```php
<?php

declare(strict_types=1);

use Rector\Renaming\Rector\MethodCall\RenameMethodRector;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

return function (ContainerConfigurator $containerConfigurator): void {
    $services = $containerConfigurator->services();
    $services->set(RenameMethodRector::class)
        ->call('configure', [[
            RenameMethodRector::OLD_TO_NEW_METHODS_BY_CLASS => [
                'App\*Module\Presenter\*Controller' => [
                    'run' => '__invoke'
                ]
            ]
        ]]);
};
```

Kittens will love you now!

*This [feature](https://github.com/rectorphp/rector/pull/1004) was added to Rector v0.3.40.*

<br>

One more thing! You can use it on any type check:

```diff
 <?php

 declare(strict_types=1);

 use Rector\Renaming\Rector\Constant\RenameClassConstantRector;use Rector\Renaming\Rector\MethodCall\RenameMethodRector;
 use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

 return function (ContainerConfigurator $containerConfigurator): void {
     $services = $containerConfigurator->services();
     $services->set(RenameClassConstantRector::class)
         ->call('configure', [[
             RenameClassConstantRector::OLD_TO_NEW_CONSTANTS_BY_CLASS => [
-                'Framework\Request' => [
+                'Framework\Request*' => [
                     200 => 'CODE_200',
                     300 => 'CODE_300',
                 ],
-                'Framework\RequestInterface' => [
-                    200 => 'CODE_200',
-                    300 => 'CODE_300',
-                ],
            ]
        ]]);
};
```

<br>

Happy instant refactorings!
