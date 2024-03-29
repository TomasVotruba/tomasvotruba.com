---
id: 13
title: "How to write open-source in PHP 2: Rise value of your package with help of skeleton"
perex: |
    After creating a repo, we have to fill it with something useful. Our code! Of course, but we also need some **metadata files**.
    What are they for? Is there some prepared code we can use? What are badges for? <strong>I will answer all these questions today.</strong>


updated_since: "February 2021"
updated_message: |
    The thephpleague/skeleton is falling behind last 3 years, promoting deprecated practise, so it was dropped. Scrutinizer to PHPStan, ECS added.
---

Other programmers who want to use your package are usually looking for **long term value**.
To estimate the value they need **to answer 4 important questions**.

1. **What is quality of package?**
2. **Does it solve my issue?**
3. **Is it trustworthy?**
4. **How well maintained is it?**

Even if you know your code is the best and the cleanest, if they don't trust you, they will never use it.

I will let you think about them a little bit. We will relate with specific files to them in second part of this article.

## Use solid skeleton → start solid brand

Now, the first step that can positively influence all the 4 answers is **using a skeleton** with prepared metadata files. Guys from [The PHP League](https://thephpleague.com) already did the job for you and created a [skeleton](https://github.com/thephpleague/skeleton) package.

## How to get Skeleton code to your local Repository in 4 steps

1. Go to repository on Github and click on *Clone or download*
2. Then *Download a ZIP*
3. Unzip the zip file to your local repository
4. And push new files to Github

    ```bash
    git add .
    git commit -m "add metadata files"
    git push origin master
    ```

### Great for start, yet obsolete later

This skeleton is great for start and to learn about metadata files.

But when I create my package now, **I just copy the most recent package I made**, delete `/src` and `/tests`
directories and I'm ready to roll. This is because:

- I upgrade my packages more often then some `skeleton` package
- and because my preferences and required code are evolving
    - e.g. A new PHP version is out, I tune my continuous integration (CI) setup etc.


## What is the Purpose of these Files?

Now we look on every directory and file and how it's related to the 4 key questions.
Just to remind you, the end user is interested in:

1. Quality - **What is quality of package?**
2. Usability - **Does it solve my issue? Is it easy to use?**
3. Trust - **Is it trustworthy?**
4. Maintenance - **How well maintained is it?**


### `/src` directory

*Meaning*

- all your PHP source code will go here

*Profit*

- musthave :)


### `/tests` directory

*Meaning*

- all tests for your code in `/src`
- basically 1:1 mirror, just every file has `Test` suffix, e.g.
    - `src/Cleaner.php`
    - `tests/CleanerTest.php`

*Profit*

- **Quality**: tested code is perceived better quality
- **Trust**: I don't have to hope that code works, **I can trust the code**

### `.gitattributes`

*Meaning*

- here are all files that are ignored by composer (using the `export-ignore` attribute)
- when somebody will install your package via `composer require you/your-package`, they won't get these files downloaded to `/vendor` directory
- usually its metadata files and tests, because application of end user does not need them

*Profit*

- **Usability**: Since your package save some internet traffic and space on hard drives, it's a bit more usable.

### `.gitignore`

*Meaning*

- here are files, that you will have locally but won't be uploaded to the remote git repository
- for packages ignore `composer.lock`, for applications rather not - on Stackoverflow you can find [more detailed answer](https://stackoverflow.com/questions/12896780/should-composer-lock-be-committed-to-version-control)
- also `/vendor` is there, as dependencies are installed by composer

*Profit*

- **Trust**: Without this I would not trust you know anything about open-source.

### `ecs.php`

*Meaning*

- config for [Easy Coding Standard](https://github.com/symplify/easy-coding-standard) - the easiest tool for coding standard
- it checks code to keep it in one compatible coding standard format

*Profit*

- **Quality**: coders can contribute in any format their like and the coding standard tool will take care of it for them

### `phpstan.neon`

*Meaning*

- configuration for [PHPStan](https://phpstan.org/) - *the* best static analyzer to PHP
- it run on every commit, checking obvious mistakes for you, like call on possible `null`

*Profit*

- **Trust**: you and your contributors can stop thinking about detailed code and go a relax - everyone can contribute easily
- **Quality**: tested code is perceived better quality

### `composer.json`

- list of dependencies
- configuration for [Packagist](https://packagist.org), where you need to add your package, so it can be installed by others
- to enable it, you have to:
    - go there
    - add repository
    - go to settings of package, **Integration and services** and **Add Service**
    - select "Packagist" and add your name and token from your [user profile](https://packagist.org/profile)

### `LICENSE`

*Meaning*

- license goes here
- it's important to have it as every country has different default approach, when this file is missing
- [MIT](https://opensource.org/licenses/MIT) is the easiest to understand open-source license

*Profit*

- **Usability**: With licence, I know what I can do with the code. Usually everything.

### `phpunit.xml`

*Meaning*

- configuration for [PHPUnit](https://phpunit.de) - testing tool
- this can be used either by end user or Travis

*Profit*

- **Usability**: I can run `vendor/bin/phpunit` with no manual configuration. It makes life easy.

### `README`

*Meaning*

- last but the most important - your welcome article for user
- THE MOST IMPORTANT FILE IN THE PACKAGE!
- Don't worry. We'll talk about writing a good readme later.

*Profit*

- **Usability**: If I understand the usage, I can rely to the issue I want to solve.
- **Trust**: Having code quality, Travis and coverage badges helps to identify the quality of the package.

So that are all files and their purpose.

## No time! Fast! Now! → Tell your story with an image

Today people are scanning the text rather then actually reading. That's why badges are so important!

Look on these 2 - what information can we get?

<img src="/assets/images/posts/2016/open-source/badge-2.png" alt="Confusing badge">

- Test are passing - **GOOD**
- There is stable tag with "?" coverage - **CONFUSING**
- Master has 89% test coverage - **GOOD**
- Last version is probably 2.5, but not sure. Do they update manually? - **CONFUSING**
- Why is master promoted on first place? Should I use that? - **CONFUSING**

<small>From [Doctrine2 repository](https://github.com/doctrine/orm/blob/master/README.md).</small>

<img src="/assets/images/posts/2016/open-source/badge-1.png" alt="Well informative badge">

- Test are passing - **GOOD**
- Code quality is 10 - **GOOD**
- Code coverage 93% test coverage - **GOOD**
- It has 166 downloads. Here it depends on the age of package. → Go check release date! - **GOOD**
- It's tagged and has stable version. - **GOOD**

## What have we Done Today?

- Where to go when **starting a new repository**.
- What is **the purpose meta files**.
- How to **enable online services** that help us to build better code.

## What's next?

- We'll peek on **coding standards**.
- How do **releases** work a what is **semantic versioning**.
- How to **pick min PHP version and package versions in composer**.

<br>

Happy coding!
