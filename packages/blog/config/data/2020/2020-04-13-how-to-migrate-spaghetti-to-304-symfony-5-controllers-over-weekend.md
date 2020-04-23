---
id: 247
title: "How to Migrate Spaghetti to 304&nbsp;Symfony 5 Controllers Over Weekend"
perex: |
    During Easter weekend, usually, people take a break and have a rest. Instead, we used these 4 days of *holiday* to migrate the 304-controller application. At least that was the goal on Friday.
    <br><br>
    Me in my colleague in the migrated project accepted the challenge. We got into many minds and code-traps. We'd like to share this experience with you and **inspire those who are still stuck on non-MVC code** and think it might take weeks or even months to switch to a framework.

tweet: "New Post on #php 🐘 blog: How to Migrate Spaghetti to 304 #symfony 5 Controllers Over Weekend"
tweet_image: "/assets/images/posts/2020/symfonize_merged.png"
---


## What is the Goal?

<em class="fas fa-fw mt-4 fa-times text-danger"></em> **We didn't want a  hybrid** with static dependency injection container, legacy controller, request separation for new website and for old website. It only creates more legacy code than in the beginning.

<em class="fas fa-fw mt-4 fa-star-half text-warning"></em> **We were ok with** keeping original business logic code untouched. We will handle spaghetti decoupling to Controller and Twig in the next phase. This was just a 1st step of many.

<em class="fas fa-fw mt-4 fa-check text-success"></em> **We wanted** to be able to use Symfony dependency injection, Twig templates, Controller rendering, Symfony Security, Events, Repository, connection to database, `.env`, Flex, Bundles, YAML configs, [local packages](/blog/2020/02/17/local-packages-3-years-later/).

<em class="fas fa-fw mt-4 fa-check text-success"></em> **We wanted automate** everything that is possible to automate.

<em class="fas fa-fw mt-4 fa-check text-success"></em> **We wanted to run** on Symfony 5.0 and PHP 7.4.

<em class="fas fa-fw mt-4 fa-check text-success"></em> **We wanted to write** any future code as if in any other Symfony application without going back.

<br>

