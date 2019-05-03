---
id: 208
title: "Alias as a Code Smell"
perex: |
    Do you have 2 class with the same name? `App\Entity\Product` and `App\Entity\Product`? Of course not. But **I bet you have at least 2 classes with the same *short name* `*\Product` and `*\Product`**.
    <br>
    <br>
    And that smells... Why?
tweet: "New Post on #php 🐘 blog: Alias as a Code Smell"
---

In recent projects, I consult I start to notice an interesting pattern. It's how important are uniquely named short classes. Short class name = the part after last `\`.

How would you like to use this code?

<img src="/assets/images/posts/2019/alias/too_many.gif" class="img-thumbnail">

## Duplicated Short Classes sponsored by DDD

When I ask why the programmer picked such a naming, more often than not they refer DDD patterns, where you don't care if the element is interface, trait or abstract. The naming is always the same. Do you know which of them is class, interface, category Query and product Query?

- `Query`
- `Query`
- `Query`
- `Query`

<br>

Now the programmer has to become a detective.

- `Query` → see the namespace, oh it's a `/Contract`, that's probably an interface
- `Query` → oh, the namespace is `/Behavior`, that's probably a trait
- `Query` → see the file location, it's in `/Model/Category`, that's probably a category query
- `Query` → see the file location, it's in `/Model/Product/Contract`, that's probably a product query interface

Good job! The **code starts to steal time and energy** the reader 🤦.

## Make it Clear with Aliases?

Luckily, there is a band-aid to this problem, aliases!

```php
<?php

namespace App;

use App\Behavior\Query as QueryTrait;
use App\Model\Category\Query as CategoryQuery;
use App\Model\Product\Contract\Query as ProductQueryInterface;
use App\Contract\Query as QueryInterface;

final class ProductQuery implements ProductQueryInterface, QueryInterface
{
    use QueryTrait;

    public function findByCategoryQuery(CategoryQuery $categoryQuery)
    {
        // ...
    }
}
```

Now we have 200 % more code and but it's a bit more clear.

<blockquote class="blockquote mt-4 mb-4 text-center">
    When you see an alias, and it's not for 3rd party code,
    <br>
    you have a code smell in there.
</blockquote>

Get rid of it!

## Use Your Common Sense

If you see a car of Tesla, you'd probably name it "Tesla car". Not a "car".

**Get rid of aliases and name your classes in a unique and clear way:**

```php
<?php

namespace App;

use App\Behavior\QueryTrait;
use App\Model\Category\CategoryQuery;
use App\Model\Product\Contract\ProductQueryInterface;
use App\Contract\QueryInterface;

final class ProductQuery implements ProductQueryInterface, QueryInterface
{
    use QueryTrait;

    public function findByCategoryQuery(CategoryQuery $categoryQuery)
    {
        // ...
    }
}
```


## Automated Smell Detection

Do you need to help to find these smells? Just add [`DuplicatedClassShortNameSniff`](https://github.com/symplify/codingstandard#use-unique-class-short-names) to your coding standard:

```yaml
# ecs.yml
services:
    Symplify\CodingStandard\Sniffs\Architecture\DuplicatedClassShortNameSniff:
        allowed_class_names:
            - 'Request'
            - 'Response'
```

<br>


Happy coding!
