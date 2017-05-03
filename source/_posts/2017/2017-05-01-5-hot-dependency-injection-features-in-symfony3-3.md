---
layout: post
title: "5 Hot Dependency Injection features in Symfony 3.3"
perex: '''
    This May will be released last non-LTS version of Symfony 3.x series.
    It has few nice DependencyInjection improvements. <strong>But combined together - they are just awesome.</strong>
    <br><br>
    I can tell that, because I've deprecated 4 of my Symplify packages that provide similar features.
'''
lang: en
---

sources and reference:

- https://github.com/TomasVotruba/tomasvotruba.cz/pull/88
- https://github.com/Symplify/Symplify/pull/155


If you follwo SMyofn blog,s you probalby knwo about most of them:
what is new?

- [autowconfiure](http://symfony.com/blog/new-in-symfony-3-3-service-autoconfiguration)
- defaults, instanceof and simpler service registraion - http://symfony.com/blog/new-in-symfony-3-3-simpler-service-configuration
- PSR-11 container: http://symfony.com/blog/new-in-symfony-3-3-psr-11-containers
- dropping named service - http://symfony.com/blog/new-in-symfony-3-3-optional-class-for-named-services
 - shortcuts like autowire, and tags: http://symfony.com/blog/new-in-symfony-3-3-added-new-shortcut-methods
 

Because of tehre wre  os many, there are asleom some extra:

- psr4 servie autodiscreory
    - using nette? here is one
        - https://github.com/contributte/di
        - https://github.com/ublaboo/directory-register
- action arugmetn injection
    - Symplfiy package
    - 

Although [I'm not fan of config based programming](https://github.com/symfony/symfony/pull/22234#issuecomment-297861051), I admin this is a must have step in evolution to be at least able to get there.

before after


## Before and after 


```yaml
# app/config/services.yml
services:
    some_controlelr:
    some_command:
    some_event_distapcher:
    cool_servics.
    cool_servid_with_tag:
```

## After


psr instance of
autocofnigrue





## How to Deprecate Packages Without Letting Anyone Behind
 
 - update a post with replacement
 - hide it from the list
 - add replacement to specific compoentn on packagist
 - move package to deprecated packages with replacement to specific package

