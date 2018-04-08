---
id: 90
title: "Try New Coding Standard PSR-12 on Your Code"
perex: |
    ...
tweet: "New Post on My Blog: Try New Coding Standard PSR-12 on Your Code"
tweet_image: "..."
---


### tl;dr;

```bash
composer require symplify/easy-coding-standard --dev
vendor/bin/ecs check /src --level psr12
``` 

And to fix the code:

```bash
vendor/bin/ecs check /src --level psr12 --fix
```

### How did PSR-12 got to ECS?

- The standard is just behind the door, but feedback is ver yimporatn phase

- reddit asked on experimentl implementaion of PSR12 https://www.reddit.com/r/PHP/comments/84vafc/phpfig_psr_status_update/
- with help of KorvinSzanto and his commit https://github.com/KorvinSzanto/PHP-CS-Fixer/commit/c0b642c186d8f666a64937c2d37442dc77f6f393
I was able to put down
- it looks liek this @tod show psr12.yml set



```
composer require symplify/easy-coding-standard --dev
vendor/bin/ecs check /src --level psr12
``` 

## DO you agree or disagree?

There are still many [missed cases to be integrated in the standard](https://github.com/KorvinSzanto/PHP-CS-Fixer/milestones), but there is never to soon to get feedback from community.

Try it out.

It will be a thing, PSR12 set is definitely coming to PHP CS Fixer and [PHP_CodeSniffer has also active issue](https://github.com/squizlabs/PHP_CodeSniffer/issues/750) as well.

PSR-12 suggests:

```php
<?php 

declare(strict_types=1);
```

I think our attention deserves to ignore anything that is the same in every file so inline it line:

```php
<?php declare(strict_types=1);
```

Namespace changes, file doc changes, `use`, `class`, `interface`... that all changes in every file, so it should be on standalone line, that will force you to notice it. But not `declare(strict_types=1);`, that is the same in every file.

Communicate, spread the ideas and find your way. This is only PSR - PS **Recommentnation**. It's better to keep [things standard for others](@todo link to standrds), but not a rigid rule that cannot be improved.

<br><br>

Happy coding! 


