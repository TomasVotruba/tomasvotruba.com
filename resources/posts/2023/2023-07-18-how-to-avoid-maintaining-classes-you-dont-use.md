---
id: 386
title: "How to avoid Maintaning Classes you Don't Use"

perex: |
    PHSPtan and static analysis help us with detect unused private methods. I also made a package to handle [unused public methods](/blog/can-phpstan-find-dead-public-methods).

    But we can do better. When we remove dead parts of our code, we sometime leak classes that are never used. But we still have to maintian them, upgrade them and test them.

    Do you want to avoid spending time and money on something you don't use?
---


Almost 4 years ago we had legacy project that required PHP and Symfony upgrade. But instead of jumping right into an upgrade, we made experiment with dead-code detection. How much was it? We [had a talk in Czech on PHP meetup in Prague](https://www.facebook.com/pehapkari/videos/milan-mimra-cto-spaceflow-tom%C3%A1%C5%A1-votruba-spaceflowjak-se-chyt%C5%99e-zbavit-technick%C3%A9h/399224180756304/
), where we shared more details.

Not to stretch your language skill, in the end, **we've removed around ~20 % of the codebase**. That's 20 % of code we we'd have to test, fix in static analysis, upgrade to newer Symfony or PHP and so on.

## How

