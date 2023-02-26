---
id: 128
title: "5 Gotchas of the Bin File in PHP CLI Applications"
perex: |
    This post focuses on bin files. It's the smallest part of PHP CLI Application, so I usually start with it.


    Yet, there are still a few blind paths you can struggle with. I'll drop a few extra tricks to make your bin file clean and easy to maintain.
---

## What is the Bin File?

The bin file is not a trash bin. It's a binary file, the entry point to your application the same way `www/index.php` is. You probably already use them:

- [vendor/bin/ecs](https://github.com/symplify/symplify/blob/master/packages/easy-coding-standard/bin/ecs)
- [vendor/bin/php-cs-fixer](https://github.com/FriendsOfPHP/PHP-CS-Fixer/blob/master/php-cs-fixer)
- [vendor/bin/phpcs](https://github.com/squizlabs/PHP_CodeSniffer/blob/master/bin/phpcs)
- [vendor/bin/phpstan](https://github.com/phpstan/phpstan-src/blob/master/bin/phpstan)

## 1. Create it

### The Name

The bin file should be **named after the application**, **short** and **easy to type**.
So when I first released EasyCodingStandard, I used `easy-coding-standard` name. It was easy to remember, but when I had a talk I often miss-typed such a long name. After a while, I moved to `ecs`.

It should be also **easy to remember** and **unique**.
Imagine that `php-cs-fixer` would be `phpcf` or `phpcf`. Since there is already `phpcs` taken, it might be trouble to remember. I think that's why the name is a little bit longer.

### The Location

Where to put the file? Few people in the past put it in the root directory (only `php-cs-fixer` from 4 projects above have it that way). But **the trend is to use `bin` directory**. The same way `index.php` was moved to `www/index.php` or `public/index.php`.

```bash
bin/your-bin-file
```

## 2. Composer Autoload

With structure like this:

```bash
/bin/your-bind-file
/src
/vendor/autoload.php
```

The obvious code to add to `/bin/your-bind-file` is:

```php
<?php declare(strict_types=1);

require_once  __DIR__ . '/../vendor/autoload.php';
```

And that's it!

<br>

Not that fast. It might work for your `www/index.php` file in your application, but is **that application ever installed as a dependency**?

How do we cover autoload for a dependency? Imagine somebody would install `your-vendor/your-package` on his application. The file structure would look like this:

```bash
/src/
/vendor/autoload.php
/vendor/your-vendor/your-package/bin/your-bin-file
```

Now we need to get to `/vendor/autoload.php` of that application:

```diff
 <?php declare(strict_types=1);

-require_once  __DIR__ . '/../vendor/autoload.php';
+require_once  __DIR__ . '/../../../../vendor/autoload.php',
```

Great, people can use our package now. But it stopped working for our local repository. We'll probably have to seek for both of them:

```php
<?php declare(strict_types=1);

$possibleAutoloadPaths = [
    // local dev repository
    __DIR__ . '/../vendor/autoload.php',
    // dependency
    __DIR__ . '/../../../../vendor/autoload.php',
];

foreach ($possibleAutoloadPaths as $possibleAutoloadPath) {
    if (file_exists($possibleAutoloadPath)) {
        require_once $possibleAutoloadPath;
        break;
    }
}
```

**Comments are very important** because this is very easy to get lost in. Trust me, I managed to fail a dozen times. Also, other people will appreciate it because it's WTF to see loading more than one `vendor/autoload.php`.

Imagine you'd move your package to a monorepo structure:

```diff
 $possibleAutoloadPaths = [
-    // local dev repository
+    // after split repository
     __DIR__ . '/../vendor/autoload.php',
     // dependency
     __DIR__ . '/../../../../vendor/autoload.php',
+    // monorepo
+    __DIR__ . '/../../../vendor/autoload.php',
 ];
```

### Exceptionally Well Done

```diff
 <?php declare(strict_types=1);

 $possibleAutoloadPaths = [
     // local dev repository
     __DIR__ . '/../vendor/autoload.php',
     // dependency
     __DIR__ . '/../../../../vendor/autoload.php',
 ];

+$isAutoloadFound = false;
 foreach ($possibleAutoloadPaths as $possibleAutoloadPath) {
     if (file_exists($possibleAutoloadPath)) {
         require_once $possibleAutoloadPath;
+        $isAutoloadFound = true;
         break;
     }
 }
+
+if ($isAutoloadFound === false) {
+    throw new RuntimeException(sprintf(
+        'Unable to find "vendor/autoload.php" in "%s" paths.',
+        implode('", "', $possibleAutoloadPaths)
+    ));
+}
```

## 3. She Bangs

Since the bin file doesn't have a `.php` suffix by convention a system doesn't know, what language it's in. What happens when we run the bin file?

```bash
bin/your-bin-file
```

↓

```bash
vendor/bin/your-bin-file: 1: vendor/bin/your-bin-file: Syntax error: "(" unexpected
```

Well, we know it's in PHP so run it with PHP:

```bash
php bin/your-bin-file
```

All works! But do we ever run this?

```php
php composer
```

No, because **we're lazy and we want to type as less as possible**. How do we achieve the same effect for our file?

We add a [*shebang*](https://www.youtube.com/watch?v=5ihtX86JzmA) - a special line that will tell the system what interpret should be used:

```php
#!/usr/bin/env php
<?php declare(strict_types=1);

// ...
```

It can be translated to:

```bash
/usr/bin/env php bin/your-bin-file
```

Try it. Does it work?

## 4. Free Access Rights

This allows to run the bin file on other people's computer:

```php
chmod +x bin/your-bin-file
```

## 5. The Composer Symlink

If we install your package, we'll find the bin file here:

```bash
/vendor/your-vendor/your-package/bin/your-bin-file
```

But not in:

```bash
/vendor/bin/your-bin-file
```

Too bad. We're super lazy, so we want it there. How can we make it happen?

The Composer has [special *bin* section](https://getcomposer.org/doc/articles/vendor-binaries.md#how-is-it-defined-), where we can **define the symlink path for your bin file**. Just add this to `composer.json` of your package:

```json
{
    "bin": "bin/your-bin-file"
}
```

Tada! After we install such a package, we'll find it in the right place.

```bash
/vendor/bin/your-bin-file
```


## Final Version

```php
#!/usr/bin/env php
<?php declare(strict_types=1);

$possibleAutoloadPaths = [
     // local dev repository
     __DIR__ . '/../vendor/autoload.php',
     // dependency
     __DIR__ . '/../../../autoload.php',
];

foreach ($possibleAutoloadPaths as $possibleAutoloadPath) {
    if (file_exists($possibleAutoloadPath)) {
        require_once $possibleAutoloadPath;
        break;
    }
}

// your PHP code to run

$container = (new ContainerFactory)->create();
$application = $container->get(Application::class);
exit($application->run());
```


And that's it!

<br>

Happy coding!
