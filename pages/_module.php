<?php

/**
 * Frontend-Ausagbe für die Seite Module
 */
/* @var $I18N \i18n */

use \akrys\redaxo\addon\UsageCheck\Config;
use \akrys\redaxo\addon\UsageCheck\RedaxoCall;

$modules = \akrys\redaxo\addon\UsageCheck\Modules\Modules::create();

$showAll = false;
switch (rex_get('showall', 'string', "")) {
	case 'true':
		$modules->showAll(true);
		$showAll = true;
		break;
	case 'false':
	default:
		//
		break;
}
$title = Config::NAME_OUT.' / '.RedaxoCall::getAPI()->getI18N('akrys_usagecheck_module_subpagetitle').
	' <span style="font-size:10px;color:#c2c2c2">'.Config::VERSION.'</span>';
echo RedaxoCall::getAPI()->getRexTitle($title);

$items = $modules->getModules($showAll);

if ($items === false) {
	echo RedaxoCall::getAPI()->getTaggedErrorMsg(RedaxoCall::getAPI()->getI18N('akrys_usagecheck_no_rights'));
	return;
}

$showAllParam = '&showall=true';
$showAllLinktext = RedaxoCall::getAPI()->getI18N('akrys_usagecheck_module_link_show_all');
if ($showAll) {
	$showAllParam = '';
	$showAllLinktext = RedaxoCall::getAPI()->getI18N('akrys_usagecheck_module_link_show_unused');
}

$modules->outputMenu($subpage, $showAllParam, $showAllLinktext);
?>

<table class="<?php echo RedaxoCall::getAPI()->getTableClass() ?>">
	<thead>
		<tr>
			<th><?php echo RedaxoCall::getAPI()->getI18N('akrys_usagecheck_module_table_heading_name'); ?></th>
			<th><?php echo RedaxoCall::getAPI()->getI18N('akrys_usagecheck_module_table_heading_functions'); ?></th>
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
						$index = 'akrys_usagecheck_module_msg_not_used';
						echo RedaxoCall::getAPI()->getTaggedErrorMsg(RedaxoCall::getAPI()->getI18N($index));
					} else {
						$index = 'akrys_usagecheck_module_msg_used';
						echo RedaxoCall::getAPI()->getTaggedInfoMsg(RedaxoCall::getAPI()->getI18N($index));
					}
					?>

					<div  class="rex-message" style="border:0;outline:0;">
						<span>
							<ol>

								<?php
								if (RedaxoCall::getAPI()->isAdmin()) {
									$url = 'index.php?page=module&subpage=&function=edit&modul_id='.$item['id'];
									$index = 'akrys_usagecheck_module_linktext_edit_code';
									$linkText = RedaxoCall::getAPI()->getI18N($index);
									?>

									<li>
										<a href="<?php echo $url; ?>"><?php echo $linkText; ?></a>
									</li>

									<?php
								}

								if ($item['slice_data'] !== null) {
									$usages = explode("\n", $item['slice_data']);

									$index = 'akrys_usagecheck_module_linktext_edit_slice';
									$linkTextRaw = RedaxoCall::getAPI()->getI18N($index);
									foreach ($usages as $usageRaw) {
										$usage = explode("\t", $usageRaw);
										$sliceID = $usage[0];
										$clang = $usage[1];
										$ctype = $usage[2];
										$articleID = $usage[3];
										$categoryID = $usage[4];
										$articleName = $usage[5];


										$hasPerm = RedaxoCall::getAPI()->hasCategoryPerm($articleID);

										if ($hasPerm) {
											$href = 'index.php?page=content&article_id='.$articleID.
												'&mode=edit&slice_id='.$sliceID.'&clang='.$clang.'&ctype='.$ctype.
												'&function=edit#slice'.$sliceID;
											$linkText = $linkTextRaw;
											$linkText = str_replace('$sliceID$', $sliceID, $linkText);
											$linkText = str_replace('$articleName$', $articleName, $linkText);
											?>

											<li><a href="<?php echo $href; ?>"><?php echo $linkText; ?></a></li>

											<?php
										}
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
