<?php

/**
 * Uninstall Script 4
 */
require_once __DIR__.'/general/uninstall.inc.php';

use \akrys\redaxo\addon\UsageCheck\Config;

$REX['ADDON']['install'][Config::NAME] = 0;
