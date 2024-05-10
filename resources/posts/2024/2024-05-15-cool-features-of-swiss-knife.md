---
id: 409
title: "Cool features of Swiss&nbsp;Knife"
perex: |
    When use swiss knife, we think of a tool that has many practical abilities.

    They're useful for different situation we might experience in the wild. Opening a box of milk? Here is a knife. Cutting a wood to start fire? Here is a chainsaw. Are letters on the paint buck to tiny? Try this magnifying class.

    Now we apply the same approach to PHP tooling.
---

While dealing with new PHP project, I often **use various tools at once** to handle work for me, but I don't want to pull 10 packages just to setup the CI. That's how [Swiss Knife](https://github.com/rectorphp/swiss-knife/) CLI package came together.

What can it do and how you can use it to improve your project?

<br>

There are 9 commands grouped in 3 feature categories you can use:

* First helps you get 100 % PSR-4 autoloading
* Second are spotters to warn you in CI
* Third are one time command that improve files

<br>

Install the package first:

```bash
composer require rector/swiss-knife --dev
```

It requires only PHP 7.2+, so you can run it on any project without obstacles.

If you consider your project modern, check only commands 4, 5, 8 and 9. They'll most likely bring you value.

## PSR-4 Autoloading

100 % PSR-4 autoloading helps us to speedup project loading, standardize class names and locations and prepare basic ground for PHPStan and Rector to work well. But it's not always an easy path. We might find same-named classes, multiple classes in single file or miss-spelled names.

We start with easy-picks, so we feel progress even in the greatest jungle.

### 1. Find multiple classes in single file

This commands spots multiple classes that are located in same file:

```bash
vendor/bin/swiss-knife find-multi-classes /src
```

Then we go through spotted files and manually extract class to separated file. This helps Composer and Rector to work with classes in reliable way as 1 file = 1 class.

### 2. Convert namespace in specific directory to defined namespace

Next commands helps with moving files in specific directory to specific namespace. This is useful when we merge multiple PSR-4 definitions in one:

```diff
 {
     "autoload": {
         "psr-4": {
-            "Product\\": "src/Product",
-            "Idea\\": "src/Idea",
-            "Execution\\": "src/Execution"
+            "App\\": "src"
        }
    }
}
```

To make composer autoload work now, we'd have to prefix all these classes in `/src` with `App\\`:

```diff
-namespace Product;
+namespace App\Product;
```

Now we can do this on scale with single command:

```bash
vendor/bin/swiss-knife namespace-to-psr-4 src --namespace-root "App\\"
```

### 3. Detect Unit Test

When we come to a project, we want to find out which tests are unit ones:

* no dependency on Kernel
* no dependency on parent container
* no extension of abstract test case with many complex features

We can then extract those tests to single directory, run them locally in speed and use PSR-4 root as well:

```php
vendor/bin/swiss-knife detect-unit-tests /tests
```

<br>

## Spotter commands

The next group of commands helps you to spot obvious errors before merging. We run them in CI to warn us, someting is wrong with the PR.

<br>

### 4. Spot commented code

Commented code can be a dead-feature "we might once use". It should be removed, as we have git to handle restore these if needed.
Commented code can be also debug mistake or forgotten removal:

```php
// this should work, todo remove later
// echo $value;
```

The CI passes and we go for merge, only to find out few weeks later there is commented code leftover to remove.

That's what this command is for:

```bash
vendor/bin/swiss-knife check-commented-code /src
```

You can also set your own amount of allowed commented lines, e.g. here any commented code under 5 lines will be allowed:

```bash
vendor/bin/swiss-knife check-commented-code /src --line-limit 5
```

Add this commands to your CI and forget.

<br>

### 5. Spot conflicts

Do you think CI would fail if there is a conflict in your code? The Github/Gitlab/Bitbucket would warn you about conflicts, yes. But once the conflict is marked as resolved, it would pass silently on any valid code:

```bash
 /**
<<<<<<< HEAD
  * @param string $name
=======
  * @param string $name Useless description
>>>>>>> branch-a
  */
```

We don't want such a code in our code base. We don't want to think about these situations either:

```bash
vendor/bin/swiss-knife check-conflicts src tests
```

Add this commands to your CI and forget.

<br>

### 6. Spot too long files

This is not very common use case, but it might come handy. When our developers or contributors run Windows, the tested fixture file name is nested and descriptive, it might crash on file length.

If you've experienced this before, this command will save you before merge:

```bash
vendor/bin/swiss-knife validate-file-length /src /tests
```

Add to CI and forget.

<br>

## Change files

### 7. Create Editorconfig

Do you prefer files with single type of spacing across whole project? Including json, yml, twig, blade etc.? Then editorconfig is a must have.

Add it at once:

```bash
vendor/bin/swiss-knife dump-editorconfig
```

<br>

### 8. Finalize classes

Regardless personal preference, the classes marked as `final` enable more PHPStan and Rector rules. E.g. Rector can safely add return type declarations in `final` classes. It will skip the class otherwise as it could create a bug in one of child classes.

<br>

Run and forget:

```bash
vendor/bin/swiss-knife finalize-classes /src /tests
```

You can check [in-depth post about this feature](/blog/finalize-classes-automated-and-safe).

<br>

### 9. Make JSON file readable again

Last but not least, sometimes we can come across a JSON output that is valid for computers, but unreadable for humans.

```bash
vendor/bin/phpstan analyse --error-format json
```

Save the result to a file:

```bash
vendor/bin/phpstan analyse --error-format json >> phpstan-result.json
```

To see very long line:

```json
{"totals":{"errors":0,"file_errors":73},"files":[...1000 chars...],"errors":[]}
```

That's where `pretty-json` commands saves us:

```bash
vendor/bin/swiss-knife pretty-json phpstan-result.json
```

↓

```json
{
    "totals": {
        "errors": 0,
        "file_errors": 73
    },
    "files": [
        ...
    ],
    "errors": []
}
```

We can use this to improve tool outputs, API results and also tests fixture files.

```bash
vendor/bin/swiss-knife pretty-json /tests
```

<br>

Protip: run commands with `--help` option, to see hidden features they offer.

<br>

Happy coding!
