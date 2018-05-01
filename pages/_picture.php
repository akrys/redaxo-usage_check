<?php

/**
 * Anzeige der nicht verwendeten Bilder.
 */
use \akrys\redaxo\addon\UsageCheck\Config;
use \akrys\redaxo\addon\UsageCheck\RedaxoCall;

$pictures = \akrys\redaxo\addon\UsageCheck\Modules\Pictures::create();

$title = new \rex_fragment();
$title->setVar('name', Config::NAME_OUT);
$title->setVar('supage_title', \rex_i18n::rawMsg('akrys_usagecheck_images_subpagetitle'));
$title->setVar('version', Config::VERSION);
echo RedaxoCall::getAPI()->getRexTitle($title->parse('fragments/title.php'));

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
$items = $pictures->getPictures();

if ($items === false) {
	echo RedaxoCall::getAPI()->getTaggedErrorMsg(\rex_i18n::rawMsg('akrys_usagecheck_no_rights'));
} else {
	$showAllLinktext = \rex_i18n::rawMsg('akrys_usagecheck_images_link_show_unused');
	$showAllParam = '';
	if (!$showAll) {
		$showAllLinktext = \rex_i18n::rawMsg('akrys_usagecheck_images_link_show_all');
		$showAllParam = '&showall=true';
	}

	echo $pictures->outputMenu($subpage, $showAllParam, $showAllLinktext);

	$fragment = new rex_fragment([
		'items' => $items,
		'pictures' => $pictures,
	]);
	echo $fragment->parse('fragments/modules/pictures.php');
}
