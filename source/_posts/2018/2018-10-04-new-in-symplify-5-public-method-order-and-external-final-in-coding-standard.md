---
id: 146
title: "New in Symplify 5: Public Method Order and External Final in CodingStandard"
perex: |
    Coding Standard 5 replaced fixers that renamed classes with more tolerant sniffs. What else is there?
    <br>
    New config options **that shorten your config file** and **2 new checkers to keep your code in order**.
tweet: "New in Symplify 5: Public Method Order and External Final in CodingStandard     #php #git #split #easy"
---

Don't you have this package installed yet?

```bash
composer require symplify/coding-standard --dev
```

Now enjoy the news ↓

## 1. Consistent Order of Public Methods

<a href="https://github.com/Symplify/Symplify/pull/1042" class="btn btn-dark btn-sm mb-3 mt-2">
    <em class="fab fa-github"></em>
    &nbsp;
    Check the pull-request #1042
</a>

There is already a fixer, that takes care of `public`, `protected` and `private` order of class elements - `OrderedClassElementsFixer`.

Let's take this one step further - to **order interface methods**. Imagine you have an interface with 2 methods.

```php
<?php

interface SomeInterface
{
    public function firstMethod();

    public function secondMethod();
}
```

```php
<?php

final class SomeClass implements SomeInterface
{
    public function firstMethod()
    {
    }

    public function secondMethod()
    {
    }
}
```

All good! Then you implement more of these, and more:

```php
<?php

final class SomeClass implements SomeInterface
{
    public function secondMethod()
    {
    }

    public function firstMethod()
    {
    }
}
```

When the class is small like this and you have 2 classes in the whole application, nobody cares. But if you implement e.g. `PhpCsFixer\Fixer\FixerInterface` that has 6 methods and you **have 20 Fixer classes with 20 various orders of those methods**, it can be really annoying to maintain them.

That's where `MethodOrderByTypeFixer` brings the order:

```yaml
# ecs.yml
services:
    Symplify\CodingStandard\Fixer\Order\MethodOrderByTypeFixer:
        method_order_by_type:
            SomeInterfade:
                - 'firstMethod'
                - 'secondMethod'
```

## 2. Exclude Classes From `::class`

<a href="https://github.com/Symplify/Symplify/pull/1038/files#diff-5bab0be1e11c555c36f4bf5bdd9dc645" class="btn btn-dark btn-sm mb-3 mt-2">
    <em class="fab fa-github"></em>
    &nbsp;
    Check the pull-request #1038
</a>

`ClassStringToClassConstantFixer` takes care of old strings classes to `::class` format:

```diff
-$this->assertInstanceOf('DateTime', $object);
+$this->assertInstanceOf(DateTime::class, $object);
```

But sometimes, you want these strings to be strings. **Before**, you had to exclude manually each such file:

```yaml
# ecs.yml
parameters:
    skip:
        Symplify\CodingStandard\Fixer\Php\ClassStringToClassConstantFixer:
            - 'src/ThisFile.php'
            - 'src/ThatFile.php'
            - 'src/ThatFileToo.php'
```

**Now** you can just exclude this classes:

```yaml
# ecs.yml
services:
    Symplify\CodingStandard\Fixer\Php\ClassStringToClassConstantFixer:
        allow_classes:
            - 'Error'
            - 'Symfony\Components\Console\*' # fnmatch() support!
```

## 3. Final for 3rd Party Classes

<a href="https://github.com/Symplify/Symplify/pull/1002/files#diff-692c4ab6d70c963f110e005dbbc800c9" class="btn btn-dark btn-sm mb-3 mt-2">
    <em class="fab fa-github"></em>
    &nbsp;
    Check the pull-request #1002
</a>

If you're strict enough to `final` or `abstract` everywhere, you'll love this. Sometimes 3rd party code is not `final`, but you'd love to never see that class in your code - Abstract Controller, Abstract Doctrine Repository or Abstract Object.

Those `abstract` classes are full of **magic everyone has to [remember](/blog/2018/08/27/why-and-how-to-avoid-the-memory-lock/)**. What if you could **prevent that spreading to your code without constant code-reviews**?

Let `ForbiddenParentClassSniff` do the job:

```yaml
# ecs.yml
services:
    Symplify\CodingStandard\Sniffs\CleanCode\ForbiddenParentClassSniff:
        forbiddenParentClasses:
            - 'Doctrine\ORM\EntityRepository'
```

This will prevent over-inheritance and embrace composition - like in [Repositories as Services](/blog/2017/10/16/how-to-use-repository-with-doctrine-as-service-in-symfony/) approach:

<em class="fas fa-fw fa-times text-danger fa-lg"></em>

```php
<?php

use Doctrine\ORM\EntityRepository;

final class ProductRepository extends EntityRepository
{
}
```

<em class="fas fa-fw fa-check text-success fa-lg"></em>

```php
<?php

use Doctrine\ORM\EntityRepository;

final class ProductRepository
{
    /**
     * @var EntityRepository
     */
    private $entityRepository;

    public function __construct(EntityRepository $entityRepository)
    {
        $this->entityRepository = $entityRepository;
    }
}
```


<br>

That's all folks. Happy sniffing!
