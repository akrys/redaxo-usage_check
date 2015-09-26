<?php
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

require_once __DIR__.'/../akrys/redaxo/addon/UsageCheck/Config.php';

/* @var $I18N \i18n */

use akrys\redaxo\addon\UsageCheck\Config;
rex_title(Config::NAME_OUT.' / '.$I18N->msg('akrys_usagecheck_overview_subpagetitle').' <span style="font-size:10px;color:#c2c2c2">'.Config::VERSION.'</span>', $REX['ADDON']['pages'][Config::NAME]);
?>


<div class="rex-addon-output">
	<h2 class="rex-hl2"><?php echo Config::NAME_OUT; ?></h2>

	<div class="rex-addon-content">
		<p class="rex-tx1">
			<?php echo $I18N->msg('akrys_usagecheck_overview_intro'); ?>
		</p>
	</div>
</div>

<div class="rex-addon-output">
	<h3 class="rex-hl2"><?php echo $I18N->msg('akrys_usagecheck_overview_images_title'); ?></h3>
	<div class="rex-addon-content">
		<?php echo $I18N->msg('akrys_usagecheck_overview_images_body'); ?>

	</div>
</div>


<div class="rex-addon-output">
	<h2 class="rex-hl2"><?php echo $I18N->msg('akrys_usagecheck_overview_module_title'); ?></h2>
	<div class="rex-addon-content">
		<?php echo $I18N->msg('akrys_usagecheck_overview_module_body'); ?>
	</div>
</div>

<div class="rex-addon-output">
	<h2 class="rex-hl2"><?php echo $I18N->msg('akrys_usagecheck_overview_template_title'); ?></h2>
	<div class="rex-addon-content">
		<?php echo $I18N->msg('akrys_usagecheck_overview_template_body'); ?>
	</div>
</div>

<div class="rex-addon-output">
	<h2 class="rex-hl2"><?php echo $I18N->msg('akrys_usagecheck_overview_action_title'); ?></h2>
	<div class="rex-addon-content">
		<?php echo $I18N->msg('akrys_usagecheck_overview_atcion_body'); ?>
	</div>

</div>
