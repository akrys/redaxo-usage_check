<?php
$api = akrys\redaxo\addon\UsageCheck\RedaxoCall::getAPI();
?>

<table class="<?= $api->getTableClass(); ?>">
	<thead>
		<tr>
			<th><?= $api->getI18N('akrys_usagecheck_action_table_heading_name'); ?></th>
			<th><?= $api->getI18N('akrys_usagecheck_action_table_heading_functions'); ?></th>
		</tr>
	</thead>
	<tbody>

		<?php
		foreach ($this->items as $item) {
			?>
			<tr>
				<td><?= $item['name']; ?></td>
				<td>
					<?php
					if ($item['modul'] === null) {
						$msg = $api->getI18N('akrys_usagecheck_action_msg_not_used');
						echo $api->getTaggedErrorMsg($msg);
					} else {
						$msg = $api->getI18N('akrys_usagecheck_action_msg_used');
						echo $api->getTaggedInfoMsg($msg);
					}
					?>

					<div  class="rex-message" style="border:0;outline:0;">
						<span>
							<ol>
								<?php
								$output = $api->getI18N('akrys_usagecheck_action_linktext_edit_code');
								?>

								<li><?= $this->actions->outputActionEdit($item, $output); ?></li>

								<?php
								if ($item['modul'] !== null) {
									$usages = explode("\n", $item['modul']);
									$idex = 'akrys_usagecheck_action_linktext_edit_in_modul';
									$linkTextRaw = akrys\redaxo\addon\UsageCheck\RedaxoCall::getAPI()->getI18N($index);
									foreach ($usages as $usageRaw) {
										$usage = (explode("\t", $usageRaw));
										$modulID = $usage[0];
										$modulName = $usage[1];
										$href = 'index.php?page=module&subpage=&function=edit&modul_id='.$modulID;
										$linkText = str_replace('$modulName$', $modulName, $linkTextRaw);
										?>

										<li><a href="<?= $href; ?>"><?= $linkText; ?></a></li>

										<?php
									}
								}
								?>
							</ol>
						</span>
					</div>
				</td>
			</tr>

			<?php
		}
		?>

	</tbody>
</table>
