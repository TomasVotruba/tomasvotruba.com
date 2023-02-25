---
id: 331
title: "How to Easily Protect your Code Base from Commented PHP Code"
perex: |
    When it comes to merging huge pull-request, here and there, a bit of commented code slips to our codebase. Do you trust your reckless developers to check every single line? Are you sure?


    We **cherish our developers' attention** so we come with a simple solution that we added to our CI. Now we know for sure. Do you think you have 0 % unwanted commented code? I dare you to have more.

---

With following approach, [we've discovered more than 150 commented out PHP lines](https://github.com/rectorphp/rector-src/commit/37b06023b666cac8ec2997e8e1605f93c25d3b6f) in Rector code today:

<img src="/assets/images/posts/2021/quick-easy-commented-code-detection.png" class="img-thumbnail">

<br>

## How we Detect Commented PHP Code Today?

The "best" way to detect commented code was with a sniff from PHP_CodeSniffer. It parsed every token in a comment and tried to decide if it's a commented PHP code, an example of PHP code, or normal text. It is instead a complex process with lots of false positives.

Is this PHP code?

```php
// if useful, remove
```

Or this one?

```php
// for example
// $value = 1000;
```

<br>

## Re-Define the "Commented PHP Code"

We had such a sniff, and it was not working correctly. We stopped to think about the problem. How does commented PHP code look like? Let's say we have this code:

```php
private function resolveFromNodeAndType(Node $node, Type $type): ?string
{
    $variableName = $this->resolveBareFromNode($node);
    if ($variableName === null) {
        return null;
    }

    $stringy = new Stringy($variableName);
    return (string) $stringy->camelize();
}
```

What happens when you try to comment it out in PHPStorm?

```php
//private function resolveFromNodeAndType(Node $node, Type $type): ?string
//{
//    $variableName = $this->resolveBareFromNode($node);
//    if ($variableName === null) {
//        return null;
//    }
//
//    $stringy = new Stringy($variableName);
//    return (string) $stringy->camelize();
//}
```

What has changed? Every line starts with `//`.

<br>

What do we use if we want to comment logic and explain behavior? The doc block:

```php
/**
 * If used on Monday, produces this code:
 *    $value = 'Monday is here';
 *    finally;
 * The rest of the week is off
 */
```

<br>

In the end, all we look for **is a more significant amount of lines starting with `//`**.

```php
// ...
// ...
// ...
```

<br>

After we re-defined the problem to a much simpler one, it was pretty easy to add this command to [symplify/easy-ci](https://github.com/symplify/easy-ci) utils package.

## 3 Steps to Detect Commented PHP Code in Your CI

**1. Install Easy CI**

```bash
composer require symplify/easy-ci --dev
```

<br>

**2. Run it**

```bash
vendor/bin/easy-ci check-commented-code <directory|ies>
vendor/bin/easy-ci check-commented-code src packages
```

<br>

Is it too strict? Tune line limit to your needs:

```bash
vendor/bin/easy-ci check-commented-code src packages --line-limit 10
```

<br>

**3. Add it to your CI**

<br>

That's it!

<br>

Happy coding!
