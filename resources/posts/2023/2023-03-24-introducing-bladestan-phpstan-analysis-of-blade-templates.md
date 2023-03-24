---
id: 382
title: "Introducing Bladestan - PHPStan analysis of Blade templates"

perex: |
    This Tuesday I was a guest in [2nd podcast of PHP Portugal](https://twitter.com/VotrubaT/status/1639241043248836610) folks. It was fun as always and apart GPT questions, I got asked about the Laravel open-source packages like [Punchcard](https://github.com/tomasVotruba/punchcard).

    I promised to put the 2nd package this week, so here it is.
---

First a short history of full circle. A year and half ago I wrote about [Twig static analysis](/blog/stamp-static-analysis-of-templates/). Last year [Canvural](https://github.com/canvural) turned the idea into the real project for Blade templates. I wanted to use this package with few extras and upgrade on Laravel 10. The package seemed abandoned and crashed on few templates, so I ported most of it parts and inlined the Symplify package for PHPStan compilation.

The result? I've been running **the Bladestan package since February on all my Laravel projects** to detect bugs without waiting production to fail on render. It's perfect helping hand and I don't have to worry about various code changes.




