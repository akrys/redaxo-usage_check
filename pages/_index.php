<?php

/**
 * Grundlegendes Frontend
 */
use \FriendsOfRedaxo\addon\UsageCheck\Config;
use \FriendsOfRedaxo\addon\UsageCheck\RedaxoCall;

//Fehlerausgabe
if (\FriendsOfRedaxo\addon\UsageCheck\Error::getInstance()->count() > 0) {
	$text = '';
	foreach (\FriendsOfRedaxo\addon\UsageCheck\Error::getInstance() as $error) {
		$fragment = new \rex_fragment([
			'text' => $error,
		]);
		$text .= $fragment->parse('fragments/msg/tagged_msg.php');

		echo RedaxoCall::getAPI()->getErrorMsg($text, false);
	}
}

$subpage = rex_be_controller::getCurrentPagePart(2, 'overview');

//	echo rex_view::title(rex_i18n::msg('backup_title'));
//	var_dump(rex_be_controller::getCurrentPageObject()->getSubPath());
//	include rex_be_controller::getCurrentPageObject()->getSubPath();

$contentFile = __DIR__.'/_'.$subpage.'.php';

if (file_exists($contentFile)) {
	include $contentFile;
} else {
	$msg = RedaxoCall::getAPI()->getI18N('akrys_usagecheck_error_content_file_not_found').':<br />'.$contentFile;
	echo RedaxoCall::getAPI()->getErrorMsg($msg, true);
}
