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
require_once __DIR__.'/../akrys/redaxo/addon/UsageCheck/Modules/Templates.php';

switch (rex_get('showall', 'string', "")) {
	case 'true':
		$showAll = true;
		break;
	case 'false':
	default:
		$showAll = false;
		break;
}

switch (rex_get('showinactive', 'string', "")) {
	case 'true':
		$showInactive = true;
		break;
	case 'false':
	default:
		$showInactive = false;
		break;
}

echo \akrys\redaxo\addon\UsageCheck\RedaxoCall::rexTitle(Config::NAME_OUT.' / '.\akrys\redaxo\addon\UsageCheck\RedaxoCall::i18nMsg('akrys_usagecheck_template_subpagetitle').' <span style="font-size:10px;color:#c2c2c2">'.Config::VERSION.'</span>', Config::NAME_OUT);

$items = \akrys\redaxo\addon\UsageCheck\Modules\Templates::getTemplates($showAll, $showInactive);

switch (\akrys\redaxo\addon\UsageCheck\RedaxoCall::getRedaxoVersion()) {
	case \akrys\redaxo\addon\UsageCheck\RedaxoCall::REDAXO_VERSION_4:
		$tableClass = 'rex-table';
		break;

	case \akrys\redaxo\addon\UsageCheck\RedaxoCall::REDAXO_VERSION_5:
		$tableClass = 'table table-striped';
		break;
}

if ($items === false) {
	echo \akrys\redaxo\addon\UsageCheck\RedaxoCall::errorMsg(\akrys\redaxo\addon\UsageCheck\RedaxoCall::i18nMsg('akrys_usagecheck_no_rights'), true);
	return;
}

$showAllParam = '';
$showAllParamCurr = '&showall=true';
$showAllLinktext = \akrys\redaxo\addon\UsageCheck\RedaxoCall::i18nMsg('akrys_usagecheck_template_link_show_unused');
if (!$showAll) {
	$showAllParam = '&showall=true';
	$showAllParamCurr = '';
	$showAllLinktext = \akrys\redaxo\addon\UsageCheck\RedaxoCall::i18nMsg('akrys_usagecheck_template_link_show_all');
}

$showInactiveParam = '';
$showInactiveParamCurr = '&showinactive=true';
$showInactiveLinktext = \akrys\redaxo\addon\UsageCheck\RedaxoCall::i18nMsg('akrys_usagecheck_template_link_show_active');
if (!$showInactive) {
	$showInactiveParam = '&showinactive=true';
	$showInactiveParamCurr = '';
	$showInactiveLinktext = \akrys\redaxo\addon\UsageCheck\RedaxoCall::i18nMsg('akrys_usagecheck_template_link_show_active_inactive');
}
?>
<div class="rex-navi-slice">
	<ul>
		<li><a href="index.php?page=<?php echo Config::NAME; ?>&subpage=<?php echo $subpage; ?><?php echo $showAllParam.$showInactiveParamCurr; ?>"><?php echo $showAllLinktext; ?></a></li>

		<?php
		if ($REX['USER']->isAdmin()) {
			?>

			<li><a href="index.php?page=<?php echo Config::NAME; ?>&subpage=<?php echo $subpage; ?><?php echo $showAllParamCurr.$showInactiveParam; ?>"><?php echo $showInactiveLinktext ?></a></li>

			<?php
		}
		?>

	</ul>
</div>
<div style='clear:both'></div>

<p class="rex-tx1">
	<?php echo \akrys\redaxo\addon\UsageCheck\RedaxoCall::i18nMsg('akrys_usagecheck_template_intro_text'); ?><br />
	<br />
</p>

<table class="<?php echo $tableClass ?>">
	<thead>
		<tr>
			<th><?php echo \akrys\redaxo\addon\UsageCheck\RedaxoCall::i18nMsg('akrys_usagecheck_template_table_heading_name'); ?></th>
			<th><?php echo \akrys\redaxo\addon\UsageCheck\RedaxoCall::i18nMsg('akrys_usagecheck_template_table_heading_functions'); ?></th>
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
						(<?php echo \akrys\redaxo\addon\UsageCheck\RedaxoCall::i18nMsg('akrys_usagecheck_template_table_inactive'); ?>)
						<br />
						<?php
					}
					?>


				</td>
				<td>
					 <?php
					 if ($item['articles'] === null && $item['templates'] === null) {
						 ?>

						<div class="rex-message">
							<div class="rex-warning">
								<p>
									<span><?php echo \akrys\redaxo\addon\UsageCheck\RedaxoCall::i18nMsg('akrys_usagecheck_images_msg_not_used'); ?></span>
								</p>
							</div>
						</div>

						<?php
					} else {
						?>

						<div class="rex-message">
							<div class="rex-info">
								<p>
									<span><?php echo \akrys\redaxo\addon\UsageCheck\RedaxoCall::i18nMsg('akrys_usagecheck_template_msg_used'); ?></span>
								</p>
							</div>
						</div>

						<?php
					}
					?>

					<div  class="rex-message" style="border:0;outline:0;">
						<span>
							<strong><?php echo \akrys\redaxo\addon\UsageCheck\RedaxoCall::i18nMsg('akrys_usagecheck_template_detail_heading'); ?></strong>
							<ol>
								 <?php
								 if ($REX['USER']->isAdmin()) {
									 ?>

									<li><a href="index.php?page=template&subpage=&function=edit&template_id=<?php echo $item['id']; ?>"><?php echo \akrys\redaxo\addon\UsageCheck\RedaxoCall::i18nMsg('akrys_usagecheck_template_linktext_edit_code'); ?></a></li>

									<?php
								}
								?>

								<?php
								if ($item['articles'] !== null) {
									$linktextRaw = \akrys\redaxo\addon\UsageCheck\RedaxoCall::i18nMsg('akrys_usagecheck_template_linktext_edit_article');
									$articles = explode("\n", $item['articles']);
									foreach ($articles as $article) {
										$usage = explode("\t", $article);
										$articleID = $usage[0];
										$articleReID = $usage[1];
										$startpage = $usage[2];
										$articleName = $usage[3];

										if ($startpage == 1) {
											$articleReID = $articleID;
										}

										//$REX['USER']->hasPerm('article['.$articleID.']') ist immer false
										if (/* $REX['USER']->hasPerm('article['.$articleID.']') || */ $REX['USER']->hasCategoryPerm($articleID)) {

											$href = 'index.php?page=structure&article_id='.$articleID.'&function=edit_art&category_id='.$articleReID.'&clang=0';
											$linktext = $linktextRaw;
											$linktext = str_replace('$articleID$', $articleID, $linktext);
											$linktext = str_replace('$articleName$', $articleName, $linktext);
											?>

											<li><a href="<?php echo $href; ?>"><?php echo $linktext; ?></a></li>

											<?php
										}
									}
								}

								//Templates, die in Templates verwendert werden, betrifft
								//nur die Coder, und das wären Admins
								if ($REX['USER']->isAdmin()) {

									if ($item['templates'] !== null) {
										$templates = explode("\n", $item['templates']);
										$linktextRaw = \akrys\redaxo\addon\UsageCheck\RedaxoCall::i18nMsg('akrys_usagecheck_template_linktext_edit_template');
										foreach ($templates as $template) {
											$usage = explode("\t", $template);

											$id = $usage[0];
											$name = $usage[1];

											$href = 'index.php?page=template&subpage=&function=edit&template_id='.$id;
											$linktext = $linktextRaw;
											$linktext = str_replace('$templateName$', $name, $linktext);
											$linktext = str_replace('$templateID$', $item['id'], $linktext);
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

