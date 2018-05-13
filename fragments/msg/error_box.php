<?php

$text = '';
foreach ($this->msg as $msg) {
	if (trim($msg) !== '') {
		$fragment = new \rex_fragment([
			'text' => $msg,
		]);

		$text .= $fragment->parse('fragments/msg/tagged_msg.php');
	}
}

if ($text !== '') {
	$fragment = new \rex_fragment([
		'text' => $text,
	]);
	echo $fragment->parse('fragments/msg/error.php');
}
