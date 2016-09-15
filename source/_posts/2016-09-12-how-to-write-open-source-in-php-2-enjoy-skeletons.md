---
title: "How to write open-source in PHP 2: Rise value of your package with help of skeleton"
categories:
    - Open source
    - PHP
perex: > # multi-line string
    After creating a repo, we have to fill it with something useful. Our code! Of course, but we also need some <strong>metadata files</strong>.
    What are they for? Is there some prepared code we can use? What are badges for? <strong>I will answer these today.</strong> 
thumbnail: "php-podcast.png"
lang: en
---

<p class="perex">{{ page.perex|raw }}</p>

Other programmer might want to use your package. They are usually looking for **long term value**. 
To estimate the value they need **to answer 4 important questions**.

1. **What is quality of package?** 
2. **Does it solve my issue?**
3. **Is it trustworthy?** 
4. **How well maintained is it?**

Even if you know your code is the best and the cleanest, if I don't trust you, I will never use it.

I will let you think about them a little bit. We will relate with specific files to them in second part of this article.

## Use solid skeleton → start solid brand

Now, the first step you can positively influence all 4 answer is **using a skeleton** with prepared metadata files. 

Guys from [The PHP League](https://thephpleague.com/) already did the job for you and created a [skeleton](https://github.com/thephpleague/skeleton) package.
 
From my point of view, **there is too much unnecessary code**, so I've prepared [clear version for you](https://github.com/TomasVotruba/open-source-pakage-skeleton).

Pick one you understand the best.

## Lets import the code

So we have some skeleton repository on Github, but how to get that into your open-source project?

Github now support importing code from other repositories by just adding `/import` to your repository.

So for repository:
 
    https://github.com/TomasVotruba/OpenSourcePackageDemo/
 
the import url is:
 
    https://github.com/TomasVotruba/OpenSourcePackageDemo/import

Paste your skeleton url there:
 
    https://github.com/TomasVotruba/skeleton-for-starters
    
And run import. It might take some time.

After the process of import, just get it to your local machine:

```bash
git pull
```

Done! Great job.


### Great for start, yet obsolete later 

This skeleton is great for start and to learn about metadata files. 

But when I create my package now, **I just copy my most recent package I made**, delete `/src` and `/tests`
directories and I'm ready to roll. This is because:
 
- I upgrade my package more often then some `skeleton` package
- and because my preferences and required code are evolving
    - e.g. I new PHP version is out, I tune my continuous integration (CI) setup etc.


## What is purpose of these files

Now we look on every directory and file and how it's related to 4 key questions.
Just to remind you, the end user is interested in:

1. Quality - **What is quality of package?** 
2. Usability - **Does it solve my issue? Is it easy to use?**
3. Trust - **Is it trustworthy?** 
4. Maintenance - **How well maintained is it?**


### `/src` directory

*Meaning*

- all your PHP source code will go here

<span class="arrow-down-on-list">↓</span>

*Profit*

- musthave :) 


### `/tests` directory

*Meaning*

- all tests for your code in `/src`
- basically 1:1 mirror, just every file has `Test` suffix, e.g.
    - `src/Cleaner.php`
    - `tests/Cleaner.php`

<span class="arrow-down-on-list">↓</span>

*Profit*

- **Quality**: tested code is perceived better quality
- **Trust**: I don't have to hope that code works, **I can trust the code** 


### `.gitattribues`

*Meaning*

- here are all files that are ignored by composer
- when somebody will install your package via `composer require you/your-package`, they won't get these files downloaded to `/vendor` directory 
- usually its metadata files and tests, because application of end user does not need them

<span class="arrow-down-on-list">↓</span>

*Profit*

- **Usability**: Since your package save some internet trafic and space on hard drives, it's a bit more usable.


### `.gitignore`

*Meaning*

