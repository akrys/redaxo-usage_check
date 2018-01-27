<?php

/**
 * Frontend-Ausagbe der Übersicht
 */

/* @var $I18N \i18n */

use \akrys\redaxo\addon\UsageCheck\Config;
use \akrys\redaxo\addon\UsageCheck\RedaxoCall;

$title = new \rex_fragment();
$title->setVar('name', Config::NAME_OUT);
$title->setVar('supage_title', RedaxoCall::getAPI()->getI18N('akrys_usagecheck_overview_subpagetitle'));
$title->setVar('version', Config::VERSION);
echo RedaxoCall::getAPI()->getRexTitle($title->parse('fragments/title.php'));

$title = Config::NAME_OUT;
$content = RedaxoCall::getAPI()->getI18N('akrys_usagecheck_overview_intro');
echo RedaxoCall::getAPI()->getPanelOut($title, $content);

$title = RedaxoCall::getAPI()->getI18N('akrys_usagecheck_overview_images_title');
$content = RedaxoCall::getAPI()->getI18N('akrys_usagecheck_overview_images_body');
echo RedaxoCall::getAPI()->getPanelOut($title, $content);

$title = RedaxoCall::getAPI()->getI18N('akrys_usagecheck_overview_module_title');
$content = RedaxoCall::getAPI()->getI18N('akrys_usagecheck_overview_module_body');
echo RedaxoCall::getAPI()->getPanelOut($title, $content);

$title = RedaxoCall::getAPI()->getI18N('akrys_usagecheck_overview_template_title');
$content = RedaxoCall::getAPI()->getI18N('akrys_usagecheck_overview_template_body');
echo RedaxoCall::getAPI()->getPanelOut($title, $content);

$title = RedaxoCall::getAPI()->getI18N('akrys_usagecheck_overview_action_title');
$content = RedaxoCall::getAPI()->getI18N('akrys_usagecheck_overview_atcion_body');
echo RedaxoCall::getAPI()->getPanelOut($title, $content);
