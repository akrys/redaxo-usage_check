<?php
/**
 * Anzeige der nicht verwendeten Bilder.
 */
require_once __DIR__.'/../akrys/redaxo/addon/UsageCheck/Config.php';
require_once __DIR__.'/../akrys/redaxo/addon/UsageCheck/RedaxoCall.php';

use akrys\redaxo\addon\UsageCheck\Config;
require_once __DIR__.'/../akrys/redaxo/addon/UsageCheck/Modules/Pictures.php';

switch (\akrys\redaxo\addon\UsageCheck\RedaxoCall::getRedaxoVersion()) {
	case \akrys\redaxo\addon\UsageCheck\RedaxoCall::REDAXO_VERSION_4:
		$tableClass = 'rex-table';
		break;

	case \akrys\redaxo\addon\UsageCheck\RedaxoCall::REDAXO_VERSION_5:
		$tableClass = 'table table-striped';
		break;
}

$showAll = rex_get('showall', 'string', "");

echo \akrys\redaxo\addon\UsageCheck\RedaxoCall::rexTitle(Config::NAME_OUT.' / '.\akrys\redaxo\addon\UsageCheck\RedaxoCall::i18nMsg('akrys_usagecheck_images_subpagetitle').' <span style="font-size:10px;color:#c2c2c2">'.Config::VERSION.'</span>', Config::NAME_OUT);

$items = \akrys\redaxo\addon\UsageCheck\Modules\Pictures::getPictures($showAll);

if ($items === false) {
	echo \akrys\redaxo\addon\UsageCheck\RedaxoCall::errorMsg(\akrys\redaxo\addon\UsageCheck\RedaxoCall::i18nMsg('akrys_usagecheck_no_rights'), true);
	return;
}

$showAllLinktext = \akrys\redaxo\addon\UsageCheck\RedaxoCall::i18nMsg('akrys_usagecheck_images_link_show_unused');
$showAllParam = '';
if (!$showAll) {
	$showAllLinktext = \akrys\redaxo\addon\UsageCheck\RedaxoCall::i18nMsg('akrys_usagecheck_images_link_show_all');
	$showAllParam = '&showall=true';
}
?>

<p class="rex-tx1">

	<?php
	switch (\akrys\redaxo\addon\UsageCheck\RedaxoCall::getRedaxoVersion()) {
		case \akrys\redaxo\addon\UsageCheck\RedaxoCall::REDAXO_VERSION_4:
			?>

			<a href="index.php?page=<?php echo Config::NAME; ?>&subpage=<?php echo $subpage; ?><?php echo $showAllParam; ?>"><?php echo $showAllLinktext; ?></a>

			<?php
			break;
		case \akrys\redaxo\addon\UsageCheck\RedaxoCall::REDAXO_VERSION_5:
			?>

			<a href="index.php?page=<?php echo Config::NAME; ?>/<?php echo $subpage; ?><?php echo $showAllParam; ?>"><?php echo $showAllLinktext; ?></a>

			<?php
			break;
	}
	?>

</a>
<p class="rex-tx1"><?php echo \akrys\redaxo\addon\UsageCheck\RedaxoCall::i18nMsg('akrys_usagecheck_images_intro_text');
	?></p>


