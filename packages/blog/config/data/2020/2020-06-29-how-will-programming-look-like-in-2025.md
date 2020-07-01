---
id: 265
title: "How Will&nbsp;Programming look&nbsp;like&nbsp;in&nbsp;2025?"
perex: |
    We often read about best practices in coding, what framework has new features, or what is new in PHP X. How one can change this to that, why is this technique good or bad, or what new package you can download to your project.
    <br>
    That's only past or present.
    <br>
    <br>
    I'm just finishing the reading of [The Inevitable](https://www.amazon.com/The-Inevitable-Kevin-Kelly-audiobook/dp/B01EB3OR32), written by Wired magazine founder, that focuses solely on the future. Inspired by this book, today, **we look at the future of programming**.

tweet: "New Post on #php 🐘 blog: How Will Programming look like in 2025?"
tweet_image: "/assets/images/posts/2020/how_will_2025.png"
---

Today we fight with technical dept, whatever that means, old legacy code that is hard to maintain, expensive to change, but also the biggest power that generates money. We should upgrade code, integrate DDD, write tests, upgrade PHP to secure it, update the server, and automate the deploy.

So much daunting work and we didn't even look at those other dozens of projects our company maintains... or at least stores on our servers, somewhere. We need to hire an expert that helps us to improve each piece just a little. Not because they're not good, just because there are too many technologies we can use and even more code we have to maintain.

**But what the future holds?**

## IDE meets AI

In the future, the typing code will become easier. Our IDE will be super-powered by AI, which learns on anonymous data of other PHP projects and from all the open-source projects on Github and Gitlab.

Thanks to this AI, we'll start typing "class HomepageC..." It will know we're creating a controller. It will know we're using Symfony from `composer.json`, what version do we use and will suggest to autocomplete the rest of the code with `final` and strict types based on used PHP version also from `composer.json`. It will generate a template with the templating system we use and mimic the bare content found on other templates our project already has.

Thanks to the massive amount of anonymous data and data from public repositories, the AI will know the best practice about testing the controller to generate the controller test.

## ~~Best~~ Verified Practise

When we say "best practice", we don't mean what I or somebody else wrote in a post or book based on a few personal experiences. These opinions are often based only on coupled of projects and the single opinionated human mind with feelings.

Best Practise will shift to Verified Practise, based on real data related to 2 hard metrics - *technical debt* and *coding effectivity*. **Technical dept** will be financial that shows how much each line of code will cost in the future. Do you write fluent static code without classes with types? The line might show 10 $. Do you write final classes with types and one public method? The line might show 2 $.

These numbers will not be random, but based on the continual colossal amount of big data analysis - still anonymous - from all the private projects that want to use this feature. The code will be compared to money expenses that were needed to maintain and improve the code. Thanks to this feedback, the AI will also know what version is cheaper for your specific project.

It will know about the context of your project and compare the data accordingly. Do you have a CLI project? It will be compared to the code of other CLI projects in the field. Do you write a website? It will be compared rather to website projects.

**Coding effectivity** will be metric for the maintainability of the project. It will be measured from 0 to 100, as in 0 - code, that takes many hours to understand, even maybe days or weeks to change. A code with a score of 100 will be easy to understand to junior, and he or she can change the code almost instantly.

## IDE Verified Auto Suggest

The IDE will be aware of these metrics and will follow patterns in your code. When you start writing a piece of code that has coding effectivity 40-50, it will pop-up with the suggestion of code with the same result of effectivity 80-90. It will do the same job as Rector or PHPStan does today.

The performance will also be included, along with coding effectivity. The code performance will be automatically measured on every change in the background Docker container, and you'll be informed about any memory or time leaks. **It will be so precise that it will mark a specific line and character that caused the leak** and suggest fixing that you may accept.

## AST Refactoring

Refactoring will also be more powerful than today. It will be based on abstract-syntax-tree and will suggest the best refactoring you do right now based on anonymous data from all the public and private projects available.

Instead of "best practice" subjective claims, you will know that:

 - solution A will cost you 3 $ per line in technical dept, will be 95 in effectiveness and 45 in performance
 - solution B will cost you 1 $ per line of technical dept, and the effectiveness will be 70 and performance 50

Do you build a startup and want to verify your idea? You'll pick A. Is your company stable, and does it need to be robust in the future? Go for slower-growing yet more stable B.

**You'll not have to argue with your colleague** or with your boss why you should use this or this solution. You **compare the numbers** and then decide based on your priorities at that moment.

## Context-Aware Architecture

Your code will have context architecture. The AI will know when is the best to transit between contexts, based on data from other projects and their final costs of the transition. Do you bootstrap in WordPress? That's ok. Does your project become more popular, and you need to transition to another PHP framework that will handle your needs better? **IDE will suggest you migrate to Laravel. One-click, and it's done.**

Three years later, your project is growing, and you have a lot of manual integration of 3rd party services that are already native in the Symfony framework. IDE will suggest you migrate... click... and boom, you're on Symfony 9. Do you find out there are **not enough Symfony developers in the market** to keep up with development? 1-click and IDE will migrate to a framework that has enough developers at a reasonable price.

## Versioned StackOverflow Answers

