# TomasVotruba.cz - [Statie](https://github.com/Symplify/Statie) based web

[![Build Status](https://img.shields.io/travis/TomasVotruba/tomasvotruba.cz.svg?style=flat-square)](https://travis-ci.org/TomasVotruba/tomasvotruba.cz)


## Install

```sh
composer create-project pehapkari/website pehapkari.cz @dev
```

To enabled live reload after any change, we need one more thing - *gulp*:

```bash
npm install -g gulp gulp-watch
```

## Run the website

Now all you gotta do it move to the directory and run the gulp (see [gulpfile.js](/gulpfile.js) for more details):

```sh
cd pehapkari.cz
gulp
```

And open [http://localhost:8000](http://localhost:8000) in your browser.

That's all!
