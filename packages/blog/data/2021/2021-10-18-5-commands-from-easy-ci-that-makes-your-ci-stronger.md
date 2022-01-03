---
id: 335
title: "5 Commands from Easy CI that makes your CI Stronger"
perex: |
    Sometimes my clients need a specific CI check that spots tiny but annoying bugs, and they cannot be discovered by PHPStan, Rector, or coding standard tool. It can be unresolved conflict `<<<<<<`, invalid config syntax, or forgotten commented code.
    <br><br>
    Usually, we write specific PHP commands just for the particular project and let them rotten in spaghetti time. Instead, I cherry-pick those commands to **a package called `symplify/easy-ci`**. That way, I can use them in any project and improve them.
    <br><br>
    Today we'll look at 5 commands you can use in your CI. **They might save you from bugs that no other tool can check**.

tweet: "New Post on the 🐘 blog: 5 Commands from Easy CI that makes your CI Stronger   #nettefw #neon #git #codereview"
---

To start, we first add the package:

```bash
composer require symplify/easy-ci --dev
```

## 1. Check Latte templates for Existing Code

PHPStorm is very good at renaming classes and methods. But its power stops at templates and configs, leading to bugs when the template contains a non-existing method. It can take days to discover this, as not everyone has tested and not everyone has I test that check every single render path of every template.

### What does it do?

This command checks `*.latte` templates for:

* existing classes
* existing methods
* existing constants

```html
{if App\Utils::validateUlr($url)}
    ...
    <p>{App\ErrorMessg:VALID}</p>
{else}
    ...
    <p>{App\ErrorMessage::NOT_VALID}</p>
{/if}
```

### How to Use it?

```bash
vendor/bin/easy-ci check-latte-template <paths>

# e.g.
vendor/bin/easy-ci check-latte-template app src
```

<p class="text-success pt-3 pb-3">✅</p>

## 2. Checks NEON/YAML Configs for Existing Classes

This command is similar to the above. If PHPStorm or any developer renames a class, it can be missed in configs.

### What does it do?

It checks all `*.neon` and `*.yaml` files for:

* existing classes
* existing class constants

```yaml
services:
    -
        class: App\SomeService
        arguments:
            level: App\Level::TOPP
```

### How to Use it?

```bash
vendor/bin/easy-ci check-config <paths>

# e.g.
vendor/bin/easy-ci check-config config
```

<p class="text-success pt-3 pb-3">✅</p>


## 3. Check NEON for Complex Syntax

How can we write the YAML syntax above in NEON? Very similar, but also in a much shorter way thanks to "NEON entities":

```neon
services:
    - App\SomeService([App\Level::TOPP])
```

Do you understand this code? I'm not sure if it's interface, class, arguments of the method call.
<br>
<br>
This magic syntax also proven very hard to analyze with other tools. That's why we added a check to require explicit and straightforward code:

```yaml
services:
    -
        class: App\SomeService
        arguments:
            level: App\Level::TOPP
```

### What does it do?

It checks `*.neon` files to a magical inlined configuration that can be written explicit and transparent way.

### How to Use it?

```bash
vendor/bin/easy-ci check-neon <paths>

# e.g.
vendor/bin/easy-ci check-neon config
```

<p class="text-success pt-3 pb-3">✅</p>

## 4. Check for Unresolved Conflicts

Sometimes we merge old pull-request, and we have to rebase dozens of commits in the past. In 99 % of use cases, it goes well, or the CI reports a failure, but in 1 %, it hits us.

```html
The price of this product is
<<<<<<< HEAD
0
=======
100
>>>>>>> branch-a
```

And then we have to explore how it got here, who added it etc. Why waste the energy if you can find this before merging?

### What does it do?

Goes through all files and detects any unresolved `<<<<<<<` code.

### How to Use it?

```bash
vendor/bin/easy-ci check-conflicts <paths>

# e.g.
vendor/bin/easy-ci check-conflicts .
```

<p class="text-success pt-3 pb-3">✅</p>

## 5. Detect Commented Code

Last but not least, sometimes, we temporarily comment out a chunk of code. We're testing something, or we don't need the method right now, so we comment it. Keeping commented code in main codebase is wrong for couple reasons:

* We have tools like `git history` and `git blame` to re-use old code.
* When we merge the pull-request, we don't known if we've left some commented code we forgot to use or remove.
* If the commented code is part of security layer, we might have a business chasing our tail.

### What does it do?

It goes through all `*.php` files and looks for lines of `//` commented code:

```php
final class MoneyTransferer
{
    public function run()
    {
           // @todo enable before merge
//         if (! $this->ensureHasAccess()) {
//              throw new ForbiddenAccessException();
//         }

        $this->sendMoney();
    }
}
```

If there are 3 and more commented lines in a row, it will let you know.

### How to Use it?

```bash
vendor/bin/easy-ci check-commented-code <paths>

# e.g.
vendor/bin/easy-ci check-commented-code app src packages
```

<br>

Is the line limit too strict for your project?
<br>
Use `--line-limit` option to modify it to your needs:

```bash
vendor/bin/easy-ci check-commented-code app src packages --line-limit 6
```

<p class="text-success pt-3 pb-3">✅</p>

<br>

Happy coding!
