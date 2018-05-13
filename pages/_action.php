<?php

/**
 * Frontend-Ausagbe für die Seite Actions
 */
require_once __DIR__.'/../akrys/redaxo/addon/UsageCheck/Config.php';

/* @var $I18N \i18n */

use \akrys\redaxo\addon\UsageCheck\Config;

$title = new \rex_fragment();
$title->setVar('name', Config::NAME_OUT);
$title->setVar('supage_title', \rex_i18n::rawMsg('akrys_usagecheck_action_subpagetitle'));
$title->setVar('version', Config::VERSION);
echo \rex_view::title($title->parse('fragments/title.php'));


require_once __DIR__.'/../akrys/redaxo/addon/UsageCheck/Modules/Actions.php';
$actions = new \akrys\redaxo\addon\UsageCheck\Modules\Actions();
$actions->setRexSql(\rex_sql::factory());

switch (rex_get('showall', 'string', "")) {
	case 'true':
		$showAll = true;
		break;
	case 'false':
	default:
		$showAll = false;
		break;
}

$actions->showAll($showAll);

$items = $actions->get();

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
	$showAllLinktext = \rex_i18n::rawMsg('akrys_usagecheck_action_link_show_all');
	if ($showAll) {
		$showAllParam = '';
		$showAllLinktext = \rex_i18n::rawMsg('akrys_usagecheck_action_link_show_unused');
	}

// <editor-fold defaultstate="collapsed" desc="Menü">
	$url = 'index.php?page='.Config::NAME.'/'.$subpage.$showAllParam;
	$menu = new \rex_fragment([
		'url' => $url,
		'linktext' => $showAllLinktext,
		'texts' => [
		\rex_i18n::rawMsg('akrys_usagecheck_action_intro_text'),
		],
	]);
	echo $menu->parse('fragments/menu/linktext.php');
// </editor-fold>

	$fragment = new rex_fragment([
		'items' => $items,
		'actions' => $actions,
	]);
	echo $fragment->parse('fragments/modules/actions.php');
}
