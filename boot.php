<?php

/**
 * Config-File Redaxo 5
 */
require_once __DIR__.'/general/config.inc.php';

//REDAXO 5
require_once __DIR__.'/akrys/redaxo/addon/UsageCheck/LangFile.php';
try {
	$langDE = new \akrys\redaxo\addon\UsageCheck\LangFile('de_de');
	$langDE->createISOFile();
} catch (\akrys\redaxo\addon\UsageCheck\Exception\LangFileGenError $e) {
	\akrys\redaxo\addon\UsageCheck\Error::getInstance()->add($e->getMessage());
}

try {
	$langEN = new \akrys\redaxo\addon\UsageCheck\LangFile('en_gb');
	$langEN->createISOFile();
} catch (\akrys\redaxo\addon\UsageCheck\Exception\LangFileGenError $e) {
	\akrys\redaxo\addon\UsageCheck\Error::getInstance()->add($e->getMessage());
}
