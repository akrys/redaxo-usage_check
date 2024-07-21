<?php

/**
 * Anzeige der nicht verwendeten Bilder.
 */
use FriendsOfRedaxo\UsageCheck\Addon;
use FriendsOfRedaxo\UsageCheck\Config;
use FriendsOfRedaxo\UsageCheck\Modules\Pictures;

if(!isset($subpage)) {
	throw new \Exception("this file should not be called directly.");
}

$pictures = new Pictures();
$pictures->setRexSql(rex_sql::factory());

$title = new rex_fragment();
$title->setVar('name', Addon::getInstance()->getName());
$title->setVar('supage_title', rex_i18n::rawMsg('akrys_usagecheck_images_subpagetitle'));
$title->setVar('version', Addon::getInstance()->getVersion());
echo rex_view::title($title->parse('fragments/title.php'));

$showAll = false;
switch (rex_get('showall', 'string', "")) {
	case 'true':
		$pictures->showAll(true);
		$showAll = true;
		break;
	case 'false':
	default:
		//
		break;
}
$catParam = '';
$fileCat = rex_get('rex_file_category', 'int', 0);
if ($fileCat) {
	$pictures->setCategory($fileCat);
	$catParam = '&rex_file_category='.$fileCat;
}

$items = $pictures->get();

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
	$showAllLinktext = rex_i18n::rawMsg('akrys_usagecheck_images_link_show_unused');
	$showAllParam = '';
	if (!$showAll) {
		$showAllLinktext = rex_i18n::rawMsg('akrys_usagecheck_images_link_show_all');
		$showAllParam = '&showall=true';
	}

// <editor-fold defaultstate="collapsed" desc="Menü">
	$url = 'index.php?page='.Config::NAME.'/'.$subpage.$showAllParam.$catParam;
	$menu = new rex_fragment([
		'url' => $url,
		'linktext' => $showAllLinktext,
		'texts' => [
			rex_i18n::rawMsg('akrys_usagecheck_images_intro_text'),
		],
	]);
	echo $menu->parse('fragments/menu/linktext.php');
// </editor-fold>

	$fragment = new rex_fragment([
		'items' => $items,
		'pictures' => $pictures,
	]);
	echo $fragment->parse('fragments/modules/pictures.php');
}
