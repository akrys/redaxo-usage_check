<?php

/**
 * Frontend-Ausagbe für die Seite Changelog
 */
require_once __DIR__.'/../FriendsOfRedaxo/addon/UsageCheck/Config.php';

use \FriendsOfRedaxo\addon\UsageCheck\Config;

$title = new \rex_fragment();
$title->setVar('name', Config::NAME_OUT);
$title->setVar('supage_title', \rex_i18n::rawMsg('akrys_usagecheck_changelog_subpagetitle'));
$title->setVar('version', Config::VERSION);
echo \rex_view::title($title->parse('fragments/title.php'));

if (!function_exists('\\glob')) {
	print 'this page requires the glob function';
	die();
}

$language = \rex::getUser()->getLanguage();
if ($language == '') {
	$language = \rex::getProperty('lang');
}

if (stristr($language, 'de_')) {
	$dir = glob(__DIR__.'/release_notes/de/*_*.php');
} else {
	$dir = glob(__DIR__.'/release_notes/en/*_*.php');
}
rsort($dir);


$fragment = new rex_fragment([
	'dir' => $dir,
	]);
echo $fragment->parse('fragments/modules/changelog.php');
