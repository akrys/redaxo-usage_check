<?php

/**
 * Generelle Configuration
 */
require_once __DIR__.'/../akrys/redaxo/addon/UsageCheck/Config.php';

/*
 * Sichergehen, dass der rex_autoloader nicht Stundenlang die PHPUnit-Klassen analysiert.
 *
 * sollte nur bei Aufrufen vom Webserver passieren. Auf der Console (z.B. während PHPUnit läuft) braucht man das
 * Verzeichnis.
 */
try {
	\akrys\redaxo\addon\UsageCheck\Config::checkVendorDir();
} catch (\Exception $e) {
	if (\rex::isBackend()) {
		print $e->getMessage();
	}
	die();
}

spl_autoload_register(array('akrys\\redaxo\\addon\\UsageCheck\\Config', 'autoload'), true, true);



//	//zu aktivieren, wenn es mit dem Autoloader doch nicht funktioniert.
//	require_once __DIR__.'/../akrys/redaxo/addon/UsageCheck/Config.php';
//	require_once __DIR__.'/../akrys/redaxo/addon/UsageCheck/Permission.php';
//	require_once __DIR__.'/../akrys/redaxo/addon/UsageCheck/Error.php';
//	require_once __DIR__.'/../akrys/redaxo/addon/UsageCheck/Exception/CloneException.php';
//
//	require_once __DIR__.'/../akrys/redaxo/addon/UsageCheck/RedaxoCall.php';
//	require_once __DIR__.'/../akrys/redaxo/addon/UsageCheck/RexV4/RedaxoCallAPI.php';
//	require_once __DIR__.'/../akrys/redaxo/addon/UsageCheck/RexV5/RedaxoCallAPI.php';
//
//	require_once __DIR__.'/../akrys/redaxo/addon/UsageCheck/LangFile.php';
//	require_once __DIR__.'/../akrys/redaxo/addon/UsageCheck/Exception/LangFileGenError.php';
//	require_once __DIR__.'/../akrys/redaxo/addon/UsageCheck/Exception/FunctionNotCallableException.php';
//	require_once __DIR__.'/../akrys/redaxo/addon/UsageCheck/Exception/InvalidVersionException.php';
//
//	require_once __DIR__.'/../akrys/redaxo/addon/UsageCheck/Modules/Actions.php';
//	require_once __DIR__.'/../akrys/redaxo/addon/UsageCheck/RexV4/Modules/Actions.php';
//	require_once __DIR__.'/../akrys/redaxo/addon/UsageCheck/RexV5/Modules/Actions.php';
//
//	require_once __DIR__.'/../akrys/redaxo/addon/UsageCheck/Modules/Modules.php';
//	require_once __DIR__.'/../akrys/redaxo/addon/UsageCheck/RexV4/Modules/Modules.php';
//	require_once __DIR__.'/../akrys/redaxo/addon/UsageCheck/RexV5/Modules/Modules.php';
//
//	require_once __DIR__.'/../akrys/redaxo/addon/UsageCheck/Modules/Templates.php';
//	require_once __DIR__.'/../akrys/redaxo/addon/UsageCheck/RexV4/Modules/Templates.php';
//	require_once __DIR__.'/../akrys/redaxo/addon/UsageCheck/RexV5/Modules/Templates.php';
//
//	require_once __DIR__.'/../akrys/redaxo/addon/UsageCheck/Modules/Pictures.php';
//	require_once __DIR__.'/../akrys/redaxo/addon/UsageCheck/RexV4/Modules/Pictures.php';
//	require_once __DIR__.'/../akrys/redaxo/addon/UsageCheck/RexV5/Modules/Pictures.php';
//
//	require_once __DIR__.'/../akrys/redaxo/addon/UsageCheck/RexV4/Permission.php';
//	require_once __DIR__.'/../akrys/redaxo/addon/UsageCheck/RexV5/Permission.php';
//phpcpd hat den code als duplikat in der Redaxo 4 und in der Redaxo 5 Config gefunden.
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
