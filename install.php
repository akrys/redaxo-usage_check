<?php

/**
 * Install Script Redaxo 5
 */
require_once __DIR__.'/general/install.inc.php';

if ($error) {
	$this->setProperty('installmsg', $error);
} else {
	$this->setProperty('install', true);
}
