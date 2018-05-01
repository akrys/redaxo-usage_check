
<table class="table table-striped">
	<thead>
		<tr>
			<th><?= \rex_i18n::rawMsg('akrys_usagecheck_changelog_header_version'); ?></th>
			<th><?= \rex_i18n::rawMsg('akrys_usagecheck_changelog_header_date'); ?></th>
			<th><?= \rex_i18n::rawMsg('akrys_usagecheck_changelog_header_changes'); ?></th>
		</tr>
	</thead>
	<tbody>

		<?php
		foreach ($this->dir as $file) {
			$data = (explode('_', str_replace('.php', '', basename($file))));
			?>

			<tr>
				<td><?= $data[1]; ?></td>
				<td><?= $data[0]; ?></td>
				<td>
					<?php require $file; ?>
				</td>
			</tr>

			<?php
		}
		?>

	</tbody>
</table>
