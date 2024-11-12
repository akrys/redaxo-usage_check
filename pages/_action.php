<?php

/**
 * Frontend-Ausagbe für die Seite Actions
 */

use FriendsOfRedaxo\UsageCheck\Addon;
use FriendsOfRedaxo\UsageCheck\Config;
use FriendsOfRedaxo\UsageCheck\Exception\PermissionException;
use FriendsOfRedaxo\UsageCheck\Modules\Actions;

if (!isset($subpage)) {
	throw new Exception("this file should not be called directly.");
}

$title = new rex_fragment();
$title->setVar('name', Addon::getInstance()->getName());
$title->setVar('supage_title', rex_i18n::rawMsg('akrys_usagecheck_action_subpagetitle'));
$title->setVar('version', Addon::getInstance()->getVersion());
echo rex_view::title($title->parse('fragments/title.php'));

$actions = new Actions();
$actions->setRexSql(rex_sql::factory());

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

try {
	$items = $actions->get();

	$showAllParam = '&showall=true';
	$showAllLinktext = rex_i18n::rawMsg('akrys_usagecheck_action_link_show_all');
	if ($showAll) {
		$showAllParam = '';
		$showAllLinktext = rex_i18n::rawMsg('akrys_usagecheck_action_link_show_unused');
	}

// <editor-fold defaultstate="collapsed" desc="Menü">
	$url = 'index.php?page='.Config::NAME.'/'.$subpage.$showAllParam;
	$menu = new rex_fragment([
		'url' => $url,
		'linktext' => $showAllLinktext,
		'texts' => [
			rex_i18n::rawMsg('akrys_usagecheck_action_intro_text'),
		],
	]);
	echo $menu->parse('fragments/menu/linktext.php');
// </editor-fold>

	$fragment = new rex_fragment([
		'items' => $items,
		'actions' => $actions,
	]);
	echo $fragment->parse('fragments/modules/actions.php');
} catch (PermissionException $e) {
	$msg = rex_i18n::rawMsg('akrys_usagecheck_no_rights');
	$fragment = new rex_fragment([
		'text' => $msg,
	]);
	$fragment = new rex_fragment([
		'text' => $fragment->parse('fragments/msg/tagged_msg.php'),
	]);
	echo $fragment->parse('fragments/msg/error.php');
}
