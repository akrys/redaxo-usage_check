<?php
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

require_once __DIR__.'/../akrys/redaxo/addon/UsageCheck/Config.php';
require_once __DIR__.'/../akrys/redaxo/addon/UsageCheck/RedaxoCall.php';

/* @var $I18N \i18n */

ini_set('error_reporting', E_ALL);
ini_set('display_errors', true);

use \akrys\redaxo\addon\UsageCheck\Config;
use \akrys\redaxo\addon\UsageCheck\RedaxoCall;
require_once __DIR__.'/../akrys/redaxo/addon/UsageCheck/Modules/Modules.php';
$modules = \akrys\redaxo\addon\UsageCheck\Modules\Modules::create();

$showAll = rex_get('showall', 'string', "");

echo RedaxoCall::rexTitle(Config::NAME_OUT.' / '.RedaxoCall::i18nMsg('akrys_usagecheck_module_subpagetitle').' <span style="font-size:10px;color:#c2c2c2">'.Config::VERSION.'</span>');

$items = $modules->getModules($showAll);

if ($items === false) {
	echo RedaxoCall::errorMsg(RedaxoCall::i18nMsg('akrys_usagecheck_no_rights'), true);
	return;
}

$showAllParam = '&showall=true';
$showAllLinktext = RedaxoCall::i18nMsg('akrys_usagecheck_module_link_show_all');
if ($showAll) {
	$showAllParam = '';
	$showAllLinktext = RedaxoCall::i18nMsg('akrys_usagecheck_module_link_show_unused');
}

$modules->outputMenu($subpage, $showAllParam, $showAllLinktext);
?>

<table class="<?php echo RedaxoCall::getTableClass() ?>">
	<thead>
		<tr>
			<th><?php echo RedaxoCall::i18nMsg('akrys_usagecheck_module_table_heading_name'); ?></th>
			<th><?php echo RedaxoCall::i18nMsg('akrys_usagecheck_module_table_heading_functions'); ?></th>
		</tr>
	</thead>
	<tbody>

		<?php
		foreach ($items as $item) {
			if (!$modules->hasRights($item)) {
				continue;
			}
			?>

			<tr>
				<td><?php echo $item['name']; ?></td>
				<td>

					<?php
					if ($item['slice_data'] === null) {
						echo RedaxoCall::errorMsg(RedaxoCall::i18nMsg('akrys_usagecheck_module_msg_not_used'));
					} else {
						echo RedaxoCall::infoMsg(RedaxoCall::i18nMsg('akrys_usagecheck_module_msg_used'));
					}
					?>

					<div  class="rex-message" style="border:0;outline:0;">
						<span>
							<ol>

								<?php
								if (RedaxoCall::isAdmin()) {
									?>

									<li><a href="index.php?page=module&subpage=&function=edit&modul_id=<?php echo $item['id'] ?>"><?php echo RedaxoCall::i18nMsg('akrys_usagecheck_module_linktext_edit_code'); ?></a></li>

									<?php
								}

								if ($item['slice_data'] !== null) {
									$usages = explode("\n", $item['slice_data']);

									$linktextRaw = RedaxoCall::i18nMsg('akrys_usagecheck_module_linktext_edit_slice');
									foreach ($usages as $usageRaw) {
										$usage = explode("\t", $usageRaw);
										$sliceID = $usage[0];
										$clang = $usage[1];
										$ctype = $usage[2];
										$articleID = $usage[3];
										$categoryID = $usage[4];
										$articleName = $usage[5];


										$hasPerm = RedaxoCall::hasCategoryPerm($articleID);

										if ($hasPerm) {
											$href = 'index.php?page=content&article_id='.$articleID.'&mode=edit&slice_id='.$sliceID.'&clang='.$clang.'&ctype='.$ctype.'&function=edit#slice'.$sliceID;
											$linktext = $linktextRaw;
											$linktext = str_replace('$sliceID$', $sliceID, $linktext);
											$linktext = str_replace('$articleName$', $articleName, $linktext);
											?>

											<li><a href="<?php echo $href; ?>"><?php echo $linktext; ?></a></li>

											<?php
										}
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
