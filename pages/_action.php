<?php
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

require_once __DIR__.'/../akrys/redaxo/addon/UsageCheck/Config.php';
require_once __DIR__.'/../akrys/redaxo/addon/UsageCheck/RedaxoCall.php';

/* @var $I18N \i18n */

use akrys\redaxo\addon\UsageCheck\Config;
require_once __DIR__.'/../akrys/redaxo/addon/UsageCheck/Modules/Actions.php';

switch (\akrys\redaxo\addon\UsageCheck\RedaxoCall::getRedaxoVersion()) {
	case \akrys\redaxo\addon\UsageCheck\RedaxoCall::REDAXO_VERSION_4:
		$tableClass = 'rex-table';
		break;

	case \akrys\redaxo\addon\UsageCheck\RedaxoCall::REDAXO_VERSION_5:
		$tableClass = 'table table-striped';
		break;
}

$showAll = rex_get('showall', 'string', "");

echo \akrys\redaxo\addon\UsageCheck\RedaxoCall::rexTitle(Config::NAME_OUT.' / '.\akrys\redaxo\addon\UsageCheck\RedaxoCall::i18nMsg('akrys_usagecheck_action_subpagetitle').' <span style="font-size:10px;color:#c2c2c2">'.Config::VERSION.'</span>', Config::NAME_OUT);

$items = \akrys\redaxo\addon\UsageCheck\Modules\Actions::getActions($showAll);

if ($items === false) {
	echo \akrys\redaxo\addon\UsageCheck\RedaxoCall::errorMsg(\akrys\redaxo\addon\UsageCheck\RedaxoCall::i18nMsg('akrys_usagecheck_no_rights'), true);
	return;
}

$showAllParam = '&showall=true';
$showAllLinktext = \akrys\redaxo\addon\UsageCheck\RedaxoCall::i18nMsg('akrys_usagecheck_action_link_show_all');
if ($showAll) {
	$showAllParam = '';
	$showAllLinktext = \akrys\redaxo\addon\UsageCheck\RedaxoCall::i18nMsg('akrys_usagecheck_action_link_show_unused');
}

switch (\akrys\redaxo\addon\UsageCheck\RedaxoCall::getRedaxoVersion()) {
	case \akrys\redaxo\addon\UsageCheck\RedaxoCall::REDAXO_VERSION_4:
		?>

		<p class="rex-tx1"><a href="index.php?page=<?php echo Config::NAME; ?>&subpage=<?php echo $subpage; ?><?php echo $showAllParam; ?>"><?php echo $showAllLinktext; ?></a></p>

		<?php
		break;
	case \akrys\redaxo\addon\UsageCheck\RedaxoCall::REDAXO_VERSION_5:
		?>

		<p class="rex-tx1"><a href="index.php?page=<?php echo Config::NAME; ?>/<?php echo $subpage; ?><?php echo $showAllParam; ?>"><?php echo $showAllLinktext; ?></a></p>

		<?php
		break;
}
?>

<p class="rex-tx1"><?php echo \akrys\redaxo\addon\UsageCheck\RedaxoCall::i18nMsg('akrys_usagecheck_action_intro_text'); ?></p>


<table class = "rex-table">
	<thead>
		<tr>
			<th><?php echo \akrys\redaxo\addon\UsageCheck\RedaxoCall::i18nMsg('akrys_usagecheck_action_table_heading_name'); ?></th>
			<th><?php echo \akrys\redaxo\addon\UsageCheck\RedaxoCall::i18nMsg('akrys_usagecheck_action_table_heading_functions'); ?></th>
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
									<span><?php echo \akrys\redaxo\addon\UsageCheck\RedaxoCall::i18nMsg('akrys_usagecheck_action_msg_not_used'); ?></span>
								</p>
							</div>
						</div>

						<?php
					} else {
						?>

						<div class="rex-message">
							<div class="rex-info">
								<p>
									<span><?php echo \akrys\redaxo\addon\UsageCheck\RedaxoCall::i18nMsg('akrys_usagecheck_action_msg_used'); ?></span>
								</p>
							</div>
						</div>

						<?php
					}
					?>

					<div  class="rex-message" style="border:0;outline:0;">
						<span>
							<ol>

								<?php
								switch (\akrys\redaxo\addon\UsageCheck\RedaxoCall::getRedaxoVersion()) {
									case \akrys\redaxo\addon\UsageCheck\RedaxoCall::REDAXO_VERSION_4:
										?>

										<li><a href="index.php?page=module&subpage=actions&action_id=<?php echo $item['id']; ?>&function=edit"><?php echo \akrys\redaxo\addon\UsageCheck\RedaxoCall::i18nMsg('akrys_usagecheck_action_linktext_edit_code'); ?></a></li>

										<?php
										break;
									case \akrys\redaxo\addon\UsageCheck\RedaxoCall::REDAXO_VERSION_5:
										?>

										<li><a href="index.php?page=module/actions&action_id=<?php echo $item['id']; ?>&function=edit"><?php echo \akrys\redaxo\addon\UsageCheck\RedaxoCall::i18nMsg('akrys_usagecheck_action_linktext_edit_code'); ?></a></li>

										<?php
										break;
								}

								if ($item['modul'] !== null) {
									$usages = explode("\n", $item['modul']);
									$linktextRaw = \akrys\redaxo\addon\UsageCheck\RedaxoCall::i18nMsg('akrys_usagecheck_action_linktext_edit_in_modul');
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