IDE will look over your code and follow your coding habits. Do you usually write your feature in 15 minutes, but this one takes almost 2 hours now? In the following years, it will be that good that notices even a slight decrease in typing speed in a matter of seconds.

The IDE will then check your code, scan through StackOverflow answers, **matches the answer that has the same version as your `composer.lock`** and suggest to use this piece of code as the most valued answer.

Do you worry this piece of code is just copy-pasted random code and **will break your project**? The answer rank is not based on human voting anymore, but on actually click-rate when it was successfully used and merged into the project code.

### Tested Code Snippets

Also, the code snippets are tested by StackOverflow daily and also before copy-pasting to your project. With exactly your version of your local environment, so you can be sure the code works. Humans do not version these answers as in the past. **Code in the answer is upgraded on every release of the technology it uses**. Is there an answer for Symfony 5, and then the Symfony 6 will be released? The old code is upgraded with the AST recipe that was released with Symfony 6 and published as a new answer. That way, both human and IDE can work with it.

## Open-Source Funding by Activity

A new project that will connect companies and open-source contributors will be created. The open-source project will be funded by companies that use it. The developers who contribute will be funded by a unified system, based on incoming finances, without fee to cover the system expenses.

**Developers will be funded by their contribution that will be measured by AI-fined metrics** that will include the impact of the feature, amount of work, invested time, code effectiveness, etc. This way, the code will be developed much more consistently than on the free time of individual contributors.

**An open-source developer will become a new full-time position** funded by this project.

What do these companies get in reward? Promotion in the particular community, pre-release automated upgrade sets, and on-demand access to expert consultants who wrote the open-source projects they use.

## Framework Consolidation

~10 PHP frameworks we have now will be consolidated by the market to lower numbers. PHP communities will learn how to co-operate more, instead of working almost identical MVC copies.

**Thanks to AST-migrations it will be possible to switch from any to any other PHP framework**. This will allow us to **narrow the market to 3-4 frameworks**. If the framework migration is a matter of 1 click in your IDE, then there will be no competition based on history and long tail effect of dinosaurs, **but only on quality**.

Reduction of frameworks will lead to **framework profiling** - one framework will excel in API, the other in CLI, another in UX websites.

When the whole PHP community focuses on a lower number of frameworks, it will **allow us to invest the saved energy to developing new technologies and new features**.

## No Legacy PHP, just 1 Version

Thanks to automated AST migrations, there will be **only two versions of the PHP** - stable and dev. As the upgrade of any package or project will become as fast and cheap as one click, there is no reason not to upgrade to the latest version. It might take PHP community a year or two to synchronize on this. But when it does, **the new PHP will be released at the end of November**, and at the end of December whole PHP open-source ecosystem **will be using it as minimal version.**

## Fully Automated Instant Upgrades

The PHP code won't have to be upgraded manually. Each PHP version will have fully upgrade AST-based recipe that anyone can use to upgrade the code automatically. GitHub will handle these recipes, so when a new PHP version is released, GitHub will automatically send a pull-request to your repository. **Automated upgrades will not be just for PHP, but for any framework or package. Like Dependabot, we know now, but upgrading the code and solving all the BC breaks for you**.

### GitHub-Upgrader

If you don't want to click on all the merges yourself, **you can enroll in *automated upgrades* program so that GitHub will handle it for you**. It will also handle the releases and handle semver the proper way.

### Semver Automated

**There will be no arguments about if this is BC break of just a patch**, as it will be handled by AI that will analyze the code before and after and decide based on that. It will be that smart, that it will detect how significant impact the BC break has. **If it would not affect any code, it will be released as a patch.**

 ## Experience-based PHP RFC

The same BC break analysis will be possible for any RFC in PHP core code. Do you want to suggest typed constants? The AI will tell you how many projects from the top 10 000 on Github would break in decimal percent. Something similar is now done manually in a couple of RFCs.

### "BC Break" Redefined

The AI will also help you to generate migration AST recipes, so the instant upgrade can entirely handle the BC break. That would lead to a redefinition of "BC break" as we know it today. The BC break would only occur when automated upgrade cannot happen, and a human is needed to change the code.

### Try RFC Locally

Also, anyone can try the RFC feature locally right when the GitHub pull-request is created. How? The Github will automatically create a temporary release with a special dev-tag and push the PHP version to the package registry. You create a pull-request to add typed constants, send it on GitHub, and in 1 minute, you can run `sudo apt-get install php-dev-typed-constant` to get the PHP to your local machine.

This way, people will be able to try the feature before the merge and even before RFC voting. That way, even voting on features will be **based on real data and experience**, instead of emotions, opinions, and arguments.

<br>

## What the Future Holds?

In the future, our options will not be limited by our history, past choices, or fast-evolving technology that makes our code deprecated. Our options will be state of the art on the market on that specific day - just one click away.

This allows us to experiment more, verify our assumptions, and have real-life feedback.  It will lead to even more automated coding processes and inventions in language, patterns, or application architecture we can't even imagine today.

<br>

<blockquote class="blockquote text-center">
"The best way to predict the future<br>is to create it."
</blockquote>

<br>

Happy creating!

