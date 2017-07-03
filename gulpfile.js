// install: npm install -g gulp gulp-watch
// run: gulp

var gulp = require('gulp');
var watch = require('gulp-watch');
var exec = require('child_process').exec;

gulp.task('default', function () {
    // Generate current version
    exec('vendor/bin/statie generate source', function (err, stdout, stderr) {
        console.log(stdout);
        console.log(stderr);
    });

    // Run local server, open localhost:8000 in your browser
    exec('php -S localhost:8000 -t output');

    // For the second arg see: https://github.com/floatdrop/gulp-watch/issues/242#issuecomment-230209702
    return watch(['source/**/*', '!**/*___jb_tmp___'], { ignoreInitial: false })
        .on('change', function() {
            exec('vendor/bin/statie generate source', function (err, stdout, stderr) {
                console.log(stdout);
                console.log(stderr);
            });
        });
});
