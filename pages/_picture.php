<?php
/**
 * Anzeige der nicht verwendeten Bilder.
 */
require_once __DIR__.'/../akrys/redaxo/addon/UsageCheck/Config.php';
require_once __DIR__.'/../akrys/redaxo/addon/UsageCheck/RedaxoCall.php';

use \akrys\redaxo\addon\UsageCheck\Config;
use \akrys\redaxo\addon\UsageCheck\RedaxoCall;
require_once __DIR__.'/../akrys/redaxo/addon/UsageCheck/Modules/Pictures.php';
$pictures = \akrys\redaxo\addon\UsageCheck\Modules\Pictures::create();

echo RedaxoCall::rexTitle(Config::NAME_OUT.' / '.RedaxoCall::i18nMsg('akrys_usagecheck_images_subpagetitle').' <span style="font-size:10px;color:#c2c2c2">'.Config::VERSION.'</span>');

$showAll = rex_get('showall', 'string', "");
$items = $pictures->getPictures($showAll);

if ($items === false) {
	echo RedaxoCall::errorMsg(RedaxoCall::i18nMsg('akrys_usagecheck_no_rights'), true);
	return;
}

$showAllLinktext = RedaxoCall::i18nMsg('akrys_usagecheck_images_link_show_unused');
$showAllParam = '';
if (!$showAll) {
	$showAllLinktext = RedaxoCall::i18nMsg('akrys_usagecheck_images_link_show_all');
	$showAllParam = '&showall=true';
}

$pictures->outputMenu($subpage, $showAllParam, $showAllLinktext);
?>

