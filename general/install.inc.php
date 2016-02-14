<?php
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

require_once __DIR__.'/../akrys/redaxo/addon/UsageCheck/Config.php';

use akrys\redaxo\addon\UsageCheck\Config;
if (\akrys\redaxo\addon\UsageCheck\RedaxoCall::getRedaxoVersion() == \akrys\redaxo\addon\UsageCheck\RedaxoCall::REDAXO_VERSION_4) {
	//REDAXO 4

	$error = '';
	if ($error != '') {
		$REX['ADDON']['installmsg'][Config::NAME] = 'dadaölsdkfjalöksdjfklösdj';
	} else {
		$REX['ADDON']['install'][Config::NAME] = 1;
	}



//	@todo Datenbank anpassung
//	$sql='ALTER TABLE `rex_article_slice` ADD INDEX ix_usagecheck_modultypid (`modultyp_id`);';
} else {
	//REDAXO 5

	$error = '';
	// Überprüfungen
	if ($error) {
		$this->setProperty('installmsg', $error);
	} else {
		$this->setProperty('install', true);
	}
}