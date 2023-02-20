---
id: 280
title: "How to Add Colors to Continuous Integration Output"
perex: |
    Today I have a tip for your CI. I learned this tip from [Jan Mikes](https://github.com/JanMikes).

    A small tip that made my everyday work with CI more colorful.

tweet: "New Post on #php 🐘 blog: How to Add Colors to Continuous Integration Output"
tweet_image: "/assets/images/posts/2020/ansi_no_mix_colors.png"
---

- Do you use Travis, Github Actions, or Gitlab CI?
- Do you use composer, PHPUnit, ECS, Rector, or PHPStan?
- Do you have colors enabled in `phpunit.xml`?

```xml
<?xml version="1.0" encoding="UTF-8"?>
<phpunit colors="true">
    <!-- ... -->
</phpunit>
```

- Do you use [composer scripts](https://blog.martinhujer.cz/have-you-tried-composer-scripts/) to prevent typos and re-use CI tool setup?

<br>

...but still missing the **colored output**?

<br>

## Sad Black/White Continuous Integration World

For many years I've run Travis, Gitlab, and [now Github Actions](/blog/2020/01/27/switch-travis-to-github-actions-to-reduce-stress/). I never knew the output could be readable for humans, so I always looked at the final checkmark for all the scripts.

It was ✅ or ❌.

<br>

When the CI runs the scripts:

```yaml
scripts:
    - composer install
    - composer fix-cs
```

<br>


This was usually the output:

<img src="/assets/images/posts/2020/ansi_no_colors.png">
<img src="/assets/images/posts/2020/ansi_no_colors_2.png">

<br>

## One day, Something Changed

The colors came to my life. I could read again, and the output was the same as in local environment!

<img src="/assets/images/posts/2020/ansi_colors.png" class="shadow img-thumbnail">
<img src="/assets/images/posts/2020/ansi_colors_2.png" class="shadow img-thumbnail">

What happened? Did they fix something on Github Actions? Or composer (and all the other tools were) was fixed?

<br>

No, **just one new word** appeared:

```diff
 scripts:
-    - composer install
+    - composer install --ansi
```

```diff
 {
     "scripts": {
-        "fix-cs": "vendor/bin/ecs check --fix"
+        "fix-cs": "vendor/bin/ecs check --fix --ansi"
     }
 }
```

Since then, I enjoy failed CI jobs more and find faster what went wrong.

<br>

Happy coding!
