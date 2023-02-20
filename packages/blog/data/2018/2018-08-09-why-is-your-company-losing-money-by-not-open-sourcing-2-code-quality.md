---
id: 130
title: "Why is Your Company Losing Money By Not Open Sourcing 2: Code Quality"
perex: |
    There is more high-quality code in open-source than in closed-source. Open-source code is rarely rewritten from the scratch investing loads of time and effort - [apart 1st version because it's designed to be dropped](https://blog.codinghorror.com/version-1-sucks-but-ship-it-anyway). This case is not so rare in the private sector in long-term projects.


    **Rubber ducking. Standard-bias of public behavior. Social learning. Embodied know-how.** Values natural for open-source, yet seen only in high-standard private coding. Why is that? And **how to make your project benefit from these values**?
tweet: "New Post on my Blog: Why is Your Company Losing Money By Not Open Sourcing 2: Code Quality #symfony #nakedman #rubberducking #psr #fig #github #free"
---

## 1. Cheap Standards vs. Expensive Private Few-Person-Based Solutions

### Why Even Bother With Standards?

Standards are not just a set of rules to bully your individual approach as a programmer or as a team. Spaces and tabs are not here to make teams, although the in-group and out-group effect is strong. Standards are brought up naturally by people, who do so much repetitive work, they want to automate it. **They see a pattern a small majority is using to save time from too little details and invest in values that matter for them instead**. Whether it's sustainable code people will love so much they'll want to grab a beer with you or 1-minute deployment of your application.

That's the reason all these PSR-2, PSR-4 etc. were created. Huge thanks to all guys from FIG - **especially for [monthly summaries on Medium](https://medium.com/php-fig)**.

Btw, do you know [what does *PSR* stand for](https://www.php-fig.org/faqs/#what-does-psr-stand-for)? *PHP Strict Rules...*? That's would terrible. It's **PHP Standard Recommendation**. Read it as:

<blockquote class="blockquote text-center">
    "Here is a baseline for a thing you don't want to think about. You can work with it and most people will understand it without you explaining anything. Just like that."
</blockquote>

When I talk about standards, there lays much more value beyond PSRs. That's how we get to *the money*.

### Where is Your Company Losing Money?

If your team start a big project, you need to resolve many *first-steps*:

- What rules should belong to coding standard?
- How to deploy and where?
- Should you use MVC or plain PHP?
- How to design architecture that will last over 5 years?
- How to make sure that after you leave, others will still understand and effectively contribute the code?
- etc.

**You can waste thousands of company money by reinventing everything from scratch. Everything that community already solved for you.**

By choosing a framework that is not much adopted or doesn't respect standard, **naturally leads to extra pointless expenses**:

- To pay somebody to teach them the framework
- To pay people to learn the specific non-standard behavior of the framework
- To pay for bugs that will be created by not following a framework that has such practices embodied in the code
- To pay for the eventual rewriting of your coupled code in next 10 years to a new framework the next team will choose, after the first team will leave you for a better experience - personally, I've seen a code of 8 such projects.

### Frameworks Teach Bad Practises

I hear you: "but most PHP frameworks teach people bad practice just to vendor lock them". I [agree](https://matthiasnoback.nl/2014/10/unnecessary-contrapositions-in-the-new-symfony-best-practices). **But it's still easier to learn what works and what doesn't from standard frameworks. Why? The amount of paths to create a website is limited, so is the scope we have to orientate. Compared to plain PHP where you can use zillion ways to create a website.**

It's like having parents - they bring you something good and something bad. Yet it's still better to learn from them than from no parents at all.

### Get Embodied Know-How For Free

So instead of putting money into lecturers that will teach you rarely used technologies, you can adopt standards that are natural in open-source and to a big part of the PHP community. **That way the know-how is embedded into your code and you don't need a lecturer to explain it.**

What does it mean? Imagine Symfony. Just by using this framework, you'll get access to many PSRs that Symfony uses. That allows you to use many PHP packages that support these PRSs. Instead of writing Doctrine yourself from scratch, just use it and save yourself loads of money.

## 2. How to Get All Services for Your Project for 0 $ a Month?

From Travis, Github, Gitlab, CircleCI, Github Pages, to Slack, Cloudflare and many more - they give you **free services for open-sourced code**. They probably believe most people will start there for free. Then they grow and change to closed-source when the code base is so huge to be kept at high standards. Maybe they need to do some nasty things that people should not know.

Instead, you can go vice-versa. Drop Jenkins, private Github, private Travis and all the other services and **go from 5000 $ of expenses to 0 $ in one week**.

### All About the Money

A reminder this is not solely about the money. You can save money for private Github and Travis just by migrating to private Gitlab that you can even host. Many friends and projects of I know work this way. But the benefit I talk about works the best in one wave with all other benefits, like embodied know-how, mentoring from experts and [open-source hiring](/blog/2018/07/26/why-is-your-company-losing-money-by-not-open-sourcing-1-hiring/).

## 3. How to Pay Top PHP Mentors 0 $

Tom Preston-Werner, one of Github founders [wrote](https://tom.preston-werner.com/2011/11/22/open-source-everything.html):

<blockquote class="blockquote">
    "<strong>Smart people like to hang out with other smart people. Smart developers like to hang out with smart code</strong>. When you open source useful code, you attract talent. Every time a talented developer cracks open the code to one of your projects, you win. I've had many great conversations at tech conferences about my open source code. Some of these encounters have led to ideas that directly resulted in better solutions to problems I was having with my projects. In an industry with such a huge range of creativity and productivity between developers, the right eyeballs on your code can make a big difference."
</blockquote>

The best people don't go after money, nor powerful Docker cluster setup, nor extra 5 days of holiday. These might be important elements and their absence won't appreciate, but the best programmers strive for something extra.

**The best programmers go for know-how, credit, and impact.**

### Impact or 5 Days of Extra Holiday?

Imagine this: you contribute a feature to a private project for selling cars. If you're lucky, 2 more programmers will see your code during the code review, then it gets merged and forgotten until there is a bug with your name in `git blame` line.

On the other hand, **if you contribute to a Symfony project, around 10-20 people on average will see your code pull-request**. And if it gets merged? **Your code will be downloaded to [110 000+ applications every day](https://packagist.org/packages/symfony/http-kernel/stats)**.

Of course, it's nonsense to compare your *about-to-be-open-sourced* project with 13-years old Symfony. Yet 10 downloads a week is a huge success compared to current zero. This number will be only growing if you continue the package development.

I remember EasyCodingStandard had 3 downloads a day (my, my girlfriend and my mum ;)). Now a 1,5 year later it has over [500 installs a day](https://packagist.org/packages/symplify/easy-coding-standard/stats).

### Give Me the Mentors

So where do the mentors come from? They're people using your package or - and that's more common than you think - **a people who love to spread their gene code into commits of your package by** cleaning it up or adding their package that will help you with the code.

Here are few examples of *win-win* situation I noticed on Github:

- [Tomas Votruba fixing coding style in php-parser](https://github.com/nikic/PHP-Parser/pull/408)
- [Gabriel Caruso and his 110 PRs to php-src](https://github.com/php/php-src/pulls?q=is%3Apr+author%3Acarusogabriel+is%3Aclosed+sort%3Aupdated-desc)
- [Ondrej Mirtes adding PHPStan to CakePHP](https://github.com/cakephp/cakephp/pull/9943)

**One could say that by open-sourcing a project you'll get the attention of alfa-males programmers.**

Such programmers will give you contributions for free. They'll talk with you for free, they'll suggest you or send you features for free and that's definitely less than what your company is paying programmers for the work. Well, not for free, but **for the credit that open-source project's popularity will give them back**. They wouldn't do it if you'd remove their names from commits messages.

### Be Opened to Free Fixes

By open-sourcing, you also say: "Do we have a bug? Come and fix it, please". One example for all:

I was using [Naucmese.cz](http://naucmese.cz) a lecture from anyone to anyone portal about 5 years ago. I found a bug. 2 bugs. After the 3rd bug, I wrote them "just open-source the project and I'll fix it for free". I was frustrated by those bugs that **I'd be happy to invest 30 minutes of my free time to fix them and make using the application much nicer experience**. If that project would be open-sourced, they'd get 2-3 hours per week of my work for free in that time. Instead, they hired me but that's another story.

My approach still stands for other projects, just write me about freshly open-sourced code and I'd be happy to be your first contributor on GitHub.

## 4. The Coding Naked Man

Do you know [rubber ducking](https://blog.codinghorror.com/rubber-duck-problem-solving)? In short: **it's way of working with an invisible second person next to you that increases the quality of code you produce**.

I've experienced this myself in many layers:

When I was working in Brno on freelance and had a small flat just for myself, I spent 1/3 of my work time playing games and watching porn.
I wanted to stop, I wanted to focus, to code, to work, but it was extremely difficult for every single of 3 months I was there alone.
Then I moved to coworking center into open-space and suddenly - it stopped. I began to be 200 % more productive in the first week. How was that possible? Well, obviously I was shy to watch porn and masturbate in the public so I worked that 1/3 of time, but there is more. **Just by having people around me, I wanted to be like them, to work, to bring values. In such a place as coworking hub, it was standard to work and to do meaningful work**. So I adapted for better just by being there.

The same goes for *Coding Naked Man* principle. When I put my first code open-source (after 2 years of hiding in fear from rejection and being a fraud) on GitHub in 2010, I was super ashamed for the code. It felt like I'm putting my favorite porn on school page where every single schoolmate could see it.

By simply putting the code out I realized that I can do much better with the code. But what's the most important, **I started to see path what exactly can I learn** just by seeing other similar projects.

### Hide Your Private Shame

You can probably see this principle work even in your company. Have you ever seen an open-source project where maintainers say, that you can use static methods, public properties, and service locators if you know why? I don't say that happens, but it's very rare.

On the other hand, when I code-review private projects on such code smells, I get almost brain-washed answers like:

 - "we had to use it there",
 - "we know it's not optimal, but...",
 - "we didn't have time",
 - "we know we have to keep it only in that place".

But when I ask more, I see they have no deeper idea why. They only know the 1 reason to write bad code even if they really don't have to. **It's like a candy you steal but nobody knows about it**. Nobody else will ever see this code, and if they'll do, you'd be already in that another company.

And it's super difficult to think open while being closed. But by having code open-source, you'll adopt this mindset by natural in just a few months. After years, I can now feel if I'm hacking something the wrong way [that will hunt me down 2 years later](/blog/2018/04/26/how-i-got-into-static-trap-and-made-fool-of-myself/) or if the code really can't be better.


<br><br>

And that's it!

Happy open-sourcing
