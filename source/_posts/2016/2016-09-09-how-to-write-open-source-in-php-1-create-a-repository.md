---
id: 12
title: "How to write open-source in PHP 1: Create a repository on Github"
perex: '''
    Do you have some code you want to share but you don't know exactly how? Well, writing open-source is complex process.
    In this series, I'll break it down to <strong>the smallest steps possible</strong>, so that you
    can <strong>start your own OS project with zero-knowledge</strong> (OS = open-source).
    Ready? Let's start with creating a Github repository!
'''
related_items: [13, 25, 26, 45, 54]
tweet: "How to write #openSource in #php 1: Create a repository on @Github"
---

## Meet Github, OS's best friend

If not already, [register on Github](http://github.com). It's a place where all open-source lives and breathes. For free!

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

That's all you need now. Hit "Create repository" and you are done!

## Little book of git

Now we practise first few git lines.

### Get to the right place

Move to the directory, where you want to host your package locally.
Open command line or Terminal in PHPStorm. Actually the PHPStorm way will open terminal already in right place. So you don't have to browse directories via `cd` command.
And call these commands there.

Do you know git?

Just follow commands, that appeared on your Github repository and <a href="#your-code-is-online">skip to next headline</a>.

    echo "# OpenSourcePackageDemo" >> README.md
    git init
    git add README.md
    git commit -m "first commit"
    git remote add origin git@github.com:TomasVotruba/OpenSourcePackageDemo.git
    git push -u origin master

### You don't understand those geek lines? I'll explain

These commits can be divided into 2 groups:

1. to setup repository, **just once**
2. to add some code, **use repeatedly**

#### 1. Setup repository

Create an empty repository git repository

    git init

Add ONLINE address where we want publish your code

    git remote add origin git@github.com:TomasVotruba/OpenSourcePackageDemo.git

#### 2. Add some code

Create a file README.md and add "Unziping Package" in it (this is just command line for geeks, I do this manually in my PHPStorm of course)

    echo "Unziping Package" >> README.md

Tell git to NOTICE this file to be added later

    git add README.md

Group all NOTICED files to single COMMIT (group of changes)

    git commit -m "first commit"

Send ALL COMMITS online. Now your local system and Github repository are synced 1:1

    git push -u origin master


<a name="your-code-is-online"></a>

## Your code is online!

Just feel the smell of success.

---

## Do you want get deeper than that? Check the Checklist (~2 min)

Fast and clear? Go to [PHP Package Checklist](http://phppackagechecklist.com/), that is easy to read and easy to follow.
This helped me to integrate workflow to all my packages in the start. I've selected [9 most important points](http://phppackagechecklist.com/#1,2,3,4,6,7,11,12,13).

Some of them I've already mentioned. Other will follow in next 2 articles.

Before creating next package, just go trough it to remind yourself what is most relevant.

---

## So our first step is behind us

What have you learned today?

- That OS stands for open-source. You can also find *OSS* as for *open-source software*.
- How to **create proper OS repository**.
- How to **add few lines there with git**.

## What is coming next?

- How does package skeleton make your work much easier.
- What are **repository meta files**.
- How and why to **use badges**.

---

## Hate me, please!

Did you came across some error or wtf? Is it boring, too long or too vague?
Just write me a comment. I want to make this series bulletproof and as much as helpful as possible.

**You will help thousands of others if you help me to fix one issue.**

Thank you!
