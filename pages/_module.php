<?php
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

require_once __DIR__.'/../akrys/redaxo/addon/UserCheck/Config.php';

use akrys\redaxo\addon\UserCheck\Config;
require_once __DIR__.'/../akrys/redaxo/addon/UserCheck/Modules.php';

$showAll = rex_get('showall', 'string', "");

rex_title(Config::NAME_OUT.' / '.$I18N->msg('akrys_usagecheck_module_subpagetitle').' <span style="font-size:10px;color:#c2c2c2">'.Config::VERSION.'</span>', $REX['ADDON']['pages'][Config::NAME]);

$items = \akrys\redaxo\addon\UserCheck\Modules::getModules($showAll);


$showAllParam = '&showall=true';
$showAllLinktext = $I18N->msg('akrys_usagecheck_module_link_show_all');
if ($showAll) {
	$showAllParam = '';
	$showAllLinktext = $I18N->msg('akrys_usagecheck_module_link_show_unused');
}
?>

<p class="rex-tx1"><a href="index.php?page=<?php echo Config::NAME; ?>&subpage=<?php echo $subpage; ?><?php echo $showAllParam; ?>"><?php echo $showAllLinktext; ?></a></p>
<p class="rex-tx1"><?php echo $I18N->msg('akrys_usagecheck_module_intro_text');?></p>


<table class = "rex-table">
	<thead>
		<tr>
			<th><?php echo $I18N->msg('akrys_usagecheck_module_table_heading_name'); ?></th>
			<th><?php echo $I18N->msg('akrys_usagecheck_module_table_heading_functions'); ?></th>
		</tr>
	</thead>
	<tbody>

		<?php
		foreach ($items as $item) {
			?>
			<tr>
				<td><?php echo $item['name']; ?></td>
				<td>
					<?php
					if ($item['slice_id'] === null) {
						?>

						<div class="rex-message">
							<div class="rex-warning">
								<p>
									<span><?php echo $I18N->msg('akrys_usagecheck_module_msg_not_used'); ?></span>
								</p>
							</div>
						</div>

						<?php
					} else {
						?>

						<div class="rex-message">
							<div class="rex-info">
								<p>
									<span><?php echo $I18N->msg('akrys_usagecheck_module_msg_used'); ?> (<?php echo $item['count']; ?>)</span>
								</p>
							</div>
						</div>

						<?php
					}
					?>

				</td>

				<?php
			}
			?>

	</tbody>
</table>
