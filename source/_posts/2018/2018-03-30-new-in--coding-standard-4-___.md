---
id: 87
title: "New in Coding Standard 4: Long Lines Breaks Automated and 6 new Fixers"
perex: |
    ...
tweet: "New Post on my Blog: ..."
tweet_image: "..."
related_items: [86]
---

## 1. Line lneght fixer set!!!

@todo also mention old ones

<a href="https://github.com/Symplify/Symplify/pull/743" class="btn btn-dark btn-sm mt-2 mb-3">
    <em class="fa fa-github"></em>
    &nbsp;
    Check the PR #743
</a>

<a href="https://github.com/Symplify/Symplify/pull/591" class="btn btn-dark btn-sm mt-2 mb-3">
    <em class="fa fa-github"></em>
    &nbsp;
    Check the PR #591
</a>

<a href="https://github.com/Symplify/Symplify/pull/585" class="btn btn-dark btn-sm mt-2 mb-3">
    <em class="fa fa-github"></em>
    &nbsp;
    Check the PR #585
</a>


typical cosntuctor situaino to solve

- more argumetns, break?
- classes decoupled, inline?
- more arumetns...


## 2. 3 options to Configurable with Breakers & Inliners

<a href="https://github.com/Symplify/Symplify/pull/747" class="btn btn-dark btn-sm mt-2 mb-3">
    <em class="fa fa-github"></em>
    &nbsp;
    Check the PR #747
</a>


```yaml
# easy-coding-standard.yml
parameters:
    max_line_length: 100 # default: 120
    break_long_lines: true # default: true
    inline_short_lines: false # default: true
```    
    
    


## 3. Keep Legacy Far Away with New `ForbiddenStaticFunctionSniff`

<a href="https://github.com/Symplify/Symplify/pull/722" class="btn btn-dark btn-sm mt-2 mb-3">
    <em class="fa fa-github"></em>
    &nbsp;
    Check the PR #722
</a>

@todo
@todo code it matches
@todo how to register


## 4. Prevent & references with `ForbiddenStaticFunctionSniff`

<a href="https://github.com/Symplify/Symplify/pull/692" class="btn btn-dark btn-sm mt-2 mb-3">
    <em class="fa fa-github"></em>
    &nbsp;
    Check the PR #692
</a>

@too
@todo codeit mathces
@todo how to regsite


## 5. Clear Child Class Naming Once and For All with `ClassNameSuffixByParentFixer`

<a href="https://github.com/Symplify/Symplify/pull/633" class="btn btn-dark btn-sm mt-2 mb-3">
    <em class="fa fa-github"></em>
    &nbsp;
    Check the PR #633
</a>

@exepciton?
@command?
@controller?
@subscrier?


@todo wtf examleples - command named ProductSorter--- 30 classes? one repository, one command, one command handler and one contorller, wtF
@todo how to rgisrer