<table class="<?php echo RedaxoCall::getTableClass(); ?>">
	<thead>
		<tr>
			<th><?php echo RedaxoCall::i18nMsg('akrys_usagecheck_images_table_heading_name'); ?></th>
			<th><?php echo RedaxoCall::i18nMsg('akrys_usagecheck_images_table_heading_functions'); ?></th>
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

					$errors = array();
					if ($used === false) {
						$errors[] = RedaxoCall::i18nMsg('akrys_usagecheck_images_msg_not_used');
					}

					if (!$pictures->exists($item)) {
						$errors[] = RedaxoCall::i18nMsg('akrys_usagecheck_images_msg_not_found');
					}

					if (!$used) {
						//Ob ein Medium lt. Medienpool in Nutzung ist, brauchen wir nur zu prüfen,
						//wenn wir glauben, dass die Datei ungenutzt ist.
						//Vielleicht wird sie ja dennoch verwendet ;-)
						//
						//Hier wird die Funktion verwendet, die auch beim Löschen von Medien aus dem Medienpool aufgerufen wird.
						//
						//ACHTUNG:
						//XAMPP 5.6.14-4 mit MariaDB unter MacOS hat ein falsch kompiliertes PCRE-Mdoul an Bord, so dass alle REGEXP-Abfragen abstürzen.
						//Der Fehler liegt also nicht hier, und auch nicht im Redaxo-Core
						switch (akrys\redaxo\addon\UsageCheck\RedaxoCall::getRedaxoVersion()) {
							case akrys\redaxo\addon\UsageCheck\RedaxoCall::REDAXO_VERSION_4:
								$used = $medium->isInUse();
								break;
							case akrys\redaxo\addon\UsageCheck\RedaxoCall::REDAXO_VERSION_5:
								$used = rex_mediapool_mediaIsInUse($medium->getFileName());
								break;
						}

						if ($used) {
							$errors[] = RedaxoCall::i18nMsg('akrys_usagecheck_images_msg_in_use');
						}
					}

					if (count($errors) > 0) {
						$text = '';
						foreach ($errors as $error) {
							if (trim($error) !== '') {
								$text.=<<<ERROR
<p>
	<span>$error</span>
</p>
ERROR;
							}
						}
						echo RedaxoCall::errorMsg($text, false);
					} else {
						echo RedaxoCall::infoMsg(RedaxoCall::i18nMsg('akrys_usagecheck_images_msg_used'), true);
					}
					?>

					<div  class="rex-message" style="border:0;outline:0;">
						<span>
							<ol>
								<li><a href="index.php?page=mediapool&subpage=detail&file_name=<?php echo $item['filename']; ?>" target="_blank"><?php echo RedaxoCall::i18nMsg('akrys_usagecheck_images_linktext_edit'); ?></a><br /></li>

								<?php
								if ($item['slice_data'] !== null) {

									$usages = explode("\n", $item['slice_data']);
									$linktextRaw = RedaxoCall::i18nMsg('akrys_usagecheck_images_linktext_edit_in_slice');
									foreach ($usages as $usage) {
										$articleData = explode("\t", $usage);

										//s.id,"\\t",s.article_id,"\\t",s.clang,"\\t",s.ctype
										$sliceID = $articleData[0];
										$articleID = $articleData[1];
										$articleName = $articleData[2];
										$clang = $articleData[3];
										$ctype = $articleData[4];

										$hasPerm = RedaxoCall::hasCategoryPerm($articleID);
										if ($hasPerm) {

											$linktext = $linktextRaw;
											$linktext = str_replace('$sliceID$', $sliceID, $linktext);
											$linktext = str_replace('$articleName$', $articleName, $linktext);
											$href = 'index.php?page=content&article_id='.$articleID.'&mode=edit&slice_id='.$sliceID.'&clang='.$clang.'&ctype='.$ctype.'&function=edit#slice'.$sliceID;
											?>

											<li><a href="<?php echo $href; ?>"><?php echo $linktext; ?></a></li>

											<?php
										}
										unset($href, $linktext, $ctype, $clang, $articleID, $articleName, $sliceID);
									}
								}

								if ($item['metaArtIDs'] !== null) {

									$usages = explode("\n", $item['metaArtIDs']);
									$linktextRaw = RedaxoCall::i18nMsg('akrys_usagecheck_images_linktext_edit_in_metadata_art');
									foreach ($usages as $usage) {
										$articleData = explode("\t", $usage);

										$articleID = $articleData[0];
										$articleName = $articleData[1];
										$clang = $articleData[2];

										$hasPerm = RedaxoCall::hasCategoryPerm($articleID);
										$href = RedaxoCall::getArticleMetaUrl($articleID, $clang);
										if ($hasPerm) {
											$linktext = $linktextRaw;
											$linktext = str_replace('$articleID$', $articleID, $linktext);
											$linktext = str_replace('$articleName$', $articleName, $linktext);
											?>

											<li><a href="<?php echo $href; ?>"><?php echo $linktext; ?></a></li>

											<?php
										}
										unset($href, $linktext, $ctype, $clang, $articleID, $articleName, $sliceID);
									}
								}

								if ($item['metaCatIDs'] !== null) {

									$usages = explode("\n", $item['metaCatIDs']);
									$linktextRaw = RedaxoCall::i18nMsg('akrys_usagecheck_images_linktext_edit_in_metadata_cat');
									foreach ($usages as $usage) {
										$articleData = explode("\t", $usage);


										//http://51.redaxo.akrys-dev.local/redaxo/index.php?page=structure&category_id=5&article_id=0&clang=1&edit_id=11&function=edit_cat&catstart=0
										//s.id,"\\t",s.article_id,"\\t",s.clang,"\\t",s.ctype
										$articleID = $articleData[0];
										$articleName = $articleData[1];
										$clang = $articleData[2];
										$parentID = $articleData[3];

										$hasPerm = RedaxoCall::hasCategoryPerm($articleID);

										if ($hasPerm) {
											$linktext = $linktextRaw;
											$linktext = str_replace('$articleID$', $articleID, $linktext);
											$linktext = str_replace('$articleName$', $articleName, $linktext);
											$href = 'index.php?page=structure&category_id='.$parentID.'&article_id=0&clang='.$clang.'&edit_id='.$articleID.'&function=edit_cat&catstart=0';
											?>

											<li><a href="<?php echo $href; ?>"><?php echo $linktext; ?></a></li>

											<?php
										}
										unset($href, $linktext, $ctype, $clang, $articleID, $articleName, $sliceID);
									}
								}



								$linktextRaw = RedaxoCall::i18nMsg('akrys_usagecheck_images_linktext_edit_in_xformtable');
								foreach ($items['fields'] as $table => $field) {

									if (!isset($item[$table])) {
										continue;
									}

									$ids = explode("\n", $item[$table]);
									foreach ($ids as $id) {

										$linktext = $linktextRaw;
										$linktext = str_replace('$entryID$', $id, $linktext);
										$linktext = str_replace('$tableName$', $field[0]['table_out'], $linktext);

										$hasPerm = RedaxoCall::hasTablePerm($table);

										if ($hasPerm) {
											$href = RedaxoCall::getXFormEditUrl($table, $id);
											if ($href == '') {
												continue;
											}
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

					<div  class="rex-message" style="border:0;outline:0;">
						<span>

							<?php
							$initCat = null;

							switch (RedaxoCall::getRedaxoVersion()) {
								case RedaxoCall::REDAXO_VERSION_4:
									/* @var $medium OOMedia */
									$medium = OOMedia::getMediaByFileName($item['filename']);
									/* @var $initCat OOMediaCategory */
									$initCat = $medium->getCategory();
									break;
								case RedaxoCall::REDAXO_VERSION_5:
									/* @var $medium rex_media */
									$medium = rex_media::get($item['filename']);
									/* @var $initCat rex_media_category */
									$initCat = $medium->getCategory();
									break;
							}

							if (isset($initCat)) {
								?>


								<small style="font-size:0.875em;">
									<br />
									<strong><?php echo RedaxoCall::i18nMsg('akrys_usagecheck_images_category_header'); ?></strong>
									<br />

									<?php
									$i = 0;
									foreach ($initCat->getParentTree() as $category) {
										?>
										<a href="index.php?page=mediapool&rex_file_category=<?php echo $category->getId(); ?>"><?php echo $category->getName() ?></a>

										/
										<?php
									}
									?>
									<a href="index.php?page=mediapool&rex_file_category=<?php echo $initCat->getId(); ?>"><?php echo $initCat->getName() ?></a>

								</small>
								<?php
							}
							?>

						</span>
					</div>
				</td>

				<?php
			}
			?>

	</tbody>
</table>