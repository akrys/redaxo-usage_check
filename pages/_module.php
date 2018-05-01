<?php

/**
 * Frontend-Ausagbe für die Seite Module
 */
/* @var $I18N \i18n */

use \akrys\redaxo\addon\UsageCheck\Config;
use \akrys\redaxo\addon\UsageCheck\RedaxoCall;

$modules = \akrys\redaxo\addon\UsageCheck\Modules\Modules::create();

$showAll = false;
switch (rex_get('showall', 'string', "")) {
	case 'true':
		$modules->showAll(true);
		$showAll = true;
		break;
	case 'false':
	default:
		//
		break;
}

$title = new \rex_fragment();
$title->setVar('name', Config::NAME_OUT);
$title->setVar('supage_title', \rex_i18n::rawMsg('akrys_usagecheck_module_subpagetitle'));
$title->setVar('version', Config::VERSION);
echo RedaxoCall::getAPI()->getRexTitle($title->parse('fragments/title.php'));

$items = $modules->getModules($showAll);

if ($items === false) {
	echo RedaxoCall::getAPI()->getTaggedErrorMsg(\rex_i18n::rawMsg('akrys_usagecheck_no_rights'));
} else {
	$showAllParam = '&showall=true';
	$showAllLinktext = \rex_i18n::rawMsg('akrys_usagecheck_module_link_show_all');
	if ($showAll) {
		$showAllParam = '';
		$showAllLinktext = \rex_i18n::rawMsg('akrys_usagecheck_module_link_show_unused');
	}

	echo $modules->outputMenu($subpage, $showAllParam, $showAllLinktext);

	$fragment = new rex_fragment([
		'items' => $items,
		'modules' => $modules,
	]);
	echo $fragment->parse('fragments/modules/modules.php');
}
