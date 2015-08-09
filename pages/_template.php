<?php
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

require_once __DIR__.'/../akrys/redaxo/addon/UserCheck/Config.php';

use akrys\redaxo\addon\UserCheck\Config;
require_once __DIR__.'/../akrys/redaxo/addon/UserCheck/Templates.php';

$showAll = rex_get('showall', 'string', "");
$showInactive = rex_get('showinactive', 'string', "");

rex_title(Config::NAME_OUT.' / '.$I18N->msg('akrys_usagecheck_template_subpagetitle').' <span style="font-size:10px;color:#c2c2c2">'.Config::VERSION.'</span>', $REX['ADDON']['pages'][Config::NAME]);

$items = \akrys\redaxo\addon\UserCheck\Templates::getTemplates($showAll, $showInactive);


$showAllParam = '';
$showAllParamCurr = '&showall=true';
$showAllLinktext = $I18N->msg('akrys_usagecheck_template_link_show_unused');
if (!$showAll) {
	$showAllParam = '&showall=true';
	$showAllParamCurr = '';
	$showAllLinktext = $I18N->msg('akrys_usagecheck_template_link_show_all');
}

$showInactiveParam = '';
$showInactiveParamCurr = '&showinactive=true';
$showInactiveLinktext = $I18N->msg('akrys_usagecheck_template_link_show_active');
if (!$showInactive) {
	$showInactiveParam = '&showinactive=true';
	$showInactiveParamCurr = '';
	$showInactiveLinktext = $I18N->msg('akrys_usagecheck_template_link_show_active_inactive');
}
?>
<div class="rex-navi-slice">
	<ul>
		<li><a href="index.php?page=<?php echo Config::NAME; ?>&subpage=<?php echo $subpage; ?><?php echo $showAllParam.$showInactiveParamCurr; ?>"><?php echo $showAllLinktext; ?></a></li>
		<li><a href="index.php?page=<?php echo Config::NAME; ?>&subpage=<?php echo $subpage; ?><?php echo $showAllParamCurr.$showInactiveParam; ?>"><?php echo $showInactiveLinktext ?></a></li>
	</ul>
</div>
<div style='clear:both'></div>

<p>
	<?php echo $I18N->msg('akrys_usagecheck_template_intro_text'); ?><br />
	<br />
</p>

<table class = "rex-table">
	<thead>
		<tr>
			<th><?php echo $I18N->msg('akrys_usagecheck_template_table_heading_name'); ?></th>
			<th><?php echo $I18N->msg('akrys_usagecheck_template_table_heading_functions'); ?></th>
		</tr>
	</thead>
	<tbody>

		<?php
		foreach ($items as $item) {
			?>
			<tr<?php echo $item['active'] == 1 ? '' : ' style="opacity:0.80;"' ?>>
				<td>
					<?php
					echo $item['name'];
					if ($item['active'] == 0) {
						?>
						<br />
						(<?php echo $I18N->msg('akrys_usagecheck_template_table_inactive'); ?>)
						<br />
						<?php
					}
					?>


				</td>
				<td>
					<?php
					if ($item['count'] <= 0) {
						?>

						<div class="rex-message">
							<div class="rex-warning">
								<p>
									<span><?php echo $I18N->msg('akrys_usagecheck_images_msg_not_used'); ?></span>
								</p>
							</div>
						</div>

						<?php
					} else {
						?>

						<div class="rex-message">
							<div class="rex-info">
								<p>
									<span><?php echo $I18N->msg('akrys_usagecheck_template_msg_used'); ?> (<?php echo $item['count']; ?>)</span>
								</p>
							</div>
						</div>

						<strong><?php echo $I18N->msg('akrys_usagecheck_template_detail_heading'); ?></strong>
						<table>
							<tr>
								<th><?php echo $I18N->msg('akrys_usagecheck_template_detail_column_article'); ?></th>
								<th><?php echo $I18N->msg('akrys_usagecheck_template_detail_column_template'); ?></th>
							<tr>
							<tr>
								<td><?php echo $item['count_articles']; ?></td>
								<td><?php echo $item['count_templates']; ?></td>
								</td>
							</tr>
						</table>

						<?php
					}
					?>

				</td>

				<?php
			}
			?>

	</tbody>
</table>

