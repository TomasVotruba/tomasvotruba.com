---
id: 243
title: "Statie is Dead, Long live Symfony Static Dumper"
perex: |
    Last week [I wrote about how Statie turned from a feature to a burden](/blog/2020/03/09/art-of-letting-go/) and why we had to let it go.
    <br>
    **What will replace it? How do you migrate?**

tweet: "New Post on #php 🐘 blog: Statie is Dead, Long live #Symfony Static Dumper"
tweet_image: "/assets/images/posts/2020/dump_static_site_demo.gif"
---

Deprecations without replacements are like nails in the coffin of your forehead while reciting a poem about the beauty of life.

The [friendsofphp.org](github.com/tomasvotruba/friendsofphp.org) and this blog **still uses PHP and is deployed to GitHub pages**. How is that possible without Statie?

From Statie, we **fluently switch to brand new package - [Symfony Static Dumper](https://github.com/symplify/symfony-static-dumper)**. A single command that **generates HTML and CSS from your Symfony application.**

<br>

In short, it looks like this:

<img src="/assets/images/posts/2020/dump_static_site_demo.gif" class="img-thumbnail">

<br>

## Killer Features?

What I love the most about it, that the Symfony Static Dumper **only handles the missing part - generate HTML and CSS**. All the rest is up to you, your imagination. You code standard Symfony application, nothing special, nothing weird.

Do you want more advantages?

- no more Javascript to run the website <em class="fas fa-fw fa-lg fa-check text-success"></em>
- we can use most of the Symfony Ecosystem <em class="fas fa-fw fa-lg fa-check text-success"></em>
- we can use database, even Doctrine <em class="fas fa-fw fa-lg fa-check text-success"></em>
- develop normal Symfony application, then dump the HTML + CSS on deploy <em class="fas fa-fw fa-lg fa-check text-success"></em>
- standard Symfony app structure <em class="fas fa-fw fa-lg fa-check text-success"></em>
- use `{{ path('contact') }}` in templates <em class="fas fa-fw fa-lg fa-check text-success"></em>
- use Symfony plugin <em class="fas fa-fw fa-lg fa-check text-success"></em>
- single command use <em class="fas fa-fw fa-lg fa-check text-success"></em>


## How to Migrate from Statie to Symfony Static Site Dumper?

Do you still use Statie? Don't worry; we have a migration path for you.

### Add Symfony Packages

First, you need to install Symfony packages:

```bash
composer require symfony/http-foundation symfony/asset symfony/twig-bridge symfony/twig-bundle symfony/flex symplify/flex-loader symplify/autodiscovery symfony/framework-bundle symfony/dotenv doctrine/cache erusev/parsedown-extra
```

And replace the static generator:

```bash
composer require symplify/symfony-static-dumper
composer remove symplify/statie
```

### Setup Basic Symfony App

- add [`config/bootstrap.php`](https://github.com/symfony/demo/blob/master/config/bootstrap.php)
- add [`bin/console`](https://github.com/symfony/demo/blob/master/bin/console)
- add [`src/HttpKernel/YourAppKernel.php`](https://github.com/TomasVotruba/friendsofphp.org/blob/master/src/HttpKernel/FopKernel.php)


### Move `/source`

- Templates to `/templates`
- Public content, e.g. `robots.txt` or `CNAME` to `/public`

### Update CI

```diff
-vendor/bin/statie generate source
+bin/console dump-static-site
```

### Pages to Controller Templates

**Before:**

```twig
---
layout: "_layouts/default.twig"
title: "Thank You"
id: thank_you
commit_limit: 5
---

<div class="container" id="contact">
    <h1>{{ title }}</h1>

    <p class="text-center bigger"></p>
</div>
```


**After:**

```twig
{# templates/thanky_you.twig #}
{% extends "default.twig" %}

{% block content %}
    <div class="container" id="contact">
        <h1>{{ title }}</h1>

        <p class="text-center bigger"></p>
    </div>
{% endblock %}
```

```php
// src/Controller/ThankYouController.php
namespace YourWebsite\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

final class ThankYouController extends AbstractController
{
    /**
     * @Route(path="thank-you", name="thank_you")
     */
    public function __invoke(): Response
    {
        return $this->render('thank_you.twig', [
            'title' => 'Thank You',
            'id' => 'thank_you',
            'commit_limit' => 5,
        ]);
    }
}
```

If you need more inspiration, look at these pull-requests:

- [migration of TomasVotruba.com](https://github.com/TomasVotruba/tomasvotruba.com/pull/940)
- [migration of FriendsOfPhp.org](https://github.com/TomasVotruba/friendsofphp.org/pull/162) - [part #2](https://github.com/TomasVotruba/friendsofphp.org/pull/169)

## How to Set it Up?

All the essential information is in [README](https://github.com/symplify/symfony-static-dumper).

Install the package via composer:

```bash
composer require symplify/symfony-static-dumper
```

Register services - no Flex, no bundles, just simple config:

```yaml
# config/services.yaml
imports:
    - { resource: '../vendor/symplify/symfony-static-dumper/config/config.yaml' }
```

And dump static website to `/output` directory:

```bash
bin/console dump-static-website
```

To see the website, just run the local server:

```bash
php -S localhost:8001 -t output
```

That's it!

<br>

Happy coding!
