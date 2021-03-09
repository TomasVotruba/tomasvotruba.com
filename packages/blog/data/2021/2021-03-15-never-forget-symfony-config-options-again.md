---
id: 307
title: "Never Forget Symfony Config Options Again"
perex: |
    Have you switched your Symfony configs from **stringy YAML to typed PHP**? If not, [do it now](/blog/2020/07/27/how-to-switch-from-yaml-xml-configs-to-php-today-with-migrify/). Here is [at least 10 reasons why](/blog/2020/07/16/10-cool-features-you-get-after-switching-from-yaml-to-php-configs/).
    Only then you'll start to notice a code smell that was there in every YAML configs.
    <br><br>
    Just now, the code smell is too smelly to ignore.

tweet: "New Post on #php 🐘 blog: How to Never Forget #symfony Config Options Again"
tweet_image: "https://user-images.githubusercontent.com/924196/110403041-b8d1f680-807c-11eb-9767-ddf6a8631594.png"
---

Let's look at the Symfony FrameworkBundle extension configuration. Where can we configure them? In `config/packages` directory.

## 1. Poor YAML

This is how it looks in YAML:

<img src="https://user-images.githubusercontent.com/924196/110402064-ff265600-807a-11eb-98ab-d0b35dff0108.gif" class="img-thumbnail">

What is the name of the key? Secret, secrets...? We don't know, and we only copy-paste from Symfony Documentation.

## 2. Plain PHP

So how does this [memory-lock](https://tomasvotruba.com/blog/2018/08/27/why-and-how-to-avoid-the-memory-lock/) change with PHP?

<img src="https://user-images.githubusercontent.com/924196/110402071-02214680-807b-11eb-894b-48830713fecf.gif" class="img-thumbnail">

No, we can **the code smell** is banging us to our eyes:

- In YAML, we're used to be very active because we have to type everything manually and correctly. One extra space or indent, and the whole file will crash.
- In PHP, we're used to IDE take care of us. When we start typing "Ent...", we expect the IDE to autocomplete `EntityManagerInterface` and add use import at the top of the file.

What is the code smell in a config? We don't have to autocomplete for the option name. **The option names are always the same**, one might say "constant," and can be found in [Symfony documentation](https://symfony.com/doc/current/reference/configuration/framework.html):

<img src="https://user-images.githubusercontent.com/924196/110402547-e0748f00-807b-11eb-9d4b-a7638a5cad52.png" class="img-thumbnail">

But I don't want to memory 50 words per Symfony config. I want to code ambitious Rector rules that will remove "legacy" from our vocabulary. If only there was something like IDE but for Symfony configs...

## 3. Smart PHP with Amnesia

<img src="https://user-images.githubusercontent.com/924196/110402065-00578300-807b-11eb-8811-7c87e2d134d6.gif" class="img-thumbnail">

Instead of typing strings from the back of your memory, make use of `Symplify\Amnesia\ValueObject\Symfony\Extension\FrameworkExtension` constants.

<br>

There is constant class for `TwigExtension`:

<img src="https://user-images.githubusercontent.com/924196/110403043-b96a8d00-807c-11eb-897d-52241af2f939.png" class="img-thumbnail">

<br>

Also for Doctrine - `DoctrineExtension`, with `ORM` and `DBAL` classes:

<img src="https://user-images.githubusercontent.com/924196/110403041-b8d1f680-807c-11eb-9767-ddf6a8631594.png" class="img-thumbnail">

Pretty cool, right? We don't have to care about string and documentation reference because **all configuration options are defined in the place we need them**. [Just in time](https://blog.codinghorror.com/the-just-in-time-theory/).

<br>

Don't forget to add [Amnesia](https://github.com/symplify/amnesia) to your project:

```bash
compose require symplify/amnesia
```

And that's it!

<br>

Happy coding!
