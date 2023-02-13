---
id: 378
title: "Run Config Transformer Everyday to&nbsp;keep YAML Away"
perex: |
    Have you moved your Symfony configs from YAML to PHP with the help of [Config Transformer](/blog/2020/07/27/how-to-switch-from-yaml-xml-configs-to-php-today-with-migrify/)? Do you use [the PHP benefits daily](/blog/2020/07/16/10-cool-features-you-get-after-switching-from-yaml-to-php-configs)?
    <br><br>
    But how do you **make sure no YAML is leaking**  from a new developer or in test configs?
---

We've added a new feature to CI that might help with this.

In recent weeks, we've [split Symplify monorepo](/blog/good-bye-monorepo) into standalone packages. A few packages got a new feature in the tidying process.

With Symfony projects upgrades, the first thing we do is move configs from YAML to PHP. That way **we can use Rector and PHPStan to help with the full upgrade path**.

We've finally removed the last YAML file, and everything is fully blown PHP configuration! Guess what happened on the following PR:

```bash
Fatal error: the *.php config file was not found
```

Damn, there were still a few YAML files in `/config` and `/tests`.

## Should we check every file? Let CI handle it

* For coding standards, we can run ECS in CI with `--dry-run` to report about this.
* For old PHP code or weird PHP structures, we have Rector with `--dry-run`

Could we run a config transformer, too, to help with this?

```bash
vendor/bin/config-transformer config tests --dry-run
```

<br>

Do you want to ensure no YAML file leaks into your code base?
Just upgrade to config transformer `12.0` and use it in your CI!

<br>

Happy coding!
