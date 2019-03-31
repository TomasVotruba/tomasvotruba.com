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

```yaml
# rector.yaml
services:
    Rector\Rector\MethodCall\MethodNameReplacerRector:
        SomeFramework\AbstractPresenter: # ← match type
            run: '__invoke' # ← old method: new method
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

Easy! But what if you write [framework-independent controllers](https://matthiasnoback.nl/2014/06/how-to-create-framework-independent-controllers/)? (I'm not a big fan of dogmas, but always do that).

```php
<?php

namespace App\SomeModule\Presenter;

final class SomeController
{
    public function run()
    {

    }
}
```

What now? Now you'll be punished for writing too clean code. This code is very poorly refactorable.

In that case, you'll have to do it manually or with a regular expression:

```php
<?php

$controllerFileContent = preg_replace(
    '#(^class .*Controller.*?^\s+public function )run(\()#ms',
    '$1__invoke$2',
    $controllerFileContent
);
```

The `'$1__invoke$2'` trick keeps all matched content (the one inside `()`) and replaces middle "run" for "__invoke". See pattern on [regex101.com](https://regex101.com/r/u5LtXX/1/) if you don't believe me.

<br>

Regulars are fine for e.g. [templating migrations](/blog/2018/07/05/how-to-convert-latte-templates-to-twig-in-27-regular-expressions/) where are no better tools to parse code, **but in PHP refactoring regulars migration are very bad and fragile joke**.

<br>

Use Rector for that instead:

```yaml
# rector.yaml
services:
    Rector\Rector\MethodCall\MethodNameReplacerRector:
        App\SomeModule\Presenter\SomeController:
            run: '__invoke'
```

Do you have more classes? No troubles! Just put each class one by one carefully to the config...

```yaml
# rector.yaml
services:
    Rector\Rector\MethodCall\MethodNameReplacerRector:
        App\SomeModule\Presenter\SomeController:
            run: '__invoke'
        App\SomeModule\Presenter\HomepageController:
            run: '__invoke'
        App\AnotherModule\Presenter\HomepageController:
            run: '__invoke'

        # ...
        # 50+ more cases
```

Well, **isn't that silly**? It is.

## Can Asterisk Save Us?

What if you could use [`fnmatch`](http://php.net/manual/en/function.fnmatch.php) pattern?

```yaml
# rector.yaml
services:
    Rector\Rector\MethodCall\MethodNameReplacerRector:
        App\*Module\Presenter\*Controller:
            run: '__invoke'
```

Kittens will love you now!

*This [feature](https://github.com/rectorphp/rector/pull/1004) was added to Rector v0.3.40.*

<br>

One more thing! You can use it on any type check:

```diff
 services:
     Rector\Rector\Constant\ClassConstantReplacerRector:
-        Framework\Request:
+        Framework\Request*:
             200: CODE_200
             300: CODE_300
             400: CODE_400
             404: CODE_404
             500: CODE_500
-        Framework\RequestInterface:
-            200: CODE_200
-            300: CODE_300
-            400: CODE_400
-            404: CODE_404
-            500: CODE_500
```

<br>

Happy instant refactorings!