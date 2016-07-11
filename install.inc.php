<?php

/**
 * Install Script Redaxo 4
 */
require_once __DIR__.'/general/install.inc.php';

//REDAXO 4
if ($error != '') {
	$REX['ADDON']['installmsg'][Config::NAME] = $error;
} else {
	$REX['ADDON']['install'][Config::NAME] = 1;
}

//	@todo Datenbank anpassung
//	$sql='ALTER TABLE `rex_article_slice` ADD INDEX ix_usagecheck_modultypid (`modultyp_id`);';
