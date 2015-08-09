<?php
/**
 * Anzeige der nicht verwendeten Bilder.
 */
require_once __DIR__.'/../akrys/redaxo/addon/UserCheck/Config.php';

use akrys\redaxo\addon\UserCheck\Config;
require_once __DIR__.'/../akrys/redaxo/addon/UserCheck/Pictures.php';

$showAll = rex_get('showall', 'string', "");

rex_title(Config::NAME_OUT.' / '.$I18N->msg('akrys_usagecheck_images_subpagetitle').' <span style="font-size:10px;color:#c2c2c2">'.Config::VERSION.'</span>', $REX['ADDON']['pages'][Config::NAME]);

$items = Pictures::getPictures($showAll);


if ($showAll) {
	?>

	<a href="index.php?page=<?php echo Config::NAME; ?>&subpage=<?php echo $subpage; ?>"><?php echo $I18N->msg('akrys_usagecheck_images_link_show_unused'); ?></a>

	<?php
} else {
	?>

	<a href="index.php?page=<?php echo Config::NAME; ?>&subpage=<?php echo $subpage; ?>&showall=true"><?php echo $I18N->msg('akrys_usagecheck_images_link_show_all'); ?></a>

	<?php
}
?>
<br /><br />
<?php echo $I18N->msg('akrys_usagecheck_images_intro_text'); ?>
<br /><br />


<table class = "rex-table">
	<thead>
		<tr>
			<th><?php echo $I18N->msg('akrys_usagecheck_images_table_heading_name'); ?></th>
			<th><?php echo $I18N->msg('akrys_usagecheck_images_table_heading_functions'); ?></th>
		</tr>
	</thead>
	<tbody>

		<?php
		foreach ($items as $item) {
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

					echo $item['filename'];
					?>

					<br />
					<small style="font-size:0.875em;font-weight:bold;"><?php echo $item['filetype']; ?></small>
				</td>
				<td>
					<?php
					if ($item['slice_id'] === null) {
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

						<?php
					}
					?>

					<div  class="rex-message" style="border:0;outline:0;">
						<span>
							<a href="http://redaxo.arbeit.local/redaxo/index.php?page=mediapool&subpage=detail&file_name=<?php echo $item['filename']; ?>"><?php echo $I18N->msg('akrys_usagecheck_images_linktext_edit'); ?></a><br />

							<?php
							if ($item['slice_id'] !== null) {
								?>

								<a href="index.php?page=content&article_id=<?php echo $item['article_id'] ?>&mode=edit&slice_id=<?php echo $item['slice_id'] ?>&clang=<?php echo $item['clang'] ?>&ctype=<?php echo $item['ctype'] ?>&function=edit#slice<?php echo $item['slice_id'] ?>"><?php echo $I18N->msg('akrys_usagecheck_images_linktext_edit_in_slice'); ?></a>

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
