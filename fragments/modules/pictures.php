<?php
$user = \rex::getUser();
$mediaPerm =  $user->getComplexPerm('media');
$structurePerm = $user->getComplexPerm('structure');
?>

<table class="table table-striped">
	<thead>
		<tr>
			<th><?= \rex_i18n::rawMsg('akrys_usagecheck_images_table_heading_name'); ?></th>
			<th><?= \rex_i18n::rawMsg('akrys_usagecheck_images_table_heading_functions'); ?></th>
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
				<td>

					<?php
					if (stristr($item['filetype'], 'image/')) {
						$url = 'index.php?rex_media_type=rex_mediapool_preview&rex_media_file='.$item['filename'];

						$fragment = new \rex_fragment([
							'src' => $url,
							'alt' => '',
							'style' => 'max-width:150px;max-height: 150px;',
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
					<small style="font-size:0.875em;font-weight:bold;"><?= $item['filetype']; ?></small>
				</td>
				<td style="width:75%;">
					<?php
					echo \FriendsOfRedaxo\addon\UsageCheck\Modules\Pictures::showUsedInfo($item, $this->items['fields']);
					?>

					<div class="rex-message" style="border:0;outline:0;">
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

					<div  class="rex-message" style="border:0;outline:0;">
						<span>

							<?php
							/* @var $initCat rex_media_category */
							/* @var $medium rex_media */

							$initCat = $medium->getCategory();

							if (isset($initCat)) {
								$title = \rex_i18n::rawMsg('akrys_usagecheck_images_category_header');
								?>

								<small style="font-size:0.875em;">
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
