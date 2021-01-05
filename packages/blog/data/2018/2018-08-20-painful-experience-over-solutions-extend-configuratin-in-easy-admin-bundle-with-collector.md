---
id: 133
title: "Painful Experience over Solutions: Extend Configuration in Easy Admin Bundle"
perex: |
    *Use SOLID to write Clean Code...* Are you tired of theoretical post about how to do achieve some therm? So am I.
    <br>
    Instead, let's **dive into real problems I came across while coding and let the code speak the theory between lines**.
    <br><br>
    Today we try to add own config option to YAML of Easy Admin Bundle (without pull-request to the package).
tweet: "New Post on my Blog: Painful Experience over Solutions: Extend Configuration in Easy Admin Bundle #symfony #easyadminbundle #phpstorm #collector #php #solid"
tweet_image: "/assets/images/posts/2018/collector-easy-admin-bundle/random.png"
---

<blockquote class="blockquote text-center">
    Hindsight is 20/20.
</blockquote>

Instead of writing about solution how to do and how awesome I am to know the solution right from the start of this page, I start right from the beginning, where I know nothing about it just like you.

<br>

### The Application

I'm coding an open-sourced training platform build Symfony 4.2 and Doctrine 2.7 for [Pehapkari community training](https://github.com/pehapkari). It's fully open-sourced on Github under the typical open-source name - [Open Training](https://github.com/tomasvotruba/open-training).

Admin is just a CRUD to maintain few entities, so I use [EasyAdminBundle](https://github.com/easyCorp/EasyAdminBundle) to handle forms, grids, update, create, delete actions in controllers for me. Huge thanks to [Javier Eguiluz](https://github.com/javiereguiluz) for this amazingly simple and powerful idea.

<img src="https://symfony.com/doc/current/bundles/EasyAdminBundle/_images/easyadmin-default-backend.png">

### The Need

There is `Training` entity with `name` and relation to `TrainingTerm` entity:

```php
<?php declare(strict_types=1);

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 */
class Training
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     * @var int
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     * @var string
     */
    private $name;

    // ...

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\TrainingTerm", mappedBy="training")
     * @var TrainingTerm[]|ArrayCollection
     */
    private $trainingTerms = [];
}
```

I want to edit this entity in administration, so I add it to `config/packages/easy_admin.yaml`:

```yaml
easy_admin:
    entities:
        Training:
            class: 'App\Entity\Training'
```

This creates a grid and form with all the entity properties - `name` and `trainingTerms`. So, when I click *Add* in the admin I can change them both. **But I want to change the `name` only and handle `TrainingTerm` entity in a standalone form.**

### Google First

Now what? I Google *easy admin custom field form* and after while I find [*Customize the Properties Displayed*](https://symfony.com/doc/current/bundles/EasyAdminBundle/book/edit-new-configuration.html#customize-the-properties-displayed) tutorial. It looks like exactly what I need.

```diff
 easy_admin:
     entities:
         Training:
             class: 'App\Entity\Training'
+            fields: ['name']
```

It works! The `trainingTerms` property is hidden in the form.

<br>

### But...

After 2 hours I need to add `price`.

```diff
 <?php declare(strict_types=1);

 namespace App\Entity;

 use Doctrine\Common\Collections\ArrayCollection;
 use Doctrine\ORM\Mapping as ORM;

 /**
  * @ORM\Entity
  */
 class Training
 {
     // ...

+    /**
+     * @ORM\Column(type="integer")
+     * @var int
+     */
+    private $price;
```

Price is there, great! Now we can earn some money. I *edit* training in admin... but, where is the price?

Because I'm coding many other features I don't realize, there is *memory-vendor-lock* - **a code smell, when after doing the A, you always have to remember the B**. Do you see it? When I add a property, I always have a to add it to `fields` in config.

```diff
 easy_admin:
     entities:
         Training:
             class: 'App\Entity\Training'
-            fields: ['name']
+            fields: ['name', 'price']
```

If this is the only case, that would be ok-ish. But now there are 10 entities with 50 properties. How the hell will I remember to do this on every new property I add?

```diff
# ...
-            fields: ['name', 'price']
+            fields: ['name', 'price', 'capacity']
```

And how can will anyone else find this out without me doing the code-review and remembering?

```diff
# ...
-            fields: ['name', 'price', 'capacity']
+            fields: ['name', 'price', 'capacity', 'duration']
```

So much *memory-leaks* it hurts my neurons.

### Creative Time

Life is not perfect and every code is legacy by the time you end the line with `;`.

<blockquote class="blockquote text-center">
    There are no solutions. Just trade-offs.
</blockquote>

I stop and think a bit. How can I write less code to prevent possible bugs and make changes as effective as possible?
**I see there are fewer properties to exclude than properties to include**, by 1:10. It would not be perfect code, but still 10 times safer and more effective code. Worth it!

```diff
 easy_admin:
     entities:
         Training:
             class: 'App\Entity\Training'
-            fields: ['name', 'price', 'capacity', 'duration', 'perex', 'description', 'place', 'trainer']
+            exclude_fields: ['trainingTerms']
```

### Make that Happen and Face False Expectations

But is that `exclude_fields` or `excluded_fields` or maybe `skip_fields`? I want to see the documentation, so I Google [*easy admin bundle excludes fields*](https://www.google.cz/search?q=easy+admin+bundle+exclude+fields&oq=easy+admin+bundle+exclude+fields&aqs=chrome..69i57.4891j0j7&sourceid=chrome&ie=UTF-8). I find [*Exclude fields in list fields* issue in EasyAdminBundle](https://github.com/EasyCorp/EasyAdminBundle/issues/589). I read it and see the content **is not what I need**. It looks like this option is not supported. I'm sad. What now?

<br>

Open-source packages are closed to extension more than you'd expect. To add one custom feature, you have to basically copy and extend the whole class or use reflection. It's not **because it's difficult to create an extendable code, it's because nobody *believes* it can be done in a nice way**. It *can*, just keep reading.

<br>

Being that suspicious I start my *inner over-engineer* voice:

- "Create own extension that will hack into the `EasyAdminExtension` and get the config and add `exclude_fields` option"
- "Create own `BetterEasyAdminBundle` that will be run before the `EasyAdminBundle` and will pass parameters there"

**This might end-up wasting many hours** on custom and useless solution (like "create own Doctrine" idea, true story). Instead I try to invest a bit more time and I continue the brainstorming:

- "Send pull-request with this feature to the core code"

Slightly better, but what if Javier doesn't like it? Or what if he's on holiday for 3 weeks? I know, it's summer and very rare to happen, but I have to finish the app in 2 weeks and I don't want to think about bugs like these in the meantime. The **least I can do is to create [an issue](https://github.com/EasyCorp/EasyAdminBundle/issues/2325)** with this idea and my reasons for it.

## Wander in the Code

I need a solution and I need it today. What can I do? No hacking, no pull-request, just looking for something in files:

<img src="/assets/images/posts/2018/collector-easy-admin-bundle/random.png" class="img-thumbnail">

Do you think this is just a random screen-shot not worth your attention?

- there is a checkbox in *Match case* because `'fields'` is lowercased and we want to focus on that only (no properties or methods with `Fields`)
- there is a limit to `*.php` because that *would be probably* place to extend
- there is a limit to *Directory*: `/../vendor/easycorp`, because we want to hack into this package
- there is `fields` word in search; later I improve it to `'fields'` to narrow results, because we know it's a string

<br>

**I still have no idea about the solution I'll pick. I'm only randomly looking for the light, blindfolded in a dark foggy forest.**
This is called *creative chaos* in coaching circles and it's the most important part of the client's work.

<br>

I scroll down a bit looking at both code and the file name. Suddenly, the fog starts slowly disappearing...

<img src="/assets/images/posts/2018/collector-easy-admin-bundle/pass.png" class="img-thumbnail">

I notice `*ConfigPass` suffix. Is that like `CompilerPassInterface`, a collector-pattern used in Symfony to modify services in the container?

Being curious I open `NormalizerConfigPass.php` file:

```php
<?php

// ...

namespace EasyCorp\Bundle\EasyAdminBundle\Configuration;

use Symfony\Component\DependencyInjection\ContainerInterface;

class NormalizerConfigPass implements ConfigPassInterface
{
    // ...
}
```

An interface! That's a good sign.

### Keep Wandering

So I look for `ConfigPassInterface` in somewhere else than just `implements ConfigPassInterface`.

That doesn't work, so I try to look for `ConfigPass`.

That doesn't work, so I try to look for any file, not just `*.php`. That show as valuable, since services are defined in YAML or XML.

<img src="/assets/images/posts/2018/collector-easy-admin-bundle/services.png" class="img-thumbnail">

I see a tag: `easyadmin.config_pass`. Let's look for that string:

<img src="/assets/images/posts/2018/collector-easy-admin-bundle/bingo.png" class="img-thumbnail">

Warmer! **I've just [found a collector](/cluster/collector-pattern-the-shortcut-hack-to-solid-code/).** To config, I look for service under `easyadmin.config.manager` name - [`ConfigManager`](https://github.com/EasyCorp/EasyAdminBundle/blob/07194017918aebe382e1ab0e53c68f6242547a0e/src/Configuration/ConfigManager.php#L112-L119) and look for `foreach` on collected services:

```php
private function doProcessConfig($backendConfig): array
    {
    foreach ($this->configPasses as $configPass) {
        $backendConfig = $configPass->process($backendConfig);
    }

    return $backendConfig;
}
```

Bingo! **That means, when I register a service with `easyadmin.config_pass` tag, I'll be able to read and modify the YAML configuration**.

So I register a service:


```yaml
services:
    ExcludeFieldsConfigPass:
        tags:
             -
                 name: "easyadmin.config_pass"
                 priority: 120 # it took me more time to figure out if -100 or 0 or 100 or 1000 means "the first"
```

That does 1 thing:

`fields` (value to be set) = entity properties − `exclude_fields` (value I set in the config)

<br>

It allows me to do simplify `config/packages/easy_admin.yaml` config:

```diff
 easy_admin:
     entities:
         Training:
             class: 'App\Entity\Training'
-            fields: ['name', 'price', 'capacity', 'duration', 'perex', 'description', 'place', 'trainer']
+            exclude_fields: ['trainingTerms']
```

You can see full code of [`ExcludeFieldsConfigPass` on Github](https://github.com/TomasVotruba/open-training/pull/7/files#diff-318660bf4cd1ad8a5d0e608e94df8fae).

Very smart move Javier - thank you!

## Learn 1 Algorithm instead of 10 Solutions

And that's all folks. I hope I've shown you how to approach problems and how to find a way in situations you're the first time in.
The same way I don't memorize Wikipedia and just Google it instead, **I don't remember solutions to 100 PHP problems, but have a couple of algorithms to approach problem solving**.

- Try A - Failed?
- Try B - Failed?
- Try C - Failed?
- Take a break to prevent [learned helplessness](https://en.wikipedia.org/wiki/Learned_helplessness) :)
- Try D - Failed?
- Try E - Failed?
- Try F - Kaboom! It works!

<br>
If artificial intelligence could figure this all out for us, we'd be screwed :).

Btw, are you coming to [Human Level AI Conference](https://www.hlai-conf.org) in Prague this weekend? I'll be there and I'd be happy if you stop me and say Hi!

<br>

Happy solving!
