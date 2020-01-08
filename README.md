# [tomasvotruba.com](https://www.tomasvotruba.com) - [Statie](https://github.com/Symplify/Statie) based web

[![Build Status Github Actions](https://img.shields.io/github/workflow/status/tomasvotruba/tomasvotruba.com/Code_Checks?style=flat-square)](https://github.com/TomasVotruba/tomasvotruba.com/actions)

## Install & Run

```sh
git clone git@github.com:TomasVotruba/tomasvotruba.com.git # use your fork if you want to contribute
cd tomasvotruba.com
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
vendor/bin/http-status-check scan https://tomasvotruba.com
```
