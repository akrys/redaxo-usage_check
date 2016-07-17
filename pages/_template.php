<?php
/**
 * Frontend-Ausagbe für die Seite Tempalte
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

$title = Config::NAME_OUT.' / '.RedaxoCall::getAPI()->i18nMsg('akrys_usagecheck_template_subpagetitle').
	' <span style = "font-size:10px;color:#c2c2c2">'.Config::VERSION.'</span>';
echo RedaxoCall::getAPI()->rexTitle($title);
$templates = akrys\redaxo\addon\UsageCheck\Modules\Templates::create();
if ($showAll) {
	$templates->showAll($showAll);
}
if ($showInactive) {
	$templates->showInactive($showInactive);
}
$items = $templates->getTemplates();

if ($items === false) {
	echo RedaxoCall::getAPI()->errorMsgAddTags(RedaxoCall::getAPI()->i18nMsg('akrys_usagecheck_no_rights'));
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
	<?php echo RedaxoCall::getAPI()->i18nMsg('akrys_usagecheck_template_intro_text'); ?><br />
	<br />
</p>

<table class="<?php echo RedaxoCall::getAPI()->getTableClass() ?>">
	<thead>
		<tr>
			<th><?php echo RedaxoCall::getAPI()->i18nMsg('akrys_usagecheck_template_table_heading_name'); ?></th>
			<th><?php echo RedaxoCall::getAPI()->i18nMsg('akrys_usagecheck_template_table_heading_functions'); ?></th>
		</tr>
	</thead>
	<tbody>

		<?php
		foreach ($items as $item) {
			?>
			<tr<?php echo $item['active'] == 1 ? '' : ' style = "opacity:0.80;"' ?>>
				<td>
					<?php
					echo $item['name'];
					if ($item['active'] == 0) {
						?>
						<br />
						(<?php echo RedaxoCall::getAPI()->i18nMsg('akrys_usagecheck_template_table_inactive'); ?>)
						<br />
						<?php
					}
					?>

				</td>
				<td>

					<?php
					if ($item['articles'] === null && $item['templates'] === null) {
						$index = 'akrys_usagecheck_images_msg_not_used';
						echo RedaxoCall::getAPI()->errorMsg(RedaxoCall::getAPI()->i18nMsg($index));
					} else {
						$index = 'akrys_usagecheck_template_msg_used';
						echo RedaxoCall::getAPI()->infoMsg(RedaxoCall::getAPI()->i18nMsg($index));
					}
					?>

					<div  class="rex-message" style="border:0;outline:0;">
						<span>
							<?php
							$text = RedaxoCall::getAPI()->i18nMsg('akrys_usagecheck_template_detail_heading');
							?>

							<strong><?php echo $text ?></strong>
							<ol>
								<?php
								$text = RedaxoCall::getAPI()->i18nMsg('akrys_usagecheck_template_linktext_edit_code');
								?>

								<li><?php $templates->outputTemplateEdit($item, $text); ?></li>

								<?php
								if ($item['articles'] !== null) {
									$index = 'akrys_usagecheck_template_linktext_edit_article';
									$linkTextRaw = RedaxoCall::getAPI()->i18nMsg($index);
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
											$href = 'index.php ? page = structure&article_id = '.$articleID.
												'&function = edit_art&category_id = '.$articleReID.'&clang = '.$clang;
											$linkText = $linkTextRaw;
											$linkText = str_replace('$articleID$', $articleID, $linkText);
											$linkText = str_replace('$articleName$', $articleName, $linkText);
											?>

											<li><a href="<?php echo $href; ?>"><?php echo $linkText; ?></a></li>

											<?php
										}
									}
								}

								//Templates, die in Templates verwendert werden, betrifft
								//nur die Coder, und das wären Admins
								$hasPerm = RedaxoCall::getAPI()->isAdmin();
								if ($hasPerm) {
									if ($item['templates'] !== null) {
										$templateData = explode("\n", $item['templates']);

										$index = 'akrys_usagecheck_template_linktext_edit_template';
										$linkTextRaw = RedaxoCall::getApi()->i18nMsg($index);
										foreach ($templateData as $templateItem) {
											$usage = explode("\t", $templateItem);

											$id = $usage[0];
											$name = $usage[1];

											$href = $templates->getEditLink($id);
											$linkText = $linkTextRaw;
											$linkText = str_replace('$templateName$', $name, $linkText);
											$linkText = str_replace('$templateID$', $item['id'], $linkText);
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

