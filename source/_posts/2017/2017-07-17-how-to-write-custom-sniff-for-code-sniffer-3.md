---
layout: post
title: "How to Write Custom Sniff for Code Sniffer 3"
perex: '''
    When I give talks about coding standards, I ask people 2 questions: do you use coding standards? Do you write your own sniffs? On average, above 50 % uses it, but only 1-2 people wrote their own sniff.
    <br><br>
    PSR-2 is great for start, but main power is in those own sniffs. Every project has their own need, every person has different preferences.
    <br><rb>   
    I Google then and found outdated or complicated sources, so I've decided to write down a reference post fo1r those, who want to start with sniffs.
    Let's look what will show all you need (and nothing more) to <strong>know to write your first sniff</strong>.
'''
---

## Write 1 Sniff, Save Hundreds Hours of Work

Coding Standards are my greatest passion last couple of years. I love their efficiency: **with one rule (class) you can improve thousands of lines in your code** in matters of milliseconds. And not only yours if you share it in a package.

With a Sniff you can change `array()` to `[]`. And more then that. Coding Standard are not exclusively about spaces, tabs and brackets nowadays.

You can use them to [refactor to newer version of your framework](https://daniel-siepmann.de/Posts/2017/2017-03-20-phpcs-code-migration.html),
 [upgrade your codebase to newer PHP](https://github.com/wimg/PHPCompatibility) or [add PHP 7.1 typehints to your methods](https://github.com/kukulich/php-type-hints-convertor).       
    
That's laziness on a completely different level :)


## So Much for The Hype

<div class="text-center">
    <img src="https://content.artofmanliness.com/uploads/2015/08/Small-Things-Over-Time-2.jpg" style="max-width:100%">
</div>

It's is possible a lot with these tools and I'll write about that in the future, but today we'll start with a much [smaller step](/blog/2017/02/22/fast-and-easy-way-to-learn-complex-topics/): a Sniff that will inform us about coding standard violation. No changes, no refactoring.

To know how to build a sniff you need to understand 3 terms: *token*, *dispatcher* and *subscriber*.

I'll explain them one by one and in the end we'll put them together.



## 1. Token

We see PHP as:

```php
<?php echo "hi";
```

Coding Standard tools see it in [tokens](http://php.net/manual/en/tokens.php):

```php
$phpCodeInTokens = token_get_all('<?php echo "hi";');
var_dump($phpCodeInTokens);
```

```php
array(5) {
  [0]=>
      array(3) {
        [0]=>
        int(379) # token id
        [1]=>
        string(6) "<?php " # token content
        [2]=>
        int(1)
      }
  [1]=>
      array(3) {
        [0]=>
        int(328) # token id
        [1]=>
        string(4) "echo" # token content
        [2]=>
        int(1)
      }
  [2]=>
      array(3) {
        [0]=>
        int(382) # token id
        [1]=>
        string(1) " " # token content
        [2]=>
        int(1)
      }
  [3]=>
      array(3) {
        [0]=>
        int(323) # token id
        [1]=>
        string(4) ""hi"" # token # content
        [2]=>
        int(1)
  }
  [4]=>
      string(1) ";" 
}
```

Don't worry, this is not a content we need to work with. **It will be converted to arrays or objects like these**:


```php
$token = [
    'type' => 328, # token id stated by PHP, you can use also more readable constant: T_ECHO (with value 328)
    'content' => 'echo'
];
```

Now you know what "token" is.



## 2. Dispatcher

Do you know [Event Dispatcher](https://pehapkari.cz/blog/2016/12/05/symfony-event-dispatcher/)? 

If not, it's a pattern (like *repository* or *factory*) that says: **when this action happens, call all methods that listen to it**, e.g. when order is finished (event), send confirmation SMS to user and send him thank-you box full of candies (subscribed methods).

```php
$dispatcher->dispatch('order_finished');
```

For Coding Standard tools **it works the same** but with different naming: 

- Event <=> *Token*
- Subscriber <=> *Sniff*

 
Almost there.
 

## 3. Subscriber


You already know that *subscriber* is a *Sniff*. Sniff is a class that waits for a specific token. 

In pseudo code:
  
```php
class Sniff
{
    public function subscribeToToken()
    {
        return T_ECHO; // number for "echo" by PHP
    }
    
    public function someMethodThatWillBeCalled(array $token)
    {
        if ($token['content'] !== 'echo') {
            // mallformed echo, probably "ECHO", "eCHO" etc.
        }
    }
}
```

Internally Coding Standard tools **dispatch all tokens found in specific file**:

```php
$tokens = $this->getAllTokens(file_get_contents($file));
foreach ($tokens as $token) {
    $codingStandardTool->dispatch($token['type']);
}
```

When the dispatcher gets a token with type `T_ECHO` (= `328`) it will call  `Sniff::someMethodThatWillBeCalled()` method.


I think now you are ready for the real code.



## Let's make `ExceptionNameSniff` Together 

[Martin Hujer](https://www.martinhujer.cz/) told me about sniff that checks that all exception classes have "Exception" suffix.

I said: How is it useful in practise? We all know that is common knowledge to write them this way. He replied: Well, we found some even in our code base.
 
 The point is not in count of fixed cases, but in **CI based responsibility*. From now on, **they'll NEVER have to think about it** and they can **focus on more valuable processes** that CI cannot do, like writing AliPay integration.  

 
### What we need?

1. I start with sentence, that declares what sniff does.

"An exception class should have "Exception" suffix."
 

2. Create a sniff class and implement a `PHP_CodeSniffer\Sniffs\Sniff` interface. 
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
**class** SomeException extends Exception { # this is one line in your the code
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
 

3. Create `process()` method
 

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

### 1. Detect the Exception Class

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
 
  
### 2. Make sure it ends with Exception
 
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


### Put Together The Final Sniff

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


You can find [final Sniff on Github](https://github.com/Symplify/Symplify/blob/master/packages/CodingStandard/src/Sniffs/Naming/ExceptionNameSniff.php) and use it right away of course.


*How to run it?*

With [EasyCodingStandard](https://github.com/Symplify/EasyCodingStandard) put the class to `easy-coding-standard.neon`: 

```yaml
checkers:
    - Symplify\CodingStandard\Sniffs\Naming\ExceptionNameSniff
```

And run:

```bash
vendor/bin/ecs check src
```


That was your first sniff - congrats. How do you like it?

Please let me know if you don't understand any part. I'd be happy to improve it.

Happy coding!
