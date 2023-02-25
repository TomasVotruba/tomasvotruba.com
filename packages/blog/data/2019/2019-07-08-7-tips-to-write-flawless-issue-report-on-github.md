---
id: 217
title: "7 Tips to Write Flawless Issue Reports on Github"
perex: |
    Reporting issue is important for both you and the maintainer of the project. It can be a private issue in your local Gitlab repository, but open-source issues on Github have a much higher volume (obviously).


    Do you want to be clear to the maintainer, be understood and resolve your issue quickly?
    Here is how to write.


updated_since: "November 2020"
updated_message: |
    Switch from deprecated `--set` option to `rector.php` config.
    Updated Rector **YAML** to **PHP** configuration, as current standard.
---

**Issue report is like any other text** - it can be a piece of text you read and take nothing from or it can fun and rich for the information you really need, for e.g. a job advertisement. You can see:

- boring job ad with a description of the company and how revolutionary their idea about selling mushroom is
- or an ad, that tells you how you can full-fill your full potential, using PHP 7.4, Symfony 4.4, with ready-made Docker tested on 10 new developers with a focus on the mutual friendship between business and clean code

## Why Do You Need to Write Better Issues?

Do you think better issues is extra work on your side just to make maintainer happier? No!
**You profit from it**.

### What Do You Gain by Flawless Issue Report?

- no further questions from the maintainers, just "thanks" → **more free time (attention) for you**, no need to think about the answers and reading their text
- higher chance of fixing of your issue → **you can enjoy the fix in 1-2 days**, without waiting - unclear issues can take over 6 months to resolve, even if it's just missing '=' in the output
- the maintainer will like you more because you're clear for them → **your issues will get higher priority** the longer and the better your write them
- feature requests → after creating a streak of closed issues, **your feature requests will be more likely accepted**

### Where Should You Apply it?

Writing issue like this takes extra attention, thought and energy. It takes a few weeks to get used to it. **So should you write flawless issues in every Github repository you work with?** It would probably drain your energy and burn-out into falling back to just poor issue reports.

Personally, I'd **focus only on few repositories that matter to me and that I plan to use in the future**. The top 3 from top of my head right now would be Symfony, PHP Parser and EasyAdminBundle.

That way you won't burn out writing 10 issues mind-full issues to 10 different projects in a week and projects you love and use daily will grow in time - *win-win*.

<br>

Now you know *why* and *where*. I think you're ready for creating the very first *flawless issue*™:

<img src="/assets/images/posts/2019/issues/new_issue.png" class="img-thumbnail">

(The examples will be related to Rector, as I work with it the most lately).

## 1. Add Project Version

The version of the used project is very important. Maybe you use LTS version, maybe it's legacy already, maybe it's experimental version and it's expected to break. You don't care about this, but it will give maintainer the context of your issue.

"I use Rector v0.5.7"

How do you find this information quickly?

```bash
composer show rector/rector | grep version
> versions : * v0.5.7
```

### Even Better

This one is nice to have if you're into bleeding edge technologies:

"I use Rector v0.5.7, and it's still broken on `dev-master`"

Test `dev-master` as well. Maybe this issue was reported before 2 days and is already fixed on `master`?
You don't have to read all the past issues - it's a waste of time (maybe there was just PR, or maybe just commit right to the `master`), just try it:

```diff
 {
     "require": {
-        "rector/rector": "^0.5.7"
+        "rector/rector": "dev-master"
     },
+    "minimum-stability": "dev",
+    "prefer-stable": true
 }
```

```bash
composer update
```

```bash
// retry your command
```

## 2. What Happened - Clear, Exact and Right To the Point

- <strike>"I was trying to run on Rector on our codebase and it broke in hundreds of cases."</strike>
- <strike>"When I run Rector, it skips all the old PHP code that it should upgrade."</strike>

These reports have exactly 0 added information. By creating an "issue", you've already told the maintainer it's broken.

What should you go for instead? Remove the ambiguous:

- "Put the red apple to the 3rd shelf from the bottom."
- "Let's meet on Tuesday 3rd July at 17:00 on Vltavska square, here is the address http://..."

In an issue:

"I run Rector on [this-code] and I got [this-exception] with [this-exception-message]"

## 3. Code over Text

- <strike>...our files</strike>
- <strike>...our code-base</strike>
- <strike>...1 PHP file with Factory with Guzzle that contains the connection to Twitter API</strike>

These reports could be replaced by black-box style "...on something unknown". 0-value.

- "On this code"

