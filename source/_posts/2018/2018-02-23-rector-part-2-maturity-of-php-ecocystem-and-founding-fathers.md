---
id: 78
title: "Rector: Part 2 - Maturity of PHP Ecosystem and Founding Fathers"
perex: '''
    What it took for Rector to be born?
    <br><br>
    Paradigm shift, ecosystem maturity, need for speed to solve common problems community has. **And a great team you share [your work with](https://austinkleon.com/show-your-work/) that feedbacks and reflects.**
'''
todo_tweet: "..."
related_items: [63, 77] 
---

You already know [What Rector does and How it works](/blog/2018/02/19/rector-part-1-what-and-how/) from part 1.



...


## Codemod...

php code sniffer
php cs fixer

great tahnks





## Founding Fathers of Rector

That's how [Rector](https://github.com/rectorphp/rector) was born. At least the ideas.

It's not only about writing code. It's about discussing the idea, finding the right API that not only me but others would understand right away, it's about using other tools. It's about talking with people who already made AST tools, ask for advices and find possible pitfalls. It's about reporting issues and talking with people who maintain  projects you depend on.

### Is PHP Ecosystem is AST Ready?

All this was needed before, but PHP missed crucial parts:  

- parse PHP code to AST - done thanks to Nikic and his [nikic/PHP-Parser](https://github.com/nikic/PHP-Parser) 
- prototype that allows context aware PHP anylisis = "this variable is of this type" - done in 2017 by Ondřej Mirtes and his [PHPStan](/blog/2017/01/28/why-I-switched-scrutinizer-for-phpstan-and-you-should-too/)
- save changed AST back to PHP without any changes - [this feature was added in 2017](https://github.com/nikic/PHP-Parser/blob/master/doc/component/Pretty_printing.markdown#formatting-preserving-pretty-printing) to `nikic/php-parser` and becomes stable with version 4 
- simple coding standard tool, that would cleanup the code after Rector - done in 2017 by [EasyCodingStandard](https://github.com/Symplify/EasyCodingStandard)
- and prototype which would allow it

@todo mention: https://github.com/nikic/PHP-Parser/issues/41

### FDD: Friendship-Driven-Development

I don't work for at any company so development of Rector doesn't solve my personal issues. That's how most project is born, like PHPStan to check Slevomat's code. That means I needed other motivation - when my frustration of wasted thousands human-hours was not enough. 
 
Here I'd like to thank [Petr Vacha](https://) for cowork weekend in Brno with in summer 2017, where it all started - in those times named as *Refactor*. You've been great friend for years and courage in times, when I needed it the most.
 
And [David Grudl](https://davidgrudl.com/), who gave me the motivation to dig deep and "just try it" when I felt desperate and useless always with lightness of Zen master. 

And also [Nikita Popov](http://nikic.github.com/), who patiently answered, taught me and fixed all my issues on `nikic/php-parser`. 

Without you, I would not make it here today.

