'use strict';

var gulp = require('gulp');
var phplint = require('phplint').lint;
var shell = require('gulp-shell');
var phpcs = require('gulp-phpcs');
var phpunit = require('gulp-phpunit');




///////////////////// PHP Lints /////////////////////
gulp.task('phplint', function (cb) {
	var directories = [
		'**/*.php',
		'!node_modules/**',
		'!vendor/**',
		'!akrys/redaxo/addon/UsageCheck/Tests/**/*'
	];
	var options = {limit: 10};

	phplint(directories, options, function (err, stdout, stderr) {
		if (err) {
			cb(err);
			process.exit(1);
		} else {
			cb();
		}
	});
});




///////////////////// PHP CS /////////////////////
gulp.task('phpcs', function () {
	//zunächst mal die alte Datei löschen.
	//Ansonsten würde die alte Datei stehen bleiben und Fehler ankreiden, obwohl alles OK ist.
	var logfile = "docs/phpcs.txt";
	var fs = require('fs');

	try {
		//ob ich jetzt ein try-catch-block reinschreibe, weil der Existenz-Check (fs.statSync() / fs.stat()) einen Fehler
		//wirft, weil die Datei nicht existiert oder ob der gleiche Fehler beim Löschen selbst auftritt, macht jetzt
		//ehrlich gesagt nun auch keinen größeren Unterschied.
//		if (fs.statSync(logfile)) {
		fs.unlinkSync(logfile);
		console.log('successfully deleted ' + logfile);
//		}
	} catch (error) {
	}

	var directories = [
		'./**/*.php',
		'!node_modules/',
		'!vendor/**/*',
		'!akrys/redaxo/addon/UsageCheck/Tests/**/*'
	];

	return gulp.src(directories)
		.pipe(phpcs({
			bin: 'vendor/bin/phpcs',
			standard: 'phpcs.xml'
		}))
		.pipe(phpcs.reporter('log'))
		.pipe(phpcs.reporter('file', {path: logfile}));
});

//gulp.task('phpcbf', shell.task(['vendor/bin/phpcbf --standard=PSR2 --ignore=vendor/,some/other/folder folder/to/include another/folder/to/include somefiletoinclude.php server.php']));




///////////////////// PHP Unit /////////////////////
gulp.task('phpunit', function () {
	var options = {
		debug: false,
		statusLine: false,
		configurationFile: './phpunit.xml',
		notify: false
	};
	gulp.src('phpunit.xml')
		.pipe(phpunit('./vendor/bin/phpunit', options));
});

gulp.task('phpdoc', shell.task(['vendor/bin/phpdoc']));




///////////////////// PHP MD /////////////////////
//,controversial
gulp.task('phpmd', shell.task(['vendor/bin/phpmd ./ html codesize,unusedcode,naming,design,cleancode --reportfile docs/phpmd.html --exclude vendor/,node_modules/,docs/,akrys/redaxo/addon/UsageCheck/Tests/ --suffixes php']));




///////////////////// PHP CPD /////////////////////
gulp.task('phpcpd', shell.task(['vendor/bin/phpcpd --min-tokens 50 --log-pmd=docs/phpcpd.xml -n --exclude vendor --exclude node_modules --exclude docs --exclude akrys/redaxo/addon/UsageCheck/Tests ./']));




///////////////////// PHP Depend /////////////////////
var pdependDocs = 'docs/pdepend';
gulp.task('pdepend', shell.task([
	'mkdir -p ' + pdependDocs,
	'vendor/bin/pdepend --summary-xml=' + pdependDocs + '/pdepend.xml --jdepend-chart=' + pdependDocs + '/chart.svg --overview-pyramid=' + pdependDocs + '/pyramid.svg --ignore=vendor,node_modules,akrys/redaxo/addon/UsageCheck/Tests/ --suffix=php .'
]));




//gulp.task('watch', function () {
//    gulp.watch(['composer.json', 'phpunit.xml', './**/*.php', '!vendor/**/*', '!node_modules/**/*'], function (event) {
//        console.log('File ' + event.path + ' was ' + event.type + ', running tasks...');
//    });
//    gulp.watch('composer.json', ['dump-autoload']);
//    gulp.watch(['phpunit.xml', './**/*.php', '!vendor/**/*', '!node_modules/**/*'], ['phplint']);
//});

gulp.task('default', gulp.parallel('phplint', 'phpcs', 'phpdoc', 'phpmd', 'pdepend', 'phpcpd', 'phpunit'));
gulp.task('md', gulp.series('phpmd'));
gulp.task('doc', gulp.series('phpdoc'));
gulp.task('lint', gulp.series('phplint'));
gulp.task('cs', gulp.series('phpcs'));
gulp.task('depend', gulp.series('pdepend'));
gulp.task('unit', gulp.series('phpunit'));

