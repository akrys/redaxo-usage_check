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
						$fragment = new \rex_fragment([
							'text' => \rex_i18n::rawMsg('akrys_usagecheck_module_msg_not_used'),
						]);

						$fragment = new \rex_fragment([
							'text' => $fragment->parse('fragments/msg/tagged_msg.php'),
						]);
						echo $fragment->parse('fragments/msg/error.php');
					} else {
						$fragment = new \rex_fragment([
							'text' => \rex_i18n::rawMsg('akrys_usagecheck_module_msg_used'),
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

								if ($item['slice_data'] !== null) {
									$usages = explode("\n", $item['slice_data']);

									$index = 'akrys_usagecheck_module_linktext_edit_slice';
									$linkTextRaw = \rex_i18n::rawMsg($index);
									foreach ($usages as $usageRaw) {
										$usage = explode("\t", $usageRaw);
										$sliceID = $usage[0];
										$clang = $usage[1];
										$ctype = $usage[2];
										$articleID = $usage[3];
										$categoryID = $usage[4];
										$articleName = $usage[5];


										$hasPerm = $structurePerm->hasCategoryPerm($articleID);

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
