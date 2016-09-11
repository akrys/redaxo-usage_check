<?php

/*
 * Generelle uninstall-Operationen
 */
require_once __DIR__.'/../akrys/redaxo/addon/UsageCheck/Config.php';

$error = '';
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
