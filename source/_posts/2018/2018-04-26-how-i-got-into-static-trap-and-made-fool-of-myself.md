---
id: 99
title: "How I Got into Static Trap and Made Fool of Myself"
perex: |
    ...
tweet: "..."
---

I build quite complex Sniff and Fixers for [Symplify\CodingStandard](https://github.com/symplify/codingstandard)


## Why Avoid Static Method Right from the Beggining?

### 1. Seduction to Vendor Lock 

### 2. Copy-Cat Coder

> A copycat crime is a criminal act that is modelled or inspired by a previous crime that has been reported in the media or described in fiction. Contents. 

- https://en.wikipedia.org/wiki/Copycat_crime



## What happened - open-source story?

Well, you've probably heard about hexagonal architecture (@matthis link), using adapters for 3rd party code and that dependency injection 




## How to Refactor Static to Dependency Injection?

<a href="https://github.com/Symplify/Symplify/pull/680" class="btn btn-dark btn-sm">
    <em class="fa fa-github fa-fw"></em>
    See pull-request #680
</a>

<a href="https://github.com/Symplify/Symplify/pull/693" class="btn btn-dark btn-sm">
    <em class="fa fa-github fa-fw"></em>
    See pull-request #693
</a>

### 1. Turn Static Methods to Factories 

https://github.com/Symplify/Symplify/pull/693/files#diff-77b0aab940d6b4e15bf451af42434261R114

### 2. use them in every level

- https://github.com/Symplify/Symplify/pull/722/files#diff-0e3df0267b61eff79fba71ed7805b40bL127
- https://github.com/Symplify/Symplify/pull/722/files#diff-ef794929ea2203a9afc69c3dd74fc4fcR143

--

Pass them info object if needed factory, same happens in controller, 


## Setup Coding Standard and Forget

<a href="https://github.com/Symplify/Symplify/pull/722" class="btn btn-dark btn-sm">
    <em class="fa fa-github fa-fw"></em>
    See pull-request #722
</a>

- to prevent this 10 hours of trauma every happening again, I made a sniff that will look after your code

it helped met o show all that static code
https://github.com/Symplify/Symplify/pull/722/files#diff-a8b950982764fcffe4b7b3acd261cf91R85