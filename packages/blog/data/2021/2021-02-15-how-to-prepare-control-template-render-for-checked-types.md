---
id: 302
title: "How to Prepare your Neon Configs for PHP 8 and Make them More Readable"
perex: |
    ...
tweet: "New Post on #php 🐘 blog: How to Prepare your Neon Configs for PHP 8 and Make them More Readable       #nettefw"
---

## What is The Goal?

- types
- validation
- have 1 source of the truth
    - have it in code (static analysis)


public function checkIn($name)
{
}

Whta is name?

- we know it's a string
- but we're dead to the code the time we leave the keyboard
- how can code know?

```diff
-public function checkIn($name)
+public function checkIn(string $name)
 {
 }
```


you would not call flush on every property change, would you?

$this->template->key =
$this->template->key2 =
$this->template->key3 =

is the same as:

$product->setName()
$this->entityManager->persist($product)
$this->entityManager->flush()
$product->setPrice()
$this->entityManager->persist($product)
$this->entityManager->flush()
$product->setCategory()
$this->entityManager->persist($product)
$this->entityManager->flush()


Do it once,



Types

## Problame of Optional Variables


## How cna we sure?




## PHSPtan to help!

see https://github.com/symplify/phpstan-rules/commit/b5465172f1fa2d77f495b17b3de91cedc0588807
