<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

require_once __DIR__.'/akrys/redaxo/addon/UsageCheck/Config.php';

use akrys\redaxo\addon\UsageCheck\Config;
// @todo Datenbank anpassung

$error = '';
if ($error != '') {
	$REX['ADDON']['installmsg'][Config::NAME] = 'dadaölsdkfjalöksdjfklösdj';
} else {
	$REX['ADDON']['install'][Config::NAME] = 1;
}



//$sql='ALTER TABLE `rex_article_slice` ADD INDEX ix_usagecheck_modultypid (`modultyp_id`);';

