---
id: 327
title: "How to Scope Your PHP Tool in 10&nbsp;Steps"
perex: |
    Do you know PHPStan, ECS, [Monorepo Builder](https://github.com/symplify/monorepo-builder), PHPUnit, [Config Transformer](https://github.com/symplify/config-transformer) or Rector?
    <br><br>
    In previous post we explored [why are these tools scoped](/blog/why-do-we-scope-php-tools), where does scoping makes sense and where not so much.
    <br><br>
    **Do you maintain a PHP tool that runs in command line**? Today we'll look on 10 steps how you can scope it too.

tweet: "New Post on the 🐘 blog: How to Scope Your PHP Tool in 10 Steps for Dummies"
---

## 1. Add php-scoper

[php-scoper](https://github.com/humbug/php-scoper) is a tool that scans our project and its `/vendor`. Then it adds a unique random prefix to every class:

```diff
-namespace Symfony\Component\Console\Command;
+namespace Scoper12345\Symfony\Component\Console\Command;

-use Symfony\Component\Console\Input\InputInterface;
+use Scoper12345\Symfony\Component\Console\Input\InputInterface;

 class Command
 {
     protected function execute(InputInterface $inputInterface,  ...)
     {
     }

     // ...
 }
```


## 2. Configure php-scoper

## 3. Develop in one Repository, Release in Another


