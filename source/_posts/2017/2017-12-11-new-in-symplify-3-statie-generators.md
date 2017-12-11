---
id: 67
title: "New in Symplify 3: Statie Generator"
perex: '''
    ...
'''
tweet: "..."
tweet_image: "..."
related_posts: [29, 32, 33, 34]
---

In Statie was missing one important faeture. Posts were the only group elemetns that you could render as standlone page.

But what If wnat a web porfolio, not with posts but with features projects?



In Statie 3 there is new Generator features.
It allows you to add multiple elements with standalone pages.


E.g products, lectures, talks with details etc.





### How does it work


### Custom one


object


##  


## Date me!


```yml
parameters:
    generators:
        route_prefix: blog/:year/:month/:day
```

To make this work, add date to the start of filename:

```
_lectures/2018-01-30-use-open-source-statie-for-open-blogging.md
```
=>

```
2018/01/30/use-open-source-statie-for-open-blogging
```


### On Real Projects


Pehapkari.. lecture this and that




### How to upgrade from Statie 2 to Statie 3?

See this PR on TomasVotruba.cz and Pehpakari.cz

https://github.com/pehapkari/pehapkari.cz/pull/358
https://github.com/pehapkari/pehapkari.cz/pull/358

and nothing surprise you