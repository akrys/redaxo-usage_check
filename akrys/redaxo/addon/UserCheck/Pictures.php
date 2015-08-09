<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Datei für ...
 *
 * @version       1.0 / 2015-08-08
 * @package       new_package
 * @subpackage    new_subpackage
 * @author        akrys
 */

/**
 * Description of Pictures
 *
 * @author akrys
 */
class Pictures
{

	/**
	 * Nicht genutze Bilder holen
	 *
	 * @param boolean $show_all
	 * @return array
	 *
	 * @todo bei Instanzen mit vielen Dateien im Medienpool testen. Die Query
	 *       riecht nach Performance-Problemen
	 */
	public static function getPictures($show_all = false)
	{

		$rexSQL = new rex_sql;
		$sql = <<<SQL
SELECT f.*, s.id as slice_id,s.article_id, s.clang, s.ctype
FROM rex_file f
left join `rex_article_slice` s on (
    s.file1=f.filename
 OR s.file2=f.filename
 OR s.file3=f.filename
 OR s.file4=f.filename
 OR s.file5=f.filename
 OR s.file6=f.filename
 OR s.file7=f.filename
 OR s.file8=f.filename
 OR s.file9=f.filename
 OR s.file10=f.filename
 OR find_in_set(f.filename, s.filelist1)
 OR find_in_set(f.filename, s.filelist2)
 OR find_in_set(f.filename, s.filelist3)
 OR find_in_set(f.filename, s.filelist4)
 OR find_in_set(f.filename, s.filelist5)
 OR find_in_set(f.filename, s.filelist6)
 OR find_in_set(f.filename, s.filelist7)
 OR find_in_set(f.filename, s.filelist8)
 OR find_in_set(f.filename, s.filelist9)
 OR find_in_set(f.filename, s.filelist10)
)

SQL;
		if (!$show_all == '') {
			$sql.='where s.id is null';
		}

		return $rexSQL->getArray($sql);
	}
}