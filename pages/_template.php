<?php

/**
 * Frontend-Ausagbe für die Seite Tempalte
 */
/* @var $I18N \i18n */

use \akrys\redaxo\addon\UsageCheck\Config;
use \akrys\redaxo\addon\UsageCheck\RedaxoCall;

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


$title = new \rex_fragment();
$title->setVar('name', Config::NAME_OUT);
$title->setVar('supage_title', \rex_i18n::rawMsg('akrys_usagecheck_template_subpagetitle'));
$title->setVar('version', Config::VERSION);
echo RedaxoCall::getAPI()->getRexTitle($title->parse('fragments/title.php'));

$templates = akrys\redaxo\addon\UsageCheck\Modules\Templates::create();
if ($showAll) {
	$templates->showAll($showAll);
}
if ($showInactive) {
	$templates->showInactive($showInactive);
}
$items = $templates->getTemplates();

if ($items === false) {
	echo RedaxoCall::getAPI()->getTaggedErrorMsg(\rex_i18n::rawMsg('akrys_usagecheck_no_rights'));
} else {
	echo $templates->outputMenu($subpage, $showAll, $showInactive);

	$fragment = new rex_fragment([
		'items' => $items,
		'templates' => $templates,
	]);
	echo $fragment->parse('fragments/modules/templates.php');
}
