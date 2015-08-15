<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
namespace akrys\redaxo\addon\UserCheck;

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
	 *       riecht nach Performance-Problemen -> 	Using join buffer (Block Nested Loop)
	 */
	public static function getPictures($show_all = false)
	{
		$rexSQL = new \rex_sql;
		$sql = <<<SQL
SELECT f.*,count(s.id) as count, s.id as slice_id,s.article_id, s.clang, s.ctype
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
		if (!$show_all) {
			$sql.='where s.id is null ';
		}

		$sql.='group by f.filename';
		return $rexSQL->getArray($sql);
	}

	/**
	 * Dateigröße ermitteln.
	 *
	 * Die Größe in Byte auszugeben ist nicht gerade übersichtlich. Daher wird
	 * hier versucht den Wert in der größt-möglichen Einheit zu ermittln.
	 *
	 * @param array $item wichtige Indezes: filesize
	 * @return string
	 */
	public static function getSizeOut($item)
	{
		$size = $item['filesize'];
		$i = 0;

		while ($size > 1024) {
			$i++;
			$size/=1024;
			if ($i > 6) {
				//WTF????
				break;
			}
		}
		$value = round($size, 2);
		switch ($i) {
			case 0:
				$unit= 'B';
				break;
			case 1:
				$unit= 'kB';
				break;
			case 2:
				$unit= 'MB';
				break;
			case 3:
				$unit= 'GB';
				break;
			case 4:
				$unit= 'TB';
				break;
			case 5:
				$unit= 'EB';
				break;
			case 6:
				$unit= 'PB';
				break;
			default:
				$unit= '????';
				break;
		}

		return $value.' '.$unit;
	}
}