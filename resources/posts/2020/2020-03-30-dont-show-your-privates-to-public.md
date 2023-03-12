---
id: 245
title: "Don't Show Your Privates to Public"
perex: |
    With typed properties in PHP 7.4, there comes **a natural tendency for using properties as public**. The type check is already there, right? It's a dangerous path that opens the door to static code with public properties everywhere, that is asking for a change in any place.


    But is that ok to show privates to the public?

---

Last week you probably spotted **[RFC: Constructor Promotion](https://wiki.php.net/rfc/constructor_promotion)** proposed by my favorite mentor [Nikita Popov](https://github.com/nikic). This RFC counters *public property by default* approach and **reduces the redundant code at the same time**.

<br>

Another counter point for public properties or methods nailed [Stefan Priebsch](https://thephp.cc/company/consultants/stefan-priebsch):

<img src="/assets/images/posts/2020/privates_meme.jpg" class="img-thumbnail">


## How Many Public Methods Has Your Project?

That meme had just 1 public method. But what about real projects?

To get **real numbers from a real project**, I run [phploc](https://matthiasnoback.nl/2019/09/using-phploc-for-quick-code-quality-estimation-part-1) to measure size of [Rector's code](https://github.com/rectorphp/rector).

```bash
phploc src packages rules --exclude tests
```

These are **the results**:

```bash
Size
  Non-Comment Lines of Code (NCLOC)              99033 (85.46%)

Structure
  Methods                                         5185
    Visibility
      Public Methods                              3516 (67.81%)
```

<br>

Let's put 100 children in one square:

<img src="/assets/images/posts/2020/privates_100_children.jpg" class="img-thumbnail">

This **is 3 500 children**:

<img src="/assets/images/posts/2020/privates_3500_children.jpg" class="img-thumbnail">

There is [~480 Rector rules](https://github.com/rectorphp/rector/blob/master/docs/rector_rules_overview.md), each has 3 public methods required by interface contract. That's 1 500 public contract methods. Even if we remove those, 3 500 - 1 500, we still have **over 2 000 public methods**:

<img src="/assets/images/posts/2020/privates_2000_children.jpg" class="img-thumbnail">

Good luck with getting all your 2 000 children to university... or bed ... or make them breakfast for them... for one day. No pressure, right?

## How to Take Care for as Few Children as Possible?

We want to reduce the number of public methods to a minimum. How?

### 1. A Method by Method Human Refactoring

We can go for a method by method refactoring with careful analysis if the method should be public or private and how we can change it to private. **It takes time, patience, attention, human performance and doesn't scale on massive projects.** That's way too expensive.

❌

What simpler way can we apply today on an any-sized project? What low hanging fruit can we focus on?


### 2. Fake Public Method Detection

What is *fake public* method, property, or constant?

```php
<?php

declare(strict_types=1);

final class SomeController
{
    /**
     * @Route(path="/")
     */
    public function homepage()
    {
        return $this->prepareData();
    }

    public function prepareData(): array
    {
        return ['status' => 'quarantine'];
    }
}
```

Can you see it?

```diff
+public function prepareData(): array
-private function prepareData(): array
```

✅

<br>

Same applies for constants and properties, that is **used only locally in the class they're defined in**:

```php
<?php

declare(strict_types=1);

final class AirCleaner
{
    public const NAME = 'cleaner';

    public function getName()
    {
        return self::NAME;
    }
}
```

```diff
-public const NAME = 'cleaner';
+private const NAME = 'cleaner';
```

✅

The most common *false publics* are constants because they're [last that got visibility in PHP 7.1](https://wiki.php.net/rfc/class_const_visibility).

## Why Should We Care about Privatization?

"Nice intellectual exercise, Tom, but nothing more," you may think, and close this post and go back to your quarantine work.

But remember our children:

<blockquote class="blockquote text-center">
    A public method is like a child: Once you've written it,<br>
    you are going to maintain it for the rest of its life.
</blockquote>

What happens if we don't care about it? Well, **`public` element is designed to be used somewhere else.**

Same way `static` method is a method to be used everywhere (and [then slowly kill you](/blog/2018/04/26/how-i-got-into-static-trap-and-made-fool-of-myself)/).

<br>

It's easy to spot now because we focus on ten lines of code, we knew that's a controller method only, and that's a clear miss-use. But in the real world, **we don't have so much time to think about 10 lines of code**.

<br>

<blockquote class="twitter-tweet"><p lang="en" dir="ltr">10 lines of code = 10 issues.<br><br>500 lines of code = &quot;looks fine.&quot;<br><br>Code reviews.</p>&mdash; I Am Developer (@iamdevloper) <a href="https://twitter.com/iamdevloper/status/397664295875805184?ref_src=twsrc%5Etfw">November 5, 2013</a></blockquote>

<br>

Limited attention span is one of the reasons. We had plenty of such potential *legacy back doors* in Rector:

<img src="/assets/images/posts/2020/privates_sample.png" class="img-thumbnail">

## Unused Method

Another reason is that **the method is completely unused**, but PHPStorm won't tell you because it is `public`. When we turned this method into `private`, we saw it's unused, and we got rid of it:

<img src="/assets/images/posts/2020/privates_sample_2.png" class="img-thumbnail">

## Not-Used Method

The effect of privatization is surprising. Take this case:

<img src="/assets/images/posts/2020/privates_sample_3.png" class="img-thumbnail">

How can the privatization of methods lead to more code? Easily. The method was `public`, but never used. After Rector run it was `private`, but never used and then removed... and that could work.

Actually, that was not a feature, it was a bug. **This method should have been used many months ago.** So we used it in the right place, the feature was complete, and potential future bug.

<br>

Do you want to know all the possible code changes?

<a href="https://github.com/rectorphp/rector/pull/3084/commits/626287ec76ed16d15136115e1510b2154c2712a9" class="btn btn-dark btn-sm">
    See pull-request
</a>


## In CI pipeline, or Won't Happen

What now? Go to your code and check method one by one clicking on them in PHPStorm to *Find Usages*, if they're used somewhere else or *false public* and should be private. It will make your code much more robust and senior...

*Just kidding*. Let Rector handle it with `PRIVATIZATION` set:

```php
use Rector\Set\ValueObject\SetList;
use Rector\Config\RectorConfig;

return function (RectorConfig $rectorConfig): void {
    $rectorConfig->import(SetList::PRIVATIZATION);
};
```

```bash
vendor/bin/rector process src
```

It has now 4 rules:

- **privatize local-only constant**
- **privatize local-only property**
- **privatize local-only method**
- **privatize local getter to local property**

Do you have an idea for another privatization rule? [Let us know on GitHub](https://github.com/rectorphp/rector/issues/new?template=2_Feature_request.md).

Stay lazy and alive!

<br>

Happy coding!

<script async src="https://platform.twitter.com/widgets.js" charset="utf-8"></script>
