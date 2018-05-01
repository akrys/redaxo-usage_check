<?php

/**
 * Frontend-Ausagbe für die Seite Module
 */
/* @var $I18N \i18n */

use \akrys\redaxo\addon\UsageCheck\Config;

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
echo \rex_view::title($title->parse('fragments/title.php'));

$items = $modules->getModules($showAll);

if ($items === false) {
	$msg = \rex_i18n::rawMsg('akrys_usagecheck_no_rights');
	$fragment = new \rex_fragment([
		'text' => $index,
	]);
	$fragment = new \rex_fragment([
		'text' => $fragment->parse('fragments/msg/tagged_msg.php'),
	]);
	echo $fragment->parse('fragments/msg/error.php');
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
