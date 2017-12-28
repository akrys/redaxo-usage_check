<?php

/**
 * Anzeige der nicht verwendeten Bilder.
 */
use \akrys\redaxo\addon\UsageCheck\Config;
use \akrys\redaxo\addon\UsageCheck\RedaxoCall;

$pictures = \akrys\redaxo\addon\UsageCheck\Modules\Pictures::create();

$title = Config::NAME_OUT.' / '.RedaxoCall::getAPI()->getI18N('akrys_usagecheck_images_subpagetitle').
	' <span style="font-size:10px;color:#c2c2c2">'.Config::VERSION.'</span>';
echo RedaxoCall::getAPI()->getRexTitle($title);

$showAll = false;
switch (rex_get('showall', 'string', "")) {
	case 'true':
		$pictures->showAll(true);
		$showAll = true;
		break;
	case 'false':
	default:
		//
		break;
}
$items = $pictures->getPictures();

if ($items === false) {
	echo RedaxoCall::getAPI()->getTaggedErrorMsg(RedaxoCall::getAPI()->getI18N('akrys_usagecheck_no_rights'));
	return;
}

$showAllLinktext = RedaxoCall::getAPI()->getI18N('akrys_usagecheck_images_link_show_unused');
$showAllParam = '';
if (!$showAll) {
	$showAllLinktext = RedaxoCall::getAPI()->getI18N('akrys_usagecheck_images_link_show_all');
	$showAllParam = '&showall=true';
}

$pictures->outputMenu($subpage, $showAllParam, $showAllLinktext);
?>

