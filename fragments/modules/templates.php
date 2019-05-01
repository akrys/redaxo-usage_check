<?php
$user = \rex::getUser();
?>

<table class="table table-striped">
	<thead>
		<tr>
			<th><?= \rex_i18n::rawMsg('akrys_usagecheck_template_table_heading_name'); ?></th>
			<th><?= \rex_i18n::rawMsg('akrys_usagecheck_template_table_heading_functions'); ?></th>
		</tr>
	</thead>
	<tbody>

		<?php
		foreach ($this->items as $item) {
			?>
			<tr<?= $item['active'] == 1 ? '' : ' style = "opacity:0.80;"' ?>>
				<td>
					<?php
					echo $item['name'];
					if ($item['active'] == 0) {
						?>
						<br />
						(<?= \rex_i18n::rawMsg('akrys_usagecheck_template_table_inactive'); ?>)
						<br />
						<?php
					}
					?>

				</td>
				<td>

					<?php
					if ((int) $item['articles'] === 0 && (int) $item['templates'] === 0) {
						$fragment = new rex_fragment(['msg' => [\rex_i18n::rawMsg('akrys_usagecheck_template_msg_not_used')]]);
						echo $fragment->parse('msg/error_box.php');
					} else {
						$fragment = new rex_fragment(['msg' => [\rex_i18n::rawMsg('akrys_usagecheck_template_msg_used')]]);
						echo $fragment->parse('msg/info_box.php');
					}
					?>

					<div  class="rex-message" style="border:0;outline:0;">
						<span>
							<strong><?= \rex_i18n::rawMsg('akrys_usagecheck_template_detail_heading') ?></strong>
							<ol>
								<?php
								$type = FriendsOfRedaxo\addon\UsageCheck\Modules\Templates::TYPE;
								$url = "index.php?page=usage_check/details&type=".$type."&id=".$item['id'];
								?>

								<li><a href="<?= $url; ?>"><?= \rex_i18n::rawMsg('akrys_usagecheck_linktext_detail_page') ?></a></li>

								<li>
									<?php
									if ($user->isAdmin()) {
										$url = 'index.php?page=templates&function=edit&template_id='.$item['id'];

										$fragmet = new \rex_fragment([
											'href' => $url,
											'text' => \rex_i18n::rawMsg('akrys_usagecheck_template_linktext_edit_code'),
										]);
										echo $fragmet->parse('fragments/link.php');
									}
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
