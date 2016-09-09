---
title: "How to write OS in PHP 1: Create a repository on Github"
categories:
    - Open source
    - PHP
perex: > # multi-line string
    You got some code you want to share but you don't know exactly how? Well, writing open-source is complex process.
    In this series, I'll break it down to <strong>the smallest steps possible</strong>, so yo 
    can <strong>start your own OS project with zero-knowledge</strong>.
    Already a pro? I got some tips for you too.
    Ready? Let's start right now with Github repository!
thumbnail: "open-source.jpg"
lang: en
---

<p class="perex">{{ page.perex|raw }}</p>

## Meet Github, OS's best friend  

If not already, register on [Github](http://github.com). It's place where all open-source lives and breathes. For free!

Then create a repository with <a href="https://github.com/new">New Repository</a> button.

### Name repository well... well how?

- name should be explicit
- noun... well pretty **same rules as for class naming**
- if you wrap or extend some other service/package, prefix with it
- it's like headline &ndash; everyone should have clue what it does without peaking on readme
- don't be cool... you already are!
 
Nice theory. What about some examples?

- **good names**: *Datagrid*, *ImageResizer*, *DoctrineFilters*
- **bad names**: *TomasPackage*, *DoctrineExtras*, *Translate*

> For our case, I've create [OpenSourcePackageDemo](https://github.com/TomasVotruba/OpenSourcePackageDemo).
> Do you understand it? Don't worry, I (or you) can change it later.  

That's all you need now. Hit "Create repository" and you are done!

## Little book of git

Do you know git or have some Git GUI tool (Git Tortoise, PHPStorm...)? 

Just follow commands, that appeared on your Github repository and <a href="#your-code-is-online">skip to next headline</a>.

    echo "# OpenSourcePackageDemo" >> README.md
    git init
    git add README.md
    git commit -m "first commit"
    git remote add origin git@github.com:TomasVotruba/OpenSourcePackageDemo.git
    git push -u origin master
    
### You don't understand those geek lines? I'll explain

Move to the directory, where you want to host your package locally. Open command line or Terminal in PHPStorm.
Actually the second one will open terminal already in right place, so you don't have to browse directories via all those `cd`s. 
I use that.

And call these commands there. Lines with asterix (*) you will do repeatedly later.
The rest just once to setup repository = you don't have to remember them now.

    # create a file README.md and add "# OpenSourcePackageDemo" in it
    # you can do this manually in your PHPStorm of cource (I do!) 
    echo "# OpenSourcePackageDemo" >> README.md
    
    # create an empty repository git repostiry; sth like creating and empty folder or PHPStorm project
    git init
    
    #* tell git you want him to NOTICE this file to be added later
    git add README.md
    
    #* group all NOTICED files to single COMMIT (group of changes files)  
    git commit -m "first commit"
    
    # add ONLINE address where we want publish your code  
    git remote add origin git@github.com:TomasVotruba/OpenSourcePackageDemo.git
    
    #* send all those COMMITS online, now your local system and Github repository are synced 1:1
    git push -u origin master


<a name="your-code-is-online"></a>

## Your code is online!

## Better setup repository

Your repository is online. But needs a bit more meta files to be real open-source project.
What files to add and why?

Guys from phpleagues already did the job for you. Just download it ot "import it".

From my point of view, there is too much unncesarey code, so 
I've prepared lite version for you.

Pick oen you think you!ll understand the best.

---

## Do you want get deeper than that?

### Check the Checklist (~2 min)
 
Fast and clear? Go to [PHP Package Checlist](http://phppackagechecklist.com/), that is easy to ready and easy to follow.
This helped me to integrate workflow to all my packages in the start. I've selected [9 points](http://phppackagechecklist.com/#1,2,3,4,6,7,11,12,13)
that are must have to follow.

Some of them I've already mentioned. Other will follow in next 2 articles.

Before creating next package, just go trough it to remind yourself what is most relevant.

### Read a book (~10 hours)

Do you know *Clean Code*? If you really do, you know it has way too detailed and long content compare what information
you get by reading it. If just there would be something less than 16 years old, not about Java but PHP and 6 times shorter...
 
There is! [Mathias Noback](), used to be Symfony popularizator, wrote a book about of [Principles of Package Design](https://leanpub.com/principles-of-package-design).
It's great for deeper understanding of package maintenance. If this is your first OS step, read it 3 articles later. Now it would be to much detailed for you.

> There is actually contest about paper version on Czech and Slovak Symfony group Twitter - [go check it and win one]()!

---

## So our first steps is behind us

What have you learned today?

- That OS stands for open-source. You can find OSS as for OS software.
- How to create proper OS repository.
- How to add few lines there with a bit of git.

## What is coming next?

- How does package skeleton make your work much easier.
- I'll explain repository meta files and what are they for.
- How and why to use badges.
- How to target min PHP version and package version in composer.

## Hate me, please!

Did I got something wrong? Did you came across some error or wtf? Is it boring, too long or too vauge?
Just write me to comments. I want to make this series bulletproof and as much as helpful as possible.

You will help thousands of others if you help me fix one issue.

Thank you!
