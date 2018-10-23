<?php

/**
 * Anzeige der nicht verwendeten Bilder.
 */
use \akrys\redaxo\addon\UsageCheck\Config;

$pictures = new \akrys\redaxo\addon\UsageCheck\Modules\Pictures();
$pictures->setRexSql(\rex_sql::factory());

$title = new \rex_fragment();
$title->setVar('name', Config::NAME_OUT);
$title->setVar('supage_title', \rex_i18n::rawMsg('akrys_usagecheck_images_subpagetitle'));
$title->setVar('version', Config::VERSION);
echo \rex_view::title($title->parse('fragments/title.php'));

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
$items = $pictures->get();

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
	$showAllLinktext = \rex_i18n::rawMsg('akrys_usagecheck_images_link_show_unused');
	$showAllParam = '';
	if (!$showAll) {
		$showAllLinktext = \rex_i18n::rawMsg('akrys_usagecheck_images_link_show_all');
		$showAllParam = '&showall=true';
	}

// <editor-fold defaultstate="collapsed" desc="Menü">
	$url = 'index.php?page='.\akrys\redaxo\addon\UsageCheck\Config::NAME.'/'.$subpage.$showAllParam;
	$menu = new \rex_fragment([
		'url' => $url,
		'linktext' => $showAllLinktext,
		'texts' => [
			\rex_i18n::rawMsg('akrys_usagecheck_images_intro_text'),
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
