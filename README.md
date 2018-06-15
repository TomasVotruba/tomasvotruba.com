# [TomasVotruba.cz](https://www.tomasvotruba.cz) - [Statie](https://github.com/Symplify/Statie) based web

[![Build Status](https://img.shields.io/travis/TomasVotruba/tomasvotruba.cz/master.svg?style=flat-square)](https://travis-ci.org/TomasVotruba/tomasvotruba.cz)
[![Coverage Status](https://img.shields.io/coveralls/TomasVotruba/tomasvotruba.cz/master.svg?style=flat-square)](https://coveralls.io/github/TomasVotruba/tomasvotruba.cz?branch=master)

## Install

```sh
composer create-project tomasvotruba/website tomasvotruba.cz @dev
npm install
```

## Run the website

Now all you gotta do it move to the directory and run the gulp (see [gulpfile.js](/gulpfile.js) for more details):

```sh
cd tomasvotruba.cz
gulp
```

And open [http://localhost:8000](localhost:8000) in your browser.

That's all!

## Check The Grammar

Copy-paste the content to [Grammarly](https://app.grammarly.com/). It's super intuitive and gives option to choose from. I learned a lot about English language from there.

## Check Status Code of All Links

```bash
vendor/bin/http-status-check scan https://tomasvotruba.cz
```
