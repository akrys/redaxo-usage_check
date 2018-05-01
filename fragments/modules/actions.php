
<table class="table table-striped">
	<thead>
		<tr>
			<th><?= \rex_i18n::rawMsg('akrys_usagecheck_action_table_heading_name'); ?></th>
			<th><?= \rex_i18n::rawMsg('akrys_usagecheck_action_table_heading_functions'); ?></th>
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
						$fragment = new \rex_fragment([
							'text' => \rex_i18n::rawMsg('akrys_usagecheck_action_msg_not_used'),
						]);
						$fragment = new \rex_fragment([
							'text' => $fragment->parse('fragments/msg/tagged_msg.php'),
						]);
						echo $fragment->parse('fragments/msg/error.php');
					} else {
						$fragment = new \rex_fragment([
							'text' => \rex_i18n::rawMsg('akrys_usagecheck_action_msg_used'),
						]);

						$fragment = new \rex_fragment([
							'text' => $fragment->parse('fragments/msg/tagged_msg.php'),
						]);
						echo $fragment->parse('fragments/msg/info.php');
					}
					?>

					<div  class="rex-message" style="border:0;outline:0;">
						<span>
							<ol>
								<?php
								$output = \rex_i18n::rawMsg('akrys_usagecheck_action_linktext_edit_code');
								?>

								<li><?= $this->actions->outputActionEdit($item, $output); ?></li>

								<?php
								if ($item['modul'] !== null) {
									$usages = explode("\n", $item['modul']);
									$idex = 'akrys_usagecheck_action_linktext_edit_in_modul';
									$linkTextRaw = \rex_i18n::rawMsg($index);
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
