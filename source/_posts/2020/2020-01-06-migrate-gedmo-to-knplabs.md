---
id: 233
title: "Migrate Gedmo to KnpLabs"
perex: |
    With [Symfony 5 upgrade](https://pehapkari.cz/blog/2019/12/23/how-we-upgraded-pehapkari-cz-from-symfony-4-to-5-in-25-days), we need any *working* Doctrine behaviors.
    <br>
    Month later, we have [KnpLabs\DoctrineBehaviors 2.0](/blog/2019/12/30/doctrine-behaviors-2-0-reloaded/#how-do-you-migrate-from-gedmo-stof-to-knplabs-doctrinebehaviors) with full Symfony 5 support.
    <br>
    <br>
    If you used older Doctrine Behaviors, you're covered with Rector migration path. 
    <br>
    But what if you're using old broken Gedmo? **I'll show you how to migrate Gedmo to KnpLabs**.
        
tweet: "New Post on #php 🐘 blog: Migrate Gedmo to KnpLabs"
---

Pick behavior your want to migrate from Gedmo to KnpLabs:

- [1. Timestampable](#1-migrate-timestampable)
- [2. Sluggable](#2-migrate-sluggable)
- [3. Tree](#3-migrate-tree)
- [4. Translatable](#4-migrate-translatable)
- [5. Blameable](#5-migrate-blameable)
- [6. Loggable](#6-migrate-loggable)
- [7. SoftDeletable](#7-migrate-softdeletable)

<br>

If you **use other Gedmo behavior that is not listed here**, you might request or better add it to [KnpLabs](https://github.com/KnpLabs/DoctrineBehaviors/) repository via pull-request. I assure you I'll provide [stable maintenance support](/blog/2019/12/30/doctrine-behaviors-2-0-reloaded/) for KnpLabs package. It's not that hard with CI on steroids it has now.

<br>

## 1. Migrate Timestampable

### Gedmo 

```php
<?php

declare(strict_types=1);

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Timestampable\Traits\TimestampableEntity;

/**
 * @ORM\Entity
 */
class Meetup
{
    use TimestampableEntity;
}
```

↓

### KnpLabs

- Replace trait with `Knp\DoctrineBehaviors\Model\Timestampable\TimestampableTrait`
- Add `Knp\DoctrineBehaviors\Contract\Entity\TimestampableInterface` interface

```php
<?php

declare(strict_types=1);

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Knp\DoctrineBehaviors\Model\Timestampable\TimestampableTrait;
use Knp\DoctrineBehaviors\Contract\Entity\TimestampableInterface;

/**
 * @ORM\Entity
 */
class Meetup implements TimestampableInterface
{
    use TimestampableTrait;
}
```



## 2. Migrate Sluggable

### Gedmo

```php
<?php

declare(strict_types=1);

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * @ORM\Entity
 */
class Meetup
{
    /**
     * @Gedmo\Slug(fields={"name"})
     */
    private $slug;
    
    public function getSlug(): ?string
    {
        return $this->slug;
    }

    public function setSlug(?string $slug): void
    {
        $this->slug = $slug;
    }
}
```

↓

### KnpLabs

- Add `Knp\DoctrineBehaviors\Model\Sluggable\SluggableTrait` trait 
- Add `Knp\DoctrineBehaviors\Contract\Entity\SluggableInterface` interface
- Remove `getSlug()`/`setSlug()` method that is already in trait

- Replace `* @Gedmo\Slug(fields={"name"})` with `getSluggableFields()` method that contains fields

    E.g., from:

    ```php
    use Gedmo\Mapping\Annotation as Gedmo;
    
    // ...
    
    /**
     * @Gedmo\Slug(fields={"name", "surname"})
     */
    private $slug;
    ```
    
    to
    
    ```php
    
    /**
     * @return string[]
     */
    public function getSluggableFields(): array
    {
        return ['name', 'surname'];
    }
    ```

<br>

In full code:

```php
<?php

declare(strict_types=1);

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Knp\DoctrineBehaviors\Model\Sluggable\SluggableTrait;
use Knp\DoctrineBehaviors\Contract\Entity\SluggableInterface;

/**
 * @ORM\Entity
 */
class Category implements SluggableInterface
{
    use SluggableTrait;

    /**
     * @return string[]
     */
    public function getSluggableFields(): array
    {
        return ['name'];
    }
}
```

## 3. Migrate Tree

This one will be tricky, because:

- Gedmo supports *nested-set*, *closure-table* and *materialized-path*.
- KnpLabs currently supports [materialized path](https://knplabs.com/en/blog/knp-doctrine-orm-behaviors)

So if you're using anything but materialized path in Gedmo, you'll have to migrate PHP code (see below) + **migrate your database data to materialized path** (write your migration or Google one).

<br>

The PHP migration looks like this:

### Gedmo

```php
<?php

declare(strict_types=1);

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\Collection;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * @ORM\Entity
 * @Gedmo\Tree(type="nested")
 */
class Category
{
    /**
     * @Gedmo\TreeLeft
     * @ORM\Column(name="lft", type="integer")
     * @var int
     */
    private $lft;

    /**
     * @Gedmo\TreeRight
     * @ORM\Column(name="rgt", type="integer")
     * @var int
     */
    private $rgt;

    /**
     * @Gedmo\TreeLevel
     * @ORM\Column(name="lvl", type="integer")
     * @var int
     */
    private $lvl;

    /**
     * @Gedmo\TreeRoot
     * @ORM\ManyToOne(targetEntity="Category")
     * @ORM\JoinColumn(name="tree_root", referencedColumnName="id", onDelete="CASCADE")
     * @var Category
     */
    private $root;

    /**
     * @Gedmo\TreeParent
     * @ORM\ManyToOne(targetEntity="Category", inversedBy="children")
     * @ORM\JoinColumn(name="parent_id", referencedColumnName="id", onDelete="CASCADE")
     * @var Category
     */
    private $parent;

    /**
     * @ORM\OneToMany(targetEntity="Category", mappedBy="parent")
     * @var Category[]|Collection
     */
    private $children;

    public function getRoot(): self
    {
        return $this->root;
    }

    public function setParent(self $category): void
    {
        $this->parent = $category;
    }

    public function getParent(): self
    {
        return $this->parent;
    }
}
```

↓

### KnpLabs

- Add `Knp\DoctrineBehaviors\Model\Tree\TreeNodeTrait` trait
- Add `Knp\DoctrineBehaviors\Contract\Entity\TreeNodeInterface` interface
- Remove all tree related properties and methods, since they're in trait now
- Remove Gedmo annotations

```php
<?php

declare(strict_types=1);

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\Collection;
use Knp\DoctrineBehaviors\Contract\Entity\TreeNodeInterface;
use Knp\DoctrineBehaviors\Model\Tree\TreeNodeTrait;

/**
 * @ORM\Entity
 */
class Category implements TreeNodeInterface
{
    use TreeNodeTrait;
}
```

## 4. Migrate Translatable

### What about Performance?

I recall picking the behavior package for new Lekarna.cz 6 years ago. I was young, and a quantity was more than quality to me, so I was leaning towards Gedmo since it had more downloads.

But it's performance surprised me. Why? The translated item had 1:many dependency on translation table, so for every single item, it **joined an X extra lines forever single translated column**.

So even if you use Symfony 4 and **everything works well** for you, **consider comparing performance with KnpLabs translations**. Who knows, it might get your multi-lingual application **10x faster**. 

<br>

### Gedmo

```php
<?php

declare(strict_types=1);

namespace App\Entity;

use Gedmo\Mapping\Annotation as Gedmo;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Translatable\Translatable;

/**
 * @ORM\Entity
 */
class Category implements Translatable
{
    /**
     * @Gedmo\Translatable
     * @ORM\Column(length=128)
     */
    private $title;
    
    /**
     * @Gedmo\Locale
     */
    private $locale;
    
    public function setTitle($title)
    {
        $this->title = $title;
    }
    
    public function getTitle()
    {
        return $this->title;
    }
    
    public function setTranslatableLocale($locale)
    {
        $this->locale = $locale;
    }
}
```

↓

### KnpLabs

- Replace interface with `Knp\DoctrineBehaviors\Contract\Entity\TranslatableInterface`
- Add `Knp\DoctrineBehaviors\Model\Translatable\TranslatableTrait`
- Remove all translated fields and locale methods from main entity
- Remove Gedmo annotations

```php
<?php

declare(strict_types=1);

namespace App\Entity;

use Knp\DoctrineBehaviors\Model\Translatable\TranslatableTrait;
use Knp\DoctrineBehaviors\Contract\Entity\TranslatableInterface;

class Category implements TranslatableInterface
{
    use TranslatableTrait;
}
```

So, where is the *title* property we need to translate? Every translated property is in the new `<entity>Translation` class.
This **approach makes sure that the complexity of 1 item with dozens of translation stays 1:1** = it's super fast! 

```php
<?php

declare(strict_types=1);

namespace App\Entity;

use Knp\DoctrineBehaviors\Contract\Entity\TranslationInterface;
use Knp\DoctrineBehaviors\Model\Translatable\TranslationTrait;

class CategoryTranslation implements TranslationInterface
{
    use TranslationTrait;

    /**
     * @ORM\Column(length=128)
     */
    private $title;
}
``` 

In short:

- *Translatable* - the primary entity you use in your code
- *Translation* - the helper entity with translated items 

Usage stays the same:
 
```php 
$category->getTitle();
```

That's all for the migration. Oh, you're still reading? Are you waiting for some easy solution to cover it all?

## 5. Migrate Blameable

### Gedmo

```php
<?php

declare(strict_types=1);

namespace App\Entity;

use Gedmo\Mapping\Annotation as Gedmo;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 */
class Category
{
    /**
     * @Gedmo\Blameable(on="create")
     */
    private $createdBy;

    /**
     * @Gedmo\Blameable(on="update")
     */
    private $updatedBy;

    public function getCreatedBy()
    {
        return $this->createdBy;
    }
     
    public function getUpdatedBy()
    {
        return $this->updatedBy;
    }
}
```

↓

### KnpLabs

- Add `Knp\DoctrineBehaviors\Contract\Entity\BlameableInterface` interface
- Add `Knp\DoctrineBehaviors\Model\Blameable\BlameableTrait` trait
- Remove Gedmo annotations

```php
<?php

declare(strict_types=1);

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Knp\DoctrineBehaviors\Contract\Entity\BlameableInterface;
use Knp\DoctrineBehaviors\Model\Blameable\BlameableTrait;

/**
 * @ORM\Entity
 */
class Category implements BlameableInterface
{
    use BlameableTrait;
}
```

## 6. Migrate Loggable

### Gedmo

```php
<?php

declare(strict_types=1);

namespace App\Entity;

use Gedmo\Mapping\Annotation as Gedmo;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @Gedmo\Loggable
 */
class Category
{
    /**
     * @Gedmo\Versioned
     * @ORM\Column(name="title", type="string", length=8)
     */
    private $title;
}
```

↓

### KnpLabs

- Add `Knp\DoctrineBehaviors\Model\Loggable\LoggableTrait` trait
- Add `Knp\DoctrineBehaviors\Contract\Entity\LoggableInterface` interface
- Remove Gedmo annotations

```php
<?php

declare(strict_types=1);

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Knp\DoctrineBehaviors\Model\Loggable\LoggableTrait;
use Knp\DoctrineBehaviors\Contract\Entity\LoggableInterface;

/**
 * @ORM\Entity
 */
class Category implements LoggableInterface
{
    use LoggableTrait;

    /**
     * @ORM\Column(name="title", type="string", length=8)
     */
    private $title;
}
```

## 7. Migrate SoftDeletable

### Gedmo

```php
<?php

declare(strict_types=1);

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * @Gedmo\SoftDeleteable(fieldName="deletedAt", timeAware=false, hardDelete=true)
 * @ORM\Entity
 */
class Cateory
{
    /**
     * @ORM\Column(name="deletedAt", type="datetime", nullable=true)
     */
    private $deletedAt;
    
    public function getDeletedAt()
    {
        return $this->deletedAt;
    }
    
    public function setDeletedAt($deletedAt)
    {
        $this->deletedAt = $deletedAt;
    }
}
```

↓

### KnpLabs

- Add `Knp\DoctrineBehaviors\Contract\Entity\SoftDeletableInterface` interface
- Add `Knp\DoctrineBehaviors\Model\SoftDeletable\SoftDeletableTrait` trait
- Remove Gedmo annotations

```php
<?php

declare(strict_types=1);

namespace App\Entity;

use Knp\DoctrineBehaviors\Contract\Entity\SoftDeletableInterface;
use Knp\DoctrineBehaviors\Model\SoftDeletable\SoftDeletableTrait;

class Category implements SoftDeletableInterface
{
    use SoftDeletableTrait;
}
```

## Instant Upgrade what you Can

All right, for all the behaviors listed above, there is a Rector set:

```bash
composer require rector/rector --dev
vendor/bin/rector process src --set doctrine-gedmo-to-knplabs
```

Instant upgrades never cover the whole migration path - e.g., they lack database migrations - but they're are always **saving you 80 % of boring work**.

If anything breaks, [create and issue on Github](https://github.com/rectorphp/rector/issues/new?template=1_Bug_report.md) so you can enjoy fixed version soon. 

<br>

Happy migration!
