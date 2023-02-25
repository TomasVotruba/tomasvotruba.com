---
id: 227
title: "The Single Best Skill to Master Command Line"
perex: |
    I have a confession to make. I'm very poor with memory stuff. My brain is using neurons to mostly process data, instead of keeping them.


    I'm very poor with memory stuff. So I use shortcuts, both in brain and code, that make me look smart. I don't like remember stuff, **I won't enjoy them and use them**.


    Accidentally, **my poor memory makes me the most productive programmer in the room**. And you can be too. How?
---

I had the Symfony & Git training last Friday. We were using a single `master` branch on Gitlab and multiple programmers send their pull-requests into. We needed to pull and rebase after the feature was merged into the `master`.

How did they do it? It was similar to:

```bash
- git checkout -b new-branch

# oh, there is some code in this branch
- git checkout .
- git checkout -b new-branch

# working and making some file changes
- git add .
- git commit -m Some message

# damn I forgot quotes
- git commit -m "Some message"
- git checkout master

# damn I forgot to push
- git push
# what branch? oh damn, this one of course
- git push new-branch

# now I can merge it and I can get new master version
- git checkout master
- git pull

# oh, I made accidentally some change
- git add .
- git commit -m "add master changes"
- git pull

# oh, I've heard this is better with some lease or what
- git pull --with-lease

# oh finally I have local code here
- git branch

# damn that branch is still here :/
- git branch -d new-branch

# damn, why is this the only big letter here?
- git branch -D new-branch
```

...30 minutes later they're done. But **does it have to take so long**?

<br>

I keep getting the same feedback from most of my trainings:

<blockquote class="blockquote">
"Wow you're fast at coding".
</blockquote>

And I keep replying:

<blockquote class="blockquote">
"I'm not, I'm just much lazier than you."
</blockquote>

<br>

What does that mean? If you'd recorded my keyboard for the process above, it would look more like this:

```bash
nb new-branch
gc "Some Message"
p
cmp
db new-branch
rema
gc
p
pul
gc
...
```

You get the idea. For you, it looks like a cat is walking my keyboard, or I'm having a party at home with way too much wine. *For you.*

***For me* it's a path of 2-3 neurons, that invoke some operation I want to achieve.**

## The Alias File

Secret is in `~/.aliases` file (`~` = user home directory), that looks like this:

```bash
# ~/.aliases
alias a="sudo subl .aliases"

alias gc="git commit -m $1"
alias db="git branch -D $1"
alias pul="git fetch -p && git pull --rebase"
```

That's it! This single hack makes me so productive.

## What do I use for CLI?

### Oh My Zen shell

I've used this on Ubuntu for the last 8 years (thanks to Michal Svec for helping me with it). It's a smart shell you can install from here - [oh-my-zsh](https://github.com/ohmyzsh/ohmyzsh).

## How to Add Aliases?

Load your aliases in the main file:

```bash
# ~/.zshrc
source $HOME/.aliases
```

There are various shells, so specific file names might differ.

Now type "a" and voilá - **the file with aliases is open**!

## Restart Console After Adding a New Alias

Let's say we type new alias:

```bash
# ~/.aliases
alias gf="git push --force"
```

And save the file. Now we get back to our and type:

```bash
gf
# zsh: command not found: gf
```

What?

Well, the file `~/.aliases` has to reloaded to make it work.
The best way to do it is to close and open the console window.

## Honor One Woman Tip

You honor one woman, you honor one console. I've noticed some programmer use multiple consoles at the same time:

- PHPStorm console
- Windows console
- bash console

When I work, I always go for **PHPStorm console**. In rare moments like performance heavy operations outside the project scope, I got with the terminal. But I still keep preferring having opened just one.

That way I can:

- **focus on one project**
- **have a single image in my head** - compared to various console design of IDE and your OS
- **have one shortcut to open it**

One, one, one.

<blockquote class="blockquote">
If it's two, it's too long.
</blockquote>

## Memory Overflow Tip: Don't Be Greedy with ShortCuts

**Shortcuts are like cocaine**. They can make you ultimately faster in small doses, but if you take too much of them, your brain will fall apart.

Pick around 10-20 shortcuts. Then every ~~now and then~~ 3 months look at and validate **if they still suit you**.

**Always alias your shortcuts you use everyday**. The one above are just examples, but maybe you don't commit that much, or you don't use git at all. Maybe you work with the server.

So instead of

```bash
ssh my-username@theserveryouuse.com
```

you 'll get with `sms` (ssh, my, server)

<br>

That's what helped me, to always pick the first letter of the work.

- git pull? → **gp**
- git commit? → **gc**
- composer update? → **cu**
- rebase on `main` → **rema**

Keep it simple and your brain gets it :)

<br>

Happy coding!
