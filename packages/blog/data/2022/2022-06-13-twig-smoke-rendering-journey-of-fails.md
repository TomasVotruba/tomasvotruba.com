---
id: 362
title: "Twig Smoke Rendering - Journey&nbsp;of&nbsp;Fails"
perex: |
    Two weeks ago [our upgrade team started](https://getrector.org/for-companies) to upgrade Twig 1 to 2 and Latte 2 to 3 for two clients. There were no test that would cover the templates, just few integration ones that might have invoked few % of happy paths render. Not good enough.
    <br><br>
    We **need a CI test to be sure** templates are fine. I had an initial idea, but knowing the value of external input, I asked on [Twitter for brainstorm](https://twitter.com/VotrubaT/status/1537029650379116544). I'm happy I did. Alexander Schranz came with [a tip](https://twitter.com/alex_s_/status/1537030374651572225) that lead me on a 2 week journey and I would love to it with you today.

---

## Why do we need Smoke Render all Templates?

First, let me tell you about the **two reasons** why we need smoke render all templates when you upgrade a major version.


### Lint is not Render





## Light in the End of the Tunnel

<blockquote class="twitter-tweet"><p lang="en" dir="ltr">We&#39;ve just added our latest toy to CI😋 with ✅<br><br>Tolerant <a href="https://twitter.com/hashtag/twig?src=hash&amp;ref_src=twsrc%5Etfw">#twig</a> renderer 🎉🎉🎉<br><br>* covers all TWIG files<br>* dynamic rendering<br>* finds non-existing filters + functions + tags<br>* even wrong constants!<br>* blazing fast ⚡️<br>* no php-parser, no magic transform<br>* fun to make 😎 <a href="https://t.co/5H8iXVNyGS">pic.twitter.com/5H8iXVNyGS</a></p>&mdash; Tomas Votruba 🇺🇦 (@VotrubaT) <a href="https://twitter.com/VotrubaT/status/1540004210888040452?ref_src=twsrc%5Etfw">June 23, 2022</a></blockquote>

<script async src="https://platform.twitter.com/widgets.js" charset="utf-8"></script>
