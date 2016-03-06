<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

require_once __DIR__.'/../akrys/redaxo/addon/UsageCheck/Config.php';
require_once __DIR__.'/../akrys/redaxo/addon/UsageCheck/RedaxoCall.php';

/* @var $I18N \i18n */

use \akrys\redaxo\addon\UsageCheck\Config;
use \akrys\redaxo\addon\UsageCheck\RedaxoCall;
echo RedaxoCall::rexTitle(Config::NAME_OUT.' / '.RedaxoCall::i18nMsg('akrys_usagecheck_overview_subpagetitle').' <span style="font-size:10px;color:#c2c2c2">'.Config::VERSION.'</span>');

echo RedaxoCall::panelOut(Config::NAME_OUT, RedaxoCall::i18nMsg('akrys_usagecheck_overview_intro'));
echo RedaxoCall::panelOut(RedaxoCall::i18nMsg('akrys_usagecheck_overview_images_title'), RedaxoCall::i18nMsg('akrys_usagecheck_overview_images_body'));
echo RedaxoCall::panelOut(RedaxoCall::i18nMsg('akrys_usagecheck_overview_module_title'), RedaxoCall::i18nMsg('akrys_usagecheck_overview_module_body'));
echo RedaxoCall::panelOut(RedaxoCall::i18nMsg('akrys_usagecheck_overview_template_title'), RedaxoCall::i18nMsg('akrys_usagecheck_overview_template_body'));
echo RedaxoCall::panelOut(RedaxoCall::i18nMsg('akrys_usagecheck_overview_action_title'), RedaxoCall::i18nMsg('akrys_usagecheck_overview_atcion_body'));
//echo RedaxoCall::panelOut($title,$text);

