<?php

/**
 * Frontend-Ausagbe der Übersicht
 */

use \akrys\redaxo\addon\UsageCheck\Config;
use \akrys\redaxo\addon\UsageCheck\RedaxoCall;

$title = new \rex_fragment();
$title->setVar('name', Config::NAME_OUT);
$title->setVar('supage_title', \rex_i18n::rawMsg('akrys_usagecheck_overview_subpagetitle'));
$title->setVar('version', Config::VERSION);
echo RedaxoCall::getAPI()->getRexTitle($title->parse('fragments/title.php'));

$title = Config::NAME_OUT;
$content = \rex_i18n::rawMsg('akrys_usagecheck_overview_intro');
echo RedaxoCall::getAPI()->getPanelOut($title, $content);

$title = \rex_i18n::rawMsg('akrys_usagecheck_overview_images_title');
$content = \rex_i18n::rawMsg('akrys_usagecheck_overview_images_body');
echo RedaxoCall::getAPI()->getPanelOut($title, $content);

$title = \rex_i18n::rawMsg('akrys_usagecheck_overview_module_title');
$content = \rex_i18n::rawMsg('akrys_usagecheck_overview_module_body');
echo RedaxoCall::getAPI()->getPanelOut($title, $content);

$title = \rex_i18n::rawMsg('akrys_usagecheck_overview_template_title');
$content = \rex_i18n::rawMsg('akrys_usagecheck_overview_template_body');
echo RedaxoCall::getAPI()->getPanelOut($title, $content);

$title = \rex_i18n::rawMsg('akrys_usagecheck_overview_action_title');
$content = \rex_i18n::rawMsg('akrys_usagecheck_overview_atcion_body');
echo RedaxoCall::getAPI()->getPanelOut($title, $content);
