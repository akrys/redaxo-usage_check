<?php

/**
 * Frontend-Ausagbe für die Seite Changelog
 */
use FriendsOfRedaxo\UsageCheck\Addon;

$title = new rex_fragment();
$title->setVar('name', Addon::getInstance()->getName());
$title->setVar('supage_title', rex_i18n::rawMsg('akrys_usagecheck_changelog_subpagetitle'));
$title->setVar('version', Addon::getInstance()->getVersion());
echo rex_view::title($title->parse('fragments/title.php'));

if (!function_exists('\\glob')) {
	print 'this page requires the glob function';
	die();
}

$language = rex::getUser()?->getLanguage();
if ($language == '') {
	$language = rex::getProperty('lang');
}

if (stristr((string) $language, 'de_')) {
	$dir = glob(__DIR__.'/release_notes/de/*_*.php');
} else {
	$dir = glob(__DIR__.'/release_notes/en/*_*.php');
}
if (!$dir) {
	$dir = [];
}
rsort($dir);

$fragment = new rex_fragment([
	'dir' => $dir,
	]);
echo $fragment->parse('fragments/modules/changelog.php');
