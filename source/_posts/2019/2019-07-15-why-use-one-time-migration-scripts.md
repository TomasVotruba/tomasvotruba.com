---
id: 218
title: "Why use One-Time Migration Scripts"
perex: |
    School system taught me to despite old book and consider them outdated, rather about stories than knowledge. I wanted to prove I'm right, so I've read [Pragmatic Programmer](https://www.amazon.com/Pragmatic-Programmer-Journeyman-Master/dp/020161622X) from 1999 and *you won't believe what happened*...
tweet: "New Post on #php 🐘 blog: Why use One-Time Migration Scripts"
---

You already probably know about instant refactoring and [pattern refactoring](/blog/2019/04/15/pattern-refactoring/) (I'm deprecating refactoring as you know it) that's possible thanks to [Rector](https://github.com/rectorphp/rector). But they require a certain knowledge of code and it's patterns.

## Instant Refactoring Today?

I was wondering, **how can you use instant refactoring at your work today with what you already know?** So did *Andrew Hunt* and *David Thomas*, authors of [Pragmatic Programmer](https://www.amazon.com/Pragmatic-Programmer-Journeyman-Master/dp/020161622X).

They write about a migration script, that you **write, use once and then delete it**. Like a mandala-script :). 
In the end, you only commit changed files, but no the script you've made for it.

## Where to use *Mandala-Script*?
  
- move files from one directory to another
- rename files from `*.yml` to `*.yaml`
- remove all trailing whitespaces
- remove `@throw` annotations from the docblocks
- update version of all `<your-framework>/*` in all `composer.json` from 3 to 4
- generate `.env`, `docker-composer.yml` and `.gitlab-ci.yml` from a set of env variables
- etc.

Any script that:

- is related to **many files**
- or is done **over and over again**
- is **more fun (effective)** to write than to do manually
- has the potential to be reused or extended in the future

### It's Good for Business

From a business point of view, it's very useful in cases, **where it can be done wrong**. We talk about configuration files like ENV and YAML. Usually, they have poor (= no) validation, so finding a bug is like reading a manual written all over the walls of your house about how to open door.

Just last month the `KEY=value` vs `KEY: value` lead to 4-5 hours wasted in my current work.

<br>

As you can see, the idea is very simple, so let's use it in a simple case.
I have prepared 2 related examples for you: 

## 1. Migrate `app/config` to `config`

In Symfony 4 [base directory were changed](http://fabien.potencier.org/symfony4-directory-structure.html). We need to use new locations. In one project it's simple and better done manually. But we use monorepo and have  20+ `/packages/X/config` directories.

Let's code: 

```bash
composer require symfony/filesystem --dev
composer require symfony/finder --dev
```

**Create `move_config_to_root.php` file**

```php
<?php

use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Finder\Finder;

require __DIR__ . '/vendor/autoload.php';

$filesystem = new Filesystem();

$finder = (new Finder())->directories()
    ->in(__DIR__ . '/packages')
    ->name('config')
    ->notPath('App/config')
    ->getIterator();

$configDirectories = iterator_to_array($finder);

foreach ($configDirectories as $configDirectory) {
    $oldPath = $configDirectory->getRealPath();
    $newPath = dirname($configDirectory->getRealPath(), 2) . DIRECTORY_SEPARATOR . 'config';

    if (!file_exists($oldPath)) {
        continue;
    }

    $filesystem->rename($oldPath, $newPath);
}
```

**Run `move_config_to_root.php` file**

```bash
php move_config_to_root.php
```

**See result**

```bash
git diff
```

Have you missed a spot? Just reset with:

```bash
git checkout .
```

Improve `move_config_to_root.php` and re-run again:  

```bash
php move_config_to_root.php
```

It took me around 5 iterations to make it right, but the script was ready in 10 minutes. As a bonus, we could re-use it to move `/templates`, `/translations` etc. as well **for just 1 minute of extra work**.

## 2. Update `resource:` Paths in `services.yaml`

But since we moved config files one level up, we also need to **update paths inside the files**. How? 
You can use `str_replace` or (like me) regular expressions. 

```bash
composer require nette/utils --dev
```

**Create `update_resource_in_configs.php`**

```php
<?php

use Nette\Utils\FileSystem;
use Nette\Utils\Strings;
use Symfony\Component\Finder\Finder;

require __DIR__ . '/vendor/autoload.php';

$finder = (new Finder())->files()
    ->in(__DIR__ . '/packages')
    ->name('services.yaml')
    ->getIterator();

$configFiles = iterator_to_array($finder);

foreach ($configFiles as $configFile) {
    $fileContent = FileSystem::read($configFile->getRealPath());

    $movedResource = Strings::replace($fileContent, '#(resource:\s(\')?)\.\.#', '$1../src');

    FileSystem::write($configFile->getRealPath(), $movedResource);
}
```

```diff
 services:
     App\:
-        resource: ..   
+        resource: ../src   
```

Again, it took us 3-4 iterations to cover all edge cases, but then it was ready and bullet-proof.

## Start Small, then Take it to the Next Level

**If you want to get deeper into this thinking and find more inspiration, read the *Pragmatic Programmer* book**. I personally found useful about 60 % of the content (compared to usual ~30 % in technical books), so <em class="fas fa-2x fa-thumbs-up text-success"></em>.

<img src="/assets/images/posts/2019/one-time/pragmatic_programmer.jpg" class="img-thumbnail">

I use this approach in Rector to create new rule + test in 1 file:  

Just edit `create-rector.yaml` (see in [Github repo](https://github.com/rectorphp/rector/blob/master/create-rector.yaml.dist)) and then run:

```bash
bin/rector create
```

It:

- creates a rule with a basic description
- creates a test case 
- creates a test fixture
- adds a rule to a set
- adds a new namespace to `composer.json` PSR-4 if needed
- dumps composer autoload if needed

The sky is the limit, so fly high :)

<br>

Happy coding! 
