<?php

/**
 * Grundlegendes Frontend
 */

$locale = null;
$language = \rex::getUser()->getLanguage();
if ($language == '') {
	$language = \rex::getProperty('lang');
}
if (!stristr($language, 'de_') && !stristr($language, 'en_')) {
	$locale = rex_i18n::getLocale();
	rex_i18n::setLocale('en_gb');
}
//Fehlerausgabe
$fragment = new rex_fragment(['msg' => FriendsOfRedaxo\addon\UsageCheck\Error::getInstance()]);
echo $fragment->parse('msg/error_box.php');

$subpage = rex_be_controller::getCurrentPagePart(2, 'overview');

//	echo rex_view::title(rex_i18n::msg('backup_title'));
//	var_dump(rex_be_controller::getCurrentPageObject()->getSubPath());
//	include rex_be_controller::getCurrentPageObject()->getSubPath();

$contentFile = __DIR__.'/_'.$subpage.'.php';

if (file_exists($contentFile)) {
	include $contentFile;
} else {
	$msg = \rex_i18n::rawMsg('akrys_usagecheck_error_content_file_not_found').':<br />'.$contentFile;
	$fragment = new \rex_fragment([
		'text' => $msg,
	]);
	echo $fragment->parse('fragments/msg/error.php');
}

//restore
if (isset($locale)) {
	\rex_i18n::setLocale($locale);
}
