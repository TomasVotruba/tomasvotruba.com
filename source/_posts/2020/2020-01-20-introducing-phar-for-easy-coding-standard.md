---
id: 235
title: "Introducing PHAR for Easy&nbsp;Coding&nbsp;Standard"
perex: |
    Recently months there was huge jump in using ECS, [almost 4 000 downloads daily now](https://packagist.org/packages/symplify/easy-coding-standard/stats)!
    <br>
    <br>
    With this downloads growth, there is also growing demand for using it on older and older PHP projects. **ECS brings huge value there, as it helps with migration of code and cleaning it up**.
    <br> 
    <br>
    The problem is that ECS uses modern packages and it **makes installation on old projects impossible**.
    <br>
    <br>
    Does it though in 2020?   
    
tweet: "New Post on #php 🐘 blog: Introducing PHAR for Easy Coding Standard"
tweet_image: "/assets/images/posts/ecs_prefixed.gif"
---

<br>

This is how installation on old project makes us angry:

<img src="/assets/images/posts/ecs_prefixed.gif" class="img-thumbnail">


**I don't like it when developers are frustrated by the limits of the system they use**. My mission is quite the opposite - make a complex system simple and easy to use by anyone.

So I took a few-days effort and made a prefixed PHAR Easy Coding Standard version for legacy projects. You can see [Pull Request on Github](https://github.com/Symplify/Symplify/pull/1734).

The first release of the prefixed version is v7.2.2, so you can enjoy all the cool features like [`only`](https://github.com/Symplify/Symplify/pull/1537) and [`paths` parameters](https://github.com/Symplify/Symplify/pull/1735).

### Add `-prefixed` and It Works 

Really. S instead of normal package installation:

```bash
composer require symplify/easy-coding-standard --dev
```

Use the `-prefixed` version:

```bash
composer require symplify/easy-coding-standard-prefixed --dev
```

**And you're ready to go!**

```bash
vendor/bin/ecs
```

## <em class="fas fa-fw fa-lg fa-check text-success"></em>

<br>

## What are Prefixed PHARs and How They Work?

I will not bother you with technical details, but if you're working on PHP CLI app and you want to **make it accessible to the majority of PHP developers**, you can learn more here:

- [How to Box Symfony App to PHAR without Killing Yourself](/blog/2019/12/02/how-to-box-symfony-app-to-phar-without-killing-yourself/)
- [How to install Rector despite Composer Conflicts](https://getrector.org/blog/2020/01/20/how-to-install-rector-despite-composer-conflicts)

<br>

Happy coding!
