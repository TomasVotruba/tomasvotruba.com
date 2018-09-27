---
id: 147
title: "Open-Source Behind The Scenes - Finding the Rector Vision"
perex: |
    Open-source is not only about programming, maintaining, adding new features and spreading the word. It's also about other decisions of the maintainer, that are hidden from users.
    <br><br>
    I often ask myself: What values should it spread around the world? Where do I take time and money to develop it? How should it scale? How to make it useful to both people and me?
tweet: "New Post on my Blog: Open-Source Behind The Scenes - Finding the #Rector Vision"
---

It's not always easy to work with community feedback, as opinions and needs are often unique, sometimes contradictory. Sometimes clean-code kills growth, sometimes it's the legacy.

To give you a little **insight into maintainer's mind**, I'll share a letter I've to send to Symfony guys where I sum up events of recent months.

<br>

"...thank you for your reply. It gave me a very nice kick and more energy to my blood to work on this.

Sorry for my late reply, but I had to explore my vision with Rector a lot. I had to face many blinds paths and demons.

I held talks in Berlin, Vienna, and Pardubice during past 3 months and had to think a lot about the following direction. I talked with Dan Leech, author of [phpactor](https://github.com/phpactor/phpactor) (~= Rector as a plugin for Vim) and from Nils Adermann with insights from Private Packagist. I got plenty of feedback, offline and online.

Many people told me to go to the "Symfony" shift. It would make money, it would save bug reports from people since I'd be the one to manage it and I could invest that money to grow Rector a more.

But then **I got feedback from more communities**. I'm in touch with the CakePHP community. In last week I made this config that can do [140 changes in Cake PHP 3.4](https://github.com/rectorphp/rector/pull/634/files#diff-66bde3273ac825a92cf71b2e0bb9f674).
 
<blockquote class="blockquote text-center">
    Everyone is having the same problem: <strong>they need to move fast wide-spread legacy code in their growing communities</strong>.
</blockquote>

Also, a company from Vienna invited me again, sponsored my travel + stay and [we made live PR to php-parser in a matter of 30 seconds](https://github.com/nikic/PHP-Parser/pull/533). It got merged 2 days after. The company itself is almost finished with a migration of private legacy code to Laravel and they were interested in Rector's features.

Have you seen "safe" package in Github trends? Rector is now [helping people to migrate their code](https://github.com/thecodingmachine/safe#automated-refactoring). 

Yes, I could go to one community and help only them to make the migration almost flawless. But now I see many communities using Rector creative way I could never think of. Also, I'm not doing this for money (like ClangMR from Google or Larashift), but so programmers can be free with the most recent version of the framework they love. 

So, for now, I'm deciding to stay open, help here and there little with projects that come along, and see what inspiration it brings to people..." 
