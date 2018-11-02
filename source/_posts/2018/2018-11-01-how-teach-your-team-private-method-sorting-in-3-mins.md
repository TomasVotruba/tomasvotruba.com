---
id: 155
title: "How to Teach Your Team Private Method Sorting in 3 mins"
perex:
    When I started PHP in 2004, all you had to do is to learn a few functions to become the most senior dev in your town. Nowadays, devs have to learn a framework, IDE and coding patterns to get at least to an average level.
    <br><br>
    Instead of reading 346 pages of [Clean Code](https://www.amazon.com/Clean-Code-Handbook-Software-Craftsmanship), you need to produce code and learn as you read it at the same time. **There will be never less information than it is today.**
    <br><br>
    That's why effective learning is a killer skill. **Today we learn how to sort private methods in 2 mins**.
tweet: "New Post on My Blog: How to Teach Your Team Private Method Sorting in 3 mins #codingstandard #cleancode #martinfowler #automate #php"
tweet_image: "/assets/images/posts/2018/private-method-order/example.png"
---

## Why is Private Method Order so Important?

This simple class is in your code now:

```php
<?php

class SomeClass
{
    public function run()
    {
        $this->call1();
        $this->call2();
    }

    private function call3()
    {
    }

    private function call2()
    {
    }

    private function call1()
    {
        $this->call3();
    }
}
```

Thanks to Gregor Harlan, there is a coding standard `OrderedClassElementsFixer`, that already takes care about `public`/`protected`/`private` elements order.

But what about these **private methods** - are they ok for you?

<br>

When you read such a code for the firs time, you might think:

- `private function call3()` - ah, it does this and that... but wait, **who is using it?**
- `private function call2()` - it does this and that, it was called in `run()` method
- `private function call1()` - it does this and... the `call3()` is used here, yes... **what it actually did?**

<blockquote class="blockquote text-center mt-5 mb-5">
    <p>
        "Be able to read down the file from top to bottom like a newspaper article,<br>
    which would naturally suggest that <strong>helper methods appear after the methods they are helping</strong>.<br>
    This would lead to maximum readability of the code structure."
    </p>
    <footer class="blockquote-footer text-right">
        <a href="https://softwareengineering.stackexchange.com/a/186421/148956">Anthony Pegram</a>, Clean Code reader
    </footer>
</blockquote>

## Nice Theory, Bro, but... Real-Life?

It's easy to spot and correct the example above...

```php
<?php

class SomeClass
{
    public function run()
    {
        $this->call1();
        $this->call2();
    }

    private function call1()
    {
        $this->call3();
    }

    private function call2()
    {
    }

    private function call3()
    {
    }
}
```

...but in real life pull-requests are usually longer than 20 lines:

<div class="text-center mb-5">
    <img src="/assets/images/posts/2018/private-method-order/example.png" class="img-thumbnail">

    <p>
        Taken from <a href="https://github.com/shopsys/shopsys/pull/554/files">my 5 days-old-PR</a>
    </p>
</div>



Nor junior nor senior dev is able to check the proper private method order in these big chunks of code.

## Unless...

...somebody or something does it for you.

<blockquote class="blockquote text-center mt-5 mb-5">
    "Automate everything that brings you <strong>more value until you die<br>compared to value lost to create it</strong>."
</blockquote>

I've already mentioned a coding standard. There is now a new fixer coming to [Symplify/CodingStandard](https://github.com/symplify/codingstandard) 5.2 that does exactly what we need in **a split of the second**:

```yaml
# ecs.yml
services:
    Symplify\CodingStandard\Fixer\Order\PrivateMethodOrderByUseFixer: ~
```

```bash
vendor/bin/ecs check src
```

You might need to re-run the command few times, because of the new order of private methods will automatically change calling order. But that's it: 

- no books, 
- no lectures, 
- 0 hours wasted on code-reviews.

<br>

Let finish with *Martin Fowler* quote:

<blockquote class="blockquote text-center">
    "Any fool can write code that a computer can understand.<br>
    Good programmers write code that <strong>humans can understand</strong>."
</blockquote>

I think we're coming to times, where:

<blockquote class="blockquote text-center">
    "Any computer can write a code that humans can understand."
</blockquote>

<br>

Have fun!
