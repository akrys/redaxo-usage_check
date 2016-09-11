<?php

/**
 * Frontend-Ausagbe der Übersicht
 */

/* @var $I18N \i18n */

use \akrys\redaxo\addon\UsageCheck\Config;
use \akrys\redaxo\addon\UsageCheck\RedaxoCall;

$title = Config::NAME_OUT.' / '.RedaxoCall::getAPI()->getI18N('akrys_usagecheck_overview_subpagetitle').
	' <span style="font-size:10px;color:#c2c2c2">'.Config::VERSION.'</span>';
echo RedaxoCall::getAPI()->getRexTitle($title);


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
