---
id: 111
title: "..."
perex: |
    ...
tweet: "New Post on my Blog: ..."
tweet_image: "..."
---


Recently I helped Shopsys to migrate from combinations of 3 to ECS:
https://github.com/shopsys/shopsys/pull/143/files

Pull requests :)


ECS is a tool that combines PHP_CodeSniffer and PHP CS Fixer. It's super easy to start to use from scratch:

```bash
composer require symplify/easy-coding-standard --dev 
vendor/bin/ecs check src --level psr12 # yes 12! 
```

But what if you already have PHP_CodeSniffer on your project and want to switch?

## 3 steps

- ignores

- names

- run configs 