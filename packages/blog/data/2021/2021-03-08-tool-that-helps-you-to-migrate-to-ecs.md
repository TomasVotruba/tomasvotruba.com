---
id: 306
title: "A Tool that helps you to Migrate to ECS"
perex: |
    Do you want to use ECS but still stuck on an older coding standard tool? I wrote a post [how to migrate from PHP_CodeSniffer](/blog/2018/06/04/how-to-migrate-from-php-code-sniffer-to-easy-coding-standard/) and [from PHP-CS-Fixer](/blog/2018/06/07/how-to-migrate-from-php-cs-fixer-to-easy-coding-standard/).
    <br><br>
    But who has time to read the step-by-step manual and do manual work? Nobody. That's why today, we'll look at a tool that **will handle the migration to ECS for you**.

tweet: "New Post on #php 🐘 blog: A Tool that helps you to Migrate to ECS"

deprecated_since: "2021-06"
deprecated_message: |
    This package didn't get much traction since it was introduced and was deprecated.
    <br>
    Use [migration post from PHP_CodeSniffer](/blog/2018/06/04/how-to-migrate-from-php-code-sniffer-to-easy-coding-standard) or [from PHP-CS-Fixer](/blog/2018/06/07/how-to-migrate-from-php-cs-fixer-to-easy-coding-standard) instead.
---

More and more projects I worked with wanted to try ECS, but they didn't want to look at every rule in XML and write them in PHP. I helped the first 3 to do manually and guide them on differences in configuration and rule naming. It was daunting work.

Are you interested **in what way is ECS better**? Check the mentioned posts:

- [ECS vs PHP_CodeSniffer](/blog/2018/06/04/how-to-migrate-from-php-code-sniffer-to-easy-coding-standard)
- [ECS vs PHP-CS-Fixer](/blog/2018/06/07/how-to-migrate-from-php-cs-fixer-to-easy-coding-standard)

<br>

When I was about to migrate 4th `phpcs.xml`, I didn't want to repeat copy-pasting, adding fully qualified names from dots, and so on. Some of you know from [GitHub Stars profile](https://stars.github.com/profiles/tomasvotruba/), that this physically hurts me more than most people.

Luckily, I had to [go the toilet](https://twitter.com/VotrubaT/status/1368894569853648900). Suddenly I realized:

<blockquote class="blockquote text-center">
"If I can do it, so can PHP tool"
</blockquote>

That's how **[Sniffer/Fixer to ECS Converter](https://github.com/symplify/sniffer-fixer-to-ecs-converter)** was born. The tool does what its name says.

## 2 Steps to Migrate Your Config

1. Install the package

```bash
composer require symplify/sniffer-fixer-to-ecs-converter --dev
```

2. Run the `convert` command

**Migrate PHP_CodeSniffer config**

```bash
vendor/bin/sniffer-fixer-to-ecs-converter convert phpcs.xml
```

**Migrate PHP-CS-Fixer config**

```bash
vendor/bin/sniffer-fixer-to-ecs-converter convert .php_cs.dist
```

In both cases, a new config `converted-ecs.php` was created.

<br>

Now you can verify the config by running ECS.

```bash
composer require symplify/easy-coding-standard --dev
vendor/bin/ecs check --config converted-ecs.php
```

If all is good, rename this config to `ecs.php` and remove your old config + tool from `composer.json`. Then you're ready to go.

That's it!

<br>

Happy coding!
