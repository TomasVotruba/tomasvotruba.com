---
id: 232
title: "Doctrine Behaviors 2.0 - Reloaded"
perex: |
    In [How we Upgraded Pehapkari.cz from Symfony 4 to 5 in 25 days](https://pehapkari.cz/blog/2019/12/23/how-we-upgraded-pehapkari-cz-from-symfony-4-to-5-in-25-days/) post, there is a section about Doctrine Behaviors in Symfony 5.
    <br><br>
    **None of stof, gedmo, nor KnpLabs is working with Symfony 5**. There are many issues in each of those projects asking for maintainers to merge PRs and tag it. One of those issues was heard, and the owner gave me maintainer access.
    <br><br>
    How does it look 3 weeks later?    
     
tweet: "New Post on #php 🐘 blog: Doctrine Behaviors 2.0 - Reloaded"
tweet_image: "/assets/images/posts/doc_beha_stats.png"
---

## From First Excitement Rush to Heavy Burden of Responsibility  

[KnpLabs/DoctrineBehaviors](https://github.com/KnpLabs/DoctrineBehaviors) development was stuck since 2015. There were 10+ opened PRs to use interfaces over traits (to give classes clear contract), bug-fix PRs, and design improvements.

When I got a message from KnpLabs with an offer to maintain, I was super happy to say ["hell, yeah"](https://sivers.org/hellyeah). An excitement rush went through my veins. I could saw all the huge refactoring for better and how it helps the whole PHP community to move another step forward. 

A moment later, when I realized what happened and how quickly, I started to be scared. The repository looked like a massive chunk of code that will need many design changes,  back compatibility breaks, etc. **All the responsibility started to fall on my shoulders, and it was heavy**.

I took it as challenge and started to [climb mountain](/blog/2018/04/30/programming-climbing-a-huge-mountain/), without knowing if or how this will end-up. 

<blockquote class="blockquote text-center">
    There were only 2 options:<br>finished tagged version 2 or burn-out in the process.
</blockquote> 

I took the risk.

## What Can We Do in 3 Weeks?

- **39 merged pull-requests**
- **93 closed issues**
- **5 alpha/beta tag releases** 
- full support of Symfony 4.4 and 5

<br>

<img src="/assets/images/posts/doc_beha_pulse.png" class="img-thumbnail">

<br>

**Added/removed lines per week:**  

<img src="/assets/images/posts/doc_beha_stats.png" class="img-thumbnail">

## What is new in Doctrine Behaviors 2.0?

All right, you get it. There were lots changes, but what exactly?

Let's start with the *sad part*.


### 3 Removed Behaviors

Tests did not cover some behaviors, nor described in README. When you look at them closely and write a test for them, you'll see **they never worked** or worked accidentally. I've cross-references these features with [gedmo/doctrine-extensions](https://packagist.org/packages/gedmo/doctrine-extensions) and saw DoctrineBehaviors is the only package that has them, resp. pretend to have them.

<br>

**Saying that these 3 behaviors were dropped:**

[Sortable](https://github.com/KnpLabs/DoctrineBehaviors/pull/473)
- it only updated all values to `1`, completely broken

[Geocodable](https://github.com/KnpLabs/DoctrineBehaviors/pull/467) 
- it only worked on PostgreSQL with a specific function
- has a conflict with km/miles
- method API was very limited to a few narrow cases 

[Filterable](https://github.com/KnpLabs/DoctrineBehaviors/pull/463)
- overcomplicated API 4 required methods for single simple filter
- basically `findBy*()` magic on steroids, better use explicit non-magic methods with clear naming

**It is recommended to implement them yourself that suite your specific needs**.

<br>

Now that the sad part is behind us let's look at new features.   

 
### From Trait to Interface

This work was initiated **by [bocharsky-bw](https://github.com/bocharsky-bw) and [@lemoinem](https://github.com/lemoinem)** back in 2016. Thank you both for clear PRs, it was quite easy for me to see changes and apply them to new and drastically different code [in this single PR](https://github.com/KnpLabs/DoctrineBehaviors/pull/442). 

**The idea is simple**: instead of traits that are problematic to detect by `instanceof` or `is_a()`, use interface for method and class contract.

<br>

What does it mean? This is *trait-first* approach:

```php
<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Knp\DoctrineBehaviors\Model\Timestampable\TimestampableTrait;

/**
 * @ORM\Entity
 */
class Product
{
    use TimestampableTrait;
}
```

You use traits, so you don't have to create properties (`$createdBy` and `$updatedBy` in this case) manually. There are also 2 split traits for methods and properties, so the code above equals:

```php
<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Knp\DoctrineBehaviors\Model\Timestampable\TimestampablePropertiesTrait;
use Knp\DoctrineBehaviors\Model\Timestampable\TimestampableMethodsTrait;

/**
 * @ORM\Entity
 */
class Product
{
    use TimestampablePropertiesTrait;
    use TimestampableMethodsTrait;
}
```
 
But what happens if you remove trait and want to override methods?

```php
<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 */
class Product
{
}
```

Well, there is no contract by an interface, so we have no idea to fix this, we introduced interfaces: 

```php
<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Knp\DoctrineBehaviors\Contract\Entity\TimestampableInterface;

/**
 * @ORM\Entity
 */
class Product implements TimestampableInterface
{
}
```

Now PHPStorm tells you what methods it needs. You can use your code or a trait to cover them.

It also easy to detect an object that uses this behavior, which significantly improved performance and architecture at the same time:

```php
use Knp\DoctrineBehaviors\Contract\Entity\TimestampableInterface;

// object
$isTimestampableEntity = $entity instanceof TimestampableInterface;

// string class name
$isTimestampableEntity = is_a($entityClass, TimestampableInterface::class, true);
``` 

### Translation now needs Id

All the behaviors didn't add any id properties. Except for translation, where the id was added with this ugly magic method (mind the `@deprecated` note):

<img src="/assets/images/posts/doc_beha_id_magic.png" class="img-thumbnail">

The [inconsistency](https://github.com/KnpLabs/DoctrineBehaviors/issues/154) [was](https://github.com/KnpLabs/DoctrineBehaviors/issues/415) [problematic](https://github.com/KnpLabs/DoctrineBehaviors/pull/158). In short: it was buggy and hard to use your id property because DoctrineBehaviors tried to *guess* what id you probably want. 

The fix was suggested [early 2019](https://github.com/KnpLabs/DoctrineBehaviors/pull/421), but the final fix was [merged in 2020](https://github.com/KnpLabs/DoctrineBehaviors/pull/480/files).

What does it mean for your entities? **You need to add their own id**: 

```diff
 <?php

 namespace App\Entity;

 use Knp\DoctrineBehaviors\Contract\Entity\TranslatableInterface;
 use Knp\DoctrineBehaviors\Model\Translatable\TranslatableTrait;

 /**
  * @ORM\Entity
  */
 class TranslatableEntityTranslation implements TranslationInterface
 {
     use TranslationTrait;

+    /**
+     * @ORM\Id
+     * @ORM\Column(type="integer")
+     * @ORM\GeneratedValue(strategy="AUTO")
+     * @var int
+     */
+    private $id;
```

### Scalar Types

Scalar types make bugs more comfortable to discover, e.g., when a subscriber is broken, and it does not add value, return null will result in a fatal error.
 
That's why all the traits have scalar param and return types:

```diff
 trait TimestampableMethodsTrait
 {
-    /**
-     * @return DateTimeInterface
-     */
-    public function getCreatedAt()
+    public function getCreatedAt(): DateTimeInterface
     {
         return $this->createdAt;
     }
 }
```

Migration path for these types will be provided based on feedback, so **in case you override these methods manually, you won't have to complete the types**. 

### Code Quality Changes

Feature improvements are essential. But to make them fast and stable, the [**code quality has the same importance**](/blog/2019/12/23/5-things-i-improve-when-i-get-to-new-repository/). 

<blockquote class="blockquote text-center">
    Energy saved on manual maintenance<br>
    can be used to improve features.
</blockquote>

How has been code quality improved?

- [ECS was added with PSR-12 coding standard check](https://github.com/KnpLabs/DoctrineBehaviors/pull/435/files)
- [`CHANGELOG.md` is fully generated](https://github.com/KnpLabs/DoctrineBehaviors/pull/470)
- [tests now respect PSR-4](https://github.com/KnpLabs/DoctrineBehaviors/commit/3b17cdf656b5f904c4222c559d78bbd17217881a)
- [Rector CI now checks dead code, code quality and Nette\Utils](https://github.com/KnpLabs/DoctrineBehaviors/pull/445)
- [PHPStan now checks the static analysis](https://github.com/KnpLabs/DoctrineBehaviors/pull/436/)  


## Community Driven

Without the PHP community, packages are just chunks of code. Community around the package, city, or a meetup group is what makes it specials, gives it power and motivation to grow.

**I'd love to thank [@laurentHCM](https://github.com/laurentHCM) and [@nicolashachet](https://github.com/nicolashachet)** for the testing process of version 2 and instant migration with Rector. For their patience to report issues, test new Rector rules, and trying to solve all the packages conflicts desperately.

You gave me **the motivation** that helped me to overcome difficult moments when I got lost in the code.

## How to Migrate From version 1 to 2?

What is the biggest problem of any significant refactoring? The backward compatibility (*BC*) breaks:

- Class rename?
- Trait renamed?
- Return type added?
- Param type added?

**Roughly over 80-120 BC breaking changes** were performed between versions 1 and 2.

Imagine you have 20 such entities in your code, using 4 behavior traits, and now you have to:

- complete interfaces
- complete param types
- complete return types
- rename traits
- add an id to every translation entity

** I'd be angry at any project, who would do such change, without providing one-click upgrade path**.

<br>

That's why during such BC breaking changes **I've prepared [instant upgrade Rector set](https://github.com/KnpLabs/DoctrineBehaviors/blob/master/upgrade/rector/doctrine-behaviors-20.yaml) you can run on your code**:

```yaml
composer require rector/rector --dev
vendor/bin/rector process src --config vendor/knplabs/doctrine-behaviors/upgrade/rector/doctrine-behaviors-20.yaml
```

Single command turns to upgrade from 1 to 2 from days to few dozens of minutes.   

## How does it Compare to Gedmo/Stof?

Are you a stof/gedmo user? Then you're familiar [with Symfony 5 issues](https://github.com/stof/StofDoctrineExtensionsBundle/issues) around it. Don't worry. The KnpLabs\DoctrineBehaviors 2 was developed bearing you in mind.

KnpLabs\DoctrineBehaviors don't support softdeletable and non-materialized tree path *yet*, but the rest of the features are covered.

### How do you Migrate from Gedmo/Stof to KnpLabs\DoctrineBehaviors?

Read [Migrate Gedmo to KnpLabs](/blog/2020/01/06/migrate-gedmo-to-knplabs) post to find the answer. 

<br>

That's all for me. Use it, bend it, break it, and report issues or pull-request so that we can make migration path together even smoother.

<br>  

**I wish you happy new year 2020 full of huge adventures not only in code!** 
