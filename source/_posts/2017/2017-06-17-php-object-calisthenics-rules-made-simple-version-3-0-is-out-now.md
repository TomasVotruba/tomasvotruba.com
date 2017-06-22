---
layout: post
title: "PHP Object Calisthenics Made Simple - Version 3.0 is Out Now"
perex: '''
    Object Calisthenics are 9 language-agnostic rules to help you write better and cleaner code.
    They help you to get rid of "else" statements, method chaining, long classes or functions, unreadable short names and much more.
    <br><br>
    <strong>Object Calisthenics 3.0 runs on CodeSniffer 3.0 and PHP 7.1. It brings 6 of them with fancy configuration and code examples</strong>.
'''
lang: en
---

If you are a coding standard nerd like me, you'll probably have more than just PSR-2 standard in your ruleset. But even if you don't, [Object Calisthenics](https://github.com/object-calisthenics/phpcs-calisthenics-rules) is a developer-friendly game changer for your code.

## Much Simpler than 2.0

**1. You don't have to know a single thing about CodeSniffer to start**

**2. Simple to install**

```bash
composer require object-calisthenics/phpcs-calisthenics-rules
```

**3. You can start with 1 sniff**

Quick quiz: what is this variable?

```php
$this->di->...;
```

*Dependency Injection? Dependency Injection Container?* Who would guess it's *Donation Invoice*! 

**[Rule #6 - Do Not Abbreviate](https://github.com/object-calisthenics/phpcs-calisthenics-rules#6-do-not-abbreviate)** checks these cases. **It detects short names that are ambiguous and hard to decode.**

To run it from the command line just include Object Calisthenics' `ruleset.xml` and specify the sniff's name:

``` bash
vendor/bin/phpcs src -sp \
--standard=vendor/object-calisthenics/phpcs-calisthenics-rules/src/ObjectCalisthenics/ruleset.xml \
--sniffs=ObjectCalisthenics.NamingConventions.ElementNameMinimalLength
```

You can run this locally or put to your CI and you are ready to go.

Nothing complicated.


## Fancy Readme With Examples

We put lots of work to README for the new release. It isn't a long text describing what exactly the rule does and how it originated - there is already [a blog post for that](http://williamdurand.fr/2013/06/03/object-calisthenics/).

Instead, README goes right to the point:
 
- **YES and NO code snippets**,
- **how to use them** - copy/paste CLI command 
- and **how to configure them** - link to particular lines in `ruleset.xml` and `easy-coding-standard.neon`.
   
<img src="/assets/images/posts/2017/object-calisthenics/rule6.png" class="thumbnail">

### Configure What You Need

As you can see in the bottom part of screenshot, most of rules are configurable. It allows you **to adapt their strictness to your specific needs and needs of your project**.
 
*Do you prefer to require min 4 chars?*

Configure **in CodeSniffer:**

```xml
<!-- ruleset.xml -->

<?xml version="1.0"?>
<ruleset name="ObjectCalisthenics">
    <!-- Rule 6: Do not abbreviate -->
    <rule ref="ObjectCalisthenics.NamingConventions.ElementNameMinimalLength">
        <properties>
            <property name="minLength" value="4"/> <!-- default: 3 -->
        </properties>
    </rule>
</ruleset>
```

Configure **in [EasyCodingStandard](https://github.com/Symplify/EasyCodingStandard):**

```yaml
# easy-coding-standard.neon

checkers:
    # Rule 6: Do not abbreviate
    ObjectCalisthenics\Sniffs\NamingConventions\ElementNameMinimalLengthSniff:
        minLength: 4 # default: 3
```

*Do you want to add "y" to allowed short names?*

Configure **in CodeSniffer:**

```xml
<!-- ruleset.xml -->

<?xml version="1.0"?>
<ruleset name="ObjectCalisthenics">
    <!-- Rule 6: Do not abbreviate -->
    <rule ref="ObjectCalisthenics.NamingConventions.ElementNameMinimalLength">
        <properties>
            <property name="minLength" value="4"/> 
            <property name="allowedShortNames" type="array" value="y,i,id,to,up"/>
            <!-- default: i,id,to,up -->
        </properties>
    </rule>
</ruleset>
```

Configure **in [EasyCodingStandard](https://github.com/Symplify/EasyCodingStandard):**

```yaml
# easy-coding-standard.neon

checkers:
    # Rule 6: Do not abbreviate
    ObjectCalisthenics\Sniffs\NamingConventions\ElementNameMinimalLengthSniff:
        minLength: 4
        allowedShortNames: ["y", "i", "id", "to", "up"] 
        # default: ["i", "id", "to", "up"]
```

### Minitip: What Can You Configure in Particular Sniff?

- Open `ElementNameMinimalLengthSniff` class in your IDE.
- **Look for public properties**. CodeSniffer uses them to set any configuration. 


## Thanks to all Contributors

Last but not least, I'd like to personally thank contributors who helped to make this version happen as it is:

- [@frenck](https://github.com/frenck)
- [@mihaeu](https://github.com/mihaeu)
- [@roukmoute](https://github.com/roukmoute)
- [@UFOMelkor](https://github.com/UFOMelkor)

Without your help, this would not have been possible.
Thank you guys.


## To Sum up Object Calisthenics 3.0

- **Simple setup over boring academic explanations**.
- Rules are *explained in examples*.
- 0-setup. You can just **copy-paste CLI commands and run it**.
- You can choose a **level of strictness that suits you** thanks to flexible configuration.


To find more details about this release see [Release notes on Github](https://github.com/object-calisthenics/phpcs-calisthenics-rules/releases/tag/v3.0.0).
