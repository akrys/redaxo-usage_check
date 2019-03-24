<?php
if (count($this->errors) > 0) {
	$fragment = new rex_fragment(['msg' => $this->errors]);
	echo $fragment->parse('msg/error_box.php');
	return;
}

$user = \rex::getUser();
$mediaPerm = \rex_structure_perm::get($user, 'media');
$structurePerm = \rex_structure_perm::get($user, 'structure');

$media = rex_media::get($this->data['first']['filename']);
?>

<div class="basis">
	<?php
	echo \akrys\redaxo\addon\UsageCheck\Modules\Pictures::showUsedInfo($this->data['first'], $this->data['fields']);
	?>

	<div style="float:left;margin-left:5px;">
		<ol>
			<?php
			$url = 'index.php?page=mediapool&subpage=detail&file_name='.$this->filename;
			$linkText = \rex_i18n::rawMsg('akrys_usagecheck_images_linktext_edit');
			?>

			<li><a href="<?= $url ?>" target="_blank"><?= $linkText; ?></a><br /></li>

			<?php
			foreach ($this->data['result'] as $type => $items) {
				switch ($type) {
					case 'slices':
						$index = 'akrys_usagecheck_images_linktext_edit_in_slice';
						$linkTextRaw = \rex_i18n::rawMsg($index);

						foreach ($items as $item) {
							//s.id,"\\t",s.article_id,"\\t",s.clang,"\\t",s.ctype
							$sliceID = $item['usagecheck_s_id'];
							$articleID = $item['usagecheck_s_article_id'];
							$articleName = $item['usagecheck_a_name'];
							$clang = $item['usagecheck_s_clang_id'];
							$ctype = $item['usagecheck_s_ctype_id'];

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
						}
						break;
					case 'art_meta':
						$index = 'akrys_usagecheck_images_linktext_edit_in_metadata_art';
						$linkTextRaw = \rex_i18n::rawMsg($index);
						foreach ($items as $item) {
							$articleID = $item['usagecheck_art_id'];
							$articleName = $item['usagecheck_art_name'];
							$clang = $item['usagecheck_art_clang'];

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
						}
						break;
					case 'cat_meta':
						$index = 'akrys_usagecheck_images_linktext_edit_in_metadata_cat';
						$linkTextRaw = \rex_i18n::rawMsg($index);

						foreach ($items as $item) {
							//http://51.redaxo.akrys-dev.local/redaxo/index.php?page=structure&category_id=5&article_id=0&clang=1&edit_id=11&function=edit_cat&catstart=0
							//s.id,"\\t",s.article_id,"\\t",s.clang,"\\t",s.ctype
							$articleID = $item['usagecheck_cat_id'];
							$articleName = $item['usagecheck_cat_name'];
							$clang = $item['usagecheck_cat_clang'];
							$parentID = $item['usagecheck_cat_parent_id'];

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
						}
						break;
					case 'media_meta':
						$index = 'akrys_usagecheck_images_linktext_edit_in_metadata_med';
						$linkTextRaw = \rex_i18n::rawMsg($index);

						foreach ($items as $item) {
							//file_id,"\t",category_id,"\t",filename
							$fileID = $item['usagecheck_med_id'];
							$fileCatID = $item['usagecheck_med_cat_id'];
							$filename = $item['usagecheck_med_filename'];

							$hasPerm = $mediaPerm->hasCategoryPerm($fileCatID);

							if ($hasPerm) {
								$linkText = $linkTextRaw;
								$linkText = str_replace('$filename$', $filename, $linkText);
								$href = "index.php?page=mediapool&subpage=detail&file_name=".$filename;
								?>

								<li><a href="<?= $href; ?>"><?= $linkText; ?></a></li>

								<?php
							}
						}
						break;
					case 'yform':
						$index = 'akrys_usagecheck_images_linktext_edit_in_yformtable';
						$linkTextRaw = \rex_i18n::rawMsg($index);
						foreach ($items as $table => $fields) {
							foreach ($fields as $field => $entries) {
								foreach ($entries as $id => $item) {

									$id = $item['usagecheck_'.$table.'_id'];
									$hasPerm = \rex::getUser()->isAdmin() || (
										\rex::getUser()->hasPerm('yform[]') &&
										\rex::getUser()->hasPerm('yform[table:'.$table.']')
										);

									$linkText = $linkTextRaw;
									$linkText = str_replace('$entryID$', $id, $linkText);
									$linkText = str_replace('$tableName$', $field, $linkText);

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
						}
						break;
					default:
//						var_dump($type, $articleData);
						break;
				}
			}
			?>

		</ol>

		<?php
		if ($media) {
			/* @var $initCat rex_media_category */
			$initCat = $media->getCategory();

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
		}
		?>
	</div>
	<div style="float:right;padding:5px;margin:0 5px 0;">

		<?php
		$url = 'index.php?rex_media_type=rex_mediapool_detail&rex_media_file='.$this->data['first']['filename'];
		$fileSize = new \akrys\redaxo\addon\UsageCheck\Lib\FileSize($this->data['first']['filesize']);

		if (!stristr($this->data['first']['filetype'], 'image/')) {
			?>

			<div>
				<?= $fileSize->getSizeOut() ?><br />
				<?= $this->data['first']['title'] ?><br />
			</div>

			<?php
		} else {

			$fragment = new \rex_fragment([
				'src' => $url,
				'alt' => '',
//		'style' => 'max-width:150px;max-height: 150px;',
			]);
			echo $fragment->parse('fragments/image.php');
			?>

			<div>
				<?= $fileSize->getSizeOut() ?><br />
				<?= $this->data['first']['title'] ?><br />
				<?= $this->data['first']['width'] ?> &times;<?= $this->data['first']['height'] ?><br />
			</div>

			<?php
		}
		?>


		<table style="border: 0px solid #000000;max-width:700px;">
			<?php
			foreach ($this->data['first'] as $key => $value) {
				if (preg_match('/^usagecheck_/', $key)) {
					continue;
				}
				?>

				<tr>
					<th style="border: 1px solid #000000;padding: 0 5px;"><?= $key ?></th>
					<td style="border: 1px solid #000000;padding: 0 5px; width:75%;"><?= $value ?></td>
				</tr>

				<?php
			}
			?>

		</table>
	</div>
</div>
