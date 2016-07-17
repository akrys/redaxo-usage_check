'use strict';

var gulp = require('gulp');



/*
 *
 * composer global require --dev "pdepend/pdepend"
 * composer global require --dev "phpmd/phpmd"
 * composer global require --dev "squizlabs/php_codesniffer"
 * composer global require "phpunit/phpunit"
 * composer global require "phpdocumentor/phpdocumentor"
 * composer global require "sebastian/phpcpd"
 */
var phplint = require('phplint').lint
var shell = require('gulp-shell');
var phpcs = require('gulp-phpcs');
var phpunit = require('gulp-phpunit');

gulp.task('phplint', function (cb) {
	phplint(['**/*.php', '!node_modules/**', '!vendor/**', ], {limit: 10}, function (err, stdout, stderr) {
		if (err) {
			cb(err)
			process.exit(1)
		} else {
			cb()
		}

	})
})


gulp.task('phpcs', function () {
	return gulp.src(['./**/*.php', '!node_modules/', '!vendor/**/*'])
		.pipe(phpcs({
			bin: 'vendor/bin/phpcs',
			standard: 'phpcs.xml',
		}))
		.pipe(phpcs.reporter('log'));
});

gulp.task('phpcbf', shell.task(['vendor/bin/phpcbf --standard=PSR2 --ignore=vendor/,some/other/folder folder/to/include another/folder/to/include somefiletoinclude.php server.php']));


gulp.task('phpunit', function () {
	return gulp.src('phpunit.xml')
		.pipe(phpunit('vendor/bin/phpunit'))
		.on('error', console.error('TESTS FAILED:\nYou killed someones baby!'))
		.pipe(function () {
			console.log('TESTS PASSED:\nAwesome you rock!');
		});
});

gulp.task('phpdoc', shell.task(['vendor/bin/phpdoc']));

//,controversial
gulp.task('phpmd', shell.task(['vendor/bin/phpmd ./ html codesize,unusedcode,naming,design,cleancode --reportfile docs/phpmd.html --exclude vendor/,node_modules/,docs/ --suffixes php']));

gulp.task('phpcpd', shell.task(['vendor/bin/phpcpd --min-tokens 50 --log-pmd=docs/phpcpd.xml -n --exclude vendor node_modules docs ./']));


var pdependDocs = 'docs/pdepend';
gulp.task('pdepend', shell.task([
	'mkdir -p ' + pdependDocs,
	'vendor/bin/pdepend --summary-xml=' + pdependDocs + '/pdepend.xml --jdepend-chart=' + pdependDocs + '/chart.svg --overview-pyramid=' + pdependDocs + '/pyramid.svg --ignore=vendor,node_modules --suffix=php .'
]));



//gulp.task('watch', function () {
//    gulp.watch(['composer.json', 'phpunit.xml', './**/*.php', '!vendor/**/*', '!node_modules/**/*'], function (event) {
//        console.log('File ' + event.path + ' was ' + event.type + ', running tasks...');
//    });
//    gulp.watch('composer.json', ['dump-autoload']);
//    gulp.watch(['phpunit.xml', './**/*.php', '!vendor/**/*', '!node_modules/**/*'], ['phplint']);
//});

gulp.task('default', ['phplint', 'phpcs', 'phpdoc', 'phpmd', 'pdepend', 'phpcpd']);
//gulp.task('complete', ['phplint', 'phpcs', 'phpunit', 'watch']);



