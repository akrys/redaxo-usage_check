<?php

/**
 * Frontend-Ausagbe für die Seite Tempalte
 */
use FriendsOfRedaxo\addon\UsageCheck\Addon;
use FriendsOfRedaxo\addon\UsageCheck\Config;
use FriendsOfRedaxo\addon\UsageCheck\Modules\Templates;

if(!isset($subpage)) {
	throw new \Exception("this file should not be called directly.");
}

switch (rex_get('showall', 'string', "")) {
	case 'true':
		$showAll = true;
		break;
	case 'false':
	default:
		$showAll = false;
		break;
}

switch (rex_get('showinactive', 'string', "")) {
	case 'true':
		$showInactive = true;
		break;
	case 'false':
	default:
		$showInactive = false;
		break;
}


$title = new rex_fragment();
$title->setVar('name', Addon::getInstance()->getName());
$title->setVar('supage_title', rex_i18n::rawMsg('akrys_usagecheck_template_subpagetitle'));
$title->setVar('version', Addon::getInstance()->getVersion());
echo rex_view::title($title->parse('fragments/title.php'));

$templates = new Templates();
$templates->setRexSql(rex_sql::factory());

if ($showAll) {
	$templates->showAll($showAll);
}
if ($showInactive) {
	$templates->showInactive($showInactive);
}
$items = $templates->get();

if (empty($items)) {
	$msg = rex_i18n::rawMsg('akrys_usagecheck_no_rights');
	$fragment = new rex_fragment([
		'text' => $msg,
	]);
	$fragment = new rex_fragment([
		'text' => $fragment->parse('fragments/msg/tagged_msg.php'),
	]);
	echo $fragment->parse('fragments/msg/error.php');
} else {
// <editor-fold defaultstate="collapsed" desc="Menü">

	$param = [
		'showAllParam' => '',
		'showAllParamCurr' => '&showall=true',
		'showAllLinktext' => 'akrys_usagecheck_template_link_show_unused',
		//
		'showInactiveParam' => '',
		'showInactiveParamCurr' => '&showinactive=true',
		'showInactiveLinktext' => 'akrys_usagecheck_template_link_show_active',
	];

	if (!$showAll) {
		$param['showAllParam'] = '&showall=true';
		$param['showAllParamCurr'] = '';
		$param['showAllLinktext'] = 'akrys_usagecheck_template_link_show_all';
	}

	if (!$showInactive) {
		$param['showInactiveParam'] = '&showinactive=true';
		$param['showInactiveParamCurr'] = '';
		$param['showInactiveLinktext'] = 'akrys_usagecheck_template_link_show_active_inactive';
	}

	$params = [
		'links' => [
			[
				'url' => 'index.php?page='.Config::NAME.'/'.$subpage.
				$param['showAllParam'].$param['showInactiveParamCurr'],
				'text' => rex_i18n::rawMsg($param['showAllLinktext']),
				'admin' => false,
			],
			[
				'url' => 'index.php?page='.Config::NAME.'/'.$subpage.
				$param['showAllParamCurr'].$param['showInactiveParam'],
				'text' => rex_i18n::rawMsg($param['showInactiveLinktext']),
				'admin' => true,
			],
		],
		'texts' => [
			rex_i18n::rawMsg('akrys_usagecheck_template_intro_text'),
		],
		'user' => rex::getUser(),
	];

	$menu = new rex_fragment($params);


	echo $menu->parse('fragments/menu/linklist.php');
// </editor-fold>

	$fragment = new rex_fragment([
		'items' => $items,
		'templates' => $templates,
	]);
	echo $fragment->parse('fragments/modules/templates.php');
}
