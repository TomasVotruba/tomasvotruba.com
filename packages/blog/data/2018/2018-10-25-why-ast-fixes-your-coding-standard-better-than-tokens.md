---
id: 153
title: "Why AST Fixes your Coding Standard Better than Tokens"
perex:
    In the last post [*Brief History of Tools Watching and Changing Your PHP Code*](/blog/2018/10/22/brief-history-of-tools-watching-and-changing-your-php-code/) we saw there are over **dozen tools in PHP that can modify code**. So there is no surprise coding standard tools are "upgrading" code from PHP 5.6 to PHP 7.2 without knowing types and that AST is moving `false` to `!`.


    Should coding standard upgrade your code? Should AST make your code cleaner? Should AST take of coding standard changes?
    **Which is born for it?**
tweet: "New Post on my Blog: Why #AST Fixes your Coding Standard Better than Tokens #php"
tweet_image: "/assets/images/posts/2018/ast-tokens/scope.png"
---

## Tokens

PHP CS Fixer can upgrade to few features of new PHP using just `token_get_all()`:

<div class="text-center">
    <img src="/assets/images/posts/2018/ast-tokens/php-cs-fixer-migrate.png" class="img-thumbnail">

    <p><a href="https://github.com/FriendsOfPHP/PHP-CS-Fixer/blob/03e13fb91c775a151dc57ae51e80ba3f2abe7da6/src/RuleSet.php#L209-L240"><code>RuleSet.php</code></a></p>
</div>


## AST

[Rector](https://github.com/rectorphp/rector) can solve rather low-level changes in [code quality](https://github.com/rectorphp/rector/issues/424) level:

```diff
-if (! $this->isTrue($condition) === false) {
+if ($this->isTrue($condition)) {
```

```diff
-count(func_get_args()) === 1);
+func_num_args() === 1
```

### The "Code Quality" Level

It's the most favorite level in Rector. Why?

- it **makes your code clear**
- it's **easy to use on any PHP code regardless framework** you're using - from pure PHP, over Drupal, Wordpress, Magento, to frameworks like Symfony, Nette, and Laravel
- it helps you to **use direct PHP functions** instead of wrapping them into complex structures ↓

```diff
-foreach ($this->oldToNewFunctions as $oldFunction => $newFunction) {
-    if ($currentFunction === $oldFunction) {
-        return $newFunction;
-    }
-}
-
-return null;
+return $this->oldToNewFunctions[$currentFunction] ?? null;
```

Huge thanks to [Gabriel Caruso](https://github.com/carusogabriel), who brought this idea to Rector and helped me to shift my view to the one I'll show you below.

<br>

If there would be no AST, this all could be handled by `token_get_all` ([like PHP_CodeSniffer and PHP CS Fixer](/blog/2017/07/31/how-php-coding-standard-tools-actually-work)/), but **such implementation needs to be lot longer to achieve similar quality**, since you have to check every previous and next tokens for any unexpected values.

<blockquote class="blockquote text-center pb-5 pt-5">
    "I really don't like programming. I built this tool to program less so that I could just reuse code."
    <footer class="blockquote-footer">Rasmus Lerdorf</a>
</blockquote>

## Shifting the Scope

We're here at the moment:

- tokens / coding standard === styling only
- AST / static analysis === context aware only

That's very narrow and old-school, but the shift has already begun...

### Tokens are Best at

- spacing and exact positions

    ```diff
    -if ($condition )
    -{
    +if ($condition) {
    ```

- sign changes

    ```diff
    -$items = array(1, 2, 3;);
    +$items = [1, 2, 3];
    ```

- doc block changes

    ```diff
    /**
    -* @param    int|string
    +* @param int|string $id
     */
    ```

### AST is Best at

- logic and structure changes

    ```diff
    -if (! $this->isTrue($condition) === false) {
    +if ($this->isTrue($condition)) {
    ```

- code cleanup

    ```diff
    -$value = $value;
    ```

- context-aware names

    ```diff
    -$formBuilder->add('name', new TextType);
    +$formBuilder->add('name', TextType::class);
    ```


## 1 Example for Coding Standards in AST

Let's take this case of useless variable:

```diff
 function () {
-    $a = true;
-    return $a;
+    return true;
 };
```

My first thought was: "Why is it assigned, is there some magic behind this? I need to explore more."
Well, there isn't - **it's a trap**. Both for the programmer and for PHP to interpret it.

So this change will not only make your code more readable but also faster. A nice side effect, right?


Let's briefly compare how tokens and AST approach this:

<table class="table table-bordered table-responsive mt-5 mb-5">
    <thead class="thead-inverse">
        <tr>
            <th class="text-center w-50">Tokens</th>
            <th class="text-center w-50">AST</th>
        </tr>
    </thead>
    <tr>
        <td>PHP_CodeSniffer</td>
        <td>Rector</td>
    </tr>
    <tr>
        <td>
            <a href="https://github.com/slevomat/coding-standard/blob/5ae298bdb3bbdf573d506d0da3e8c6eadde6ba12/SlevomatCodingStandard/Sniffs/Variables/UselessVariableSniff.php">
                <code>UselessVariableSniff</code>
            </a>
        </td>
        <td>
            <a href="https://github.com/rectorphp/rector/blob/9855690778272de1033ad1f8c520bbee0a877201/packages/CodeQuality/src/Rector/Return_/SimplifyUselessVariableRector.php">
                <code>SimplifyUselessVariableRector</code>
            </a>
        </td>
    </tr>
    <tr>
        <td><strong>329 lines</strong></td>
        <td><strong>120 lines</strong></td>
    </tr>
    <tr>
        <td>2 helper services</td>
        <td>1 helper service</td>
    </tr>
</table>

Note that it would be very difficult to write both versions shorter and keep reliability high and I believe [kukulich](https://github.com/kukulich) is very good at implementing Sniffs effectively. **It's a matter of used technology, not implementation skill**.

To sum up, **the AST version takes only 36,47 % of code what token version**.

<br>

Also, AST implementation also solved this case without no extra work:

```diff
 function test() {
     $a = 1;
     $b = 1;
-    $c = [
+    return [
         $b-- => $a++,
         --$b => ++$a,
     ];
-    return $c;
 }
```

## Coding Standards on Steroids with AST

I still imagine how PHP would look like today if we had AST in [2012 when Fabien started PHP CS Fixer](https://github.com/nikic/PHP-Parser/issues/41).

<img src="/assets/images/posts/2018/ast-tokens/scope.png" class="mb-2">

<br>

- Would you be interested in such AST rules for coding standard?

- **What rules would you add** if it would be easier to create them with AST?
