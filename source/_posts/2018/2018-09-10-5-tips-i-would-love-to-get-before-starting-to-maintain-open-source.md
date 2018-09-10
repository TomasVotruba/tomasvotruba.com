---
id: 139
title: "5 Tips I Would Love to Get Before Starting to Maintain an Open Source"
perex: |
    I wasn't always confident while writing publicly every single line of PHP code. I had to take many blind paths and earned experiences, spend a night full of stress coding in unknown waters and make a lot of over-complicated code that backfired to me years later.s
    <br><br>
    They say "experience cannot be passed and it must be experienced" and I agree with that, but still there are some shortcuts that would speed-up my path to joyful open-source coding I have today. Here are 5 of them.  
tweet: "New Post on my Blog: 5 Advises I Would Love to Get Before Starting to Maintain #OpenSource    #php #fuckups"
---

## 1. Be Open to Change any Package

Everything changes and when it comes to software, it's exponentially faster. How much changed the school system we have nowadays and how much changed content we absorb - mostly through the internet - every day in the last 5 years?

I used Nette for as my favorite framework for many many years. Later I found out what components and packages are and I picked few packages from Nette and few from Symfony for my base stack. One package was the hearth of all my packages - Dependency injection component. Nette\DI with default autowiring was light-years ahead in 2014. But Nette didn't have any new features anymore and the software grew exponentially. Symfony 2.8 came with default autologin. Symfony 3.3 came with service autodiscovery (link).

I loved most of the new features in Symfony and I backported them to Nette. Since I know their both DI components by the hearth, it was easy for me. But it my lazy-brain stated itching. Why do I create manually - maintain and test - a code that was already written in another framework? It was until one afternoon I was happy to share with [Martin Hujer](https://www.martinhujer.cz/). I shared my itching and how I love these Symfony DI features and he asked me very simple yet powerful question 

*Well, why don't you switch that component from Nette to Symfony? It should be pretty easy.* 

I didn't know the answer and I promised to make an experiment that night.

I tried it on Statie and [this PR was born](https://github.com/Symplify/Symplify/pull/184). I'm still happy when I see this pull-request with `+425 −723`. The refactoring is the one where you leave less code, but more readable and with better features.

The experiment worked and whole Symplify and Rector are now running on Symfony DependencyInjection (+ [few addiotions](package builder)). 

**So if there are good reasons to switch, don't let your past preferences hold you back - just switch**.

## 2. Don't keep every feature you have

When I started my first open-source PHP packages, called Zenify, I focused to have as many features as possible. My packages were not so popular at that time, so it was not an issue, or was it?

It came more obvious when I [came to ApiGen](@todo apigen link) in 2014. It had like 30 option to setup. Settings encoding to UTF-8, adding colors to the command line, to check coding style (oh yeah), to enable or disable progress bar, to check if there is a newer version and many more. First I added more features that were requested and felt great, that the package is growing in its value. But does eating more and more food make your more healthy? The more feature there was, the more difficult it was to refactoring, to test and to add new features. 

I investigate Github issues of ApiGen and found out, that many features were implemented after a single request. There was a possibility that only that single person needed it so there are 300 lines of PHP untested code just because the single person wanted a feature for that one run he used ApiGen.

The amount of code != the value of the code. I realized there is a lot of "dead" code just a few people are using. This alone would be ok. But dozens of these "little" features made the core code unable to maintain, so tool didn't work for 95 %.

I didn't know which options are used and which are not, so I tried a little experiment. I made a release candidate version without these options. If the features were really important, some would create an issue - and there were such rare cases. But most of these features could be really dropped - [8 in total](https://github.com/ApiGen/ApiGen/releases/tag/v4.0.0) to be precise.

**Instead of adding more and more features, you should focus on keeping the main value fit. Take 80/20 rule - drop 20 % of the least used features to make 80 % of those most valuable grow faster.**

## 3. Lock to LTS, Maintained Dependencies and green PHP

I thought supporting the oldest version is the best because that's how you get the most users. I supported PHP 5.3, 5.4, 5.5, 5.6 and 7.0. What a wide range of diversity, who cares PHP 5.3 was in End of life, were still people using it so I have to be there for them!

How naive I was :) it only caused me troubles with maintaining and I taught people it's ok to use PHP versions without security fixes.
Now I support PHP 7.1 and 7.2 and it's just a fine amount of PHP versions to maintain.

You don't have to make the same mistakes as me. There are many great examples that work today:

- https://github.com/sebastianbergmann/phpunit/wiki/Release-Process - stick to [*menstruation* dependency](@todo post)

- http://php.net/supported-versions.php - stay green on PHP
- https://gophp71.org/ - go with the mainstream

**Go through them and suck the gold in.**

But this can also backfire. I was so frustrated by maintaining more versions that I could manage, that I bounced to the other extreme - tried to "educate" people to update as early as they can. I tend to lock to the highest Symfony version, no matter if LTS or not. So until just a couple of months ago, Symplify supported only Symfony 4, not Symfony 3.4 today. I had many discussion about that, where I explained reasons like the one above. The last person who finally "broke" me for good was [Lukasz Chrusciel](@todo link) when he asked: "How difficult would it be to support Symfony 3.4 as well?"

I didn't know, so I made an experiment. And [this PR was born](https://github.com/Symplify/Symplify/pull/818/files).

**Since then I know you should lock to [Symfony LTS](https://symfony.com/roadmap#maintained-symfony-branches), not higher.**


## 4. Don't maintain multiple packages nor branches, just one repository

It's very easy to create a PHP package nowadays. It's very easy to create 10 of them. When I started with Zenify, then Symplify, the package-counter could be around 20 for 2 years. I thought that only single-project-maintainers [David Grudl](@todo thank link) that earn money for living from them could maintain such a big number of packages. I had zero income from them, but I wanted them to live, so maintained them... and then I burned out.

After that, I recall I was looking at Symfony repository - many packages but just repository. WTF? It took a few more months to find out, [what monorepo is](https://gomonorepo.org/) and understand how it works and how to set it up in very very basic form.

Again, nowadays there are a website and tools like

- gomonorpeo - @todo link
- monroepobuderil - https://github.com/symplify/monorepobuilder
- monorepo-tools https://github.com/shopsys/monorepo-tools

...so if you know YAML syntax, you know how to open a command line and you have a Github account, you also know how to run your own monorep in 10 minutes.

Again, I didn't know if that was the right step for me - even Nette went from monorepo to multirepo few months before in that time -, so I tried an experiment. I made a monorepo with 2 packages and it was just awesome. I never came back.


## 5. Don't listen to Advises, Experiment for Yourself

No one knows the answer. The most of these fuckups above were based on learning from others. "If he's doing it and he's popular, he's right." Or maybe he isn't, try it for yourself. Fail fast to learn fast.

I wouldn't know how to do it right if I didn't go wrong first.

<br>

Happy growing!