<table class="<?php echo RedaxoCall::getAPI()->getTableClass(); ?>">
	<thead>
		<tr>
			<th><?php echo RedaxoCall::getAPI()->getI18N('akrys_usagecheck_images_table_heading_name'); ?></th>
			<th><?php echo RedaxoCall::getAPI()->getI18N('akrys_usagecheck_images_table_heading_functions'); ?></th>
		</tr>
	</thead>
	<tbody>

		<?php
		foreach ($items['result'] as $item) {
			$continue = false;
			try {
				$medium = $pictures->getMedium($item);
			} catch (\Exception $e) {
				continue;
			}

			$initCat = null;
			?>

			<tr>
				<td>

					<?php
					$pictures->outputImagePreview($item);
					?>

					<strong><?php echo $item['title']; ?></strong><br />

					<?php
					echo $item['filename'].' ('.$pictures->getSizeOut($item).')';
					?>

					<br />
					<small style="font-size:0.875em;font-weight:bold;"><?php echo $item['filetype']; ?></small>
				</td>
				<td>

					<?php
					$used = false;
					if ($item['slice_data'] !== null) {
						$used = true;
					}

					$table = '';
					foreach ($items['fields'] as $tablename => $field) {
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
						$errors[] = RedaxoCall::getAPI()->getI18N('akrys_usagecheck_images_msg_not_used');
					}

					if (!$pictures->exists($item)) {
						$errors[] = RedaxoCall::getAPI()->getI18N('akrys_usagecheck_images_msg_not_found');
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
							$errors[] = RedaxoCall::getAPI()->getI18N('akrys_usagecheck_images_msg_in_use');
						}
					}

					if (count($errors) > 0) {
						$text = '';
						foreach ($errors as $error) {
							if (trim($error) !== '') {
								$text.=RedaxoCall::getAPI()->getTaggedMsg($error);
							}
						}
						echo RedaxoCall::getAPI()->getErrorMsg($text);
					} else {
						$text = 'akrys_usagecheck_images_msg_used';
						echo RedaxoCall::getAPI()->getTaggedInfoMsg(RedaxoCall::getAPI()->getI18N($text));
					}
					?>

					<div  class="rex-message" style="border:0;outline:0;">
						<span>
							<ol>
								<?php
								$url = 'index.php?page=mediapool&subpage=detail&file_name='.$item['filename'];
								$linkText = RedaxoCall::getAPI()->getI18N('akrys_usagecheck_images_linktext_edit');
								?>

								<li><a href="<?php echo $url ?>" target="_blank"><?php echo $linkText; ?></a><br /></li>

								<?php
								if ($item['slice_data'] !== null) {
									$usages = explode("\n", $item['slice_data']);

									$index = 'akrys_usagecheck_images_linktext_edit_in_slice';
									$linkTextRaw = RedaxoCall::getAPI()->getI18N($index);
									foreach ($usages as $usage) {
										$articleData = explode("\t", $usage);

										//s.id,"\\t",s.article_id,"\\t",s.clang,"\\t",s.ctype
										$sliceID = $articleData[0];
										$articleID = $articleData[1];
										$articleName = $articleData[2];
										$clang = $articleData[3];
										$ctype = $articleData[4];

										$hasPerm = RedaxoCall::getAPI()->hasCategoryPerm($articleID);
										if ($hasPerm) {
											$linkText = $linkTextRaw;
											$linkText = str_replace('$sliceID$', $sliceID, $linkText);
											$linkText = str_replace('$articleName$', $articleName, $linkText);

											$href = 'index.php?page=content&article_id='.$articleID.
												'&mode=edit&slice_id='.$sliceID.'&clang='.$clang.
												'&ctype='.$ctype.'&function=edit#slice'.$sliceID;
											?>

											<li><a href="<?php echo $href; ?>"><?php echo $linkText; ?></a></li>

											<?php
										}
										unset($href, $linkText, $ctype, $clang, $articleID, $articleName, $sliceID);
									}
								}

								if ($item['metaArtIDs'] !== null) {
									$usages = explode("\n", $item['metaArtIDs']);
									$index = 'akrys_usagecheck_images_linktext_edit_in_metadata_art';
									$linkTextRaw = RedaxoCall::getAPI()->getI18N($index);
									foreach ($usages as $usage) {
										$articleData = explode("\t", $usage);

										$articleID = $articleData[0];
										$articleName = $articleData[1];
										$clang = $articleData[2];

										$hasPerm = RedaxoCall::getAPI()->hasCategoryPerm($articleID);
										$href = RedaxoCall::getAPI()->getArticleMetaUrl($articleID, $clang);
										if ($hasPerm) {
											$linkText = $linkTextRaw;
											$linkText = str_replace('$articleID$', $articleID, $linkText);
											$linkText = str_replace('$articleName$', $articleName, $linkText);
											?>

											<li><a href="<?php echo $href; ?>"><?php echo $linkText; ?></a></li>

											<?php
										}
										unset($href, $linkText, $ctype, $clang, $articleID, $articleName, $sliceID);
									}
								}

								if ($item['metaCatIDs'] !== null) {
									$usages = explode("\n", $item['metaCatIDs']);

									$index = 'akrys_usagecheck_images_linktext_edit_in_metadata_cat';
									$linkTextRaw = RedaxoCall::getAPI()->getI18N($index);
									foreach ($usages as $usage) {
										$articleData = explode("\t", $usage);

										//http://51.redaxo.akrys-dev.local/redaxo/index.php?page=structure&category_id=5&article_id=0&clang=1&edit_id=11&function=edit_cat&catstart=0
										//s.id,"\\t",s.article_id,"\\t",s.clang,"\\t",s.ctype
										$articleID = $articleData[0];
										$articleName = $articleData[1];
										$clang = $articleData[2];
										$parentID = $articleData[3];

										$hasPerm = RedaxoCall::getAPI()->hasCategoryPerm($articleID);

										if ($hasPerm) {
											$linkText = $linkTextRaw;
											$linkText = str_replace('$articleID$', $articleID, $linkText);
											$linkText = str_replace('$articleName$', $articleName, $linkText);
											$href = 'index.php?page=structure&category_id='.$parentID.
												'&article_id=0&clang='.$clang.'&edit_id='.$articleID.
												'&function=edit_cat&catstart=0';
											?>

											<li><a href="<?php echo $href; ?>"><?php echo $linkText; ?></a></li>

											<?php
										}
										unset($href, $linkText, $ctype, $clang, $articleID, $articleName, $sliceID);
									}
								}

								if ($item['metaMedIDs'] !== null) {
									$index = 'akrys_usagecheck_images_linktext_edit_in_metadata_med';
									$linkTextRaw = RedaxoCall::getAPI()->getI18N($index);

									$usages = explode("\n", $item['metaMedIDs']);
									foreach ($usages as $usage) {
										$mediaData = explode("\t", $usage);

										//file_id,"\t",category_id,"\t",filename
										$fileID = $mediaData[0];
										$fileCatID = $mediaData[1];
										$filename = $mediaData[2];

										$hasPerm = RedaxoCall::getAPI()->hasMediaCategoryPerm($fileCatID);
										$hasPerm = true;

										if ($hasPerm) {
											$linkText = $linkTextRaw;
											$linkText = str_replace('$filename$', $filename, $linkText);
											$href = "index.php?page=mediapool&subpage=detail&file_name=".$filename;
											?>

											<li><a href="<?php echo $href; ?>"><?php echo $linkText; ?></a></li>

											<?php
										}
										unset($href, $linkText, $ctype, $clang, $articleID, $articleName, $sliceID);
									}
								}


								$index = 'akrys_usagecheck_images_linktext_edit_in_xformtable';
								$linkTextRaw = RedaxoCall::getAPI()->getI18N($index);
								foreach ($items['fields'] as $table => $field) {
									if (!isset($item[$table])) {
										continue;
									}

									$ids = explode("\n", $item[$table]);
									foreach ($ids as $id) {
										$linkText = $linkTextRaw;
										$linkText = str_replace('$entryID$', $id, $linkText);
										$linkText = str_replace('$tableName$', $field[0]['table_out'], $linkText);

										$hasPerm = RedaxoCall::getAPI()->hasTablePerm($table);

										if ($hasPerm) {
											$href = RedaxoCall::getAPI()->getXFormEditUrl($table, $id);
											if ($href == '') {
												continue;
											}
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

					<div  class="rex-message" style="border:0;outline:0;">
						<span>

							<?php
							$initCat = null;

							switch (RedaxoCall::getRedaxoVersion()) {
								case RedaxoCall::REDAXO_VERSION_5:
									/* @var $medium rex_media */
									$medium = rex_media::get($item['filename']);
									/* @var $initCat rex_media_category */
									$initCat = $medium->getCategory();
									break;
							}

							if (isset($initCat)) {
								$title = RedaxoCall::getAPI()->getI18N('akrys_usagecheck_images_category_header');
								?>

								<small style="font-size:0.875em;">
									<br />
									<strong><?php echo $title ?></strong>
									<br />

									<?php
									$i = 0;
									foreach ($initCat->getParentTree() as $category) {
										$url = 'index.php?page=mediapool&rex_file_category='.$category->getId();
										$linkText = $category->getName();
										?>
										<a href="<?php echo $url ?>"><?php echo $linkText ?></a>

										/
										<?php
									}

									$url = 'index.php?page=mediapool&rex_file_category='.$initCat->getId();
									$linkText = $initCat->getName();
									?>
									<a href="<?php echo $url ?>"><?php echo $linkText ?></a>

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