<table class="<?php echo $tableClass ?>">
	<thead>
		<tr>
			<th><?php echo \akrys\redaxo\addon\UsageCheck\RedaxoCall::i18nMsg('akrys_usagecheck_images_table_heading_name'); ?></th>
			<th><?php echo \akrys\redaxo\addon\UsageCheck\RedaxoCall::i18nMsg('akrys_usagecheck_images_table_heading_functions'); ?></th>
		</tr>
	</thead>
	<tbody>

		<?php
		foreach ($items['result'] as $item) {
			if (\akrys\redaxo\addon\UsageCheck\RedaxoCall::getRedaxoVersion() == \akrys\redaxo\addon\UsageCheck\RedaxoCall::REDAXO_VERSION_4) {
				if (!$REX['USER']->isAdmin() && !$REX['USER']->hasPerm('media['.$item['category_id'].']')) {
					continue;
				}
			} else {
				$user = \rex::getUser();
				if (!$user->isAdmin() && !$user->hasPerm('media['.$item['category_id'].']')) {
					continue;
				}
			}
			?>

			<tr>
				<td>

					<?php
					if (stristr($item['filetype'], 'image/')) {
						switch (\akrys\redaxo\addon\UsageCheck\RedaxoCall::getRedaxoVersion()) {
							case \akrys\redaxo\addon\UsageCheck\RedaxoCall::REDAXO_VERSION_4:
								?>

								<img alt="" src="../index.php?rex_img_type=rex_mediapool_detail&amp;rex_img_file=<?php echo $item['filename']; ?>" style="max-width:150px;max-height: 150px;" />
								<br /><br />

								<?php
								break;
							case \akrys\redaxo\addon\UsageCheck\RedaxoCall::REDAXO_VERSION_5:
								?>

								<img alt="" src="index.php?rex_media_type=content&rex_media_file=<?php echo $item['filename'] ?>" style="max-width:150px;max-height: 150px;" />
								<br /><br />

								<?php
								break;
						}
					}
					?>

					<strong><?php echo $item['title']; ?></strong><br />

					<?php
					echo $item['filename'].' ('.akrys\redaxo\addon\UsageCheck\Modules\Pictures::getSizeOut($item).')';
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

					$errors = array();
					if ($used === false) {
						$errors[] = \akrys\redaxo\addon\UsageCheck\RedaxoCall::i18nMsg('akrys_usagecheck_images_msg_not_used');
					}

					if (!\akrys\redaxo\addon\UsageCheck\Modules\Pictures::exists($item)) {
						$errors[] = \akrys\redaxo\addon\UsageCheck\RedaxoCall::i18nMsg('akrys_usagecheck_images_msg_not_found');
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
						echo \akrys\redaxo\addon\UsageCheck\RedaxoCall::errorMsg($text, false);
					} else {
						echo \akrys\redaxo\addon\UsageCheck\RedaxoCall::infoMsg(\akrys\redaxo\addon\UsageCheck\RedaxoCall::i18nMsg('akrys_usagecheck_images_msg_used'), true);
					}
					?>

					<div  class="rex-message" style="border:0;outline:0;">
						<span>
							<ol>
								<li><a href="index.php?page=mediapool&subpage=detail&file_name=<?php echo $item['filename']; ?>" target="_blank"><?php echo \akrys\redaxo\addon\UsageCheck\RedaxoCall::i18nMsg('akrys_usagecheck_images_linktext_edit'); ?></a><br /></li>

								<?php
								if ($item['slice_data'] !== null) {

									$usages = explode("\n", $item['slice_data']);
									$linktextRaw = \akrys\redaxo\addon\UsageCheck\RedaxoCall::i18nMsg('akrys_usagecheck_images_linktext_edit_in_slice');
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


								$linktextRaw = \akrys\redaxo\addon\UsageCheck\RedaxoCall::i18nMsg('akrys_usagecheck_images_linktext_edit_in_xformtable');
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

					<div  class="rex-message" style="border:0;outline:0;">
						<span>

							<?php
							/*
							 * aktuell nicht möglich, da nur gezählt wird, wie die Datei verwendet wrude
							 * sonst hätten wir jedes Bild hier x-mal vorliegen.
							  if ($item['slice_id'] !== null) {
							  ?>

							  <a href="index.php?page=content&article_id=<?php echo $item['article_id'] ?>&mode=edit&slice_id=<?php echo $item['slice_id'] ?>&clang=<?php echo $item['clang'] ?>&ctype=<?php echo $item['ctype'] ?>&function=edit#slice<?php echo $item['slice_id'] ?>"><?php echo \akrys\redaxo\addon\UsageCheck\RedaxoCall::i18nMsg('akrys_usagecheck_images_linktext_edit_in_slice'); ?></a>

							  <?php
							  }
							 */
							$initCat = null;

							switch (\akrys\redaxo\addon\UsageCheck\RedaxoCall::getRedaxoVersion()) {
								case \akrys\redaxo\addon\UsageCheck\RedaxoCall::REDAXO_VERSION_4:
									/* @var $medium OOMedia */
									$medium = OOMedia::getMediaByFileName($item['filename']);
									/* @var $initCat OOMediaCategory */
									$initCat = $medium->getCategory();
									break;
								case \akrys\redaxo\addon\UsageCheck\RedaxoCall::REDAXO_VERSION_5:
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
									<strong><?php echo \akrys\redaxo\addon\UsageCheck\RedaxoCall::i18nMsg('akrys_usagecheck_images_category_header'); ?></strong>
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
