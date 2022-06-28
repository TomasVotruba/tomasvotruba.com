---
id: 364
title: "Twig Smoke Rendering - Journey&nbsp;of&nbsp;Fails"
perex: |
    In previous post, we explored [the "whys" for Twig Smoke Rendering](/blog/twig-smoke-rendering-why-do-we-even-need-it).
    <br><br>
    Today we will set on the journey towards this tool and mainly, the beauty of fails on every single step.
---

<br>

This rendering approach gives us control of filters and functions by default. The variables don't have to exist, but filters are still run on them. If **template uses filter that does not exist** or exists, but we didn't register it via TWIG extension, it will crash and we will know about it.

## ✅

<br>

## 4. Way Too Tolerant Constants

@todo

## ✅

<br>

## 5. Where is the Form?

@todo

## ✅

<br>



## Light in the End of the Mountain

<blockquote class="twitter-tweet"><p lang="en" dir="ltr">We&#39;ve just added our latest toy to CI😋 with ✅<br><br>Tolerant <a href="https://twitter.com/hashtag/twig?src=hash&amp;ref_src=twsrc%5Etfw">#twig</a> renderer 🎉🎉🎉<br><br>* covers all TWIG files<br>* dynamic rendering<br>* finds non-existing filters + functions + tags<br>* even wrong constants!<br>* blazing fast ⚡️<br>* no php-parser, no magic transform<br>* fun to make 😎 <a href="https://t.co/5H8iXVNyGS">pic.twitter.com/5H8iXVNyGS</a></p>&mdash; Tomas Votruba 🇺🇦 (@VotrubaT) <a href="https://twitter.com/VotrubaT/status/1540004210888040452?ref_src=twsrc%5Etfw">June 23, 2022</a></blockquote>

<script async src="https://platform.twitter.com/widgets.js" charset="utf-8"></script>
