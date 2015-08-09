<?php
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */


/**
 * Textile Addon
 *
 * @author markus[dot]staab[at]redaxo[dot]de Markus Staab
 *
 * @package redaxo4
 * @version svn:$Id$
 */
require $REX['INCLUDE_PATH'].'/layout/top.php';



$page = rex_request('page', 'string');
$subpage = rex_request('subpage', 'string');

if ($subpage === '') {
	$subpage = 'overview';
}

$contentFile = __DIR__.'/_'.$subpage.'.php';


if (file_exists($contentFile)) {
	include $contentFile;
} else {
	?>
	<div class="rex-message">
		<div class="rex-warning">
			<p>
				<span>
					<?php echo $I18N->msg('error_content_file_not_found'); ?>:<br />
					<?php echo $contentFile; ?>
				</span>
			</p>
		</div>
	</div>

	<?php
}



require $REX['INCLUDE_PATH'].'/layout/bottom.php';
