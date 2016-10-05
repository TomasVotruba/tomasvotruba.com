---
title: "How to Use Symfony Bundles in Nette Without Rewriting DI Logic"
categories:
    - Symfony
    - Nette
    - Symplify
perex: >
    Every framework has its own unique Dependency Injection Container (DIC), where you register your services. <strong>Imagine a set of special glues that are required to add the same paper on different surfaces.</strong> Today I will show you how to use universal glue for Nette surface.
     
thumbnail: "nette.png"
lang: "en"
---

<p class="perex">{{ page.perex|raw }}</p>

To be specific: 

- In Nette you use [Nette\DI package](https://github.com/nette/di) and make an **extension**
- In Symfony, you have to use [Symfony\DependencyInjection package](http://symfony.com/doc/current/components/dependency_injection.html) and create a **bundle**.

So when you hear somebody saying:

> "I saw that in Symfony bundle and want to use in Nette"
    
you know it won't be easy.


## DRY (Do-Repeat-Yourself) in Package Ratio

That's why you see "double" integrations for 3rd party packages like:

**[Doctrine\Migrations](https://github.com/doctrine/migrations)**

- for Symfony: [Doctrine/DoctrineMigrationsBundle](https://github.com/doctrine/DoctrineMigrationsBundle/)
- for Nette: [Zenify/DoctrineMigrations](https://github.com/Zenify/DoctrineMigrations)

**[MessageBus - CQRS](http://simplebus.github.io/)** 

- for Symfony: [SimpleBus/SymfonyBridge](https://github.com/SimpleBus/SymfonyBridge)
- for Nette: [newPOPE/Nette-CQRS-Commands](https://github.com/newPOPE/Nette-CQRS-Commands)
 
**[Elastica](https://github.com/ruflin/Elastica)**

- for Symfony: [FriendsOfSymfony/FOSElasticaBundle](https://github.com/FriendsOfSymfony/FOSElasticaBundle)
- for Nette: [Kdyby/ElasticSearch](https://github.com/Kdyby/ElasticSearch)
 
And so on. Let's ignore the syntax sugar that every programmer adds to his own integrations.   

### This leads to

- **lot of duplicated code** (register event subscribers, commands and all the services from 3rd party package)
- **lot of duplicated maintenance** (fix compatibility with new version of 3rd party package)
- greater possibility of **burnout syndrome**

## Is this really needed?

Well, if you look closer to Nette and Symfony DICs, you see **they are quite similar**. So answer is **NO**.

Both Nette and Symfony have 3 basic operations dealing with DIC:
 
### 1. Register a service

Common for all packages.

- Nette: `loadConfiguration()` method
- Symfony: `Extension` class


### 2. Decorate already registered services

Add setter, pass an arguments, add reference to other service, collect services of certain type.
Used less often, yet still very useful.
  
- Nette: `beforeCompile()` method
- Symfony: `CompilerPassInterface` classes


### 3. Add some magic or static code in the end

Usually workarounds, hacks, tweaks or performance tuning. Quite rare.
 
- Nette: `afterCompile()` method
- Symfony: specific `CompilerPassInterface` classes


## Enough Theory, Give me the Solution!

Okay, okay... These are last few lines before the code, I promise.
 
Thanks to step 1. and 2. I could create an extension, that will **take any Symfony bundle and register its services into the Nette application**: [Symplify/NetteAdapterForSymfonyBundles](https://github.com/Symplify/NetteAdapterForSymfonyBundles)

(The other way round is also possible, but no bundle for that. Yet. Let me know, if you would like to create that.)

## How to register a Symfony Bundle into your Nette Application in 3 steps 

### 1. Install package

```language-yaml
composer require symplify/nette-adapter-for-symfony-bundles
```
  
### 2. Register extension

```language-yaml
# app/config/config.neon
extensions:
    symfonyBundles: Symplify\NetteAdapterForSymfonyBundles\DI\NetteAdapterForSymfonyBundlesExtension
```

### 3. Register desired Symfony Bundle

```language-yaml
# app/config/config.neon
symfonyBundles:
    bundles:
		- League\Tactician\Bundle\TacticianBundle
```

And that's it!

For further use, **just check Readme for [Symplify/NetteAdapterForSymfonyBundles](https://github.com/Symplify/NetteAdapterForSymfonyBundles)**.

---

So next time you see a Symfony bundle you would like to use in Nette, stop thinking about writing brand new duplicated extension and try this bundle first. You might save yourself great amount of time :) 


## Made for you

Missing some feature or found a bug? Let me know. I want to make this package suit your needs and work as best as possible.
