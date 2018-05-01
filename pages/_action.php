<?php

/**
 * Frontend-Ausagbe für die Seite Actions
 */
require_once __DIR__.'/../akrys/redaxo/addon/UsageCheck/Config.php';
require_once __DIR__.'/../akrys/redaxo/addon/UsageCheck/RedaxoCall.php';

/* @var $I18N \i18n */

use \akrys\redaxo\addon\UsageCheck\Config;
use \akrys\redaxo\addon\UsageCheck\RedaxoCall;

$title = new \rex_fragment();
$title->setVar('name', Config::NAME_OUT);
$title->setVar('supage_title', \rex_i18n::rawMsg('akrys_usagecheck_action_subpagetitle'));
$title->setVar('version', Config::VERSION);
echo RedaxoCall::getAPI()->getRexTitle($title->parse('fragments/title.php'));


require_once __DIR__.'/../akrys/redaxo/addon/UsageCheck/Modules/Actions.php';
$actions = \akrys\redaxo\addon\UsageCheck\Modules\Actions::create();

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

$items = $actions->getActions();

if ($items === false) {
	echo RedaxoCall::getAPI()->getTaggedErrorMsg(\rex_i18n::rawMsg('akrys_usagecheck_no_rights'));
} else {
	$showAllParam = '&showall=true';
	$showAllLinktext = \rex_i18n::rawMsg('akrys_usagecheck_action_link_show_all');
	if ($showAll) {
		$showAllParam = '';
		$showAllLinktext = \rex_i18n::rawMsg('akrys_usagecheck_action_link_show_unused');
	}

	echo $actions->outputMenu($subpage, $showAllParam, $showAllLinktext);

	$fragment = new rex_fragment([
		'items' => $items,
		'actions' => $actions,
	]);
	echo $fragment->parse('fragments/modules/actions.php');
}
