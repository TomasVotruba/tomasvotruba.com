---
layout: post
title: "Minimalistic Way to Create Your First Nette Extension"
perex: '''
    Nette extension allows you not only to create open-source packages, but also to <strong>split your application to small and logical chunks of code</strong>.
    <br><br>
Open-source extensions are more complex using many Nette\DI features, but today I will show you, how to <strong>start with one Nette\DI method and one service only</strong>.
'''
lang: en
reviwed_by: [5, 8]
---

First, I will tell you a little trick, **how to make learning faster and easier**. Don't worry, after this one headline we get to the code.

## Set The Smallest Step Possible

When I want to learn new skill, I try to realize: what is **the smallest step possible**? Also called *lean method*, or in software [Lean Software Development](https://en.wikipedia.org/wiki/Lean_software_development) (LSD, right?).

### I Learned Extensions the Hard Way

1. **By Reading Nette documentation** [that describes over 20 features and uses cases it has](https://doc.nette.org/en/last/di-extensions). That is information, which is useful, because I can use the tool to it's potential, but not the best adopt the skill.

2. **By Reading extension of open-source packages**. They are similar to documentation: many features on various use cases I didn't understand yet.

That lead to **overstretching my brain muscle**. It's like trying to jump over huge hole before even walking.


### It made me think: "Could it be simpler?"

What is essential purpose of the extension? It registers services to Nette Dependency Injection Container.

- Register services to Container?
- Register services?
- **Register 1 service** - that's the one and only step we'll make today.

So next time you'll think "gosh, this is so hard, I don't understand it, I'm so slow/lazy/...", stop for a moment and carefully look at the problem. There might be an easier way.

**And now to the code!**


## Register service in Nette Sandbox

I consider [Nette Sandbox](https://github.com/nette/sandbox) the best way to show learn any Nette feature. Let's use it.

If you want to register a service, what will you do?

```yaml
# app/config/config.neon

services:
    - App\Model\UserManager # put it there
```

File `app/config/config.neon` is like a socket.

<img src="/assets/images/posts/2017/nette-extension/single-socket.jpg" class="thumbnail">

There is **one place to active your computer**, when you plug it in.

But what if your want to **plug in computer and mobile charger** in the same time?

<img src="/assets/images/posts/2017/nette-extension/multi-socket.jpg" class="thumbnail">

To load more services, we use the same interface as `app/config/config.neon`: **a file with service section that lists services**.


## Create Local Extension in 5 Steps

Let's say we want to decouple a FileSystem utilities.

### 1. Pick a Name for Directory

What about "FileSystem"? If you agree, create `src/FileSystem` directory.
We will put configuration (sevices.neon) and classes there.


### 2. Create or move Related Classes there

Starting small, one service will do. When it grows, we can decouple it later.

```php
# src/FileSystem/FileSystem.php

<?php declare(strict_types=1);

namespace FileSystem;

final class FileSystem
{
    // some awesome methods!
}

```

### 3. Create config

This is similar to `app/config/services.neon`, just in different location:

```yaml
# src/FileSystem/config/services.neon

- FileSystem\FileSystem
```

### 4. Create an Extension

```php
# src/FileSystem/DI/FileSystemExtension.php

<?php declare(strict_types=1);

namespace FileSystem\DI;

use Nette\DI\Compiler;
use Nette\DI\CompilerExtension;

final class FileSystemExtension extends CompilerExtension
{
    public function loadConfiguration()
    {
        // this method loads servcies from config and registers them do Nette\DI Container
        Compiler::loadDefinitions(
            $this->getContainerBuilder(),
            $this->loadFromFile(__DIR__.'/../config/services.neon')
        );
    }
}
```

### 5. Register it in Application

```yaml
# app/config/config.neon

extensions:
    - FileSystem\DI\FileSystemExtension
```

That's it!


## Try it Out

Now try using `FileSystem\FileSystem` in HomepagePresenter:

```php
# app/presenters/HomepagePresenter.php

<?php declare(strict_types=1);

namespace App\Presenters;

use FileSystem\FileSystem;

class HomepagePresenter extends BasePresenter
{
    public function __construct(FileSystem $fileSystem)
    {
        $this->fileSystem = $fileSystem;
    }
}
```

and running application:

<img src="/assets/images/posts/2017/nette-extension/bug.png" class="thumbnail">

**Fails**? Damn, I can't put this on my blog.

Oh, **we need to tell composer about these classes**. He doesn't know, where to find it.
```javascript
{
    "require-dev": {
        "..."
    },
    "autoload": {
        "psr-4": {
            "FileSystem\\": "src/FileSystem"
        }
    }
}
```

And manually rebuild `autoload.php` (composer does by default only after `composer update`):

```bash
composer dump-autoload
```

Refresh and...

<img src="/assets/images/posts/2017/nette-extension/good.png" class="thumbnail">

...it works!

Phew! That would have been embarrassing.


### To Sum Up

**Now you know how to do your first extension**.

- create a directory in `/src`
- add `/src/<package-name>/services.neon`
- add `/src/DI/<package-name>Extension.neon`
- register extension to `app/config/config.neon`
- and extend `autoload` section in `composer.json` (prevents from putting failing code to public blog :))

**Let me know if you get stuck somewhere**. I want this tutorial to be as easy to understand as possible.