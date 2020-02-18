<?php

/**
 * Frontend-Ausagbe für die Seite Module
 */
use FriendsOfRedaxo\addon\UsageCheck\Addon;
use FriendsOfRedaxo\addon\UsageCheck\Config;
use FriendsOfRedaxo\addon\UsageCheck\Modules\Modules;

$modules = new Modules();
$modules->setRexSql(rex_sql::factory());

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

$title = new rex_fragment();
$title->setVar('name', Addon::getInstance()->getName());
$title->setVar('supage_title', rex_i18n::rawMsg('akrys_usagecheck_module_subpagetitle'));
$title->setVar('version', Addon::getInstance()->getVersion());
echo rex_view::title($title->parse('fragments/title.php'));

$items = $modules->get($showAll);

if ($items === false) {
	$msg = rex_i18n::rawMsg('akrys_usagecheck_no_rights');
	$fragment = new rex_fragment([
		'text' => $index,
	]);
	$fragment = new rex_fragment([
		'text' => $fragment->parse('fragments/msg/tagged_msg.php'),
	]);
	echo $fragment->parse('fragments/msg/error.php');
} else {
	$showAllParam = '&showall=true';
	$showAllLinktext = rex_i18n::rawMsg('akrys_usagecheck_module_link_show_all');
	if ($showAll) {
		$showAllParam = '';
		$showAllLinktext = rex_i18n::rawMsg('akrys_usagecheck_module_link_show_unused');
	}

// <editor-fold defaultstate="collapsed" desc="Menü">
	$url = 'index.php?page='.Config::NAME.'/'.$subpage.$showAllParam;
	$menu = new rex_fragment([
		'url' => $url,
		'linktext' => $showAllLinktext,
		'texts' => [
			rex_i18n::rawMsg('akrys_usagecheck_module_intro_text'),
		],
	]);
	echo $menu->parse('fragments/menu/linktext.php');
// </editor-fold>

	$fragment = new rex_fragment([
		'items' => $items,
		'modules' => $modules,
	]);
	echo $fragment->parse('fragments/modules/modules.php');
}
