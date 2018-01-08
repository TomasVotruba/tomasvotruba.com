---
id: 71 
title: "Clean and Decoupled Controllers, Commands and Event Subscribers Once and for All with Delegator Pattern"
perex: '''
    Do you write your application for **better future sustainability** or just to get paid for it today?
    If the first one, you care about design patterns. I'm happy to see you!
    <br>
    <br>
    Today I will show you **why and how to use *delegator pattern*** in your application so it makes it to the pension.
'''
tweet: "New post on my blog: Clean and Decoupled Controllers, Commands and Event Subscribers Once and for All with Delegator Pattern #php #cleancode #symfony #icology"
tweet_image: "/assets/images/posts/2018/delegator/trash-everywhere.jpg"
related_items: []
---

<br>

<blockquote class="blockquote text-center">
    Every code is trash!
</blockquote>

<br>

You'll see. But before we dig into code... what are the reasons to write sustainable code and how it looks like?

## Why Should You Care About Future Sustainability

There are 3 levels of developers by the time-frame focus they work on. Every group has it's advantages and disadvantages. You'll soon see which one you fit in.

### 1. Developers who **Code for NOW**
 
This project. Single site for 2018 elections. Microsite for new product release in 2019. Include anything that is hype in socials last year.

**If the code would be a trash** (literally!), they'd throw everything to 1 bag or maybe right in the city streets or nature. **Someone 
else will handle cleaning up the city** #yolo

<img src="/assets/images/posts/2018/delegator/trash-everywhere.jpg" class="img-thumbnail">

### 2. Developers who Code for next 1-2 YEARS

The project has tests, continuous integration, uses stable packages with 1.0 release. It's startup or a project with profit. The team is fine and slowly growing. It's their first or second project and they try to take good care about it, with experiences they have.  

They don't make any mess around the city and **put all trash to 1 trash bin**. Take them out regularly once a week. They're nice to the world. Well, at least at first sight.

<img src="/assets/images/posts/2018/delegator/orbit-junk.jpg" class="img-thumbnail">


### 3. Developer who Code for next 5-10 YEARS - Future Sustainability

...or at least with that mindset in their minds. The code won't probably work with PHP 9.0, but they do their best to make it as easy as possible to do so. 

They have great experience with handful of project described in previous group. They already worked on 5 open-source projects **they need to last as long as possible without as little maintenance as possible**.
 
**To the trash again...**    
    
It's like recycling plastic bags, glass bottler and papers. 

You put effort to it:

- create space in your home to keep 3 separated trash bins, 
- explain everyone to use them and split every product to own bin
- and when it's full you take these bags out for 5 minutes walk to their destination.  

<img src="/assets/images/posts/2018/delegator/manage-it-right.jpg" class="img-thumbnail">

**Though you never see the trash again, you believe it's good for your future self and for your children**, to keep planet clean and away from trash lands. Economists would call it *positive externality*. 


<br>

Now you know **why it's good to separate waste** (= code), let's get to real code.  

## What happens when application grows?

Let's imagine a middle ages project - 4yeras:
2014-2018

(also add code)

- yera 0 - oh put this in controller, it's fast, in documnetaion and easy to extends to anotehr - it's just list of products
- in 1 year from proet start, you start using commands, and want to recount prices there
- in 2 yeras, mobile and REST API comes, so you need to creat special Rest controlelrs providign list of products
- in 3 years, you're slowly thinking about AI and product recommentation and you're using EventSubscribers - saving information about user in Redis and using machine learning with eveolution algorithm suggesting best products right away - it's just 

## where you en up?

## where do you want to end-up

...this decopuled

## Does business give you millions of money for refacgoring playign? Nope!


...do it right from the first step

Do you separate your trash after you see plastic and paper products are being more and more expensive and there are dumps all over you country?


No. You think for the future **prevention**

<live as for today, platn for the fuure> quote...



Same can be applied to your code!



This is what we did in Lekanara.cz, czech biggest drug seller running on Nette 2.4, Doctrine 2.5, with monorepo approach (link that post with local split packages) and bunch of other tools.

10 years it lasted, this should get anoter 10 with not major changs

When you start with the best known approach possible, you'll probably endup in well grown project that you'll love to contribute the older it gets. Just like children :)


Happy Growing!
