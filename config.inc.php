<?php

require_once __DIR__.'/akrys/redaxo/addon/UsageCheck/Config.php';
require_once __DIR__.'/akrys/redaxo/addon/UsageCheck/Permission.php';

use akrys\redaxo\addon\UsageCheck\Config;
/* Addon Parameter */
$REX['ADDON']['rxid'][Config::NAME] = Config::ID;
$REX['ADDON']['name'][Config::NAME] = 'Usage Check';
$REX['ADDON']['perm'][Config::NAME] = 'usage_check[]';
$REX['ADDON']['version'][Config::NAME] = '1.0';
$REX['ADDON']['author'][Config::NAME] = 'Axel Krysztofiak <akrys@web.de>';
$REX['ADDON']['supportpage'][Config::NAME] = 'localhost/nixda';
$REX['PERM'][] = 'usage_check[]';


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

	$REX['ADDON']['pages'][akrys\redaxo\addon\UsageCheck\Config::NAME] = array();


	$REX['ADDON']['pages'][akrys\redaxo\addon\UsageCheck\Config::NAME][] = array('overview', $I18N->msg('akrys_usagecheck_overview'));
	$REX['ADDON']['pages'][akrys\redaxo\addon\UsageCheck\Config::NAME][] = array('picture', $I18N->msg('akrys_usagecheck_picture'));
	$REX['ADDON']['pages'][akrys\redaxo\addon\UsageCheck\Config::NAME][] = array('module', $I18N->msg('akrys_usagecheck_module'));
	if ($REX['USER'] && $REX['USER']->isAdmin()) {
		$REX['ADDON']['pages'][akrys\redaxo\addon\UsageCheck\Config::NAME][] = array('action', $I18N->msg('akrys_usagecheck_action'));
	}
	$REX['ADDON']['pages'][akrys\redaxo\addon\UsageCheck\Config::NAME][] = array('template', $I18N->msg('akrys_usagecheck_templates'));
	$REX['ADDON']['pages'][akrys\redaxo\addon\UsageCheck\Config::NAME][] = array('changelog', $I18N->msg('akrys_usagecheck_changelog'));
}




/*

//Grundlegende Idee:
//Die Menüpunkte anhand der Berechtigungen hinzufügen.
//
//Mögliche Extension-Points
//- ADDONS_INCLUDED
//- PAGE_HEADER
//- PAGE_CHECKED
//
//ADDONS_INCLUDED geht nicht, weil die Berechtigungen noch nicht ausgewertet wurden.
//PAGE_HEADER geht nicht, weil die Pages schon zum Menü hinzugefügt wurden.
//PAGE_CHECKED geht nicht, weil erst die Seiten hinzugefügt werden und dann erst Berechtigungen ausgewertet werden.
//
//s. auch http://www.redaxo.org/de/doku/tutorials/addon-entwicklung-in-7-folgen/addon-entwicklung-teil-6---seitengenerierung/

rex_register_extension('PAGE_CHECKED', '\\usage_check_perms');

function usage_check_perms()
{
	global $REX, $I18N;
	var_dump(array_keys($GLOBALS['REX']['USER']->pages));
	var_dump(OOAddon::isAvailable('xform'));
	var_dump(OOPlugin::isAvailable('xform', 'manager'));
}
 */
