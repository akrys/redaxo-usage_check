<?php

/**
 * Generelle Configuration
 */
require_once __DIR__.'/../akrys/redaxo/addon/UsageCheck/Config.php';
require_once __DIR__.'/../akrys/redaxo/addon/UsageCheck/Permission.php';
require_once __DIR__.'/../akrys/redaxo/addon/UsageCheck/Error.php';
require_once __DIR__.'/../akrys/redaxo/addon/UsageCheck/RedaxoCall.php';
require_once __DIR__.'/../akrys/redaxo/addon/UsageCheck/Exception/FunctionNotCallableException.php';
require_once __DIR__.'/../akrys/redaxo/addon/UsageCheck/Exception/LangFileGenError.php';

//phpcpd hat den code als duplikat in der Redaxo 4 und in der Redaxo 5 Config gefunden.
require_once __DIR__.'/../akrys/redaxo/addon/UsageCheck/LangFile.php';
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
