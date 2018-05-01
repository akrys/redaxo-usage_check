<?php
$api = akrys\redaxo\addon\UsageCheck\RedaxoCall::getAPI();
?>

<table class="<?= $api->getTableClass() ?>">
	<thead>
		<tr>
			<th><?= \rex_i18n::rawMsg('akrys_usagecheck_template_table_heading_name'); ?></th>
			<th><?= \rex_i18n::rawMsg('akrys_usagecheck_template_table_heading_functions'); ?></th>
		</tr>
	</thead>
	<tbody>

		<?php
		foreach ($this->items as $item) {
			?>
			<tr<?= $item['active'] == 1 ? '' : ' style = "opacity:0.80;"' ?>>
				<td>
					<?php
					echo $item['name'];
					if ($item['active'] == 0) {
						?>
						<br />
						(<?= \rex_i18n::rawMsg('akrys_usagecheck_template_table_inactive'); ?>)
						<br />
						<?php
					}
					?>

				</td>
				<td>

					<?php
					if ($item['articles'] === null && $item['templates'] === null) {
						$index = 'akrys_usagecheck_images_msg_not_used';
						echo $api->getTaggedErrorMsg(\rex_i18n::rawMsg($index));
					} else {
						$index = 'akrys_usagecheck_template_msg_used';
						echo $api->getTaggedInfoMsg(\rex_i18n::rawMsg($index));
					}
					?>

					<div  class="rex-message" style="border:0;outline:0;">
						<span>
							<?php
							$text = \rex_i18n::rawMsg('akrys_usagecheck_template_detail_heading');
							?>

							<strong><?= $text ?></strong>
							<ol>
								<?php
								$text = \rex_i18n::rawMsg('akrys_usagecheck_template_linktext_edit_code');
								?>

								<li><?= $this->templates->outputTemplateEdit($item, $text); ?></li>

								<?php
								if ($item['articles'] !== null) {
									$index = 'akrys_usagecheck_template_linktext_edit_article';
									$linkTextRaw = \rex_i18n::rawMsg($index);
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

										$hasPerm = $this->templates->hasArticlePerm($articleID);

										if ($hasPerm) {
											$href = 'index.php ? page = structure&article_id = '.$articleID.
												'&function = edit_art&category_id = '.$articleReID.'&clang = '.$clang;
											$linkText = $linkTextRaw;
											$linkText = str_replace('$articleID$', $articleID, $linkText);
											$linkText = str_replace('$articleName$', $articleName, $linkText);
											?>

											<li><a href="<?= $href; ?>"><?= $linkText; ?></a></li>

											<?php
										}
									}
								}

								//Templates, die in Templates verwendert werden, betrifft
								//nur die Coder, und das wären Admins
								$hasPerm = $api->isAdmin();
								if ($hasPerm) {
									if ($item['templates'] !== null) {
										$templateData = explode("\n", $item['templates']);

										$index = 'akrys_usagecheck_template_linktext_edit_template';
										foreach ($templateData as $templateItem) {
											$usage = explode("\t", $templateItem);

											$id = $usage[0];
											$name = $usage[1];

											$href = $this->templates->getEditLink($id);
											$linkText = $linkTextRaw;
											$linkText = str_replace('$templateName$', $name, $linkText);
											$linkText = str_replace('$templateID$', $item['id'], $linkText);
											?>

											<li><a href="<?= $href; ?>"><?= $linkText; ?></a></li>

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
