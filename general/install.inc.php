<?php

/*
 * Generelle uninstall-Operationen
 */

require_once __DIR__.'/../akrys/redaxo/addon/UsageCheck/Config.php';

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
