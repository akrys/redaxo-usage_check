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

echo RedaxoCall::rexTitle(Config::NAME_OUT.' / '.RedaxoCall::i18nMsg('akrys_usagecheck_template_subpagetitle').' <span style="font-size:10px;color:#c2c2c2">'.Config::VERSION.'</span>');
$templates = akrys\redaxo\addon\UsageCheck\Modules\Templates::create();
$items = $templates->getTemplates($showAll, $showInactive);

if ($items === false) {
	echo RedaxoCall::errorMsg(RedaxoCall::i18nMsg('akrys_usagecheck_no_rights'), true);
	return;
}
?>
<div class="rex-navi-slice">

	<?php
	$templates->outputMenu($subpage, $showAll, $showInactive);
	?>

</div>
<div style='clear:both'></div>

<p class="rex-tx1">
	<?php echo RedaxoCall::i18nMsg('akrys_usagecheck_template_intro_text'); ?><br />
	<br />
</p>

<table class="<?php echo RedaxoCall::getTableClass() ?>">
	<thead>
		<tr>
			<th><?php echo RedaxoCall::i18nMsg('akrys_usagecheck_template_table_heading_name'); ?></th>
			<th><?php echo RedaxoCall::i18nMsg('akrys_usagecheck_template_table_heading_functions'); ?></th>
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
						(<?php echo RedaxoCall::i18nMsg('akrys_usagecheck_template_table_inactive'); ?>)
						<br />
						<?php
					}
					?>


				</td>
				<td>

					<?php
					if ($item['articles'] === null && $item['templates'] === null) {
						echo RedaxoCall::errorMsg(RedaxoCall::i18nMsg('akrys_usagecheck_images_msg_not_used'));
					} else {
						echo RedaxoCall::infoMsg(RedaxoCall::i18nMsg('akrys_usagecheck_template_msg_used'));
					}
					?>

					<div  class="rex-message" style="border:0;outline:0;">
						<span>
							<strong><?php echo RedaxoCall::i18nMsg('akrys_usagecheck_template_detail_heading'); ?></strong>
							<ol>

								<li><?php $templates->outputTemplateEdit($item, RedaxoCall::i18nMsg('akrys_usagecheck_template_linktext_edit_code')); ?></li>

								<?php
								if ($item['articles'] !== null) {
									$linktextRaw = RedaxoCall::i18nMsg('akrys_usagecheck_template_linktext_edit_article');
									$articles = explode("\n", $item['articles']);
									foreach ($articles as $article) {
										$usage = explode("\t", $article);
										$articleID = $usage[0];
										$articleReID = $usage[1];
										$startpage = $usage[2];
										$articleName = $usage[3];
										$clang = $usage[4];

										if ($startpage == 1) {
											$articleReID = $articleID;
										}

										$hasPerm = $templates->hasArticlePerm($articleID);

										if ($hasPerm) {
											$href = 'index.php?page=structure&article_id='.$articleID.'&function=edit_art&category_id='.$articleReID.'&clang='.$clang;
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
								$hasPerm = RedaxoCall::isAdmin();
								if ($hasPerm) {

									if ($item['templates'] !== null) {
										$templateData = explode("\n", $item['templates']);
										$linktextRaw = RedaxoCall::i18nMsg('akrys_usagecheck_template_linktext_edit_template');
										foreach ($templateData as $templateItem) {
											$usage = explode("\t", $templateItem);

											$id = $usage[0];
											$name = $usage[1];

											$href = $templates->getEditLink($id);
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

