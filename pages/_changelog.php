<?php

/**
 * Frontend-Ausagbe für die Seite Changelog
 */
require_once __DIR__.'/../akrys/redaxo/addon/UsageCheck/Config.php';
require_once __DIR__.'/../akrys/redaxo/addon/UsageCheck/RedaxoCall.php';

use \akrys\redaxo\addon\UsageCheck\Config;
use \akrys\redaxo\addon\UsageCheck\RedaxoCall;

$title = new \rex_fragment();
$title->setVar('name', Config::NAME_OUT);
$title->setVar('supage_title', RedaxoCall::getAPI()->getI18N('akrys_usagecheck_changelog_subpagetitle'));
$title->setVar('version', Config::VERSION);
echo RedaxoCall::getAPI()->getRexTitle($title->parse('fragments/title.php'));

if (!function_exists('\\glob')) {
	print 'this page requires the glob function';
	die();
}

if (stristr(RedaxoCall::getAPI()->getLang(), 'de_')) {
	$dir = glob(__DIR__.'/release_notes/de/*_*.php');
} else {
	$dir = glob(__DIR__.'/release_notes/en/*_*.php');
}
rsort($dir);


$fragment = new rex_fragment([
	'dir' => $dir,
	]);
echo $fragment->parse('fragments/modules/changelog.php');
