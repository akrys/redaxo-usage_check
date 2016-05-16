<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

require_once __DIR__.'/../akrys/redaxo/addon/UsageCheck/Config.php';

use akrys\redaxo\addon\UsageCheck\Config;
$error = '';

require_once __DIR__.'/../akrys/redaxo/addon/UsageCheck/LangFile.php';
try {
	$langDE = new \akrys\redaxo\addon\UsageCheck\LangFile('de_de');
	$langDE->createISOFile();
} catch (\akrys\redaxo\addon\UsageCheck\Exception\LangFileGenError $e) {
	$error = $e->getMessage();
}

try {
	$langEN = new \akrys\redaxo\addon\UsageCheck\LangFile('en_gb');
	$langEN->createISOFile();
} catch (\akrys\redaxo\addon\UsageCheck\Exception\LangFileGenError $e) {
	$error = $e->getMessage();
}



switch (\akrys\redaxo\addon\UsageCheck\RedaxoCall::getRedaxoVersion()) {
	case \akrys\redaxo\addon\UsageCheck\RedaxoCall::REDAXO_VERSION_4:
		//REDAXO 4
		if ($error != '') {
			$REX['ADDON']['installmsg'][Config::NAME] = 'dadaölsdkfjalöksdjfklösdj';
		} else {
			$REX['ADDON']['install'][Config::NAME] = 1;
		}

//	@todo Datenbank anpassung
//	$sql='ALTER TABLE `rex_article_slice` ADD INDEX ix_usagecheck_modultypid (`modultyp_id`);';

		break;
	case \akrys\redaxo\addon\UsageCheck\RedaxoCall::REDAXO_VERSION_5:
		//REDAXO 5
		if ($error) {
			$this->setProperty('installmsg', $error);
		} else {
			$this->setProperty('install', true);
		}

		break;
}
