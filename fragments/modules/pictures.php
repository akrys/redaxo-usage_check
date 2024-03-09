<?php

$user = rex::getUser();
$mediaPerm = $user->getComplexPerm('media');
$structurePerm = $user->getComplexPerm('structure');
?>

<?= rex_i18n::rawMsg('akrys_usagecheck_images_heading_category_filter'); ?>:<br />

<form action="index.php" method="get">
	<input type="hidden" name="page" value="usage_check/picture" />
	<input type="hidden" name="showall" value="<?= rex_get('showall', 'string', "") ?>" />

	<?php
	$catsSel = new rex_media_category_select();
	$catsSel->setSize(1);
	$catsSel->setStyle('class="form-control selectpicker"');
	$catsSel->setName('rex_file_category');
	$catsSel->setId('rex_file_category');
	$catsSel->setAttribute('class', 'selectpicker form-control');
	$catsSel->setAttribute('data-live-search', 'true');
	$catsSel->setSelected(rex_get('rex_file_category', 'int', 0));
	$catsSel->addOption(rex_i18n::msg('pool_kats_no'), '0');

	$catsSel->setAttribute('onchange', 'this.form.submit()');

	echo $catsSel->get();
	?>
</form>
<br />

<table class="table table-striped">
	<thead>
		<tr>
			<th class="name"><?= \rex_i18n::rawMsg('akrys_usagecheck_images_table_heading_name'); ?></th>
			<th class="function"><?= \rex_i18n::rawMsg('akrys_usagecheck_images_table_heading_functions'); ?></th>
		</tr>
	</thead>
	<tbody>

		<?php
		foreach ($this->items['result'] as $item) {
			$continue = false;
			try {
				$medium = FriendsOfRedaxo\addon\UsageCheck\Medium::get($item);
			} catch (\Exception $e) {
				continue;
			}

			$initCat = null;

			$fileSize = new \FriendsOfRedaxo\addon\UsageCheck\Lib\FileSize($item['filesize']);
			?>

			<tr>
				<td class="name">

					<?php
					if (stristr($item['filetype'], 'image/')) {
						$url = 'index.php?rex_media_type=rex_mediapool_preview&rex_media_file='.$item['filename'];

						$fragment = new \rex_fragment([
							'src' => $url,
							'alt' => '',
						]);
						echo $fragment->parse('fragments/image.php');
					}
					?>
					<br /><br />

					<strong><?= $item['title']; ?></strong><br />

					<?php
					echo $item['filename'].' ('.$fileSize->getSizeOut($item).')';
					?>

					<br />
					<small class="filetype"><?= $item['filetype']; ?></small>
				</td>
				<td class="function">
					<?php
					echo \FriendsOfRedaxo\addon\UsageCheck\Modules\Pictures::showUsedInfo($item, $this->items['fields']);
					?>

					<div class="rex-message list">
						<span>
							<ol>
								<?php
								$type = FriendsOfRedaxo\addon\UsageCheck\Modules\Pictures::TYPE;
								$url = "index.php?page=usage_check/details&type=".$type."&id=".$item['id'];
								?>

								<li><a href="<?= $url; ?>"><?= \rex_i18n::rawMsg('akrys_usagecheck_linktext_detail_page') ?></a></li>

								<?php
								$url = 'index.php?page=mediapool&subpage=detail&file_name='.$item['filename'];
								$linkText = \rex_i18n::rawMsg('akrys_usagecheck_images_linktext_edit');
								?>

								<li><a href="<?= $url ?>" target="_blank"><?= $linkText; ?></a><br /></li>
							</ol>
						</span>
					</div>

					<div  class="rex-message">
						<span>

							<?php
							/* @var $initCat rex_media_category */
							/* @var $medium rex_media */

							$initCat = $medium->getCategory();

							if (isset($initCat)) {
								$title = \rex_i18n::rawMsg('akrys_usagecheck_images_category_header');
								?>

								<small>
									<br />
									<strong><?= $title ?></strong>
									<br />

									<?php
									$i = 0;
									foreach ($initCat->getParentTree() as $category) {
										$url = 'index.php?page=mediapool&rex_file_category='.$category->getId();
										$linkText = $category->getName();
										?>
										<a href="<?= $url ?>"><?= $linkText ?></a>

										/
										<?php
									}
									$url = 'index.php?page=mediapool&rex_file_category='.$initCat->getId();
									$linkText = $initCat->getName();
									?>
									<a href="<?= $url ?>"><?= $linkText ?></a>

								</small>
								<?php
							}
							?>

						</span>
					</div>
				</td>
			</tr>

			<?php
		}
		?>

	</tbody>
</table>
