<?php
/**
 * Anzeige der nicht verwendeten Bilder.
 */
require_once __DIR__.'/../akrys/redaxo/addon/UserCheck/Config.php';

/* @var $I18N \i18n */

use akrys\redaxo\addon\UserCheck\Config;
require_once __DIR__.'/../akrys/redaxo/addon/UserCheck/Pictures.php';

$showAll = rex_get('showall', 'string', "");

rex_title(Config::NAME_OUT.' / '.$I18N->msg('akrys_usagecheck_images_subpagetitle').' <span style="font-size:10px;color:#c2c2c2">'.Config::VERSION.'</span>', $REX['ADDON']['pages'][Config::NAME]);

$items = \akrys\redaxo\addon\UserCheck\Pictures::getPictures($showAll);



$showAllLinktext = $I18N->msg('akrys_usagecheck_images_link_show_unused');
$showAllParam = '';
if (!$showAll) {
	$showAllLinktext = $I18N->msg('akrys_usagecheck_images_link_show_all');
	$showAllParam = '&showall=true';
}
?>
<p class="rex-tx1"><a href="index.php?page=<?php echo Config::NAME; ?>&subpage=<?php echo $subpage; ?><?php echo $showAllParam; ?>"><?php echo $showAllLinktext; ?></a>
</a>
<p class="rex-tx1"><?php echo $I18N->msg('akrys_usagecheck_images_intro_text'); ?></p>


<table class = "rex-table">
	<thead>
		<tr>
			<th><?php echo $I18N->msg('akrys_usagecheck_images_table_heading_name'); ?></th>
			<th><?php echo $I18N->msg('akrys_usagecheck_images_table_heading_functions'); ?></th>
		</tr>
	</thead>
	<tbody>

		<?php
		foreach ($items['result'] as $item) {
			?>
			<tr>
				<td>
					<?php
					if (stristr($item['filetype'], 'image/')) {
						?>

						<img alt="" src="../index.php?rex_img_type=rex_mediapool_detail&amp;rex_img_file=<?php echo $item['filename']; ?>" />
						<br /><br />

						<?php
					}
					?>

					<strong><?php echo $item['title']; ?></strong><br />

					<?php
					echo $item['filename'].' ('.akrys\redaxo\addon\UserCheck\Pictures::getSizeOut($item).')';
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

					if ($used === false) {
						?>

						<div class="rex-message">
							<div class="rex-warning">
								<p>
									<span><?php echo $I18N->msg('akrys_usagecheck_images_msg_not_used'); ?></span>
								</p>
							</div>
						</div>

						<?php
					} else {
						?>

						<div class="rex-message">
							<div class="rex-info">
								<p>
									<span><?php echo $I18N->msg('akrys_usagecheck_images_msg_used'); ?></span>
								</p>
							</div>
						</div>

						<div  class="rex-message" style="border:0;outline:0;">
							<span>

								<ol>
									<li><a href="http://redaxo.arbeit.local/redaxo/index.php?page=mediapool&subpage=detail&file_name=<?php echo $item['filename']; ?>" target="_blank"><?php echo $I18N->msg('akrys_usagecheck_images_linktext_edit'); ?></a><br /></li>

									<?php
									if ($item['slice_data'] !== null) {

										$usages = explode("\n", $item['slice_data']);
										$linktextRaw = $I18N->msg('akrys_usagecheck_images_linktext_edit_in_slice');
										foreach ($usages as $usage) {
											$articleData = explode("\t", $usage);

											//s.id,"\\t",s.article_id,"\\t",s.clang,"\\t",s.ctype
											$sliceID = $articleData[0];
											$articleID = $articleData[1];
											$articleName = $articleData[2];
											$clang = $articleData[3];
											$ctype = $articleData[4];


											$linktext = $linktextRaw;
											$linktext = str_replace('$sliceID$', $sliceID, $linktext);
											$linktext = str_replace('$articleName$', $articleName, $linktext);
											$href = 'index.php?page=content&article_id='.$articleID.'&mode=edit&slice_id='.$sliceID.'&clang='.$clang.'&ctype='.$ctype.'&function=edit#slice'.$sliceID;
											?>

											<li><a href="<?php echo $href; ?>"><?php echo $linktext; ?></a></li>

											<?php
											unset($href, $linktext, $ctype, $clang, $articleID, $articleName, $sliceID);
										}
									}


									$linktextRaw = $I18N->msg('akrys_usagecheck_images_linktext_edit_in_xformtable');
									foreach ($items['fields'] as $table => $field) {

										if (!isset($item[$table])) {
											continue;
										}

										$ids = explode("\n", $item[$table]);
										foreach ($ids as $id) {

											$linktext = $linktextRaw;
											$linktext = str_replace('$entryID$', $id, $linktext);
											$linktext = str_replace('$tableName$', $field[0]['table_out'], $linktext);


											$href = 'index.php?page=xform&subpage=manager&tripage=data_edit&table_name='.$table.'&rex_xform_search=0&data_id='.$id.'&func=edit&start=';
											?>

											<li><a href="<?php echo $href; ?>"><?php echo $linktext; ?></a></li>

											<?php
										}
									}
									?>

								</ol>

							</span>
						</div>

						<?php
					}
					?>

					<div  class="rex-message" style="border:0;outline:0;">
						<span>

							<?php
							/*
							 * aktuell nicht möglich, da nur gezählt wird, wie die Datei verwendet wrude
							 * sonst hätten wir jedes Bild hier x-mal vorliegen.
							  if ($item['slice_id'] !== null) {
							  ?>

							  <a href="index.php?page=content&article_id=<?php echo $item['article_id'] ?>&mode=edit&slice_id=<?php echo $item['slice_id'] ?>&clang=<?php echo $item['clang'] ?>&ctype=<?php echo $item['ctype'] ?>&function=edit#slice<?php echo $item['slice_id'] ?>"><?php echo $I18N->msg('akrys_usagecheck_images_linktext_edit_in_slice'); ?></a>

							  <?php
							  }
							 */

							/* @var $medium OOMedia */
							$medium = OOMedia::getMediaByFileName($item['filename']);
							/* @var $cat OOMediaCategory */
							$initCat = $medium->getCategory();

							if (isset($initCat)) {
								?>


								<small style="font-size:0.875em;">
									<br />
									<strong><?php echo $I18N->msg('akrys_usagecheck_images_category_header'); ?></strong>
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
