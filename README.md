# Tomas Votruba - Sculpin Blog

[![Build Status](https://img.shields.io/travis/TomasVotruba/tomasvotruba.cz.svg?style=flat-square)](https://travis-ci.org/TomasVotruba/tomasvotruba.cz)

## How to run it?

```sh
composer update
vendor/bin/sculpin generate --watch --server --port 8001 # it needs to be run from vendor, to autoload all composer classes 
```

And open `http://localhost:8001`.

## For production?

```sh
vendor/bin/sculpin generate --env=prod
```
