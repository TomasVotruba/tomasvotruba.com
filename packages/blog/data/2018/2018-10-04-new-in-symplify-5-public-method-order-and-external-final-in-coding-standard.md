---
id: 146
title: "New in Symplify 5: Public Method Order and External Final in CodingStandard"
perex: |
    Coding Standard 5 replaced fixers that renamed classes with more tolerant sniffs. What else is there?
    <br>
    New config options **that shorten your config file** and **2 new checkers to keep your code in order**.
tweet: "New in Symplify 5: Public Method Order and External Final in CodingStandard     #php #git #split #easy"

updated_since: "August 2020"
updated_message: |
    Removed unsupported rules in Coding Standard 8, add PHPStan rule that handle it better.
---

Don't you have this package installed yet?

```bash
composer require symplify/coding-standard --dev
```

Now enjoy the news ↓

## 3. Final for 3rd Party Classes

If you're strict enough to `final` or `abstract` everywhere, you'll love this. Sometimes 3rd party code is not `final`, but you'd love to never see that class in your code - Abstract Controller, Abstract Doctrine Repository or Abstract Object.

Those `abstract` classes are full of **magic everyone has to [remember](/blog/2018/08/27/why-and-how-to-avoid-the-memory-lock/)**. What if you could **prevent that spreading to your code without constant code-reviews**?

Let PHPStan rule do the job:

```yaml
# phpstan.neon
rules:
    - Symplify\CodingStandard\Rules\ForbiddenParentClassRule

parameters:
    symplify:
        forbidden_parent_classes:
            - 'Doctrine\ORM\EntityRepository'
            - 'Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository'
```

This will prevent over-inheritance and embrace composition - like in [Repositories as Services](/blog/2017/10/16/how-to-use-repository-with-doctrine-as-service-in-symfony/) approach:

❌

```php
<?php

use Doctrine\ORM\EntityRepository;

final class ProductRepository extends EntityRepository
{
}
```
✅

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
