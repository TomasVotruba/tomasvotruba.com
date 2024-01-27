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



asdf
