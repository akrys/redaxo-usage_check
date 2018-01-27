<?php
$api = akrys\redaxo\addon\UsageCheck\RedaxoCall::getAPI();
?>

<table class="<?= $api->getTableClass() ?>">
	<thead>
		<tr>
			<th><?= $api->getI18N('akrys_usagecheck_module_table_heading_name'); ?></th>
			<th><?= $api->getI18N('akrys_usagecheck_module_table_heading_functions'); ?></th>
		</tr>
	</thead>
	<tbody>

		<?php
		foreach ($this->items as $item) {
			if (!$this->modules->hasRights($item)) {
				continue;
			}
			?>

			<tr>
				<td><?= $item['name']; ?></td>
				<td>

					<?php
					if ($item['slice_data'] === null) {
						$index = 'akrys_usagecheck_module_msg_not_used';
						echo $api->getTaggedErrorMsg($api->getI18N($index));
					} else {
						$index = 'akrys_usagecheck_module_msg_used';
						echo $api->getTaggedInfoMsg($api->getI18N($index));
					}
					?>

					<div  class="rex-message" style="border:0;outline:0;">
						<span>
							<ol>

								<?php
								if ($api->isAdmin()) {
									$url = 'index.php?page=modules/modules&function=edit&module_id='.$item['id'];
									$index = 'akrys_usagecheck_module_linktext_edit_code';
									$linkText = $api->getI18N($index);
									?>

									<li>
										<a href="<?= $url; ?>"><?= $linkText; ?></a>
									</li>

									<?php
								}

								if ($item['slice_data'] !== null) {
									$usages = explode("\n", $item['slice_data']);

									$index = 'akrys_usagecheck_module_linktext_edit_slice';
									$linkTextRaw = $api->getI18N($index);
									foreach ($usages as $usageRaw) {
										$usage = explode("\t", $usageRaw);
										$sliceID = $usage[0];
										$clang = $usage[1];
										$ctype = $usage[2];
										$articleID = $usage[3];
										$categoryID = $usage[4];
										$articleName = $usage[5];


										$hasPerm = $api->hasCategoryPerm($articleID);

										if ($hasPerm) {
											$href = 'index.php?page=content&article_id='.$articleID.
												'&mode=edit&slice_id='.$sliceID.'&clang='.$clang.'&ctype='.$ctype.
												'&function=edit#slice'.$sliceID;
											$linkText = $linkTextRaw;
											$linkText = str_replace('$sliceID$', $sliceID, $linkText);
											$linkText = str_replace('$articleName$', $articleName, $linkText);
											?>

											<li><a href="<?= $href; ?>"><?= $linkText; ?></a></li>

											<?php
										}
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
