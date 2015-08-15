<?php

require_once __DIR__.'/akrys/redaxo/addon/UserCheck/Config.php';

use akrys\redaxo\addon\UserCheck\Config;
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
	$REX['ADDON']['pages'][Config::NAME] = array(
		array('overview', $I18N->msg('akrys_usagecheck_overview')),
		array('picture', $I18N->msg('akrys_usagecheck_picture')),
		array('module', $I18N->msg('akrys_usagecheck_module')),
		array('template', $I18N->msg('akrys_usagecheck_templates')),
		array('changelog', $I18N->msg('akrys_usagecheck_changelog')),
	);
}

