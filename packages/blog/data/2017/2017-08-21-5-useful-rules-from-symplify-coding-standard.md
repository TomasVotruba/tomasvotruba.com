---
id: 51
title: "5 Useful Rules From Symplify Coding Standard"
perex: |
     <a href="https://github.com/symplify/coding-standard">Symplify Coding Standard</a> was born from Zenify, back from the days I was only Nette programmer. It focuses on <strong>maintainability and clean architecture</strong>. I try to make them simple: <strong>each of them does one job</strong>.
     <br><br>
     With over 108 000 downloads I think I should write about 5 of them you can use in your projects today.
tweet: "Add Final Interface, Class Constant fixer and more to your Coding Standard #php #architecture #php_codesniffer"

deprecated_since: "September 2018"
deprecated_message: |
    [Symplify 5.0](https://github.com/symplify/symplify/tree/v5.0.0) was released and with that, many checkers were replaced by better ones.

    Checkers 2, 4 and 5 were replaced by `SlamCsFixer\FinalInternalClassFixer` - **class is either final or abstract**.

    `@inject` refactoring was replaced by `AnnotatedPropertyInjectToConstructorInjectionRector` from [Rector](https://github.com/rectorphp/rector).
---

Most of the content was outdated for technical changes or idea was proven to be not very helpful. To avoid confusion among readers, to content was removed.

If you want, you can still see it in the git history, as this blog is [fully open-sourced](https://github.com/tomasvotruba/tomasvotruba.com).
