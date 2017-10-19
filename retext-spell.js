// run: js retext-spell.js
// run: js retext-spell.js "some text to process"
var retext = require('retext');
var spell = require('retext-spell');
var dictionary = require('dictionary-en-gb');
var report = require('vfile-reporter');

var input = process.argv[2];

retext()
    .use(spell, dictionary)
    .process(input, function (err, file) {
        console.error(report(err || file));
    });
