<?php
$api = akrys\redaxo\addon\UsageCheck\RedaxoCall::getAPI();
?>

<table class="<?= $api->getTableClass(); ?>">
	<thead>
		<tr>
			<th><?= $api->getI18N('akrys_usagecheck_images_table_heading_name'); ?></th>
			<th><?= $api->getI18N('akrys_usagecheck_images_table_heading_functions'); ?></th>
		</tr>
	</thead>
	<tbody>

		<?php
		foreach ($this->items['result'] as $item) {
			$continue = false;
			try {
				$medium = $this->pictures->getMedium($item);
			} catch (\Exception $e) {
				continue;
			}

			$initCat = null;
			?>

			<tr>
				<td>

					<?= $this->pictures->outputImagePreview($item); ?><br /><br />

					<strong><?= $item['title']; ?></strong><br />

					<?php
					echo $item['filename'].' ('.$this->pictures->getSizeOut($item).')';
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
						$errors[] = $api->getI18N('akrys_usagecheck_images_msg_not_used');
					}

					if (!$this->pictures->exists($item)) {
						$errors[] = $api->getI18N('akrys_usagecheck_images_msg_not_found');
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
						switch (akrys\redaxo\addon\UsageCheck\RedaxoCall::getRedaxoVersion()) {
							case akrys\redaxo\addon\UsageCheck\RedaxoCall::REDAXO_VERSION_5:
								$used = rex_mediapool_mediaIsInUse($medium->getFileName());
								break;
						}

						if ($used) {
							$errors[] = $api->getI18N('akrys_usagecheck_images_msg_in_use');
						}
					}

					if (count($errors) > 0) {
						$text = '';
						foreach ($errors as $error) {
							if (trim($error) !== '') {
								$text .= $api->getTaggedMsg($error);
							}
						}
						echo $api->getErrorMsg($text);
					} else {
						$text = 'akrys_usagecheck_images_msg_used';
						echo $api->getTaggedInfoMsg(akrys\redaxo\addon\UsageCheck\RedaxoCall::getAPI()->getI18N($text));
					}
					?>

					<div  class="rex-message" style="border:0;outline:0;">
						<span>
							<ol>
								<?php
								$url = 'index.php?page=mediapool&subpage=detail&file_name='.$item['filename'];
								$linkText = $api->getI18N('akrys_usagecheck_images_linktext_edit');
								?>

								<li><a href="<?= $url ?>" target="_blank"><?= $linkText; ?></a><br /></li>

								<?php
								if ($item['slice_data'] !== null) {
									$usages = explode("\n", $item['slice_data']);

									$index = 'akrys_usagecheck_images_linktext_edit_in_slice';
									$linkTextRaw = $api->getI18N($index);
									foreach ($usages as $usage) {
										$articleData = explode("\t", $usage);

										//s.id,"\\t",s.article_id,"\\t",s.clang,"\\t",s.ctype
										$sliceID = $articleData[0];
										$articleID = $articleData[1];
										$articleName = $articleData[2];
										$clang = $articleData[3];
										$ctype = $articleData[4];

										$hasPerm = $api->hasCategoryPerm($articleID);
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
									$linkTextRaw = $api->getI18N($index);
									foreach ($usages as $usage) {
										$articleData = explode("\t", $usage);

										$articleID = $articleData[0];
										$articleName = $articleData[1];
										$clang = $articleData[2];

										$hasPerm = $api->hasCategoryPerm($articleID);
										$href = $api->getArticleMetaUrl($articleID, $clang);
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
									$linkTextRaw = $api->getI18N($index);
									foreach ($usages as $usage) {
										$articleData = explode("\t", $usage);

										//http://51.redaxo.akrys-dev.local/redaxo/index.php?page=structure&category_id=5&article_id=0&clang=1&edit_id=11&function=edit_cat&catstart=0
										//s.id,"\\t",s.article_id,"\\t",s.clang,"\\t",s.ctype
										$articleID = $articleData[0];
										$articleName = $articleData[1];
										$clang = $articleData[2];
										$parentID = $articleData[3];

										$hasPerm = $api->hasCategoryPerm($articleID);

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
									$linkTextRaw = $api->getI18N($index);

									$usages = explode("\n", $item['metaMedIDs']);
									foreach ($usages as $usage) {
										$mediaData = explode("\t", $usage);

										//file_id,"\t",category_id,"\t",filename
										$fileID = $mediaData[0];
										$fileCatID = $mediaData[1];
										$filename = $mediaData[2];

										$hasPerm = $api->hasMediaCategoryPerm($fileCatID);
										$hasPerm = true;

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


								$index = 'akrys_usagecheck_images_linktext_edit_in_xformtable';
								$linkTextRaw = $api->getI18N($index);
								foreach ($this->items['fields'] as $table => $field) {
									if (!isset($item[$table])) {
										continue;
									}

									$ids = explode("\n", $item[$table]);
									foreach ($ids as $id) {
										$linkText = $linkTextRaw;
										$linkText = str_replace('$entryID$', $id, $linkText);
										$linkText = str_replace('$tableName$', $field[0]['table_out'], $linkText);

										$hasPerm = $api->hasTablePerm($table);

										if ($hasPerm) {
											$href = $api->getXFormEditUrl($table, $id);
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
							$initCat = null;

							switch (akrys\redaxo\addon\UsageCheck\RedaxoCall::getRedaxoVersion()) {
								case akrys\redaxo\addon\UsageCheck\RedaxoCall::REDAXO_VERSION_5:
									/* @var $medium rex_media */
									$medium = rex_media::get($item['filename']);
									/* @var $initCat rex_media_category */
									$initCat = $medium->getCategory();
									break;
							}

							if (isset($initCat)) {
								$title = $api->getI18N('akrys_usagecheck_images_category_header');
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
