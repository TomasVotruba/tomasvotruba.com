---
title: Nečekaný růst Symfony v ČR
categories:
    - Symfony
---


I'm glad you've pointed out this. I might write an article on this as well in the future :).

As for tags, I'm used to findByType method in Nette\DI that will get you all services of certain type. If you require certain method and your code depends on it, it makes sense to use interface (= contract).

It makes sense for adding metadata like event listener, that adds event name, or priority via tags.
In this case, it would be duplicated information like:

services:
    some:
        class: SomeClass
        implements: SomeInterface

I try to ease usage to the end user, which I really miss in Symfony.
If there would be use case not enforcing a method, I would enforce a tag.
