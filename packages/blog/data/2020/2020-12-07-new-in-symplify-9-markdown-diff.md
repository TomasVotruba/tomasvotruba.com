---
id: 291
title: "New in Symplify 9: Markdown Diff"
perex: |
    When we maintain a package docs or generate documentation in Markdown, we write a code snippet from time to time.
    The clearest way to show what you exactly mean is a diff. That's why diff is used in GitHub commit suggestions.
tweet: "New Post on #php 🐘 blog: New in #Symplify 9: Markdown Diff"
---

<blockquote class="blockquote text-center">
    "Diff is worth 100 code-review words."
</blockquote>

But creating automated diff in Markdown is a pickle. We picked the pickle and turned it into a package - **[symplify/markdown-diff](https://github.com/symplify/markdown-diff) package**.

The package does exactly what it says and is mostly used in [symplify/rule-doc-generator](/blog/2020/11/30/new-in-symplify-9-documentation-generator-for-php-cs-fixer-code-sniffer-phpstan-rector-rules. I'd say this is the smallest package on Symplify.


## 3 step to  Markdown Diff

1. Install Package

```bash
composer require symplify/markdown-diff
```

2. Register in `config/bundles.php`

```php
use Symplify\MarkdownDiff\Bundle\MarkdownDiffBundle;

return [
    MarkdownDiffBundle::class => [
        'all' => true,
    ],
];
```

3. Use it

```php
namespace App;

use Symplify\MarkdownDiff\Differ\MarkdownDiffer;

final class SomeClass
{
    /**
     * @var MarkdownDiffer
     */
    private $markdownDiffer;

    public function __construct(MarkdownDiffer $markdownDiffer)
    {
        $this->markdownDiffer = $markdownDiffer;
    }

    public function run(): void
    {
        $markdownDiff = $this->markdownDiffer->diff('oldContent', 'newContent');

        // ...

        file_put_contents(getcwd() . '/docs/diff.md', $markdownDiff);
    }
}
```

↓


```diff
-'oldContent'
+'newContent'
```

Pretty simple, right?

## Smarter than Normal

Compared to [sebastian/diff](https://packagist.org/packages/sebastian/diff) it builds on, this package does an extra cleaning job:

- removes line numbers
- removes `---/+++ @@` spam
- removes trailing white space
- most important one: **provides full diff, so you'll get full contents of compared files**

That's all for today.

<br>

Happy coding!
