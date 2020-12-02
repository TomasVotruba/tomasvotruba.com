---
id: 289
title: "How to get a Dynamic PHP Matrix in GitHub Actions"
perex: |
    Do you want to run your tests on each PHP version you support? PHP 7.3, 7.4 and 8.0?
    Instead of 3 workflows with copy-paste steps, you can define **just one with a matrix for PHP versions**.
    <br><br>
    PHP is release every year and version constraints are in `composer.json`.
    How could we automate this with a dynamic matrix?

tweet: "New Post on #php 🐘 blog: How to make a Dynamic Matrix in GitHub Actions"
---

@the problem

Do you know memory locks? It's like code smell, if this then that... You have to always put keys into your left pocker, after you lock the door of your office. This one of them.

This is comletely unneded operation, but double the work and double the maintenance. No gain, except fear of control.
https://github.com/spatie/packagist-api/pull/19/commits/bf441dda3d0ca8bcec81f8083399807a61e02b31

1 examples...

* PHP and Monorepo Package Split
* @todo add php version to symplify/easy-ci package
