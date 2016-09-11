<?php

/**
 * Install Script Redaxo 4
 */
require_once __DIR__.'/general/install.inc.php';

use \akrys\redaxo\addon\UsageCheck\Config;

if ($error != '') {
	$REX['ADDON']['installmsg'][Config::NAME] = $error;
} else {
	$REX['ADDON']['install'][Config::NAME] = 1;
}
