---
id: 39
title: "Symplify packages deprecations brought by Symfony 3.3"
perex: |
    <a href="/blog/2017/05/07/how-to-refactor-to-new-dependency-injection-features-in-symfony-3-3/">Symfony 3.3 brings new Dependency Injection features</a>,
    that were supplemented by few of Symplify packages in Symfony 3.2 and below.
    There is no need - as <a href="https://github.com/Lexinek">Honza Mikes</a> said - to <em>bring the wood to the forest</em>. So they were deprecated.
    <br><br>
    I will show <strong>why and what packages were deprecated and how to upgrade your app
    to use Symfony 3.3 instead</strong>.
related_items: [40]
tweet: "What was changed and dropped from #symplify thanks to #symfony 3.3?"
---

I will provide you some insights behind deprecations first. It was not an instant decision based on few Symfony blog posts,
but a long process of maturing inspired by community, feedback and intuition.

If you only want to see before/after changes in you application, you can [skip right to the code](#a-href-https-github-com-deprecatedpackages-defaultautowire-defaultautowire-a).

<div class="text-center">
    <img src="/assets/images/posts/2017/symplify-deprecations/pr-notes.png" style="max-width:100%" class="img-thumbnail">
</div>


## Bringing The Wood to the Open Source Forest

One of motivations to create and build open-source project is <strong>competition</strong>. You want to create better software
than there already is, to push the society further in the development. If my project is the best, there is much less motivation to develop it.

That's why it's the best to have **2 elements of similar level so they push each other forward**, like:
Google & Facebook, USA & Russia, Symfony & Laravel or PHP & Javascript (in a way).

## Swallowing My Open-Source Ego

When [Symfony 3.3 introduced new Dependency Injection features](/blog/2017/05/07/how-to-refactor-to-new-dependency-injection-features-in-symfony-3-3/),
I realized they have 90% similar to feature I created my packages for.

Internal dialog of my Ego and inner Zen master started:

- "It's still not the same, I should keep them and promote them."
- "But I can do much greater good than adding 10% value. I could focus on completely new packages that bring 100 % value."
- "But I have created them and I have been taking care of them for a long time. People should use them."
- "It's ok, they were good in the past, but things have changed."
- "Ok, maybe I could ask community, what they think about it."


**So I [asked people on Slack and Github](https://github.com/Symplify/Symplify/pull/162)** what they think about it. Almost everybody (my inner Zen master included)
agreed **to drop them and let Symfony handle that**.

I must say, it was much easier to decide after getting such feedback. **Thank you [Filip Prochazka](https://filip-prochazka.com/),
[Tomas Fejfar](https://www.tomasfejfar.cz/),  [Stof](https://github.com/Symplify/Symplify/issues/161),
[theofidry](https://github.com/symfony/symfony/pull/22234#issuecomment-297999703),
[Jachym Tousek](https://github.com/enumag), [Jan Mikes](https://github.com/lexinek)
and [Javier Eguilez](https://github.com/Symplify/Symplify/pull/162#issuecomment-299441503)**
for your help with this.


## Letting Package Go

I realized I could let packages go and I made these PRs:

- [drop DefaultAutowire](https://github.com/Symplify/Symplify/pull/162#issuecomment-299441503)
- [drop ServiceDefinitionDecorator, AutoServiceRegistration, ControllerAutowire and SymfonyEventDispatcher](https://github.com/Symplify/Symplify/pull/155)

That eventually lead to:

- [dropping EventDispatcher integration to Nette](https://github.com/Symplify/Symplify/pull/170)

Don't worry:

- **all have better replacement** (I will show you below)
- **you can still use them** (they just won't get any updates)


Let's look on each package now:

## [DefaultAutowire](https://github.com/DeprecatedPackages/DefaultAutowire)

This package turned on autowiring by default for most of services.

### Before

All services were autowired.

### After

[Use `_defaults` section](/blog/2017/05/07/how-to-refactor-to-new-dependency-injection-features-in-symfony-3-3/#1-let-s-add-code-defaults-code):

```yaml
# app/config/services.yml
services:
    _defaults:
        autowire: true
```

All services in this config will be autowired now. You can use it in any `.yml` file.


## [ServiceDefinitionDecorator](https://github.com/DeprecatedPackages/ServiceDefinitionDecorator)

This package helped with service tagging and method calls by type.


### Before

```yaml
# app/config/config.yml
decorator:
    Symfony\Component\Console\Command\Command:
        tags:
            - { name: "console.command" }

    AbstractEntityRepository:
        calls:
            - ["setEntityManager", ["@event_manager"]]
```

### After

Use [`autoconfigure`](/blog/2017/05/07/how-to-refactor-to-new-dependency-injection-features-in-symfony-3-3/#2-use-autoconfigure)
and [`_instanceof`](/blog/2017/05/07/how-to-refactor-to-new-dependency-injection-features-in-symfony-3-3/#5-use-code-instanceof-code)
features.

```yaml
# app/config/services.yml
services:
    autoconfigure: true

    Symfony\Component\Console\Command\Command: ~

    _instanceof:
        AbstractEntityRepository:
            calls:
                - ["setEntityManager", ["@event_manager"]]
```

### Minitip

You can also use `@required` annotation instead of `_instanceof` in this case, as **[shared by Kevin Bond](/blog/2017/05/07/how-to-refactor-to-new-dependency-injection-features-in-symfony-3-3/#comment-3306767439)** - thank you Kevin!



## [AutoServiceRegistration](https://github.com/DeprecatedPackages/AutoServiceRegistration)

This package helped you to register multiple services at once with Finder.

### Before

```yaml
# app/config/config.yml
symplify_auto_service_registration:
    directories_to_scan:
        - %kernel.root_dir%
    class_suffixes_to_seek:
        - Controller
```

### After

Use [PSR-4 based service autodiscovery and registration](/blog/2017/05/07/how-to-refactor-to-new-dependency-injection-features-in-symfony-3-3/#4-use-psr-4-based-service-autodiscovery-and-registration).

```yaml
# app/config/services.yml

services:
    App\: # no more manual registration of similar groups of services
        resource: ../{Controller,Command,Subscriber}
```

Tada!

And just like that, you can drop 4 of Symplify bundles and use Symfony 3.3 happily and safely ever after.

---

That's all for today. In next post I will show you **how to deprecate package without letting anyone behind**.
Not even your blog readers that could come across and old post about it.
