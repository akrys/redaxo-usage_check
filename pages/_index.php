<?php
/**
 * Grundlegendes Frontend
 */
use \akrys\redaxo\addon\UsageCheck\Config;
use \akrys\redaxo\addon\UsageCheck\RedaxoCall;

//Fehlerausgabe
if (count(\akrys\redaxo\addon\UsageCheck\Error::getInstance()) > 0) {
	$text = '';
	foreach (\akrys\redaxo\addon\UsageCheck\Error::getInstance() as $error) {
		$text.=<<<TEXT

<p>
	<span>
		$error
	</span>
</p>

TEXT;

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
