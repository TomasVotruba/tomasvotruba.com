---
layout: post
title: "PHP Object Calisthenics 3.0 Released!"
perex: '''
    Object Calisthenics are 9 language-agnostic rules to help you write better and cleaner code.
    They help you to get rid of else, method chaining, long classes or functions, unreadable short names and so on.
    <br><br>
    <strong>Object Calisthenics 3.0 runs on CodeSniffer 3.0, PHP 7.1 and brings 6 of them with fancy configuration and code examples</strong>.
'''
lang: en
---

If you are coding standard nerd like me, you'll probably more than PSR2 standards in your ruleset. But even if you fon't, [Object Calisthenics](https://github.com/object-calisthenics/phpcs-calisthenics-rules) is developer-friendly game changer for your code.

## Why Developer-Friendly Game Changer?

### 1. You don't have to know single thing about CodeSniffer to start

### 2. You can start use right now

```bash
composer require object-calisthenics/phpcs-calisthenics-rules
```

### 3. You can run only 1 sniff for start

Quick quiz: what is this variable?

```php
$this->di->...;
```

*Dependency Injection? Dependency Injection Container?* Who would guess it's Donation Iterator! 

**[Rule #6 - Do Not Abbreviate](https://github.com/object-calisthenics/phpcs-calisthenics-rules#6-do-not-abbreviate)** checks these cases. **It detects short names that are ambiguous and hard to decode.**

To run it in CLI: 

``` bash
vendor/bin/phpcs src -sp \
--standard=vendor/object-calisthenics/phpcs-calisthenics-rules/src/ObjectCalisthenics/ruleset.xml \
--sniffs=ObjectCalisthenics.NamingConventions.ElementNameMinimalLength
```

You can run this locally or put to your CI integration and you are ready to go.

Nothing complicated.


## Fancy Readme With Examples

We put lot of work to README examples, so it's not boring text describing what exactly the rule does, but instead right to the point **YES code and NO code snippets**.
   
<img src="/assets/images/posts/2017/object-calisthenics/rule6.png" class="thumbnail">

### Configure What You Need

As you can see in the bottom, most of rules are configurable. **You can adapt their strictness to your specific needs and needs of your project**.
 
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

Configure **in EasyCodingStandard:**

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
            <!-- default: i,id,to,up ->
        </properties>
    </rule>
</ruleset>
```

Configure **in EasyCodingStandard:**

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



## To Sum up Object Calisthenics 3.0

- This release focuses **on simple setup** rather than on academic explanation of their meaning.  
- Rules are explained on examples.
- You can choose a **level of strictness that suits you**.


To find more details about this release see [Release notes on Github](https://github.com/object-calisthenics/phpcs-calisthenics-rules/releases/tag/v3.0.0).
