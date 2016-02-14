<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
require_once __DIR__.'/../akrys/redaxo/addon/UsageCheck/Config.php';

use akrys\redaxo\addon\UsageCheck\Config;
if (\akrys\redaxo\addon\UsageCheck\RedaxoCall::getRedaxoVersion() == \akrys\redaxo\addon\UsageCheck\RedaxoCall::REDAXO_VERSION_4) {
	//REDAXO 4
	$REX['ADDON']['install'][Config::NAME] = 0;

//	$sql='ALTER TABLE rex_article_slice DROP INDEX modultyp_id;';
} else {
	//REDAXO 5
	$this->setProperty('install', false);
}