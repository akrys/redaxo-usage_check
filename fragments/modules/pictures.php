<?php
$user = \rex::getUser();
$mediaPerm = \rex_structure_perm::get($user, 'media');
$structurePerm = \rex_structure_perm::get($user, 'structure');
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
				$medium = akrys\redaxo\addon\UsageCheck\Medium::get($item);
			} catch (\Exception $e) {
				continue;
			}

			$initCat = null;

			$fileSize = new \akrys\redaxo\addon\UsageCheck\Lib\FileSize($item['filesize']);
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
						echo \akrys\redaxo\addon\UsageCheck\Modules\Pictures::showUsedInfo($item, $this->items['fields']);
					?>

					<div class="rex-message" style="border:0;outline:0;">
						<span>
							<ol>
								<?php
								$type = akrys\redaxo\addon\UsageCheck\Modules\Pictures::TYPE;
								$url = "index.php?page=usage_check/details&type=".$type."&id=".$item['id'];
								?>

								<li><a href="<?= $url; ?>"><?= \rex_i18n::rawMsg('akrys_usagecheck_linktext_detail_page') ?></a></li>

								<?php
								$url = 'index.php?page=mediapool&subpage=detail&file_name='.$item['filename'];
								$linkText = \rex_i18n::rawMsg('akrys_usagecheck_images_linktext_edit');
								?>

								<li><a href="<?= $url ?>" target="_blank"><?= $linkText; ?></a><br /></li>

								<?php
								/*
								  if ($item['slice_data'] !== null) {
								  $usages = explode("\n", $item['slice_data']);

								  $index = 'akrys_usagecheck_images_linktext_edit_in_slice';
								  $linkTextRaw = \rex_i18n::rawMsg($index);
								  foreach ($usages as $usage) {
								  $articleData = explode("\t", $usage);

								  //s.id,"\\t",s.article_id,"\\t",s.clang,"\\t",s.ctype
								  $sliceID = $articleData[0];
								  $articleID = $articleData[1];
								  $articleName = $articleData[2];
								  $clang = $articleData[3];
								  $ctype = $articleData[4];

								  $hasPerm = $structurePerm->hasCategoryPerm($articleID);
								  if ($hasPerm) {
								  $linkText = $linkTextRaw;
								  $linkText = str_replace('$sliceID$', $sliceID, $linkText);
								  $linkText = str_replace('$articleName$', $articleName, $linkText);

								  $href = 'index.php?page=content&article_id='.$articleID.
								  '&mode=edit&slice_id='.$sliceID.'&clang='.$clang.
								  '&ctype='.$ctype.'&function=edit#slice'.$sliceID;
								  ?>

								  <li><a href="<?= $href; ?>"><?= $linkText; ?></a></li>

								  <?php
								  }
								  unset($href, $linkText, $ctype, $clang, $articleID, $articleName, $sliceID);
								  }
								  }
								  ?>

								  <?php
								  if (isset($item['usagecheck_metaArtIDs']) && (int) $item['usagecheck_metaArtIDs'] > 0) {
								  $usages = explode("\n", $item['usagecheck_metaArtIDs']);
								  $index = 'akrys_usagecheck_images_linktext_edit_in_metadata_art';
								  $linkTextRaw = \rex_i18n::rawMsg($index);
								  foreach ($usages as $usage) {
								  $articleData = explode("\t", $usage);

								  $articleID = $articleData[0];
								  $articleName = $articleData[1];
								  $clang = $articleData[2];

								  $hasPerm = $structurePerm->hasCategoryPerm($articleID);
								  $href = 'index.php?'.
								  'page=content/metainfo&'.
								  'article_id='.$articleID.'&'.
								  'clang='.$clang.'&'.
								  'ctype=1';
								  if ($hasPerm) {
								  $linkText = $linkTextRaw;
								  $linkText = str_replace('$articleID$', $articleID, $linkText);
								  $linkText = str_replace('$articleName$', $articleName, $linkText);
								  ?>

								  <li><a href="<?= $href; ?>"><?= $linkText; ?></a></li>

								  <?php
								  }
								  unset($href, $linkText, $ctype, $clang, $articleID, $articleName, $sliceID);
								  }
								  }

								  if (isset($item['usagecheck_metaCatIDs']) && (int) $item['usagecheck_metaCatIDs'] > 0) {
								  $usages = explode("\n", $item['usagecheck_metaCatIDs']);
								  $index = 'akrys_usagecheck_images_linktext_edit_in_metadata_cat';
								  $linkTextRaw = \rex_i18n::rawMsg($index);
								  foreach ($usages as $usage) {
								  $articleData = explode("\t", $usage);

								  //http://51.redaxo.akrys-dev.local/redaxo/index.php?page=structure&category_id=5&article_id=0&clang=1&edit_id=11&function=edit_cat&catstart=0
								  //s.id,"\\t",s.article_id,"\\t",s.clang,"\\t",s.ctype
								  $articleID = $articleData[0];
								  $articleName = $articleData[1];
								  $clang = $articleData[2];
								  $parentID = $articleData[3];

								  $hasPerm = $structurePerm->hasCategoryPerm($articleID);

								  if ($hasPerm) {
								  $linkText = $linkTextRaw;
								  $linkText = str_replace('$articleID$', $articleID, $linkText);
								  $linkText = str_replace('$articleName$', $articleName, $linkText);
								  $href = 'index.php?page=structure&category_id='.$parentID.
								  '&article_id=0&clang='.$clang.'&edit_id='.$articleID.
								  '&function=edit_cat&catstart=0';
								  ?>

								  <li><a href="<?= $href; ?>"><?= $linkText; ?></a></li>

								  <?php
								  }
								  unset($href, $linkText, $ctype, $clang, $articleID, $articleName, $sliceID);
								  }
								  }

								  if (isset($item['usagecheck_metaMedIDs']) && (int) $item['usagecheck_metaMedIDs'] > 0) {
								  $index = 'akrys_usagecheck_images_linktext_edit_in_metadata_med';
								  $linkTextRaw = \rex_i18n::rawMsg($index);

								  $usages = explode("\n", $item['usagecheck_metaMedIDs']);
								  foreach ($usages as $usage) {
								  $mediaData = explode("\t", $usage);

								  //file_id,"\t",category_id,"\t",filename
								  $fileID = $mediaData[0];
								  $fileCatID = $mediaData[1];
								  $filename = $mediaData[2];

								  $hasPerm = $mediaPerm->hasCategoryPerm($fileCatID);

								  if ($hasPerm) {
								  $linkText = $linkTextRaw;
								  $linkText = str_replace('$filename$', $filename, $linkText);
								  $href = "index.php?page=mediapool&subpage=detail&file_name=".$filename;
								  ?>

								  <li><a href="<?= $href; ?>"><?= $linkText; ?></a></li>

								  <?php
								  }
								  unset($href, $linkText, $ctype, $clang, $articleID, $articleName, $sliceID);
								  }
								  }

								  $index = 'akrys_usagecheck_images_linktext_edit_in_yformtable';
								  $linkTextRaw = \rex_i18n::rawMsg($index);
								  foreach ($this->items['fields'] as $table => $field) {
								  if (!isset($item[$table])) {
								  continue;
								  }

								  $hasPerm = \rex::getUser()->isAdmin() || (
								  \rex::getUser()->hasPerm('yform[]') &&
								  \rex::getUser()->hasPerm('yform[table:'.$table.']')
								  );

								  $ids = explode("\n", $item[$table]);
								  foreach ($ids as $id) {
								  $linkText = $linkTextRaw;
								  $linkText = str_replace('$entryID$', $id, $linkText);
								  $linkText = str_replace('$tableName$', $field[0]['table_out'], $linkText);

								  if ($hasPerm) {
								  $href = 'index.php?page=yform/manager/data_edit&'.
								  'table_name='.$table.'&'.
								  'data_id='.$id.'&'.
								  'func=edit';
								  if ($href == '') {
								  continue;
								  }
								  ?>

								  <li><a href="<?= $href; ?>"><?= $linkText; ?></a></li>

								  <?php
								  }
								  }
								  }
								 */
								?>

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
