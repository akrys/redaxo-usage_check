<?php
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

require_once __DIR__.'/../akrys/redaxo/addon/UserCheck/Config.php';

use akrys\redaxo\addon\UserCheck\Config;
rex_title(Config::NAME_OUT.' / '.$I18N->msg('akrys_usagecheck_changelog_subpagetitle').' <span style="font-size:10px;color:#c2c2c2">'.Config::VERSION.'</span>', $REX['ADDON']['pages'][Config::NAME]);
?>


<table class = "rex-table">
	<thead>
		<tr>
			<th>Version</th>
			<th>Date</th>
			<th>Changes</th>
		</tr>
	</thead>
	<tbody>
		<tr>
			<td>0.1 DEVELOP</td>
			<td>2015-08-09</td>
			<td>
				<ul>
					<li>First version at all</li>
					<li>View unused Pictures, Modules and Templates</li>
				</ul>
				<h2>Known Todo:</h2>
				<ul>
					<li>File links inserted to XForm-Tables should also be analyzed</li>
					<li>Comments on Items to give a hint why these are not used</li>
				</ul>
			</td>
		</tr>
	</tbody>
</table>