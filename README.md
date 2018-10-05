# [TomasVotruba.cz](https://www.tomasvotruba.cz) - [Statie](https://github.com/Symplify/Statie) based web

[![Build Status](https://img.shields.io/travis/TomasVotruba/tomasvotruba.cz/master.svg?style=flat-square)](https://travis-ci.org/TomasVotruba/tomasvotruba.cz)

## Install & Run

```sh
git clone git@github.com:TomasVotruba/tomasvotruba.cz.git # use your fork if you want to contribute
composer install
npm install
gulp # see gulpfile.js for more 
```

And open [http://localhost:8000](localhost:8000) in your browser.

That's all!

## Maintenance

How to keep fit and slim!

### Check Status Code of All Links

Once couple of months, check if all external links are still alive, so people won't get lost.

```bash
vendor/bin/http-status-check scan https://tomasvotruba.cz
```