- here are files, that you will have locally but won't be uploaded to the remote git repository
- for packages ignore `composer.lock`, for applications rather not - on Stackoverflow you can find [more detailed answer](http://stackoverflow.com/questions/12896780/should-composer-lock-be-committed-to-version-control)
- also `/vendor` is there, as dependencies are installed by composer

<span class="arrow-down-on-list">↓</span>

- **Trush**: Without this I would not trust you know anything about open-source. 


### `.scrutinzer`

- configuration for [Scrutinizer](https://scrutinizer-ci.com/) - code quality and code coverage tool
- to enable it, [login](https://scrutinizer-ci.com/login) and new repository
    - I recommend login via Github, since it adds hooks to your repository 
- it would be triggered every time your commit to master or create a PR

<span class="arrow-down-on-list">↓</span>

<div>
    <img src="/../../../../images/posts/2016/open-source/quality-and-coverage.png" alt="Code quality and coverage badges in README">
    <br>
    <em>Code quality and coverage badges in README</em>
</div>
<br>

- **Quality**: Tests are fine, but with 5 % coverage, they have to added value. When you have 90% coverage, you got attention.
    Also there is some code quality score from 0 to 10, which tells about duplicated code, long methods etc. Also factor to get attention.
    It's easy to get 10 with bit of practise.


### `.travis`

- configuration for [Travis](https://travis-ci.org/) - continuous integration tool for tests
- to enable it, go register there and add the repository 

<span class="arrow-down-on-list">↓</span>

- **Trust**: Do you have test but you don't run them for every change? How can I know the code works? 

### `composer.json`

- list of dependencies
- also configuration for [Packagist](https://packagist.org/), where you need to add your package, so it can be installed by others
- to enable it, you have to:
    - go there
    - add repository
    - go to settings of package, **Integration and services** and **Add Service**
    - add there your name and token you in find in your [user profile](https://packagist.org/profile/) on Packagist

### `LICENSE`

- code for license, good to add as every country has different default approach, when this file is missing
- [MIT](https://opensource.org/licenses/MIT) is the easiest to understand open-source license

<span class="arrow-down-on-list">↓</span>

- **Usability**: With licence, I know what I can do with the code. Usually everything.

### `phpunit.xml`

- configuration for [PHPUnit](https://phpunit.de/) - testing tool
- this can be used either by end user or Travis 

<span class="arrow-down-on-list">↓</span>

- **Usability**: I can run `vendor/bin/phpunit` with no manual configuration. It makes life easy.

### `README`

- last but the most important - your welcome article for user
- THE MOST IMPORTANT FILE IN THE PACKAGE!
- Don't worry. We'll talk about writing a good readme later. 

<span class="arrow-down-on-list">↓</span>

- **Usability**: If I understand the usage, I can rely to my issue I want to solve. 
- **Trust**: Having code quality, Travis and coverage badges helps to identify the quality of the package.

So that are all files and their purpose.

## No time! Fast! Now! → Tell your story with an image

Today people are rather scanning then reading. That's why badges are so important!

Look on these 2 - what information can we get?

<img src="/../../../../images/posts/2016/open-source/badge-2.png" alt="Confusing badge">

- Test are passing - **GOOD**
- There is stable tag with "?" coverage - **CONFUSING**
- Master has 89% test coverage - **GOOD**
- Last version is probably 2.5, but not sure. Do they update manually? - **CONFUSING**
- Why is master promoted on first place? Should I use that? - **CONFUSING**

<small>Can be found on [Doctrine2 repository](https://github.com/doctrine/doctrine2).</small>

<img src="/../../../../images/posts/2016/open-source/badge-1.png" alt="Well informative badge">

- Test are passing - **GOOD** 
- Code quality is 10 - **GOOD**
- Code coverage 93% test coverage - **GOOD**
- It has 166 downloads. Here it depends on the age of package. → Go check release date! - **GOOD**
- It's tagged and has stable version. - **GOOD**

## What have we done today?

- Where to go when **starting a new repository**
- What is **purpose meta files**.
- How to **enable online services** that help us to build better code. 

## What's next?

- We'll peak on **coding standards**.
- How do **releases** work a what is **semantic versioning**. 
- How to **pick min PHP version and package versions in composer**.

---

## Hate me, please!

Did you came across some error or wtf? Is it boring, too long or too vague?
Just write me a comment. I want to make this series bulletproof and as much as helpful as possible.

**You will help thousands of others if you help me to fix one issue.**

Thank you!
