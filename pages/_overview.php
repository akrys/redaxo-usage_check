<?php

/**
 * Frontend-Ausagbe der Übersicht
 */
require_once __DIR__.'/../akrys/redaxo/addon/UsageCheck/Config.php';
require_once __DIR__.'/../akrys/redaxo/addon/UsageCheck/RedaxoCall.php';

/* @var $I18N \i18n */

use \akrys\redaxo\addon\UsageCheck\Config;
use \akrys\redaxo\addon\UsageCheck\RedaxoCall;

$title = Config::NAME_OUT.' / '.RedaxoCall::getAPI()->i18nMsg('akrys_usagecheck_overview_subpagetitle').
	' <span style="font-size:10px;color:#c2c2c2">'.Config::VERSION.'</span>';
echo RedaxoCall::getAPI()->rexTitle($title);

echo RedaxoCall::getAPI()->panelOut(Config::NAME_OUT, RedaxoCall::getAPI()->i18nMsg('akrys_usagecheck_overview_intro'));

$title = RedaxoCall::getAPI()->i18nMsg('akrys_usagecheck_overview_images_title');
$content = RedaxoCall::getAPI()->i18nMsg('akrys_usagecheck_overview_images_body');
echo RedaxoCall::getAPI()->panelOut($title, $content);

$title = RedaxoCall::getAPI()->i18nMsg('akrys_usagecheck_overview_module_title');
$content = RedaxoCall::getAPI()->i18nMsg('akrys_usagecheck_overview_module_body');
echo RedaxoCall::getAPI()->panelOut($title, $content);

$title = RedaxoCall::getAPI()->i18nMsg('akrys_usagecheck_overview_template_title');
$content = RedaxoCall::getAPI()->i18nMsg('akrys_usagecheck_overview_template_body');
echo RedaxoCall::getAPI()->panelOut($title, $content);

$title = RedaxoCall::getAPI()->i18nMsg('akrys_usagecheck_overview_action_title');
$content = RedaxoCall::getAPI()->i18nMsg('akrys_usagecheck_overview_atcion_body');
echo RedaxoCall::getAPI()->panelOut($title, $content);
//echo RedaxoCall::getAPI()->panelOut($title,$text);
