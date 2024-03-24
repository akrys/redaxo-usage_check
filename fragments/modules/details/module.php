<?php
if (count($this->errors) > 0) {
	$fragment = new rex_fragment(['msg' => $this->errors]);
	echo $fragment->parse('msg/error_box.php');
	return;
}
$user = rex::getUser();
$structurePerm = $user?->getComplexPerm('structure');
?>

<div class="basis module">
	<strong><?= rex_i18n::rawMsg('akrys_usagecheck_module'); ?> "<?= $this->data['first']['name'] ?>"</strong><br />

	<?php
	if ($this->data['first']['usagecheck_s_id'] === null) {
		$fragment = new rex_fragment(['msg' => [rex_i18n::rawMsg('akrys_usagecheck_module_msg_not_used')]]);
		echo $fragment->parse('msg/error_box.php');
	} else {
		$fragment = new rex_fragment(['msg' => [rex_i18n::rawMsg('akrys_usagecheck_module_msg_used')]]);
		echo $fragment->parse('msg/info_box.php');
	}
	?>
	<ol>

		<?php
		if ($user?->isAdmin()) {
			$url = 'index.php?page=modules/modules&function=edit&module_id='.$this->data['first']['id'];
			$index = 'akrys_usagecheck_module_linktext_edit_code';
			$linkText = rex_i18n::rawMsg($index);
			?>

			<li>
				<a href="<?= $url; ?>"><?= $linkText; ?></a>
			</li>

			<?php
		}


		$index = 'akrys_usagecheck_module_linktext_edit_slice';
		$linkTextRaw = rex_i18n::rawMsg($index);
		if (isset($this->data['result']['modules'])) {
			foreach ($this->data['result']['modules'] as $item) {
				$sliceID = $item['usagecheck_s_id'];
				$clang = $item['usagecheck_s_clang_id'];
				$ctype = $item['usagecheck_s_ctype_id'];
				$articleID = $item['usagecheck_a_id'];
				$categoryID = $item['usagecheck_a_parent_id'];
				$articleName = $item['usagecheck_a_name'];

				/**
				 * @var rex_structure_perm $structurePerm
				 */
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
</div>
