<?php
$user = rex::getUser();
$structurePerm = $user->getComplexPerm('structure');
?>

<div class="basis template">
	<strong><?= rex_i18n::rawMsg('akrys_usagecheck_templates'); ?> "<?= $this->data['first']['name'] ?>"</strong><br />

	<?php
	if ($this->data['first']['article_id'] === null && $this->data['first']['usagecheck_template_t2_id'] === null) {
		$fragment = new rex_fragment(['msg' => [rex_i18n::rawMsg('akrys_usagecheck_template_msg_not_used')]]);
		echo $fragment->parse('msg/error_box.php');
	} else {
		$fragment = new rex_fragment(['msg' => [rex_i18n::rawMsg('akrys_usagecheck_template_msg_used')]]);
		echo $fragment->parse('msg/info_box.php');
	}
	?>
	<ol>
		<?php
		if (isset($this->data['result']['articles'])) {
			$index = 'akrys_usagecheck_template_linktext_edit_article';
			$linkTextRaw = rex_i18n::rawMsg($index);
			foreach ($this->data['result']['articles'] as $item) {
				$articleID = $item['usagecheck_article_a_id'];
				$articleReID = $item['usagecheck_article_a_parent_id'];
				$startpage = $item['usagecheck_article_a_startarticle'];
				$articleName = $item['usagecheck_article_a_name'];
				$clang = $item['usagecheck_article_a_clang_id'];


				if ($startpage == 1) {
					$articleReID = $articleID;
				}

				$hasPerm = $structurePerm->hasCategoryPerm($articleID);
				if ($hasPerm) {
					$href = 'index.php?page=structure&article_id='.$articleID.
						'&function=edit_art&category_id='.$articleReID.'&clang='.$clang;
					$linkText = $linkTextRaw;
					$linkText = str_replace('$articleID$', $articleID, $linkText);
					$linkText = str_replace('$articleName$', $articleName, $linkText);
					?>

					<li><a href="<?= $href; ?>"><?= $linkText; ?></a></li>

					<?php
				}
			}
		}

		//Templates, die in Templates verwendert werden, betrifft
		//nur die Coder, und das wären Admins
		$hasPerm = $user->isAdmin();
		if ($hasPerm) {
			if (isset($this->data['result']['templates'])) {
				$index = 'akrys_usagecheck_template_linktext_edit_template';
				$linkTextRaw = rex_i18n::rawMsg($index);

				foreach ($this->data['result']['templates'] as $item) {
					$id = $item['usagecheck_template_t2_id'];
					$name = $item['usagecheck_template_t2_name'];

					$href = 'index.php?page=templates&function=edit&template_id='.$id;
					$linkText = $linkTextRaw;
					$linkText = str_replace('$templateName$', $name, $linkText);
					$linkText = str_replace('$templateID$', $id, $linkText);
					?>

					<li><a href="<?= $href; ?>"><?= $linkText; ?></a></li>

					<?php
				}
			}
		}
		?>
	</ol>
</div>
