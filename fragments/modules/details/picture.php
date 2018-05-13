<?php
if (count($this->errors) > 0) {
	$fragment = new rex_fragment(['msg' => $this->errors]);
	echo $fragment->parse('msg/error_box.php');
	return;
}



$media = rex_media::get($this->data[0]['filename']);
?>

<div class="basis">
	<?php
	$url = 'index.php?rex_media_type=rex_mediapool_detail&rex_media_file='.$this->data[0]['filename'];

	$fileSize = new \akrys\redaxo\addon\UsageCheck\Lib\FileSize($this->data[0]['filesize']);

	$fragment = new \rex_fragment([
		'src' => $url,
		'alt' => '',
//		'style' => 'max-width:150px;max-height: 150px;',
	]);
	echo $fragment->parse('fragments/image.php');
	?>
	<table>
		<tr><th></th><td><?= $fileSize->getSizeOut() ?></td>
		<tr><th></th><td><?= $this->data[0]['title'] ?></td>
		<tr><th></th><td><?= $this->data[0]['width'] ?> &times;<?= $this->data[0]['height'] ?></td>
			<?php
			foreach ($this->data[0] as $key => $value) {
				if (!preg_match('/^[as]_/msi', $key)) {
					?>
				<tr><th><?= $key ?></th><td><?= $value ?></td>
					<?php
				}
			}
			?>

	</table>
</div>
<?php
/*
  <pre>
  <?php
  foreach ($this->data as $row) {
  var_dump($row);
  }
  ?>
  </pre>
 */
?>