Well, we wanted a full-stack framework, as you can find in [symfony/demo](https://github.com/symfony/demo).

Isn't that too much for one weekend? 😂

<blockquote class="blockquote text-center">
"Only those who attempt the absurd can achieve the impossible."

<footer class="blockquote-footer">Albert Einstein</footer>
</blockquote>

Honestly, I'm just **[freaking lazy](https://blog.codinghorror.com/how-to-be-lazy-dumb-and-successful/)** to do work for a longer time than a few days (in a row).

## The Application in The Start

So how does the application look like?

[Symfony documentation describes](https://symfony.com/doc/current/controller.html) **a controller** as *a PHP function you create that reads information from the Request object and creates and returns a Response object*. In our case, the "Request object" was an entry URL, "Response object" was spaghetti rendered as `echo "string";`.

Saying that the application had:

- 304 "controllers"
- templating done directly in the file with inlined PHP in HTML
- base *layout* done with `include "header.php";`
- 2 good old HTML frames, one for the menu - the other for the rest of application

<br>

Typical *controller* looked like this:

```php
<?php
// contact.php

include 'header.php';

$content = get_data_from_database();

// 500 lines of spaghetti code

echo $content;
```

## First: Make a Plan

The migration pull-request itself is just **half of the work**. First, we had to have [coding standards, PSR-4 autoloading, PHPStan on level 8](/blog/2019/12/16/8-steps-you-can-make-before-huge-upgrade-to-make-it-faster-cheaper-and-more-stable/) etc. When I say PHPStan on level 8, we skipped those errors with 50+ cases.

The next half is [to have a full team on board](https://pehapkari.cz/blog/2019/04/20/how-we-migrated-54-357-lines-of-code-nette-to-symfony-in-2-people-under-80-hours) and have **a clear plan**.

## PHP Template in Symfony 5?

We had a goal, so what's the plan? First, we wanted to switch PHP + HTML to controllers. Maybe we could use something like [PHP templates](https://symfony.com/doc/4.4/templating/PHP.html) + render them with a controller?

The idea is great, except **PHP templates were deprecated** in Symfony 4 and **removed** in Symfony 5:

<img src="/assets/images/posts/2020/symfonize_php_template_nope.png" class="img-thumbnail mt-3 mb-3">

## Raw Symfony Application

Hm, so what now? If it too huge, take something smaller. First, we need to actually have a Symfony project:

- remove `vendor`
- remove `composer.lock`

- install Symfony dependencies:

```bash
composer require symfony/asset symfony/cache symfony/console symfony/dotenv \
    symfony/flex symfony/framework-bundle symfony/http-foundation symfony/http-kernel \
    symfony/twig-bridge symfony/twig-bundle symplify/auto-bind-parameter \
    symplify/autodiscovery symplify/autowire-array-parameter symplify/flex-loader \
    symplify/package-builder twig/twig doctrine/cache symfony/security-core \
    symfony/security-bundle symfony/security-csrf doctrine/orm doctrine/doctrine-bundle \
    doctrine/annotations doctrine/common doctrine/dbal symfony/error-handler symfony/form
```

- don't forget `dev` too:

```bash
composer require --dev symfony/maker-bundle symfony/web-profiler-bundle
```

Few fixes of bundles installation that Flex missed, adding database credential to `.env.local` file to login into the database, and we're ready to continue **with an uplifted spirit of success**.

<img src="/assets/images/posts/2020/symfonize_raw_run.png" class="img-thumbnail mt-3 mb-3">

Soon to be **demolished again by new problems** we never faced before... Let's look at the controllers.

<br>

We wanted to use [Rector](https://github.com/rectorphp/rector) to convert all the files to classic Symfony controllers.

The simple rule is: `<filename>.php`

- route name: `filename`
- route path: `filename`

## What Will Be Inside Controller?

Just simply copy-paste the spaghetti code first, maybe that will be enough:

```php
<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Response;

final class ContactController extends AbstractController
{
    /**
     * @Route(path="contact", name="contact")
     */
    public function __invoke(): Response
    {
        $content = get_data_from_database();
        // 500 lines of spaghetti code

        // this won't work, we need to return Response object :/
        echo $content;
    }
}
```

If the content would be echoed **just once**, we could use:

```php
$content = get_data_from_database();
// 500 lines of spaghetti code

return new \Symfony\Component\HttpFoundation\Response($content);
```

But there is `echo` all over the place - like **50 times in those 500 lines of spaghetti code**.

<br>

Then we remembered, there are `ob_*` functions **that collect echoed content, but don't show it**. If we wrap the spaghetti and get content with `ob_get_contents()` in the end, it might work.

```php
ob_start();

// 500 lines of spaghetti code

$content = (string) ob_get_contents();
ob_end_clean();
return new \Symfony\Component\HttpFoundation\Response($content);
```

<br>

4 hours of writing a Rector rule for the migration and voilá - we had **304 new Symfony controllers**:

```php
<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Response;

final class ContactController extends AbstractController
{
    /**
     * @Route(path="contact", name="contact")
     */
    public function __invoke(): Response
    {
        ob_start();

        $content = get_data_from_database();
        // 500 lines of spaghetti code

        $content = (string) ob_get_contents();
        ob_end_clean();
        return new Response($content);
    }
}
```

That wasn't that hard. Let's run the website to enjoy the fruits of Eden:

<img src="/assets/images/posts/2020/symfonize_first_crash.png" class="img-thumbnail mt-3 mb-3">

Hm, maybe we should **update all the links from `contact.php` to `contact` routes** in every PHP file too. Also, all 304 links to all controller we just converted.

## How to get Base Template into Templates?

Now when you entered `https://localhost:8000/contact`, you saw the raw page. From cool Symfony controller, but still a raw page. **We wanted to use Twig templates**, so we could enjoy filters, helpers, global variables, assets, etc.

<br>

This was our goal:

```php
<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Response;

final class ContactController extends AbstractController
{
    /**
     * @Route(path="contact", name="contact")
     */
    public function __invoke(): Response
    {
        return $this->render('controller/contact.twig');
    }
}
```

In the end, that `__invoke` method is actually in every controller **in this exact format**. But we still **miss one piece of the puzzle**.

We also wanted to use normal `base.twig`, as we're used to in every MVC project:

```twig
<!DOCTYPE html>
<html>
    <head>
      {# some assets #}
   </head>
   <body>
      <div class="row">
         <div class="col-4">
            {% include "_snippet/menu.twig" %}
         </div>
         <div class="col-8">
              {% block main %}
              {% endblock %}
           </div>
       </div>
    </body>
</html>
```

What's inside the `controller/contact.twig`?

```twig
{% extends "base.twig" %}

{% block main %}
    PHP? Spaghetti? Magic?
{% endblock %}
```

How would **you solve it**? If you find a better way, let us know in the comments.

**Remember**: no PHP in Twig templates and no going back to Symfony 4.

<br>
<br>
<br>

We came up with this trick:

```twig
{% extends "base.twig" %}

{% block main %}
    {{ render(controller('App\\Controller\\ContractController::content')) }}
{% endblock %}
```

In each controller there will be not only the `__invoke()` method, but also the `content` method:

```php
<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Response;

final class ContactController extends AbstractController
{
    /**
     * @Route(path="contact", name="contact")
     */
    public function __invoke(): Response
    {
        return $this->render('controller/contact.twig');
    }

    /**
     * @Route(path="contact_content", name="contact_content")
     */
    public function content(): Response
    {
        ob_start();

        $content = get_data_from_database();
        // 500 lines of spaghetti code

        $content = (string) ob_get_contents();
        ob_end_clean();
        return new Response($content);
    }
}
```

With this approach, we have all we wanted:

<em class="fas fa-fw mt-4 fa-check text-success"></em> **We can** use Symfony dependency injection, Twig templates, Controller rendering, Symfony Security, Events, Repository, connection to database, `.env`, Flex, Bundles, YAML configs, [local packages](/blog/2020/02/17/local-packages-3-years-later/).

<em class="fas fa-fw mt-4 fa-check text-success"></em> **We can to write** any future code as if in any other Symfony application without going back.

<br>

To add chery on top, we added Symfony login:

<img src="/assets/images/posts/2020/symfonize_logged_in.png" class="img-thumbnail mt-3 mb-3">

And that's it!

### Blind Paths to Avoid

- don't go to Symfony 5.1-dev, stay with **stable Symfony 5.0** - the install of dev packages is very slow both on CI and locally
- don't go to Symfony 4 just to use PHP templates; it's just postponing the problem + adding more legacy code
- don't rush to PHP 7.4 before migration is finished, for the migration start **PHP 7.2** is good enough (ECS and Rector need it as minimal version)

### Caveats

- do [8 Steps You Can Make Before Huge Upgrade to Make it Faster, Cheaper and More Stable](/blog/2019/12/16/8-steps-you-can-make-before-huge-upgrade-to-make-it-faster-cheaper-and-more-stable/) first
- **have a strict deadline** - our original plan was the end of May, but when Easter came, we couldn't resist
- **don't fall in the bait of manual refactoring** of that one thing in the code you don't like - **stay focused on the migration** and improve code when the 1st step is finished and merged
- **have a buddy**, that helps you psychically when you're stuck - without a friend, this might turn into a nightmare of being stuck in circles; even only talking about the problem helps - it's better if it's somebody outside your normal work (consultant, old collegaue), so they're not stuck in your legacy project mindset
- **be ok with broken code** for a while, it's normal even with small changes
- don't deploy code to the production, merge the PR as soon as possible, but test the application on a separate server at least for a week

## The Final Plan

- install Symfony dependencies
- add `bin/console`, `src/AppKernel.php` and `public/index.php` files
- run base Symfony homepage
- add credentials to `.env.local` and login to database
- prepare migration rule to Symfony controllers and let Rector
    - migrate controllers
    - create `*` templates
    - create `*_content.twig` templates
- let Rector dump changed files into `old_controller_files.json`
- load data from `old_controller_files.json` and
    - use `preg_replace` in all files to replace `contact.php` to `contact`, files names to routes
    - delete old files
- delete `old_controller_files.json`

<br>

We made many mistakes, took many blind paths, so you don't have to (you can take new blind paths), but in the end, we made it from Friday till Monday - **in 4 days**:

<img src="/assets/images/posts/2020/symfonize_merged.png" class="img-thumbnail mt-3 mb-3">

**Are you still on a legacy project?** What's your excuse that prevents your change for better?

If you have more questions, e.g., technical ones about the automated parts, let us know in the comments. We'll try to answer as best as we can.

<br>

Happy coding!


