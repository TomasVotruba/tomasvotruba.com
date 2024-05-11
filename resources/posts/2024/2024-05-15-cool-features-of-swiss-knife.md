---
id: 409
title: "Cool features of Swiss&nbsp;Knife"
perex: |
    When using a Swiss knife, we think of a tool with many practical abilities.

    They're useful for different situations we might experience in the wild. Opening a box of milk? Here is a knife. Cutting wood to start a fire? Here is a chainsaw. Are the letters on the paint bucket too tiny? Try this magnifying class.

    Now, we apply the same approach to PHP tooling.
---

While working on a new PHP project, I often use various tools at once to handle work, but I don't want to pull 10 packages just to set up the CI. That's how the [Swiss Knife](https://github.com/rectorphp/swiss-knife/) CLI package came together.

What can it do, and how can you use it to improve your project?

<br>

There are 9 commands grouped in 3 feature categories you can use:

* First helps you get 100 % PSR-4 autoloading
* Second are spotters to warn you in CI
* Third are one-time commands that improve files

<br>

Install the package first:

```bash
composer require rector/swiss-knife --dev
```

It requires only PHP 7.2+, so you can run it on any project without obstacles.

If your project is modern, check only commands 4, 5, 8, and 9. They'll bring you value.

## PSR-4 Autoloading

100 % PSR-4 autoloading helps us speed up project loading, standardize class names and locations, and prepare the ground for PHPStan and Rector to work well. But it's not always easy. We might find classes with the same name, multiple classes in a single file, or misspelled names.

We start with easy picks, so we feel progress even in the most complex jungle.

### 1. Find multiple classes in a single file

This command spots multiple classes located in the same file:

```bash
vendor/bin/swiss-knife find-multi-classes /src
```

Then, we go through the spotted files and manually extract the class into separate files. This helps the Composer and Rector work with classes reliably, as 1 file = 1 class.

### 2. Convert namespace in a specific directory to defined namespace

Following commands helps with moving files in a specific directory to a specific namespace. It can be helpful when we merge multiple PSR-4 definitions in one:

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

Now, we can do this on a scale with a single command:

```bash
vendor/bin/swiss-knife namespace-to-psr-4 src --namespace-root "App\\"
```

### 3. Detect Unit Test

When we come to a project, we want to find out which tests are unit ones:

* no dependency on Kernel
* no dependency on the parent container
* no extension of an abstract test case with many complex features

We can then extract those tests to a single directory, run them locally in speed, and use PSR-4 root as well:

```php
vendor/bin/swiss-knife detect-unit-tests /tests
```

<br>

## Spotter commands

The next group of commands helps you to spot apparent errors before merging. We run them in CI to warn us something is wrong with the PR.

<br>

### 4. Spot commented code

Commented code can be a dead feature "we might once use". It should be removed, as we have git to restore these if needed.
Commented code can also be debug mistakes or forgotten removal:

```php
// this should work, todo remove it later
// echo $value;
```

The CI passes, and we go for merge, only to find out a few weeks later that a comment code is left over to remove.

That's what this command is for:

```bash
vendor/bin/swiss-knife check-commented-code /src
```

You can also set your own amount of allowed commented lines, e.g., here, any commented code under 5 lines will be allowed:

```bash
vendor/bin/swiss-knife check-commented-code /src --line-limit 5
```

Add the command to your CI and forget.

<br>

### 5. Spot conflicts

Would CI fail if there is a conflict in your code? The Github/Gitlab/Bitbucket would warn you about conflicts. But once the conflict is marked as resolved, it would pass silently on any valid code:

```bash
 /**
<<<<<<< HEAD
  * @param string $name
=======
  * @param string $name Useless description
>>>>>>> branch-a
  */
```

We want to avoid such a code in our code base. We don't want to think about these situations either:

```bash
vendor/bin/swiss-knife check-conflicts src tests
```

Add the command to your CI and forget.

<br>

### 6. Spot too-long files

This is not an everyday use case, but it might be helpful. When our developers or contributors run Windows, the tested fixture file name is nested and descriptive; it might crash on file length.

If you've experienced this before, this command will save you before merge:

```bash
vendor/bin/swiss-knife validate-file-length /src /tests
```

Add to CI and forget.

<br>

## Change files

### 7. Create Editorconfig

Do you prefer files with a single type of spacing across the whole project? Including JSON, YML, TWIG, Blade, etc.? Then editorconfig is a must-have.

Add it at once:

```bash
vendor/bin/swiss-knife dump-editorconfig
```

<br>

### 8. Finalize classes

Regardless of personal preference, the classes marked as `final` enable more PHPStan and Rector rules. E.g. Rector can safely add return type declarations in `final` classes. Otherwise, it will skip the class, as it could create a bug in one of the child classes.

<br>

Run and forget:

```bash
vendor/bin/swiss-knife finalize-classes /src /tests
```

You can check [in-depth post about this feature](/blog/finalize-classes-automated-and-safe).

<br>

### 9. Make JSON file readable again

Last but not least, sometimes we encounter JSON output that is valid for computers but unreadable for humans.

```bash
vendor/bin/phpstan analyse --error-format json
```

Save the result to a file:

```bash
vendor/bin/phpstan analyse --error-format json >> phpstan-result.json
```

To see a very long line:

```json
{"totals":{"errors":0,"file_errors":73},"files":[...1000 chars...],"errors":[]}
```

That's where the `pretty-json` command saves us:

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

We can use this to improve tool outputs, API results, and also test fixture files.

```bash
vendor/bin/swiss-knife pretty-json /tests
```

<br>

Protip: run commands with the `--help` option to see hidden features they offer.

<br>

Happy coding!
