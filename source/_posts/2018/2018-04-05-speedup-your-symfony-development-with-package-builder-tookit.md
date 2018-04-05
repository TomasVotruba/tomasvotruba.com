---
id: 89
title: "4 Way to Speedup Your Symfony Development with PackageBuilder"
perex: |
    ...
tweet: "..."
tweet_image: "/..."
---



## 1. Console like, -vvv aware renders for Exceptions and Errors

- [#732] Add support for `Error` rendering to `Symplify\PackageBuilder\Console\ThrowableRenderer` (former `ExceptionRenderer`)
- [#720] Add `Symplify\PackageBuilder\Console\ExceptionRenderer` to render exception nicely Console Applications but anywhere outside it; follow up to [#715] and [#702]


If you use Symfony Console, you are familari with errorsl ike:

@todo image

And if you need more intel on exceptoins, jsut run: `-vvv`

Also works with Error, like ParseError php code.



But what if you need to use it standalaloe. e.g before console sbuild?

```php
some console build code

$containre-createForm confi (...)
# config is missing? exceptino
```

It willi end up-like

```bash

```


I needed thsi in my CLI apps to work and thanks to @ondram I came iwth
decoupled Symfony\Console Applicaiton logic ...


Use like:_:

```php
```

And you'll get nice errors :)



## 2. Drop manual `public: true` for every servie you test 

[#680](https://github.com/Symplify/Symplify/pull/680/files#diff-412c71ea9d7b9fa9322e1cf23e39a1e7) 

If you need to test a service...
get()

But than it must be public

So I've seen config like:


```yaml
services:
    SomeNamespace\:
        resource: '..'

    SomeNamespace\SomeClass:
        public: true

```

Or special "tests" configs like:

```yaml
public-services-test.yml
```


Just add `PublicForTestsCompilerPass`
It detects phpunit run and adds public to each service, so you don't have to add it for every new service you tset


iin pracice it will lead to clena code lke this @todo PR from my ysmypl




## 3. Autowire Singly-Implemented Interfaces

- [#645] 


Autowiring works great in combination with PSR-4 autoloading:

```yaml
# app/config/services.yml
services:
    _defaults:
        autowire: true

    SomeService: ~
```

But what if it has interface:

```yaml
# app/config/services.yml
services:
    _defaults:
        autowire: true

    # SomeInterface
    SomeServiceImplementingSomeInterface: ~
    SomeServiceUsingSomeInterface: ~
```

You get error like... @todo

To solve it you need to use alias:

@todo it only applies ton consoleoutput probably

```yaml
# app/config/services.yml
services:
    _defaults:
        autowire: true

    # SomeInterface
    SomeServiceImplementingSomeInterface:
        alias: SomeInterface 
    SomeServiceUsingSomeInterface: ~
```

And add that for every class that implements an interface:

```yaml
# app/config/services.yml
services:
    _defaults:
        autowire: true

    # SomeInterface
    SomeNamespace\ProductRepository:
        alias: SomeNamespace\ProductRepositoryInterface
    SomeNamespace\CategoryRepository:
        alias: SomeNamespace\CategoryRepositoryInterface
    SomeNamespace\UserRepository:
        alias: SomeNamespace\CategoryRepositoryInterface
    # ...
```

That way, you're actually being punished for using clean code and separation of interfaces. But is that really necessary?

```yaml
# app/config/services.yml
services:
    _defaults:
        autowire: true

    # SomeInterface
    SomeNamespace:
        resource: 'src' 
```

How to fix this? I got inspired by @todo singly-implement  
 
`AutowireSinglyImplementedCompilerPass`



## 4. How to Decouple Parameters to multiple files in safe way

- [#755]

- parameters many 
- separate to many files
- ...

Just add `Symplify\PackageBuilder\Yaml\AbstractParameterMergingYamlFileLoader` for standalone use
