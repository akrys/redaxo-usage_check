<?php

/**
 * Generelle Configuration
 */
require_once __DIR__.'/FriendsOfRedaxo/addon/UsageCheck/Config.php';

/*
 * Sichergehen, dass der rex_autoloader nicht Stundenlang die PHPUnit-Klassen analysiert.
 *
 * sollte nur bei Aufrufen vom Webserver passieren. Auf der Console (z.B. während PHPUnit läuft) braucht man das
 * Verzeichnis.
 */
try {
	\FriendsOfRedaxo\addon\UsageCheck\Config::checkVendorDir();
} catch (\Exception $e) {
	if (\rex::isBackend()) {
		print $e->getMessage();
	}
	die();
}

spl_autoload_register(array('FriendsOfRedaxo\\addon\\UsageCheck\\Config', 'autoload'), true, true);

rex_fragment::addDirectory(realpath(__DIR__));

if (rex::isBackend()) {
	rex_view::addCssFile($this->getAssetsUrl('css/style.css'));
}
