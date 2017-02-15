---
layout: post
title: "Minimalistic Way to Create Your First Nette Extension"
perex: '''
    Nette extension allows you not only to create open-source packages, but also to <strong>split your application so small and logical chunks of code</strong>.
    <br><br>
Open-source extensions are more complex and using many cool Nette\DI features, but today I will show you, how to <strong>start with one Nette\DI method and one service only</strong>.
'''
lang: en
---

First, I will tell you bit self-learning theory. After this one headline we get to the code. It might be useful to you, because **it makes learning for me much more easier**.

## Set The Smallest Step Possible

When I want to learn new information, I try to realize what is **the smallest step possible**. Also called *lean method*, or in software [Lean Software Development](https://en.wikipedia.org/wiki/Lean_software_development) (LSD).

### I Learned Extensions the Hard Way

1. **Reading Nette documentation** [that describes over 20 features and uses cases it has](https://doc.nette.org/en/last/di-extensions). That is information, which is useful, because I can use the tool to it's potential.
2. **Reading extension of open-source packages** - mostly Nella, Kdyby and Venne. Those are similar to documentation: many features on various use cases I didn't understand yet.

That would lead to **overstretching your brain muscle**. It hurts and disables you the same way overstretching your leg muscle does.

### It made me think: "Could it be simpler?"

What is extension? Extension registers services to Nette Service Container.

- Register services to Nette Container?
- Register services?
- Register service...

**Register 1 service** - that's the one and only step we'll make today.

## Register service in Nette Sandbox

I consider [Nette Sandbox](https://github.com/nette/sandbox) the best way to show learn any Nette feature. Let's use it.

If you want to register `App\Repository\UserRepository` service, what will you do?

```yaml
# app/config/config.neon

services:
    - App\Model\UserManager
    # ...
    - App\Repository\UserRepository
```

File `app/config/config.neon` is like a socket.

<img src="/assets/images/posts/2017/nette-extension/single-socket.jpg" class="thumbnail">

There is **one place to active your computer**, when you plug it in.

But what if your want to **plug in computer and mobile charger** in the same time?

<img src="/assets/images/posts/2017/nette-extension/multi-socket.jpg" class="thumbnail">

To load more services, we use the same interface as `app/config/config.neon`: a file with service section that lists services.

### Use Standalone Config

This is smaller pre-steps to this - [`includes` section](https://doc.nette.org/en/2.4/configuring#toc-multiple-configuration-files). A decoupling I do when there are dozens of services and when I'm not sure, what to decouple yet.

```yaml
# app/config/config.neon

includes:
    - repositories.neon

services:
    - App\Model\UserManager
```

```yaml
# app/config/repositories.neon

services:
    - App\Repository\UserRepository
```

Nice and clear, right?

Extension is the same pro process - *it does the same thing just on different place**.

## Create Local Extension in 3 Steps

### 1. Move to ``src/Repository`

Where `Repository` is name of our package.

```bash
app/config/repositories.neon => src/Repository/config/services.neon
```

```bash
app/Model/UserRepository.php => src/Repository/UserRepository.php
```

src/

Moving files (stěhování ?)

Now we move the config and the class inside to new place.



Security  config services neom


Let's try app, doesn't work

2. Create extension
All it does it implelemts cooler etenaion.

How to load services?

Instead of neon import there is PHP load condigiraotn. Weload neon and add it to Nette, our multizasuvla by PHP. Like this

….Code….

And register there extension like tjis = plugging it in.

Refresh and it…

@obr: fail red screen autoload

Fails? Damn, I can't put this on my blog :(


3. Load missing classes

Add this code tocomposer.json

@Link to pehapkari článek



Obr: it works and shines


That's all.


