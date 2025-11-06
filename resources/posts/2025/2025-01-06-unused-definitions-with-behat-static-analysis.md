---
id: 423
title: "Unused Definitions with Behat Static Analysis"
perex: |
    Recently, I've been working on projects with Behat tests. There are hundreds of definitions that can be used in feature file scenarios.

    I accidentally noticed that one of the definitions is not used at all and could be removed. This would result in less code to maintain, less code to read, and less code to upgrade.

    But I thought, "That's weird." **Why did not Behat report this definition** in our CI? Oh, because there is no Behat static analysis report out of the box. Let's fix that.

updated_since: "November 2025"
updated_message: "Updated to new package name and command names."
---

Behat definitions are marked with annotations:

```php
/**
 * @When I do something
 */
public function doSomething(): void
{
    //...
}
```

...or with PHP 8.0 attributes:

```php
use Behat\Step\Then;

#[Then('I see light')]
public function seeTheLight(): void
{
    //...
}
```

These definitions can be used in `*.feature` files:

```bash
Given I do something
 Then I see light
```

But as the project develops, there feature files can change:


```diff
 Given I do something
-Then I see light
+Then I see green
```

Now we should remove the "I see light" definition because it's not used anymore. But we often focus on business code and testing and don't have time to check this.

This is ideal work for static analysis!

## Behat Static Analysis

**Why static analysis?** We could have a Behat extension, that would run tests, compare used definitions, and report issues in the end. The problem is that Behat tests are extremely slow and we don't want to bind the tool with a specific Behat version.

We're lazy. We want to run a single command on any Behat version and **get fast reliable feedback within a couple of seconds**.

## What do we actually analyze?

The process is simple:

* we collect all definitions from PHP files, both annotations and attributes
* we separate full string match, regex, and :mask
* we look into `*.feature` files and check if the definition is still used

```bash
Checking static, named, and regex masks from 100 *Feature files
==============================================================

Found 1036 masks:

 * 747 exact
 * 106 /regex/
 * 181 :named

 1036/1036 [▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓] 100%

the product price is :value
tests/Behat/ProductContext.php

/^I submit order form and see payment page$/
tests/Behat/OrderContext.php


 [ERROR] Found 2 unused definitions
```


That's it! We can now remove the unused definitions and keep the codebase clean.

Add this command to your CI and you'll never have to worry about unused Behat definitions again. "What command?" you ask.

## Add Behastan to your Project

Instead of adding yet another CLI tool you have to update, we made it part of [Rector Swiss Knife](https://github.com/rectorphp/swiss-knife/).

It's a tool that can [do many useful things](/blog/cool-features-of-swiss-knife), but it's not a jack of all trades. It's a master of one - maintainable code.

```bash
composer require rector/behastan --dev
```

<br>

To run static analysis on your Behat definitions, just provide a directory with your Behat definitions and feature files:

```bash
vendor/bin/behastan unused-definitions tests
```

That's it!

<br>

Happy coding!
