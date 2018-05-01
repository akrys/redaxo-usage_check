<?php

/**
 * Frontend-Ausagbe der Übersicht
 */
use \akrys\redaxo\addon\UsageCheck\Config;

$title = new \rex_fragment();
$title->setVar('name', Config::NAME_OUT);
$title->setVar('supage_title', \rex_i18n::rawMsg('akrys_usagecheck_overview_subpagetitle'));
$title->setVar('version', Config::VERSION);
echo \rex_view::title($title->parse('fragments/title.php'));

$fragment = new \rex_fragment();

$fragment->setVar('heading', Config::NAME_OUT, false);
$fragment->setVar('body', \rex_i18n::rawMsg('akrys_usagecheck_overview_intro'), false);
echo $fragment->parse('core/page/section.php');

$fragment->setVar('heading', \rex_i18n::rawMsg('akrys_usagecheck_overview_images_title'), false);
$fragment->setVar('body', \rex_i18n::rawMsg('akrys_usagecheck_overview_images_body'), false);
echo $fragment->parse('core/page/section.php');

$fragment->setVar('heading', \rex_i18n::rawMsg('akrys_usagecheck_overview_module_title'), false);
$fragment->setVar('body', \rex_i18n::rawMsg('akrys_usagecheck_overview_module_body'), false);
echo $fragment->parse('core/page/section.php');

$fragment->setVar('heading', \rex_i18n::rawMsg('akrys_usagecheck_overview_template_title'), false);
$fragment->setVar('body', \rex_i18n::rawMsg('akrys_usagecheck_overview_template_body'), false);
echo $fragment->parse('core/page/section.php');

$fragment->setVar('heading', \rex_i18n::rawMsg('akrys_usagecheck_overview_action_title'), false);
$fragment->setVar('body', \rex_i18n::rawMsg('akrys_usagecheck_overview_atcion_body'), false);
echo $fragment->parse('core/page/section.php');
