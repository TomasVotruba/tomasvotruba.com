---
id: 159
title: "How to Get Property Typehints in PHP 7.4 to Your Code"
perex: |
    ...       
tweet: "New Post on My Blog: How to Get Property Typehints in #PHP 7.4 to Your Code"
---

## PHP 7.4 and Beyond

<a href="https://github.com/rectorphp/rector/pull/643/" class="btn btn-dark btn-sm">
    <em class="fab fa-github fa-fw"></em>
    See pull-request #643
</a>

Yes, you've seen right. Rector already supports future versions of PHP, in particular, PHP 7.4 and [typed properties](https://wiki.php.net/rfc/typed_properties_v2).

So in 1 year and 1 month, you'll be able to just run Rector...

```bash
vendor/bin/rector process src --level php74
```

...and get these beauties in no time: 

```diff
 final class SomeClass 
 {
-    /** 
-     * @var int 
-     */
-    private count; 
+    private int count;
 }
```

It's smart enough to touch only what it should and leave the rest be:


```diff
 final class SomeClass
 {
-    /**
-     * @var boolean
-     */
-    public $a;
+    public bool $a;

     /**
-     * @var bool
      * another comment
      */
-    private $b = false;
+    private bool $b = false;

     /**
      * @var callable
      */
     private $c;

-    /**
-     * @var AnotherClass|null
-     */
-    private $d = null;
+    private ?AnotherClass $d = null;

     /**
      * @var int
      */
     public $e = 'string';
}
```
