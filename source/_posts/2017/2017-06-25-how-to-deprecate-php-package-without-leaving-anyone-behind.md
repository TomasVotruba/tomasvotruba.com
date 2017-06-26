---
layout: post
title: "How to Deprecate PHP Package Without Leaving Anyone Behind"
perex: '''
    You create PHP open-source packages because you personally use them in your projects. And you take care of them.
    Sometimes we change a job or switch a language we work in and we don't have time to take care of them properly. Number of issues and PRs grows and new alternative packages arise.  
    <br><br>
    You can do 2 things in this situation: let it be (don't care) or <strong>take responsibility, deprecate package and inform your users about better alternative</strong>. 
'''
lang: en
---

## Why Care About Deprecation as a Maintainer

I created few packages for [Nette](https://nette.org) in the past. I used them in my projects extensively and they were slowly growing in downloads. Then I switched to [Symfony](https://symfony.com) and I worked mostly with Symfony projects.

The things is: 95 % of users of your package don't know about your personal development and life path. They use `composer require vendor/package-name` and that's it. Of course, few people follow issues and PRs around your package and they might notice 6-months gap in activity. But in open-source these "pauses" are quite often: you need to finish project, focus on boost of another package, you have some personal or family events (wedding, newborn child, break-up, moving to another country etc.).

**6-months pause gap and 6-months end-of-development pause look the same**. 

This is the second case.

### No more Mr. Nice Guy - Don't do Anything in Secret

:(

A. If you don't inform people, they might:

- build new open-source project on non-supported package
- build application on your package
- integrate your package deeply in architecture of your package
- promote practise that you don't support anymore but don't have time to put them in the package
 
:) 

B. But if you do inform people, they will:

- **be informed** - either notified by composer or on your blog (I will your show how later)
- **know** what part of their application won't be upgraded anymore
- **be able to plan** next upgrade much better with focus on this


### Packages as Relationships - They Stand on Trust

Iẗ́'s the same in personal relationships. You have a meeting with a friend on Saturday on his birthday party. During the week you got close with a girl you like and you'd like to spend a weekend with her, because it's great opportunity to get to know her better. But what about your friend?
   
You can either wait it out and don't tell him anything (A) or call him and explain the situation and let him know, you'll come next week as alternative (B).

What would you choose if you were your friend?
 
Actually B makes really great and strong relationships, because people know they can trust you if anything difficult ever happens between you.


## Deprecate Package != Remove Package 

I must admin I confused "deprecation" with "removing" not so long time ago. I though when I deprecate package, application who use it as composer dependency will stop working. Imagine going from level 50 to 20. **That would be actually cause by deleting a code but not by deprecating it**.
 
Deprecating package is rather having level 50 and staying there. It will never be worse, but it won't be better either. Deprecation won't break anything. It rather "there will be no new features coming anymore".

If you are familiar with Symfony BC promise, you can use similar logic on package (@todo).



## 3 Steps To Make People Safe About Deprecation 

### 1. Explain Why

"This package is deprecated" isn't really satisfying, is it?

It common effect that people most likely accept change with **a explanation behind it**.

So you can do it like this: ... (@todo copy from cotnrolerl autowire)

    
### 2. Suggest a replacement

"This package is deprecated, because this concept is archaic and not used anymore." No, that is not helpful :(


Software develops all the time, so there born better and better packages everyday. 
It so much helpful if you suggest a replaement.

@on packagist and github, you can suggest this and that

doesn'T have to be 100% the same, but a repalcment you'd yuse if not yyour package...


### 3. Inform on All Possible Places They can Meet Your Package

First programmer added package to his composer, another read about it on Github, another saw a blog post that your wrote about it.

- packakge way
    - suggespt replacement on packagist
        - @todo screen
    - composer will show message on usage of deprecating package
        - @todo screen
        
- github way
    - deprecate on github
        - add readme notification @todo screen
    - move under deprecated packages
        - @todo screen

- blog post way - deprecated and link
