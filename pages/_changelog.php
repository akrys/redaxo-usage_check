<?php
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

require_once __DIR__.'/../akrys/redaxo/addon/UserCheck/Config.php';

/* @var $I18N \i18n */

use akrys\redaxo\addon\UserCheck\Config;
rex_title(Config::NAME_OUT.' / '.$I18N->msg('akrys_usagecheck_changelog_subpagetitle').' <span style="font-size:10px;color:#c2c2c2">'.Config::VERSION.'</span>', $REX['ADDON']['pages'][Config::NAME]);

if (!function_exists('\\glob')) {
	print 'this page requires the glob function';
	die();
}

if (stristr($REX['LANG'], 'de_')) {
	$dir = glob(__DIR__.'/release_notes/de/*_*.php');
} else {
	$dir = glob(__DIR__.'/release_notes/en/*_*.php');
}
rsort($dir);
?>


<table class = "rex-table">
	<thead>
		<tr>
			<th><?php echo $I18N->msg('akrys_usagecheck_changelog_header_version'); ?></th>
			<th><?php echo $I18N->msg('akrys_usagecheck_changelog_header_date'); ?></th>
			<th><?php echo $I18N->msg('akrys_usagecheck_changelog_header_changes'); ?></th>
		</tr>
	</thead>
	<tbody>
		<?php
		foreach ($dir as $file) {

			$data = (explode('_', str_replace('.php', '', basename($file))));
			?>

			<tr>
				<td><?php echo $data[0]; ?></td>
				<td><?php echo $data[1]; ?></td>
				<td>
					<?php require $file; ?>
				</td>
			</tr>

			<?php
		}
		?>

	</tbody>
</table>