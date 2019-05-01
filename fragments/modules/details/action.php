
<div class="basis">
	<strong><?= \rex_i18n::rawMsg('akrys_usagecheck_action'); ?> "<?= $this->data['first']['name'] ?>"</strong><br />
	<?php
	if ($this->data['first']['usagecheck_ma_module'] === null) {
		$fragment = new rex_fragment(['msg' => [\rex_i18n::rawMsg('akrys_usagecheck_module_msg_not_used')]]);
		echo $fragment->parse('msg/error_box.php');
	} else {
		$fragment = new rex_fragment(['msg' => [\rex_i18n::rawMsg('akrys_usagecheck_module_msg_used')]]);
		echo $fragment->parse('msg/info_box.php');
	}
	?>
	<ol>

		<li>
			<?php
			$url = 'index.php?page=modules/actions&action_id='.$this->data['first']['id'].'&function=edit';
			$fragmet = new \rex_fragment([
				'href' => $url,
				'text' => \rex_i18n::rawMsg('akrys_usagecheck_action_linktext_edit_code'),
			]);
			echo $fragmet->parse('fragments/link.php');
			?>

		</li>

		<?php
		if (isset($this->data['result']['action'])) {
			$index = 'akrys_usagecheck_action_linktext_edit_in_modul';
			$linkTextRaw = \rex_i18n::rawMsg($index);

			foreach ($this->data['result']['action'] as $item) {
				$modulID = $item['usagecheck_ma_module'];
				$modulName = $item['usage_check_m_name'];
				$href = 'index.php?page=modules/modules&start=0&function=edit&module_id='.$modulID;
				$linkText = str_replace('$modulName$', $modulName, $linkTextRaw);
				?>

				<li><a href="<?= $href; ?>"><?= $linkText; ?></a></li>

				<?php
			}
		}
		?>

	</ol>
</div>
