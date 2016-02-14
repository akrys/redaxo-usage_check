<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

require_once __DIR__.'/../akrys/redaxo/addon/UsageCheck/Config.php';
require_once __DIR__.'/../akrys/redaxo/addon/UsageCheck/RedaxoCall.php';

use akrys\redaxo\addon\UsageCheck\Config;
switch (\akrys\redaxo\addon\UsageCheck\RedaxoCall::getRedaxoVersion()) {
	case \akrys\redaxo\addon\UsageCheck\RedaxoCall::REDAXO_VERSION_4:
		$tableClass = 'rex-table';
		break;

	case \akrys\redaxo\addon\UsageCheck\RedaxoCall::REDAXO_VERSION_5:
		$tableClass = 'table table-striped';
		break;
}

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

		echo \akrys\redaxo\addon\UsageCheck\RedaxoCall::errorMsg($text, false);
	}
}

if (\akrys\redaxo\addon\UsageCheck\RedaxoCall::getRedaxoVersion() == \akrys\redaxo\addon\UsageCheck\RedaxoCall::REDAXO_VERSION_4) {
	$page = rex_request('page', 'string');
	$subpage = rex_request('subpage', 'string');

	if ($subpage === '') {
		$subpage = 'overview';
		header('location: index.php?page='.Config::NAME.'&subpage=overview');
	}
} else {

	$subpage = rex_be_controller::getCurrentPagePart(2, 'overview');

//	echo rex_view::title(rex_i18n::msg('backup_title'));
//	var_dump(rex_be_controller::getCurrentPageObject()->getSubPath());
//	include rex_be_controller::getCurrentPageObject()->getSubPath();
}

$contentFile = __DIR__.'/_'.$subpage.'.php';

if (file_exists($contentFile)) {
	include $contentFile;
} else {
	echo \akrys\redaxo\addon\UsageCheck\RedaxoCall::errorMsg(\akrys\redaxo\addon\UsageCheck\RedaxoCall::i18nMsg('akrys_usagecheck_error_content_file_not_found').':<br />'.$contentFile, true);
}

