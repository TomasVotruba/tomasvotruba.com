---
id: 48
layout: post
title: "How to Deprecate Code Functionality in a Symfony Way"
perex: '''
    When you develop open-source package, you evolve, the code evolves.
      You need to use new class, or remove parameter, and you stick to semver.
      <br><br>
      I will show you how to make such changes safely with gradual deprecation and major version bumps.
'''
related_posts: [46, 47, 37]
---


I learned a lot about deprecations from 
- bc promise: https://symfony.com/doc/current/contributing/code/bc.html
- 3.4 => 4.0 remove code
- and reading Symfony code.


You can see it here...


## Prepare Deprecation Trigger in the Code


```php
public function getName()
{
}
```

into

```php
public function getFullName()
{
}

public function getFirstName()
{
}

public function getSurname()
{,
}
```


```php
public function getName()
{
    @trigger_error('Error message', E_USER_DEPRECATED);
    return $this->getFullName();
}

```


Notice `@`.

It's because we don't want to code show errors just yet. The right time is last version before major version bump. In Symfony case, `@` is used in version 3.1, 3.2 and 3.3. 
Version 3.4 will report these errors right away without supression `@`.



...


## 3 Most Popular Deprecations


### 1. Deprecate Class

...

### 2. Deprecate Interface

...

### 3. Deprecate Method argument

...


## Write Descriptive Messages

"This class is deprecated."

Ehm, what should do with that?

Don't make the programmer think, make it easier for him.
 
  
"This class is deprecated. Use ThisClass"

"This class is deprecated. Use @see ThisClass".

link in PHPStorm... (gif)



