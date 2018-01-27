<?php ?>
<ul>

	<?php
	foreach ($this->links as $link) {
		if ($link['admin'] == false || $this->user->isAdmin()) {
			?>

			<li><a href="<?= $link['url']; ?>"><?= $link['text']; ?></a></li>
			<?php
		}
	}
	?>

</ul>

<?php
if (is_array($this->texts)) {
	foreach ($this->texts as $text) {
		?>

		<p class="rex-tx1"><?= $text; ?></p>
		<?php
	}
}
