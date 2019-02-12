---
id: 185
title: "Introducing Neon to YAML Converter"
perex: |
    I wrote about [How to migrate between Neon to Yaml](/blog/2018/03/12/neon-vs-yaml-and-how-to-migrate-between-them/) almost a year ago. Recently we needed to migrate many files with parameters, imports, and mainly services.
    <br><br>
    **Neon and YAML are basically arrays**, right? So why not let a tool let do the dirty work?

tweet: "New Post on #php 🐘 blog: Introducing #Neon to #Yaml Converter   #symfony #nettefw"
---

## When Do You need it?

- You want to **migrate your package** dependency injection component from `Nette\DI` to `Symfony\HttpKernel`
- You want to **migrate your application** from Nette to Symfony
- One of your dependency decided to migrate from `*.neon` to `*.yaml`, e.g. EasyCodingStandard 2 → 3, and you need to **migrate your config**, e.g. from `ecs.neon` to `ecs.yaml`

## How to Use it?

To use [Symplify/NeonToYamlConverter](https://github.com/Symplify/NeonToYamlConverter), require it a composer dependency:

```bash
composer require symplify/neon-to-yaml-converter --dev
```

Run it on one file or directory - it takes all `*.neon`, `*.yml` and `*.yaml` files:

```bash
vendor/bin/neon-to-yaml-converter convert file.neon
```

## The 2 Most Problematic Places Converter Tool Handles

In Neon there are nested parameters = you can use `%payu.user%` to get parameter `user` in `payu` array. In YAML used in Symfony code, there are only one level parameters. That means you can use only the `payu` parameter, nothing nested.

That's why all parameters have to be converted to the **single level of nesting**, here to `payu_user`:

```diff
 parameters:
-    payu:
-       user: Pepa
+    payu_user: Pepa
-       password: abz123
+    payu_password: abz123

 services:
     PayuService:
         arguments:
-            - '%payu.user%'
+            - '%payu_user%'
-            - '%payu.password%'
+            - '%payu_password%'
```

Another case are [Neon entities](/blog/2018/03/12/neon-vs-yaml-and-how-to-migrate-between-them/#4-very-complex-syntax). Their goal is to make syntax short. Its cost is less readability.

Code is actually parsed to an object, that has different meaning in different places:

```diff
 services:
-    - App\SomeService(@anotherService, %perex%)
+    App\SomeService:
+        arguments:
+            - @anotherService
+            - %perex%
```

Those of you who don't use Neon for years, would you guess that?

All this converter handles for you.

<br>

Next time you **migrate your config, package or whole application** from Neon to YAML, let [Symplify\NeonToYamlConverter
](https://github.com/Symplify/NeonToYamlConverter) do the work for you.

Happy coding!
