---
id: 404
title: "Rector Book 2024 Release with Brand new Chapter"
perex: |
    I'm happy to introduce the latest update to our book - *Rector, the Power of Automated Refactoring*, along with Rector version 0.19.5 from this week.

    This release includes 2 new commands, brand new configuration with smart IDE autocomplete, brand new chapter and DX improvements to help you master code refactoring with ease.
---

<a href="https://leanpub.com/rector-the-power-of-automated-refactoring?utm_source=getrectororg_book_detail" style="float:right;max-width: 16em">
    <img src="https://getrector.com/assets/images/logo/logo_bigger/rector_book.png" class="img-fluid img-thumbnail ms-4 mt-0">
</a>

We've released the [Rector - The Power of Automated Refactoring](https://leanpub.com/rector-the-power-of-automated-refactoring) book with goal of continuous upgrades. It's been a year since last upgrade, so it's time to step up and deliver fresh 2024 book release.

<br>

Key Highlights of this update are:

### New Chapter: Explore "Node Type and Refactor Examples"

* Featuring typical `refactor()` use-cases, enhancing your refactoring skills.

### Simplified Configuration

* Introducing a minimalist `RectorConfig::configure()` config for smoother setup.

```php
use Rector\Config\RectorConfig;

return RectorConfig::configure()
    ->withPaths([__DIR__ . '/app', __DIR__ . '/tests'])
    ->withImportNames(removeUnusedImports: true)
    ->withPreparedSets(codeQuality: true, codingStyle: true,  instanceOf: true)
    ->withPhpSets();
```

### Convenient Commands

* New commands, such as `setup-ci` and `custom-rule`, to streamline your workflow.

### Code Examples in Git Repository

* Access a complete code repository at [`rectorphp/rector-book-code-examples`](https://github.com/rectorphp/rector-book-code-examples) for comprehensive learning.


### Improved Visuals

* Enhancements in Rector rule and test file visualization for better understanding.
* Dependency Updates: Keeping pace with technology, we've updated dependencies, including Rector (0.15 → 0.19.3), PHP (8.0 → 8.2), ECS (to 12.1), and PHPUnit (9.5 → 10.5).
* Clarification: Added a section on differentiating between `Stmt` and `Expr` in the "Creating Your First Rector Rule" chapter.
* Refactoring Insights: Discover the power of removed parent nodes and the use of attributes for more efficient refactoring.

<br>

This update empowers you to become a code refactoring expert with the latest 2024 Rector features.

<br>

It's available immediately for everyone who already owns the book.

<br>

If you haven't purchased the book yet, **[grab your copy now](https://leanpub.com/rector-the-power-of-automated-refactoring)**!

<br>

Happy coding!
