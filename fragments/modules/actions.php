
<table class="table table-striped">
	<thead>
		<tr>
			<th class="name"><?= \rex_i18n::rawMsg('akrys_usagecheck_action_table_heading_name'); ?></th>
			<th class="function"><?= \rex_i18n::rawMsg('akrys_usagecheck_action_table_heading_functions'); ?></th>
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
						$fragment = new rex_fragment(['msg' => [\rex_i18n::rawMsg('akrys_usagecheck_action_msg_not_used')]]);
						echo $fragment->parse('msg/error_box.php');
					} else {
						$fragment = new rex_fragment(['msg' => [\rex_i18n::rawMsg('akrys_usagecheck_action_msg_used')]]);
						echo $fragment->parse('msg/info_box.php');
					}
					?>

					<div class="rex-message list">
						<span>
							<ol>
								<?php
								$type = FriendsOfRedaxo\addon\UsageCheck\Modules\Actions::TYPE;
								$url = "index.php?page=usage_check/details&type=".$type."&id=".$item['id'];
								?>

								<li><a href="<?= $url; ?>"><?= \rex_i18n::rawMsg('akrys_usagecheck_linktext_detail_page') ?></a></li>

								<li>
									<?php
									$url = 'index.php?page=modules/actions&action_id='.$item['id'].'&function=edit';
									$fragmet = new \rex_fragment([
										'href' => $url,
										'text' => \rex_i18n::rawMsg('akrys_usagecheck_action_linktext_edit_code'),
									]);
									echo $fragmet->parse('fragments/link.php');
									?>

								</li>
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
