---
id: 385
title: "Two Kinds of Legacy Code Upgrade"

perex: |
    I often speak with project owners or CTOs, who ask for help with legacy project upgrades. They typically want something like "upgrade to PHP 8.0" or "upgrade to Symfony 5.4". There are two ways to do that.

    Which one is the best for you? Let's ask the important question first.
---

<blockquote class="blockquote text-center">
"Do you plan to sell the project next year<br>
or will you work on it for the next 5 years?"
</blockquote>



Put in other words:

* do you want to meet your KPI in an as short time as possible to "make your boss happy",
* or do you want to make the codebase work for you and save your money in the future?


<br>

Let's say the goal is to upgrade from PHP 7.0 to PHP 7.4.


## 1. The Superficial Upgrade


This kind of upgrade is like a house renovation, not for your own living but for selling it to someone else. You want to **increase the perceived value of the house, but you need to care about the quality of the renovation**.

<img src="https://www.boredpanda.com/blog/wp-content/uploads/2020/10/house-renovations-that-look-worse-than-before-5f8999bd31606__700.jpg" class="img-thumbnail" style="width: 20em">

The same applies to code. You want to **increase the perceived value of the codebase, but you need to care about the quality of the code**.

<br>

When we say in the news that our code base uses PHP 7.4, what does it usually mean? 2 things:

1. The `composer.json` uses this version as a minimum:

```json
{
    "require": {
        "php": ">=7.4"
    }
}
```

<br>

2. The `Dockerfile` uses the exact PHP version:

```bash
FROM php:7.4-cli-alpine
```

This may seem like it allows you to say in a job advertisement that your project "use PHP 7.4".

<br>

But **what does the code really look like?**

```php
class ProjectManager
{
    private $managerName;

    public function __construct($managerName)
    {
        $this->managerName = $managerName;
    }

    public function handle($project)
    {
        if (date('l') == 'Wednesday') {
            $this->organizeMeeting($project);
        }
    }
}
```

You're right; we define the PHP version number in the `composer.json` and `Dockerfile.`

<br>

But we **don't really use any of PHP 7.4 features**:

* param type declarations (since PHP 7.0+)
* return type declarations (since PHP 7.0+)
* void type declaration (since PHP 7.1+)
* Property type declaration (since PHP 7.4+)

<br>

Still, you can tell your boss, who doesn't understand the complexity of the upgrade, that you use PHP 7.4 already. Collect the bonus for handling the upgrade and make them happy. At least for a year or two, till the technical debt hits you back.

<br>

## 2. The Long-Term Value Upgrade

The other way is like reconstructing a **house for you and your family to live in for at least the next 20 years**.

You care about the usability of the house:

* new windows will make the noise and
* temperature changes under control, noise isolation,
* cheap energy use during winter,
* warm water for the shower,
* and enough storage space for 4-seasons of belongings.

<img src="https://user-images.githubusercontent.com/924196/252729834-a174bb1a-e5f9-403f-be38-250a00608ff2.png" class="img-thumbnail" style="width: 20em">

It's not about making it look nice and shiny but about **making it work for you in the long- term**.


It takes more time, and it's more expensive because you deserve to have a decent home. It might be frustrating at the start, as you must take down old paint and furniture and check the sewage and electricity wires. After 2 months of reconstruction, the house looks much worse than you started.

But in the long term, you know for sure the foundations are done correctly, and you don't have to worry about any "surprises" like turning on the washing machine and induction cooker table.

<br>

The same applied to the legacy codebase upgrade: you use the latest PHP features right away:

```diff
-class ProjectManager
+final class ProjectManager
 {
     private $managerName;

-    public function __construct($managerName)
+    public function __construct(string $managerName)
     {
         $this->managerName = $managerName;
     }

-    public function handle($project)
+    public function handle(string $project): void
     {
-        if (date('l') == 'Wednesday') {
+        if (date('l') === 'Wednesday') {
             $this->organizeMeeting($project);
         }
     }
 }
```

This code is in much better shape than the one above, and it uses only PHP 7.1

It allows hiring new people faster, adding new features with fewer regression bugs, and it's easier to maintain.

<br>

In [Rector team](https://getrector.com/hire-team), we **always go for the long-term value**, as we care about clients' success and exponential growth, not about short-term profit that will put the company down.


<br>

Which one do you prefer?
