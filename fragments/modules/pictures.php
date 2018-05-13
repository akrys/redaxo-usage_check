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
				<td>

					<?php
					$used = false;
					if ($item['slice_data'] !== null) {
						$used = true;
					}

					$table = '';
					foreach ($this->items['fields'] as $tablename => $field) {
						if ($item[$tablename] !== null) {
							$used = true;
							$table = $tablename;
							break;
						}
					}

					if ($item['metaArtIDs'] !== null) {
						$used = true;
					}

					if ($item['metaCatIDs'] !== null) {
						$used = true;
					}

					if ($item['metaMedIDs'] !== null) {
						$used = true;
					}

					$errors = array();
					if ($used === false) {
						$errors[] = \rex_i18n::rawMsg('akrys_usagecheck_images_msg_not_used');
					}

					if (!akrys\redaxo\addon\UsageCheck\Medium::exists($item)) {
						$errors[] = \rex_i18n::rawMsg('akrys_usagecheck_images_msg_not_found');
					}

					//Ob ein Medium lt. Medienpool in Nutzung ist, brauchen wir nur zu prüfen,
					//wenn wir glauben, dass die Datei ungenutzt ist.
					//Vielleicht wird sie ja dennoch verwendet ;-)
					//
					//Hier wird die Funktion verwendet, die auch beim Löschen von Medien aus dem Medienpool aufgerufen
					//wird.
					//
					//ACHTUNG:
					//XAMPP 5.6.14-4 mit MariaDB unter MacOS hat ein falsch kompiliertes PCRE-Mdoul an Bord, so dass
					//alle REGEXP-Abfragen abstürzen.
					//Der Fehler liegt also nicht hier, und auch nicht im Redaxo-Core
					if (!$used) {
						$used = rex_mediapool_mediaIsInUse($medium->getFileName());

						if ($used) {
							$errors[] = \rex_i18n::rawMsg('akrys_usagecheck_images_msg_in_use');
						}
					}

					if (count($errors) > 0) {
						$fragment = new rex_fragment(['msg' => $errors]);
						echo $fragment->parse('msg/error_box.php');
					} else {
						$fragment = new rex_fragment(['msg' => [\rex_i18n::rawMsg('akrys_usagecheck_images_msg_used')]]);
						echo $fragment->parse('msg/info_box.php');
					}
					?>

					<div  class="rex-message" style="border:0;outline:0;">
						<span>
							<ol>
								<?php
								$url = 'index.php?page=mediapool&subpage=detail&file_name='.$item['filename'];
								$linkText = \rex_i18n::rawMsg('akrys_usagecheck_images_linktext_edit');
								?>

								<li><a href="<?= $url ?>" target="_blank"><?= $linkText; ?></a><br /></li>

								<?php
								if ($item['slice_data'] !== null) {
									$type = akrys\redaxo\addon\UsageCheck\Modules\Pictures::TYPE;
									$url = "index.php?page=usage_check/details&type=".$type."&id=".$item['id'];
									?>

									<a href="<?= $url; ?>">
										zur Detail-Seite des Eintrags
									</a>

									<?php
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

								if ($item['metaArtIDs'] !== null) {
									$usages = explode("\n", $item['metaArtIDs']);
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

								if ($item['metaCatIDs'] !== null) {
									$usages = explode("\n", $item['metaCatIDs']);

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

								if ($item['metaMedIDs'] !== null) {
									$index = 'akrys_usagecheck_images_linktext_edit_in_metadata_med';
									$linkTextRaw = \rex_i18n::rawMsg($index);

									$usages = explode("\n", $item['metaMedIDs']);
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
								?>

							</ol>
						</span>
					</div>

					<div  class="rex-message" style="border:0;outline:0;">
						<span>

							<?php
							/* @var $medium rex_media */
							$medium = rex_media::get($item['filename']);
							/* @var $initCat rex_media_category */
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