```php
<?php

use Guzzle\Client;

final class GuzzleFactory
{
     public function create()
     {
         return new Client([
             'key' => 'some_key'
         ]);
     }
}
```

## 4. Highlight What You Can

Why do we use IDE? Why do traffic lights have colors? What does <span class="text-danger">red</span> in PHPUnit mean?

**Colors gives us meta-information**, that is processed by other parts of the brain then reading is. That way we understand element with colors faster and easier:

```php
echo "1" . 5 + 0x15;
```

vs.

echo "1" . 5 + 0x15;

I'm not a dog so I fancy the 1st one.

<br>

In the most of Github issues you'll use `php` and `diff` syntax highligh:

<span markdown=0>
```php
echo "Hi";
```
</span>

<span markdown=0>
```diff
-expected
+reality
```
</span>

## 4. The Smaller the Better

"What? I thought the more information you'll have, the easier is to fix it." This tip is counter-intuitive. The information is not about quantity, but quality.

The issue report should contain:

- **all the relevant information**
- **0 of the irrelevant information**

At first, it might be hard to figure out what's relevant for the maintainer of that particular project, but based on their feedback you can see it.

Let's look at the code of the previous example - this one has all the relevant information:

```php
<?php

use Guzzle\Client;

final class GuzzleFactory
{
     public function create()
     {
         return new Client([
             'key' => 'some_key'
         ]);
     }
}
```

This one has duplicated of irrelevant information:

```php
<?php

use Guzzle\Client;

final class GuzzleFactory
{
     public function create()
     {
         return new Client([
             'key' => 'some_key',
             'another_key' => 'some_key',
         ]);
     }
}

final class FacebookFactory
{
     public function create()
     {
         return new FacebookClient([
             'key' => 'some_key',
             'another_key' => 'some_key',
         ]);
     }
}

final class TwitterFactory
{
   public function create()
   {
       return new TwitterClient([
           'key' => 'some_key',
           'another_key' => 'some_key',
       ]);
   }
}
```

If all the code snippets produce the "Factory return type cannot be resolved" error, use just the first one.

(Hi *Honza* :D)

## 5. What Did you Do?

Now the maintainer knows:

- what do you use
- what is the code causing it
- what is the error message

✅ Great job! If all the issues were reported like this, the productivity in open-source will sky-rocket (and I mean "Elon Musk" sky-rocket).

The 1st questions that will pop-up in the maintainer's head are "How did you do that?"

- Did you run it from command line?
- What exact command have you used?
- Did you use Docker?
- Do you use custom `rector.php` config? What's inside?
- Is the error still there with no config?

Again, think of *4. The smaller the better* tip while copy-pasting the `config`.

- Which of those 400 lines in the config are responsible for this error?
- What combinations of rules and parameters are causing the error?

"I installed Rector as dev dependency to composer and run:"

```bash
vendor/bin/rector process src/SomeFile.php --dry-run
```

With `rector.php` config:

```php
use Rector\Symfony\Set\SymfonySetList;
use Rector\Config\RectorConfig;

return function (RectorConfig $rectorConfig): void {
    $rectorConfig->import(SymfonySetList::SYMFONY_43);
};
```

Perfect!

## 6. What do You Want?

Now the maintainer has all the information about your code, your steps that lead to it and the configuration that caused it.

But **what do you want**?

- Do you expect the code to throw a better exception?
- Are you ok with the behavior, just not sure if it's expected?
- Do you want it to work a certain way?

Here is the `diff` syntax becomes very useful:

```diff
<?php

-echo 1 + 5; // real
+echo 6; // expected
```

Having comments is really great. Why?

```diff
<?php

-echo $value + $value2;
+echo $value+$value2;
```

Which one is preferred?

## 7. 1 Issue = 1 Issue

Last but not least, **1 issue report should talk about 1 issue**. Imagine you go for a trip and you talk to your friend how to get there:

New issue - * traveling*:

- Let's take a train
- We could take a bus, it's 50 € cheaper
- But I'm not sure what to pack
- Do you have a tent?
- Ok, we'll take a bus

*issue is closed*

- What are we gonna eat guys?
- We could eat wursts all the time!

<br>

Do you have 3 different (see tip #4 again) problems with your code? Create 3 different issues. Don't be afraid, you're not complaining too much.

**It's better to have 3 separated issue**, that one is easy-pick, 2nd is a question and 3rd need a test case, that all these at once.

<br>

Now go out, try, fail and learn :)

<br>

Happy coding!
