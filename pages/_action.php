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
echo RedaxoCall::rexTitle(Config::NAME_OUT.' / '.RedaxoCall::i18nMsg('akrys_usagecheck_action_subpagetitle').' <span style="font-size:10px;color:#c2c2c2">'.Config::VERSION.'</span>');

$showAll = rex_get('showall', 'string', "");
require_once __DIR__.'/../akrys/redaxo/addon/UsageCheck/Modules/Actions.php';
$actions = \akrys\redaxo\addon\UsageCheck\Modules\Actions::create();
$items = $actions->getActions($showAll);

if ($items === false) {
	echo RedaxoCall::errorMsg(RedaxoCall::i18nMsg('akrys_usagecheck_no_rights'), true);
	return;
}

$showAllParam = '&showall=true';
$showAllLinktext = RedaxoCall::i18nMsg('akrys_usagecheck_action_link_show_all');
if ($showAll) {
	$showAllParam = '';
	$showAllLinktext = RedaxoCall::i18nMsg('akrys_usagecheck_action_link_show_unused');
}

$actions->outputMenu($subpage, $showAllParam, $showAllLinktext);
?>

<p class="rex-tx1"><?php echo RedaxoCall::i18nMsg('akrys_usagecheck_action_intro_text'); ?></p>


<table class="<?php echo RedaxoCall::getTableClass(); ?>">
	<thead>
		<tr>
			<th><?php echo RedaxoCall::i18nMsg('akrys_usagecheck_action_table_heading_name'); ?></th>
			<th><?php echo RedaxoCall::i18nMsg('akrys_usagecheck_action_table_heading_functions'); ?></th>
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
						echo RedaxoCall::errorMsg(RedaxoCall::i18nMsg('akrys_usagecheck_action_msg_not_used'));
					} else {
						echo RedaxoCall::infoMsg(RedaxoCall::i18nMsg('akrys_usagecheck_action_msg_used'));
					}
					?>

					<div  class="rex-message" style="border:0;outline:0;">
						<span>
							<ol>

								<li><?php $actions->outputActionEdit($item, RedaxoCall::i18nMsg('akrys_usagecheck_action_linktext_edit_code')); ?></li>

								<?php
								if ($item['modul'] !== null) {
									$usages = explode("\n", $item['modul']);
									$linktextRaw = RedaxoCall::i18nMsg('akrys_usagecheck_action_linktext_edit_in_modul');
									foreach ($usages as $usageRaw) {
										$usage = (explode("\t", $usageRaw));
										$modulID = $usage[0];
										$modulName = $usage[1];
										$href = 'index.php?page=module&subpage=&function=edit&modul_id='.$modulID;
										$linktext = str_replace('$modulName$', $modulName, $linktextRaw);
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
