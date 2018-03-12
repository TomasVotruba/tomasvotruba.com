---
id: 9
title: Filters Pattern in Nette Database
perex: |
    You want to delete comments, so your readers won't see any spam or violent content.
    But you want to see them in administration. So you would have to create 2 different methods.
    Today I will show you, how to make only single one.

deprecated: true
deprecated_since: "January 2017"
deprecated_message: |
    I have deprecated this package, because it was not very active - it has been downloaded only 5 times during past 4 months.
    <br><br>
    It is still available <a href="https://github.com/DeprecatedPackages/NetteDatabaseFilters">here for inspiration</a> though.
---

## Current way to do this

Let's say we have a `CommentRepository` class, where we put all methods that work with "comment" table.

In it, we have 2 methods:

- 1 for frontend
- 1 for administration

```php
namespace App\Repository;

use Nette\Database\Context;
use Nette\Database\Table\Selection;


class CommentRepository
{

    /**
     * @var Selection
     */
    private $commentTable;


    public function __construct(Context $database)
    {
        $this->commentTable = $database->table('comment');
    }


    /**
     * Returns only comments, that are not deleted.
     */
    public function fetchCommentsForFrontend()
    {
        return $this->commentTable->where('is_deleted = ?', FALSE)
            ->fetchAll();
    }


    public function fetchCommentsForAdministration()
    {
        return $this->commentTable->fetchAll();
    }

}
```

And **decide manually**, where to use `fetchCommentsForFrontend()` and where to use `fetchAllCommentsForAdministration()`.

This approach is bad practise, because it will eventually **make your every repository class double its size**.

No need for that! This has been already solved somewhere else.

Do you know Doctrine Filters? No? Go check [this short article to get the clue](/blog/2016/04/30/decouple-your-doctrine-filters). I'll wait here.


## Soft delete filter - in theory

In short, with filters you can modify any query. In our case:

- detect if the query is for "comment" table
- detect if we are frontend or backend
- if frontend, add where "is_deleted=0" condition to hide deleted comment

This will influence **every query for "comment" table**.
So you can be sure you'll never forget to add the condition.

## Show me the code

There is not much to talk about, because filters are made to be simple. So here is filter:

```php
# app/Database/Filter/SoftDeletableFilter.php

namespace App\Database\Filter;

use Nette\Application\Application;
use Nette\Database\Table\Selection;
use Zenify\NetteDatabaseFilters\Contract\FilterInterface;


class SoftDeletableFilter implements FilterInterface
{

    public function __construct(Application $application)
    {
        $this->application = $application;
    }


    public function applyFilter(Selection $selection)
    {
        // 1. apply only to "comment" table
        $tableName = $selection->getName();
        if ($tableName !== 'comment') {
            return;
        }

        // 2. skip for admin presenters
        // add your custom method, that detects admin presenter via name or class inheritance
        if ($this->isAdminPresenter($this->application->getPresenter())) {
            return;
        }

        // 3. show only visible (not deleted) comments
        $selection->where('is_deleted = ?', FALSE);
    }

}
```

And that's all.

These filters are possible in Nette\Database only thanks to [Zenify/NetteDatabaseFilters](https://github.com/Zenify/NetteDatabaseFilters) package.

Do you want to try it for yourself? Let's go.


## Your First Filter in 4 steps

### 1. Install package

```bash
composer require zenify/nette-database-filters
```

### 2. Register Extension

```yaml
# app/config/config.neon
extensions:
    - Zenify\NetteDatabaseFilters\DI\NetteDatabaseFiltersExtension
```

### 3. Create your filter

The one above...


### 4. Register it as a service

```yaml
# app/config/config.neon
services:
    - App\Database\Filter\SoftDeletableFilter
```

And that's it! Now your filter will be reflected in whole application.

So you can reduce your repository code and use `fetchComments()` in all places.

```php
# app/Repository/CommentRepository.php

namespace App\Repository;

use Nette\Database\Context;
use Nette\Database\Table\Selection;


class CommentRepository
{

    /**
     * @var Selection
     */
    private $commentTable;


    public function __construct(Context $database)
    {
        $this->commentTable = $database->table('comment');
    }


    public function fetchComments()
    {
        return $this->commentTable->fetchAll();
    }

}
```

For further use just **check Readme for [Zenify/NetteDatabaseFilters](https://github.com/Zenify/NetteDatabaseFilters#nette-database-filters)**.

## Protip for multiple tables with the same column!

What if you have **multiple tables with "is_deleted" column**? "comment", "article", "page" table... maybe "banner", "user" in the furture.

- Do you have to create filter for every one of them? **No.**
- Do you have to name them all in the filter class? **No.**

- Do you need to check the column presence only? **YES!**

And I will show you how do it:

```php
# app/Database/Filter/SoftDeletableFilter.php

// ...

public function applyFilter(Selection $selection)
{
    if (!$this->isSoftdelable($selection)) {
        return;
    }

    // ... condition code
}

/**
 * @return bool
 */
private function isSoftdelable(Selection $selection)
{
    $selectionToCheck = clone $selection;
    return $selectionToCheck->fetch()
        ->offsetExists('is_deleted');
}
```

Pretty neat, huh?


## What Have You Learned Today?

- that Database Filters is a pattern for decorating query of specific table
- that Nette Database can implement this pattern in a form of service
- that you can add filter via simple service with [Zenify/NetteDatabaseFilters](https://github.com/Zenify/NetteDatabaseFilters)

If you have some tips how to this simpler or want to share your experience with filters, just let me know bellow.

Happy coding!
