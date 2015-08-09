<?php
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

require_once __DIR__.'/../akrys/redaxo/addon/UserCheck/Config.php';

use akrys\redaxo\addon\UserCheck\Config;
rex_title(Config::NAME_OUT.' / '.$I18N->msg('akrys_usagecheck_overview_subpagetitle').' <span style="font-size:10px;color:#c2c2c2">'.Config::VERSION.'</span>', $REX['ADDON']['pages'][Config::NAME]);

print __FILE__;
?>


<div class="rex-addon-output">
	<h2 class="rex-hl2">Usage Check</h2>

	<div class="rex-addon-content">
		<p class="rex-tx1">rex-tx1</p>

		<p class="rex-tx1">rex-tx1</p>
	</div>
</div>

<div class="rex-addon-output">
	<h2 class="rex-hl2">H2</h2>

	<div class="rex-addon-content">
		<p class="rex-tx1">rex-tx1</p>

		<h3>H3</h3>

	</div>
</div>
