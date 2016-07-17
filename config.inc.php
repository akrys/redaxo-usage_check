<?php

/**
 * Config-File Redaxo 4
 */
require_once __DIR__.'/general/config.inc.php';

use \akrys\redaxo\addon\UsageCheck\Config;
use \akrys\redaxo\addon\UsageCheck\RedaxoCall;

//REDAXO 4
/* Addon Parameter */
$REX['ADDON']['rxid'][Config::NAME] = Config::ID;
$REX['ADDON']['name'][Config::NAME] = 'Usage Check';
$REX['ADDON']['perm'][Config::NAME] = 'usage_check[]';
$REX['ADDON']['version'][Config::NAME] = Config::VERSION;
$REX['ADDON']['author'][Config::NAME] = 'Axel Krysztofiak <akrys@web.de>';
$REX['ADDON']['supportpage'][Config::NAME] = 'https://github.com/akrys/redaxo-usage_check';
$REX['PERM'][] = 'usage_check[]';

//Eigener Error-Status
$REX['ADDON']['errors'][Config::NAME] = array();

/*
 * I18N gibt es nicht am Frontend, nur im Backend
 *
 * ->
 * Fatal error: Call to a member function appendFile()
 *
 * 2 Möglichkeiten:
 * <code>
 * if ($REX['REDAXO'])
 * {}
 * </code>
 * oder
 *
 * <code>
 * if (isset($I18N))
 * {}
 * </code>
 *
 * Wobei isset($I18N) semantisch genauer ist, als nur zu prüfen, ob man im
 * Backend ist, was ja -genau betrachtet- noch nichts über die Verfügbarkeit
 * der Übersetzungen aussagt.
 *
 */
if (isset($I18N)) {
	/*
	 * Überestzungen hinzufügen
	 * lege ich aktuell aber nur in UTF-8
	 * Wer heute noch ISO nutzt, hat ganz andere Probleme, als fehlende Übersetzungen
	 * eines Redaxo-Addons…
	 */
	$I18N->appendFile($REX['INCLUDE_PATH'].'/addons/'.Config::NAME.'/lang/');

	$pages = array();
	$pages[] = array('overview', RedaxoCall::getAPI()->i18nMsg('akrys_usagecheck_overview'));
	$pages[] = array('picture', RedaxoCall::getAPI()->i18nMsg('akrys_usagecheck_picture'));
	$pages[] = array('module', RedaxoCall::getAPI()->i18nMsg('akrys_usagecheck_module'));
	if ($REX['USER'] && $REX['USER']->isAdmin()) {
		$pages[] = array('action', RedaxoCall::getAPI()->i18nMsg('akrys_usagecheck_action'));
	}
	$pages[] = array('template', RedaxoCall::getAPI()->i18nMsg('akrys_usagecheck_templates'));
	$pages[] = array('changelog', RedaxoCall::getAPI()->i18nMsg('akrys_usagecheck_changelog'));

	$REX['ADDON']['pages'][Config::NAME] = $pages;
}
