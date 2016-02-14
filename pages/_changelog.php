<?php
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

require_once __DIR__.'/../akrys/redaxo/addon/UsageCheck/Config.php';
require_once __DIR__.'/../akrys/redaxo/addon/UsageCheck/RedaxoCall.php';

switch (\akrys\redaxo\addon\UsageCheck\RedaxoCall::getRedaxoVersion()) {
	case \akrys\redaxo\addon\UsageCheck\RedaxoCall::REDAXO_VERSION_4:
		$language = $REX['LANG'];
		$tableClass = 'rex-table';
		break;
	case\akrys\redaxo\addon\UsageCheck\RedaxoCall::REDAXO_VERSION_5:
		$language = rex::getProperty('lang');
		$tableClass = 'table table-striped';
		break;
}

use akrys\redaxo\addon\UsageCheck\Config;
echo \akrys\redaxo\addon\UsageCheck\RedaxoCall::rexTitle(Config::NAME_OUT.' / '.\akrys\redaxo\addon\UsageCheck\RedaxoCall::i18nMsg('akrys_usagecheck_changelog_subpagetitle').' <span style="font-size:10px;color:#c2c2c2">'.Config::VERSION.'</span>', Config::NAME_OUT);

if (!function_exists('\\glob')) {
	print 'this page requires the glob function';
	die();
}

if (stristr($language, 'de_')) {
	$dir = glob(__DIR__.'/release_notes/de/*_*.php');
} else {
	$dir = glob(__DIR__.'/release_notes/en/*_*.php');
}
rsort($dir);
?>


<table class="<?php echo $tableClass ?>">
	<thead>
		<tr>
			<th><?php echo \akrys\redaxo\addon\UsageCheck\RedaxoCall::i18nMsg('akrys_usagecheck_changelog_header_version'); ?></th>
			<th><?php echo \akrys\redaxo\addon\UsageCheck\RedaxoCall::i18nMsg('akrys_usagecheck_changelog_header_date'); ?></th>
			<th><?php echo \akrys\redaxo\addon\UsageCheck\RedaxoCall::i18nMsg('akrys_usagecheck_changelog_header_changes'); ?></th>
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