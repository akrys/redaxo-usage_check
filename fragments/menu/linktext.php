
<p class="rex-tx1"><a href="<?= $this->url; ?>"><?= $this->linktext; ?></a></p>
<?php
if (is_array($this->texts)) {
	foreach ($this->texts as $text) {
		?>

		<p class="rex-tx1"><?= $text; ?></p>
		<?php
	}
}
