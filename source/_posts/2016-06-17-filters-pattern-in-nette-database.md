---
title: Filters Pattern in Nette Database
categories:
    - Nette
    - Filters
    - Zenify
perex: > # multi-line string
    You want to delete comments, so your readers won't see any spam or violent content.
    But you want to see them in administration. So you would have to create 2 different methods.
    Today I will show you, how to make only single one.

lang: "en"
thumbnail: "nette.png"
---

<p class="perex">{{ page.perex }}</p>
  
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

```language-php
namespace App\Database\Filter;

use Nette\Application\Application;
use Nette\Database\Table\Selection;
use Zenify\NetteDatabaseFilters\Contract\FilterInterface;


final class SoftDeletableFilter implements FilterInterface
{
    public function __construct(Application $application)
    {
        $this->application = $application;
    }


    /**
     * {@inheritdoc}
     */
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

```language-bash
composer require zenify/nette-database-filters
```

### 2. Register Extension

```language-yaml
# app/config/config.neon
extensions:
    - Zenify\NetteDatabaseFilters\DI\NetteDatabaseFiltersExtension
```

### 3. Create your filter

The one above...


### 4. Register it as a service

```language-yaml
# app/config/config.neon
services:
    - App\Database\Filter\SoftDeletableFilter
```

And that's it! Now your filter will be reflected in whole application.

For further use just **check Readme for [Zenify/NetteDatabaseFilters](https://github.com/Zenify/NetteDatabaseFilters#nette-database-filters)**.

## What Have You Learned Today?

- that Database Filters is a pattern for decorating query of specific table
- that Nette Database can implement this pattern in a form of service
- that you can add filter via simple service with [Zenify/NetteDatabaseFilters](https://github.com/Zenify/NetteDatabaseFilters)

If you have some tips how to this simpler or want to share your experience with filters, just let me know bellow.

Happy coding!
