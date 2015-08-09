<?php

require_once __DIR__.'/akrys/redaxo/addon/UserCheck/Config.php';

use akrys\redaxo\addon\UserCheck\Config;
/*
 * Überestzungen hinzufügen
 * lege ich aktuell aber nur in UTF-8
 * Wer heute noch ISO nutzt, hat ganz andere Probleme, als fehlende Übersetzungen
 * eines Redaxo-Addons…
 */
$I18N->appendFile($REX['INCLUDE_PATH'].'/addons/'.Config::NAME.'/lang/');

/* Addon Parameter */
$REX['ADDON']['rxid'][Config::NAME] = Config::ID;
$REX['ADDON']['name'][Config::NAME] = 'Usage Check';
$REX['ADDON']['perm'][Config::NAME] = 'usage_check[]';
$REX['ADDON']['version'][Config::NAME] = '1.0';
$REX['ADDON']['author'][Config::NAME] = 'Axel Krysztofiak <akrys@web.de>';
$REX['ADDON']['supportpage'][Config::NAME] = 'localhost/nixda';
$REX['PERM'][] = 'usage_check[]';

$REX['ADDON']['pages'][Config::NAME] = array(
	array('overview', $I18N->msg('akrys_usagecheck_overview')),
	array('picture', $I18N->msg('akrys_usagecheck_picture')),
	array('module', $I18N->msg('akrys_usagecheck_module')),
	array('template', $I18N->msg('akrys_usagecheck_templates')),
	array('changelog', $I18N->msg('akrys_usagecheck_changelog')),
);


/*
$REX["ADDON"][Config::NAME]["settings"] = array(
	'version' => 'V 1.0 2013-12-04',
	'links' => array(
		'copyaa' => 'Daten kopierenaa',
		'changelogaaa' => 'Changelogaa',
	)
);
*/
/*
foreach ($REX["ADDON"][Config::NAME]["settings"]['links'] as $key => $value) {
	$REX['ADDON'][Config::NAME]['SUBPAGES'][] = array($key, $value);
}
*/

/*

				rex_register_extension('PAGE_HEADER', 'a135_addAssets');
				rex_register_extension('MEDIA_FORM_EDIT', 'a135_addLink2Media', $params);
				rex_register_extension('MEDIA_LIST_FUNCTIONS', 'a135_addLink2Medialist');

				rex_register_extension('OUTPUT_FILTER', 'a135_showCrop');
// delete thumbnails on mediapool changes
if (!function_exists('akrys_media_updated')) {
	rex_register_extension('MEDIA_UPDATED', 'akrys_media_updated');

	function akrys_media_updated($params)
	{
		//hier könnte man ein update machen mit den zusatzfeldern.
	}
}
 */

