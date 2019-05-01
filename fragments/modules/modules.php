<?php
$user = \rex::getUser();
$structurePerm = \rex_structure_perm::get($user, 'structure')
?>

<table class="table table-striped">
	<thead>
		<tr>
			<th><?= \rex_i18n::rawMsg('akrys_usagecheck_module_table_heading_name'); ?></th>
			<th><?= \rex_i18n::rawMsg('akrys_usagecheck_module_table_heading_functions'); ?></th>
		</tr>
	</thead>
	<tbody>

		<?php
		foreach ($this->items as $item) {
			if (!$user->isAdmin() && !$user->getComplexPerm('modules')->hasPerm($item['id'])) {
				continue;
			}
			?>

			<tr>
				<td><?= $item['name']; ?></td>
				<td>

					<?php
					if ($item['slice_data'] === null) {
						$fragment = new rex_fragment(['msg' => [\rex_i18n::rawMsg('akrys_usagecheck_module_msg_not_used')]]);
						echo $fragment->parse('msg/error_box.php');
					} else {
						$fragment = new rex_fragment(['msg' => [\rex_i18n::rawMsg('akrys_usagecheck_module_msg_used')]]);
						echo $fragment->parse('msg/info_box.php');
					}
					?>

					<div  class="rex-message" style="border:0;outline:0;">
						<span>
							<ol>
								<?php
								$type = FriendsOfRedaxo\addon\UsageCheck\Modules\Modules::TYPE;
								$url = "index.php?page=usage_check/details&type=".$type."&id=".$item['id'];
								?>

								<li><a href="<?= $url; ?>"><?= \rex_i18n::rawMsg('akrys_usagecheck_linktext_detail_page') ?></a></li>

								<?php
								$user = \rex::getUser();
								if ($user->isAdmin()) {
									$url = 'index.php?page=modules/modules&function=edit&module_id='.$item['id'];
									$index = 'akrys_usagecheck_module_linktext_edit_code';
									$linkText = \rex_i18n::rawMsg($index);
									?>

									<li>
										<a href="<?= $url; ?>"><?= $linkText; ?></a>
									</li>

									<?php
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
