<?php
/**
 * Frontend-Ausagbe für die Seite Actions
 */
require_once __DIR__.'/../akrys/redaxo/addon/UsageCheck/Config.php';
require_once __DIR__.'/../akrys/redaxo/addon/UsageCheck/RedaxoCall.php';

/* @var $I18N \i18n */

use \akrys\redaxo\addon\UsageCheck\Config;
use \akrys\redaxo\addon\UsageCheck\RedaxoCall;

$title = Config::NAME_OUT.' / '.RedaxoCall::getAPI()->i18nMsg('akrys_usagecheck_action_subpagetitle').
	' <span style="font-size:10px;color:#c2c2c2">'.Config::VERSION.'</span>';
echo RedaxoCall::getAPI()->rexTitle($title);

switch (rex_get('showall', 'string', "")) {
	case 'true':
		$actions->showAll(true);
		break;
	case 'false':
	default:
		//
		break;
}

require_once __DIR__.'/../akrys/redaxo/addon/UsageCheck/Modules/Actions.php';
$actions = \akrys\redaxo\addon\UsageCheck\Modules\Actions::create();

$items = $actions->getActions();

if ($items === false) {
	echo RedaxoCall::getAPI()->errorMsgAddTags(RedaxoCall::getAPI()->i18nMsg('akrys_usagecheck_no_rights'));
	return;
}

$showAllParam = '&showall=true';
$showAllLinktext = RedaxoCall::getAPI()->i18nMsg('akrys_usagecheck_action_link_show_all');
if ($showAll) {
	$showAllParam = '';
	$showAllLinktext = RedaxoCall::getAPI()->i18nMsg('akrys_usagecheck_action_link_show_unused');
}

$actions->outputMenu($subpage, $showAllParam, $showAllLinktext);
?>

<p class="rex-tx1"><?php echo RedaxoCall::getAPI()->i18nMsg('akrys_usagecheck_action_intro_text'); ?></p>


<table class="<?php echo RedaxoCall::getAPI()->getTableClass(); ?>">
	<thead>
		<tr>
			<th><?php echo RedaxoCall::getAPI()->i18nMsg('akrys_usagecheck_action_table_heading_name'); ?></th>
			<th><?php echo RedaxoCall::getAPI()->i18nMsg('akrys_usagecheck_action_table_heading_functions'); ?></th>
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
						$msg = RedaxoCall::getAPI()->i18nMsg('akrys_usagecheck_action_msg_not_used');
						echo RedaxoCall::getAPI()->errorMsgAddTags($msg);
					} else {
						$msg = RedaxoCall::getAPI()->i18nMsg('akrys_usagecheck_action_msg_used');
						echo RedaxoCall::getAPI()->infoMsgAddTags($msg);
					}
					?>

					<div  class="rex-message" style="border:0;outline:0;">
						<span>
							<ol>
								<?php
								$output = RedaxoCall::getAPI()->i18nMsg('akrys_usagecheck_action_linktext_edit_code');
								?>

								<li><?php $actions->outputActionEdit($item, $output); ?></li>

								<?php
								if ($item['modul'] !== null) {
									$usages = explode("\n", $item['modul']);
									$idex = 'akrys_usagecheck_action_linktext_edit_in_modul';
									$linkTextRaw = RedaxoCall::getAPI()->i18nMsg($index);
									foreach ($usages as $usageRaw) {
										$usage = (explode("\t", $usageRaw));
										$modulID = $usage[0];
										$modulName = $usage[1];
										$href = 'index.php?page=module&subpage=&function=edit&modul_id='.$modulID;
										$linkText = str_replace('$modulName$', $modulName, $linkTextRaw);
										?>

										<li><a href="<?php echo $href; ?>"><?php echo $linkText; ?></a></li>

										<?php
									}
								}
								?>
							</ol>
						</span>
					</div>
				</td>
			</tr>

			<?php
		}
		?>

	</tbody>
</table>
