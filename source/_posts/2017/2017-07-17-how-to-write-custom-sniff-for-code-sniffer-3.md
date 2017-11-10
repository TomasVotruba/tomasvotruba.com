---
id: 46
layout: post
title: "How to Write Custom Sniff for Code Sniffer 3"
perex: '''
    When I give talks about coding standards, I ask people 2 questions: do you use coding standards? Do you write your own sniffs? On average, above 50 % uses it, but only 1-2 people wrote their own sniff.
    <br><br>
    PSR-2 is great for start, but main power is in those own sniffs. Every project has their own need, every person has different preferences.
    <br><br>   
    I Google then and found outdated or complicated sources, so I've decided to write down a reference post for those, who want to start with sniffs.
    Let's look what will show all you need (and nothing more) to <strong>know to write your first sniff</strong>.
'''
related_posts: [48, 47, 37]
tweet: "How to Write Custom Sniff for #phpCodeSniffer 3"
---

**Are you new to PHP Coding Standard Tools**? You can read intro [How PHP Coding Standard Tools Actually Work](/blog/2017/07/31/how-php-coding-standard-tools-actually-work/) to grasp the idea behind them. Or [just go on](https://www.youtube.com/watch?v=t99KH0TR-J4&feature=youtu.be&t=16) if you're ready to start...
<br>

Today we'll pick an example a from my friend [Martin Hujer](https://www.martinhujer.cz/). Once told me about sniff that checks **that all exception classes have "Exception" suffix**.

I said: How is it useful in practise? We all know that is common knowledge to write them this way. He replied: Well, we found some even in our code base.
 
 The point is not in the count of fixed cases, but in *CI based responsibility*. From now on, **people'll NEVER have to think about it** and they can **focus on more valuable processes** that CI cannot do, like writing AliPay integration.  

 
## 6 Steps To `ExceptionNameSniff`

### 1. Start With Sentence That Declares What Sniff Does

"An exception class should have "Exception" suffix."

### 2. Create a Sniff Class and Implement a `PHP_CodeSniffer\Sniffs\Sniff` interface
 
It covers 2 required methods: 

```php
use PHP_CodeSniffer\Sniffs\Sniff;

final class ExceptionNameSniff implements Sniff
{
    /**
     * @return int[]
     */
    public function register(): array
    {
    }
    
    public function process(File $file, $position): void
    {
    }
}
```

A `register()` method returns list of tokens to subscribe to. Which token should we put there?

*Note: You can find all tokens in [PHP manual](http://php.net/manual/en/tokens.php).*

From "An exception class should have "Exception" suffix." I thought the `T_CLASS` would be ideal:


```php
public function register(): array
{
    return [T_CLASS];
}
```

It would match this part of php code:

```php
**class** SomeException extends Exception { # this is one line in your code
```

`T_CLASS` would match also these false positives:

```php
new **class**() extends Exception { # anonymous class
**class** SomeClass { # class without parent
```

It might be a little tricky to find out the easiest way to check the rule. Here you'd have to detect these cases and skip them as well.


What is exception in natural language description (not PHP)? *A class that extends another class that has suffix "Exception".*


So this would save us bit of coding and thinking: 

```php
public function register(): array
{
    return [T_EXTENDS];
}
```
 

### 3. Create `process()` Method

This method has 2 arguments. 

 
```php
public function process(File $file, $position)
{
} 
```

- 1. The `File $file` object holds all tokens of the file and helper methods.
- 2. The `$position` is int for current located `T_EXTENDS` token. 


There are 2 parts while writing a sniff:
 
- First we need to detect, if this is the use case we try to match - an exception class. Because `T_EXTENDS` doesn't tell a lot.
- Then we need to check if it meets our rules and add error if not.

Let's take it one a by one:

### 4. Detect the Exception Class

*A class that extends another class that has suffix "Exception".*

A `File` has useful `findNext()` method:

```php
$file->findNext(array ['tokens to find'], int 'where to start looking');
```

It returns position of token found or null, if none.

We need to **find a string after** `T_EXTENDS`.

```php
$parentClassNamePosition = $file->findNext([T_STRING], $position);
// File has all the tokens, so we get the one with name
$parentClassNameToken = $file->getTokens()[$parentClassNamePosition];

// and check it's Exception
if (substr($parentClassNameToken['content'], -strlen('Exception')) !== 'Exception')) {
    // the parent class it not and exception
    return;
}
```

When the code gets pass this check, we know we have exception there.
 
  
### 5. Make Sure it Ends with "Exception"
 
Would you what to do know? The process will be the same - to check if class name ends with "Exception" -, but instead of `findNext()` method we'll use `findPrevious()`:

```php
// Get position of nearest previous string token
$classNamePosition = $file->findPrevious([T_STRING], $position);
// Get the token for it
$classNameToken = $file->getTokens()[$classNamePosition];
// Detect the content of token ends with "Exception"
if (substr($classNamePosition['content'], -strlen('Exception')) === 'Exception')) {
    // the current class ends with "Exception" 
    return;
}
```

When this section passes, we know we have exception without "Exception" suffix there.



**Reporting the error**

The last method we will use is `addFixableError()`.

In pseudo code: 

```php
$file->addFixableError(
    'Infomative message about error',
    'Where is the token with invalid content',
    'ID of this Sniff to display in error report - class or some string'
);
```

In out case:

```php
$file->addFixableError(
    'An exception class should have "Exception" suffix.',
    $position - 2,
    self::class
);
```

Tada!


### 6. Put Together The Final Sniff

And extract `stringEndsWith()` method to make code more readable.

```php
use PHP_CodeSniffer\Sniffs\Sniff;

final class ExceptionNameSniff implements Sniff
{
    /**
     * @return int[]
     */
    public function register(): array
    {
        return [T_EXTENDS];
    }
    
    public function process(File $file, $position): void
    {
        $parentClassNamePosition = $file->findNext([T_STRING], $position);
        $parentClassNameToken = $file->getTokens()[$parentClassNamePosition];
        
        // Does it ends with "Exception"?
        if (! $this->stringEndsWith($parentClassNameToken['content'], 'Exception')) {
            // The parent class it not and exception, neiter it this
            return;
        }
        
        $classNamePosition = $file->findPrevious([T_STRING], $position);
        $classNameToken = $file->getTokens()[$classNamePosition];
        if ($this->stringEndsWith($classNamePosition['content'], 'Exception')) {
            // The current class ends with "Exception", it's ok 
            return;
        }
     
        $file->addFixableError('An exception class should have "Exception" suffix.', $position - 2, self::class)
    }
    
    private function stringEndsWith(string $name, string $needle): bool 
    {
        return (substr($name, -strlen($needle)) === $needle);
    }
}
```


You can find [final Sniff on Github](https://github.com/Symplify/Symplify/blob/eeeaab688f6b349e55ab0b3179749dc9e5e49035/packages/CodingStandard/src/Sniffs/Naming/ExceptionNameSniff.php) and use it right away of course.


## How to run it?

With [EasyCodingStandard](https://github.com/Symplify/EasyCodingStandard) put the class to `easy-coding-standard.neon`: 

```yaml
checkers:
    - Symplify\CodingStandard\Sniffs\Naming\ExceptionNameSniff
```

And run:

```bash
vendor/bin/ecs check src
```

Congrats to your first sniffs! How do you like it?

Happy coding!
