<?php
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

require_once __DIR__.'/../akrys/redaxo/addon/UsageCheck/Config.php';
require_once __DIR__.'/../akrys/redaxo/addon/UsageCheck/RedaxoCall.php';

use \akrys\redaxo\addon\UsageCheck\Config;
use \akrys\redaxo\addon\UsageCheck\RedaxoCall;

echo RedaxoCall::rexTitle(Config::NAME_OUT.' / '.RedaxoCall::i18nMsg('akrys_usagecheck_changelog_subpagetitle').' <span style="font-size:10px;color:#c2c2c2">'.Config::VERSION.'</span>', Config::NAME_OUT);

if (!function_exists('\\glob')) {
	print 'this page requires the glob function';
	die();
}

if (stristr(RedaxoCall::getLang(), 'de_')) {
	$dir = glob(__DIR__.'/release_notes/de/*_*.php');
} else {
	$dir = glob(__DIR__.'/release_notes/en/*_*.php');
}
rsort($dir);
?>


<table class="<?php echo RedaxoCall::getTableClass(); ?>">
	<thead>
		<tr>
			<th><?php echo RedaxoCall::i18nMsg('akrys_usagecheck_changelog_header_version'); ?></th>
			<th><?php echo RedaxoCall::i18nMsg('akrys_usagecheck_changelog_header_date'); ?></th>
			<th><?php echo RedaxoCall::i18nMsg('akrys_usagecheck_changelog_header_changes'); ?></th>
		</tr>
	</thead>
	<tbody>

		<?php
		foreach ($dir as $file) {

			$data = (explode('_', str_replace('.php', '', basename($file))));
			?>

			<tr>
				<td><?php echo $data[1]; ?></td>
				<td><?php echo $data[0]; ?></td>
				<td>
					 <?php require $file; ?>
				</td>
			</tr>

			<?php
		}
		?>

	</tbody>
</table>