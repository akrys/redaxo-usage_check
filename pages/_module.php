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
require_once __DIR__.'/../akrys/redaxo/addon/UsageCheck/Modules/Modules.php';

$showAll = rex_get('showall', 'string', "");

echo \akrys\redaxo\addon\UsageCheck\RedaxoCall::rexTitle(Config::NAME_OUT.' / '.\akrys\redaxo\addon\UsageCheck\RedaxoCall::i18nMsg('akrys_usagecheck_module_subpagetitle').' <span style="font-size:10px;color:#c2c2c2">'.Config::VERSION.'</span>', Config::NAME_OUT);
switch (\akrys\redaxo\addon\UsageCheck\RedaxoCall::getRedaxoVersion()) {
	case \akrys\redaxo\addon\UsageCheck\RedaxoCall::REDAXO_VERSION_4:
		$tableClass = 'rex-table';
		break;

	case \akrys\redaxo\addon\UsageCheck\RedaxoCall::REDAXO_VERSION_5:
		$tableClass = 'table table-striped';
		break;
}


$items = \akrys\redaxo\addon\UsageCheck\Modules\Modules::getModules($showAll);

if ($items === false) {
	echo \akrys\redaxo\addon\UsageCheck\RedaxoCall::errorMsg(\akrys\redaxo\addon\UsageCheck\RedaxoCall::i18nMsg('akrys_usagecheck_no_rights'), true);
	return;
}

$showAllParam = '&showall=true';
$showAllLinktext = \akrys\redaxo\addon\UsageCheck\RedaxoCall::i18nMsg('akrys_usagecheck_module_link_show_all');
if ($showAll) {
	$showAllParam = '';
	$showAllLinktext = \akrys\redaxo\addon\UsageCheck\RedaxoCall::i18nMsg('akrys_usagecheck_module_link_show_unused');
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

<p class="rex-tx1"><?php echo \akrys\redaxo\addon\UsageCheck\RedaxoCall::i18nMsg('akrys_usagecheck_module_intro_text'); ?></p>


<table class = "rex-table">
	<thead>
		<tr>
			<th><?php echo \akrys\redaxo\addon\UsageCheck\RedaxoCall::i18nMsg('akrys_usagecheck_module_table_heading_name'); ?></th>
			<th><?php echo \akrys\redaxo\addon\UsageCheck\RedaxoCall::i18nMsg('akrys_usagecheck_module_table_heading_functions'); ?></th>
		</tr>
	</thead>
	<tbody>

		<?php
		foreach ($items as $item) {
			switch (\akrys\redaxo\addon\UsageCheck\RedaxoCall::getRedaxoVersion()) {
				case \akrys\redaxo\addon\UsageCheck\RedaxoCall::REDAXO_VERSION_4:
					if (!$REX['USER']->isAdmin() && !$REX['USER']->hasPerm('module['.$item['id'].']')) {
						continue;
					}
					break;
				case \akrys\redaxo\addon\UsageCheck\RedaxoCall::REDAXO_VERSION_5:
					$user = \rex::getUser();
					if (!$user->isAdmin() && !$user->getComplexPerm('modules')->hasPerm($item['id'])) {
						continue;
					}
					break;
			}
			?>

			<tr>
				<td><?php echo $item['name']; ?></td>
				<td>

					<?php
					if ($item['slice_data'] === null) {
						echo \akrys\redaxo\addon\UsageCheck\RedaxoCall::errorMsg(\akrys\redaxo\addon\UsageCheck\RedaxoCall::i18nMsg('akrys_usagecheck_module_msg_not_used'));
					} else {
						echo \akrys\redaxo\addon\UsageCheck\RedaxoCall::infoMsg(\akrys\redaxo\addon\UsageCheck\RedaxoCall::i18nMsg('akrys_usagecheck_module_msg_used'));
					}
					?>

					<div  class="rex-message" style="border:0;outline:0;">
						<span>
							<ol>

								<?php
								switch (\akrys\redaxo\addon\UsageCheck\RedaxoCall::getRedaxoVersion()) {
									case \akrys\redaxo\addon\UsageCheck\RedaxoCall::REDAXO_VERSION_4:
										if ($REX['USER']->isAdmin()) {
											?>

											<li><a href="index.php?page=module&subpage=&function=edit&modul_id=<?php echo $item['id'] ?>"><?php echo \akrys\redaxo\addon\UsageCheck\RedaxoCall::i18nMsg('akrys_usagecheck_module_linktext_edit_code'); ?></a></li>

											<?php
										}
										break;
									case \akrys\redaxo\addon\UsageCheck\RedaxoCall::REDAXO_VERSION_5:
										$user = \rex::getUser();
										if ($user->isAdmin()) {
											?>

											<li><a href="index.php?page=module&subpage=&function=edit&modul_id=<?php echo $item['id'] ?>"><?php echo \akrys\redaxo\addon\UsageCheck\RedaxoCall::i18nMsg('akrys_usagecheck_module_linktext_edit_code'); ?></a></li>

											<?php
										}
										break;
								}

								if ($item['slice_data'] !== null) {
									$usages = explode("\n", $item['slice_data']);

									$linktextRaw = \akrys\redaxo\addon\UsageCheck\RedaxoCall::i18nMsg('akrys_usagecheck_module_linktext_edit_slice');
									foreach ($usages as $usageRaw) {
										$usage = explode("\t", $usageRaw);
										$sliceID = $usage[0];
										$clang = $usage[1];
										$ctype = $usage[2];
										$articleID = $usage[3];
										$categoryID = $usage[4];
										$articleName = $usage[5];

										$hasPerm = false;
										switch (\akrys\redaxo\addon\UsageCheck\RedaxoCall::getRedaxoVersion()) {
											case \akrys\redaxo\addon\UsageCheck\RedaxoCall::REDAXO_VERSION_4:
												if (/* $REX['USER']->hasPerm('article['.$articleID.']') || */$REX['USER']->hasCategoryPerm($articleID)) {
													$hasPerm = true;
												}
												break;
											case \akrys\redaxo\addon\UsageCheck\RedaxoCall::REDAXO_VERSION_5:
												$user = \rex::getUser();
												$perm = rex_structure_perm::get($user, 'structure');
												$hasPerm = $perm->hasCategoryPerm($articleID);
												break;
										}

										//$REX['USER']->hasPerm('article['.$articleID.']') ist immer false
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
