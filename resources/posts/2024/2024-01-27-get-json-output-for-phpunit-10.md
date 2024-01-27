---
id: 402
title: "Get Json output for PHPUnit 10"
perex: |
    Early this year I created few custom Rector rules for our client. It modified code based on PHPUnit error result report.

    The only problem is, that PHPUnit outputs a string. So I had to parse it manually with regexes.

    Having a JSON output would make my life easier. I'm used that PHP tools provide the JSON out of the box, but I could not find it in PHPUnit.
---

So I asked on Twitter:

<blockquote class="twitter-tweet"><p lang="en" dir="ltr">Hello internet, I need to pipe <a href="https://twitter.com/PHPUnit?ref_src=twsrc%5Etfw">@phpunit</a> output to another tool and look for a json format. I can&#39;t find it in the options, neither GPT knows how.<br><br>Any ideas how it&#39;s doable?</p>&mdash; Tomas Votruba (@VotrubaT) <a href="https://twitter.com/VotrubaT/status/1744306260063801715?ref_src=twsrc%5Etfw">January 8, 2024</a></blockquote>

Most of replies were about a custom printer. Sounds easy, let's try it! Unfortunately, it worked only up to PHPUnit 9.

PHPUnit 10 introduced brand new [event system](https://localheinz.com/articles/2023/02/14/extending-phpunit-with-its-new-event-system/) that removed the custom printer completely. Any single operation is now an event. There is not much helpful sources about it, so asking GPT fails hard with PHPUnit 9 context.

I thought "parsing text is not that bad, is it?"

<br>

## Upgrading Rector book with Custom PHPUnit output

Coincidentally, I've worked on a upgrade of [Rector book past 3 weeks](https://leanpub.com/rector-the-power-of-automated-refactoring/). We've just released a new Rector version, so I want to keep book up to date for new readers. We generate book samples from real code, with real tests, with real... PHPUnit output.

Guess what, we've used PHPUnit 9 so far and to limit the output to fit 72 chars in a book, we used - custom printer! That doesn't work anymore, but we still need PHPUnit output to look nice in the book. So I had to learn the new event system anyway.

After that experience, **creating a PHPUnit JSON result printer extension was a piece of cake**.

## PHPUnit Json Result output

1. Add package

```bash
composer require --dev tomasvotruba/phpunit-json-result-printer
```

2. Register extension in `phpnit.xml`

```xml
<extensions>
    <bootstrap class="TomasVotruba\PHPUnitJsonResultPrinter\PHPUnitJsonResultPrinterExtension" />
</extensions>
```

3. Run PHPUnit and see result:

```bash
vendor/bin/phpunit
```

And see result:

```json
{
    "counts": {
        "tests": 1,
        "failed": 1,
        "assertions": 1,
        "errors": 0,
        "warnings": 0,
        "deprecations": 0,
        "notices": 0,
        "success": 1,
        "incomplete": 0,
        "risky": 0,
        "skipped": 0
    },
    "failed": [
        {
            "test_class": "TomasVotruba\\PHPUnitJsonResultPrinter\\Test\\OutputCleanerTest",
            "test_method": "testSame",
            "message": "Failed asserting that 'not equal' is identical to 100.",
            "exception_class": "PHPUnit\\Framework\\ExpectationFailedException",
            "line": 16,
            "data_provider": {
                "key": 0,
                "data": "Array &0 [\n    0 => 'not equal',\n    1 => 100,\n]",
                "provider_method": "provideData"
            }
        }
    ]
}
```

<br>

Now you can pipe it into any other tool like Rector or PHPStan, and build your next rules based on it:

```diff
    public static function provideData(): Iterator
    {
        yield [
-           'not equal',
            100,
        ];
    }
```

<br>

Happy coding!
