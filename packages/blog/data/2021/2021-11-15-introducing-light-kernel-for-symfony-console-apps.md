---
id: 347
title: "Introducing Light Kernel for Symfony&nbsp;Console&nbsp;Apps"
perex: |
    In the first post of this miniseries, we looked on [Symfony HttpKernel package](/blog/when-symfony-http-kernel-is-too-big-hammer-to-use) with critical eye on how it makes projects way too heavy.
    <br><br>
    In the second post, we [looked at a bundles](/blog/decomposing-symfony-kernel-what-does-minimal-symfony-bundle-do) from very raw point of view.
    <br><br>
    In spirit of [thesis, antithesis and synthesis](https://link.springer.com/referenceworkentry/10.1007%2F978-1-4020-8265-8_200183) philosophy, we'll try to combine both parts. We'll find a solution to original question: **How can we run Kernel in Symfony Console Apps without http burdens?**

tweet: "New Post on the 🐘 blog: Introducing Light Kernel for #Symfony Console Apps"
---

<div class="card border-warning mt-4">
    <div class="card-header text-black bg-warning shadow">
        <strong>Proof over theory?</strong>
        ECS, Monorepo Builder, EasyCI, Config Transformer and Rector are using this method since 1st November 2021. ECS is now <a href="https://github.com/symplify/easy-coding-standard/commit/278d4d52958c1ca01c21219cb6e14ca4493914ad">40 000 lines lighter</a>, while keeping all the features running.
    </div>
</div>



