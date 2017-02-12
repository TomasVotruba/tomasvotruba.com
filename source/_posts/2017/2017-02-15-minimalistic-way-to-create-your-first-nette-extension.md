---
layout: post
title: "Minimalistic Way to Create Your First Nette Extension"
perex: '''
    Nette extension allows you not only to create packages for others (like Zenify,  Nextras or Kdyby), but also to split your application so small and logical chunks of code.
    <br><br>
    Those first are more complex and using many cool Nette\DI features, but today I will show you, how to start with one method, so you can create your first Nette extension from scratch.
'''
lang: en
---

First, I will tell you bit about self-learning theory. It's just one headline, then we get to the code. I find it very important, because **it makes learning for me much more easier**.

## Set The Smallest Step Possible

When I want to learn new information, I try to relalize, what is the smallest possible step. Also called lean method (@todo link) or (@Babauta link).

I learned extensions the hard way:

1. reading [Nette documentation that describes over 20 features and uses cases it has]. That is information, which is useful, because I can use the tool to it's potential.
2. reading extension that other Nette programmers wrote - mostly Nella, Kdyby and Venne. Those are similar to documentation: many features on various use cases I didn't understand yet.

That's what I call **overstretching the brain muscle*. It hurts and disables you the same way overstretching  your leg muscle does.

It made me think: "could it be simpler?"

What is extension? Extension registers services to Nette Service Container.

Register services to Nette Container?

...

Register services?

...

Register service...

**Register 1 service.**

That's the step!


## Register in service Nette Sandbox

If you wan't to register service in Nette Sandbox, what will you do?

```yaml
# app/config/config.neon
services:
    - UserManager
    - RouterFactory..
    - @todo
    # add your service here
```

Imagine Nette Sandbox is your (zásuvka). There is one pace to active your computer, when you plug it it int.

@picture - one zásuvka

What it we wan't to plug in compuser and our mobile charger in the same time?

Our goal is to add @rozbočku,

@picture - many zásuvkca,



