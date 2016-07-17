<?php
/**
 * Frontend-Ausagbe für die Seite Changelog
 */
require_once __DIR__.'/../akrys/redaxo/addon/UsageCheck/Config.php';
require_once __DIR__.'/../akrys/redaxo/addon/UsageCheck/RedaxoCall.php';

use \akrys\redaxo\addon\UsageCheck\Config;
use \akrys\redaxo\addon\UsageCheck\RedaxoCall;

$title=Config::NAME_OUT.' / '.RedaxoCall::getAPI()->i18nMsg('akrys_usagecheck_changelog_subpagetitle').
	' <span style="font-size:10px;color:#c2c2c2">'.Config::VERSION.'</span>';
echo RedaxoCall::getAPI()->rexTitle($tile, Config::NAME_OUT);

if (!function_exists('\\glob')) {
	print 'this page requires the glob function';
	die();
}

if (stristr(RedaxoCall::getAPI()->getLang(), 'de_')) {
	$dir = glob(__DIR__.'/release_notes/de/*_*.php');
} else {
	$dir = glob(__DIR__.'/release_notes/en/*_*.php');
}
rsort($dir);
?>


<table class="<?php echo RedaxoCall::getAPI()->getTableClass(); ?>">
	<thead>
		<tr>
			<th><?php echo RedaxoCall::getAPI()->i18nMsg('akrys_usagecheck_changelog_header_version'); ?></th>
			<th><?php echo RedaxoCall::getAPI()->i18nMsg('akrys_usagecheck_changelog_header_date'); ?></th>
			<th><?php echo RedaxoCall::getAPI()->i18nMsg('akrys_usagecheck_changelog_header_changes'); ?></th>
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