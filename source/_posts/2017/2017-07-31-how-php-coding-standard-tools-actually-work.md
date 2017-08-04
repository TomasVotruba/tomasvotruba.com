---
id: 48
layout: post
title: "How PHP Coding Standard Tools Actually Work"
perex: '''
    Do you use <a href="https://github.com/FriendsOfPHP/PHP-CS-Fixer">PHP-CS-Fixer</a> or <a href="https://github.com/squizlabs/PHP_CodeSniffer">PHP_CodeSniffer</a>? Do you know the way they work is ~80 % the same? Do you wonder how they work under the hood?
    <br><br>
    Today I will share <strong>3 main pillars of their architecture</strong>. 
'''
related_posts: [46, 47, 37]
---

Why should these 3 pillars be even important for you? When I understood tools behind them and their basic principals, **I was able to more effective Sniffs and Fixers** (*Checkers* further on) **that were clear to their communities**.

## Write 1 Checker, Save Hundreds Hours of Work

Coding Standards are my greatest passion for last couple of years. I love their efficiency: **with one rule (class) you can improve thousands of lines in your code** in matters of milliseconds. And not only yours if you share it in a package.

With a Checker you can change `array()` to `[]`. And more then that. Coding Standard are not exclusively about spaces, tabs and brackets nowadays.

You can use them to [refactor to newer version of your framework](https://daniel-siepmann.de/Posts/2017/2017-03-20-phpcs-code-migration.html),
 [upgrade your codebase to newer PHP](https://github.com/wimg/PHPCompatibility) or [add PHP 7.1 typehints to your methods](https://github.com/kukulich/php-type-hints-convertor).       
    
That's laziness on a completely different level :)


## So Much for The Hype

<div class="text-center">
    <img src="https://content.artofmanliness.com/uploads/2015/08/Small-Things-Over-Time-2.jpg" style="max-width:100%">
</div>

A lot is possible to do with these tools and I'll write about that in the future, but today we'll start with a much [smaller step](/blog/2017/02/22/fast-and-easy-way-to-learn-complex-topics/): a Checker that will inform us about coding standard violation. No changes, no refactoring.

To know how to build a Checker you need to understand 3 terms: *token*, *dispatcher* and *subscriber*.

I'll explain them one by one.



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
- Subscriber <=> *Checker*

 
Almost there.
 

## 3. Subscriber


You already know that *subscriber* is a *Checker*. Checker is a class that waits for a specific token. 

In pseudo code:
  
```php
class Checker
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

When the dispatcher gets a token with type `T_ECHO` (= `328`) it will call  `Checker::someMethodThatWillBeCalled()` method.


I think now you are ready for the real code.

### Do You Want Real Code?

I already wrote [how to write a Sniff for PHP_CodeSniffer](/blog/2017/07/17/how-to-write-custom-sniff-for-code-sniffer-3/) or [how to write a Fixer for PHP-CS-Fixer](/2017/07/24/how-to-write-custom-fixer-for-php-cs-fixer-24/) on this topic where I write code to solve  real life use cases.

Enjoy saved time by writing a code that works for you.

Happy coding!
