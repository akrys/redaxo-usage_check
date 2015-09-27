<?php
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

require_once __DIR__.'/../akrys/redaxo/addon/UsageCheck/Config.php';

/* @var $I18N \i18n */

use akrys\redaxo\addon\UsageCheck\Config;
require_once __DIR__.'/../akrys/redaxo/addon/UsageCheck/Modules/Actions.php';

$showAll = rex_get('showall', 'string', "");

rex_title(Config::NAME_OUT.' / '.$I18N->msg('akrys_usagecheck_action_subpagetitle').' <span style="font-size:10px;color:#c2c2c2">'.Config::VERSION.'</span>', $REX['ADDON']['pages'][Config::NAME]);

$items = \akrys\redaxo\addon\UsageCheck\Modules\Actions::getActions($showAll);

if (!$items) {
	?>

	<div class="rex-message">
		<div class="rex-warning">
			<p>
				<span><?php echo $I18N->msg('akrys_usagecheck_no_rights'); ?></span>
			</p>
		</div>
	</div>

	<?php
	return;
}

$showAllParam = '&showall=true';
$showAllLinktext = $I18N->msg('akrys_usagecheck_action_link_show_all');
if ($showAll) {
	$showAllParam = '';
	$showAllLinktext = $I18N->msg('akrys_usagecheck_action_link_show_unused');
}
?>

<p class="rex-tx1"><a href="index.php?page=<?php echo Config::NAME; ?>&subpage=<?php echo $subpage; ?><?php echo $showAllParam; ?>"><?php echo $showAllLinktext; ?></a></p>
<p class="rex-tx1"><?php echo $I18N->msg('akrys_usagecheck_action_intro_text'); ?></p>


<table class = "rex-table">
	<thead>
		<tr>
			<th><?php echo $I18N->msg('akrys_usagecheck_action_table_heading_name'); ?></th>
			<th><?php echo $I18N->msg('akrys_usagecheck_action_table_heading_functions'); ?></th>
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
					if ($item['modul'] === null) {
						?>

						<div class="rex-message">
							<div class="rex-warning">
								<p>
									<span><?php echo $I18N->msg('akrys_usagecheck_action_msg_not_used'); ?></span>
								</p>
							</div>
						</div>

						<?php
					} else {
						?>

						<div class="rex-message">
							<div class="rex-info">
								<p>
									<span><?php echo $I18N->msg('akrys_usagecheck_action_msg_used'); ?></span>
								</p>
							</div>
						</div>

						<?php
					}
					?>

					<div  class="rex-message" style="border:0;outline:0;">
						<span>
							<ol>
								<li><a href="index.php?page=module&subpage=actions&action_id=<?php echo $item['id']; ?>&function=edit"><?php echo $I18N->msg('akrys_usagecheck_action_linktext_edit_code'); ?></a></li>

								<?php
								if ($item['modul'] !== null) {
									$usages = explode("\n", $item['modul']);
									$linktextRaw = $I18N->msg('akrys_usagecheck_action_linktext_edit_in_modul');
									foreach ($usages as $usageRaw) {
										$usage = (explode("\t", $usageRaw));
										$modulID = $usage[0];
										$modulName = $usage[1];
										$href = 'index.php?page=module&subpage=&function=edit&modul_id='.$modulID;
										$linktext = str_replace('$modulName$', $modulName, $linktextRaw)
										?>

										<li><a href="<?php echo $href; ?>"><?php echo $linktext; ?></a></li>

										<?php
									}
								}
								?>
							</ol>
						</span>
					</div>
				</td>

				<?php
			}
			?>

	</tbody>
</table>
