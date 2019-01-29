---
id: 181
title: "2 Files that Your Symfony Application Misses"
perex: |
    Following files are supported by PHPStorm and Symfony plugin for years (since 2016) and they make working with a code so elegant. **Yet, I came across them just recently.**
    <br><br>
    They immediately became must-have of each repository with Symfony code.
    
tweet: "New Post on #php 🐘 blog: 2 Files that Your #Symfony Application Misses     #phpstorm #ide #twig"
tweet_image: "/assets/images/posts/2019/meta/ide-twig-json.gif"
---

## Do You Know the Frustration of Clicking to Existing `*.twig` File?

Add directory with all your twig files to `ide-twig.json` in the project root:

<img src="/assets/images/posts/2019/meta/ide-twig-json.gif" class="img-thumbnail">

- Refresh!
- Enjoy :)

<br>

You'll appreciate this feature in a project **with [multiple packages](/blog/2018/11/19/when-you-should-use-monorepo-and-when-local-packages/#3-local-packages)**. It's real life-saver:

```json
{
    "namespaces": [
        { "path": "templates" },
        { "path": "packages/Provision/templates" },
        { "path": "packages/Registration/templates" },
        { "path": "packages/Training/templates" },
        { "path": "packages/KnowHow/templates" },
        { "path": "packages/Marketing/templates" },
        { "path": "packages/User/templates" }
    ]
}
```

*Note: You need to install [Symfony Plugin](https://plugins.jetbrains.com/plugin/7219-symfony-plugin) first. Then enable it in each project (yes, they're 2 different steps).*

You can use [more magic](https://www.slideshare.net/Haehnchen/symfonycon-berlin-2016-symfony-plugin-for-phpstorm-3-years-later-69804748#45) like namespaces, but they're nothing better than explicit paths.  

## Why PHPStorm doesn't "get" It? 

So simple it hurts:

```php
<?php

$service = $this->container->get(Type::class);
$service; // PHPStorm: type of "object"
$service; // you need: object of "Type"
```

To solve this, you need to **spam your code with annotations**:

```diff
 $service = $this->container->get(Type::class);
+/** @var Type $service */
 $service;
```

Is there a better way?

<img src="/assets/images/posts/2019/meta/phpstorm_meta.gif" class="img-thumbnail">

The `.phpstorm.meta.php` configuration seems a bit magic at first, but you'll understand it:

```php
<?php

namespace PHPSTORM_META;

// $container->get(Type::class) → instance of "Type"
override(\Psr\Container\ContainerInterface::get(0), type(0));
```

And your container calls are now type-aware:

```php
<?php

$service = $this->container->get(Type::class);
$service; // PHPStorm: object of "Type"
```

Pretty cool, right?


<br>

You can use this [for much more](https://confluence.jetbrains.com/display/PhpStorm/PhpStorm+Advanced+Metadata), like **Doctrine repository autocomplete by entity class**. 

I'm using this for container and thanks to that there is [49 fewer annotations](https://github.com/Symplify/Symplify/commit/d53003ebc41dddcb228e517c98d59de70ebc17a0) in Symplify code.

<br>

Do you **use another metafile** for PHPStorm? Let me know in the comments.


Happy coding!
