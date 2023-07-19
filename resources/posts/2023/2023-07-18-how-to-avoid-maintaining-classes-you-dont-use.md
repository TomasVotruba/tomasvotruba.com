---
id: 386
title: "How to avoid Maintaining Classes you Don't Use"

perex: |
    PHPStan and static analysis help us with detect unused private methods. I also made a package to handle [unused public methods](/blog/can-phpstan-find-dead-public-methods).

    But we can do better. When we remove dead parts of our code, we sometime leak classes that are never used. But we still have to maintain them, upgrade them and test them.

    Do you want to avoid spending time and money on something you don't use?
---


Almost 4 years ago we had legacy project that required PHP and Symfony upgrade. But instead of jumping right into an upgrade, we made experiment with dead-code detection.

How much was it? We [had a talk in Czech on PHP meetup in Prague](https://www.facebook.com/pehapkari/videos/milan-mimra-cto-spaceflow-tom%C3%A1%C5%A1-votruba-spaceflowjak-se-chyt%C5%99e-zbavit-technick%C3%A9h/399224180756304/
), where we shared more details. It's often more than people guess they have in a codebase they maintain for years.

Not to stretch your language skill, in the end, **we've removed around ~20 % of the codebase**. That's 20 % of code we we'd have to test, fix in static analysis, upgrade to newer Symfony or PHP and so on.

<br>

## How to find a Leaking Class?

A leaking class requires our attention but we don't really need it:

* it's never used in a service config
* we don't call it anywhere in the codebase
* it doesn't use marker interface, [that is is collected by container](/blog/2018/03/08/why-is-collector-pattern-so-awesome) and injected - e.g. the `Command` classes injected to console `Application`

There is also one special case, that is missed by both PHPStan and PhpStorm. Sometimes we test the class, so it looks used. Then we have to test it, because it *is used* - **a class that is used only in tests can be also removed**.

<br>

We've been using this principal on projects past 4 years and it's very simple in it's nature:

* First, we find all existing classes we own - typically in `/app` or `/src` directory
* Then we find all class useages - calls, property fetches or constant fetches
* In the end we diff those 2 lists and we have a list of classes that are never used

Simple!

<br>

## 3. Steps to run it on Your Project

1. Install the [TomasVotruba/class-leak](https://github.com/TomasVotruba/class-leak) package

```bash
composer require tomasvotruba/class-leak --dev
```

*The package is available on PHP 7.2+, [as downgraded](/blog/how-to-develop-sole-package-in-php81-and-downgrade-to-php72/).*

<br>

2. Run it on your source directories, not tests

```bash
vendor/bin/class-leak check bin src config
```

<br>

3. Check the reported classes and remove them

Also remove tests and fake usages if needed, e.g. we've had some classes registered only in `config/services.php` in Symfony projects, that were never used in the project source code itself.

<br>

In case you want to skip some classes, you can use `--skip-type` option:

```bash
 vendor/bin/class-leak check bin src packages --skip-type="Symplify\\EasyCI\\Twig\\Contract\\TwigTemplateAnalyzerInterface"
```

<br>

That's it. **Add the job to your CI and let it spot the leaking classes for you**. You'll never have to maintain leaking classes again!

<br>

Happy coding!




