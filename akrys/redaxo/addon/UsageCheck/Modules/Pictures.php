<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
namespace akrys\redaxo\addon\UsageCheck\Modules;

require_once __DIR__.'/../Permission.php';

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
	 *
	 * @todo die Funktion sollte daten in einem anderen Objekt aufrufen, so dass ein Objekt zurückgegeben werden kann. Da kann man mit getResult() und getFields() besser auf die Daten zugreifen.
	 */
	public static function getPictures($show_all = false)
	{

		if (!\akrys\redaxo\addon\UsageCheck\Permission::check(\akrys\redaxo\addon\UsageCheck\Permission::PERM_MEDIAPOOL)) {
			return false;
		}

		$rexSQL = new \rex_sql;

		$sqlParts = self::getXFormTableSQLParts();
		$havingClauses = $sqlParts['havingClauses'];
		$additionalSelect = $sqlParts['additionalSelect'];
		$additionalJoins = $sqlParts['additionalJoins'];
		$tableFields = $sqlParts['tableFields'];

		$sql = <<<SQL
SELECT f.*,count(s.id) as count,
group_concat(distinct concat(s.id,"\\t",s.article_id,"\\t",a.name,"\\t",s.clang,"\\t",s.ctype) Separator "\\n") as slice_data

$additionalSelect

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

left join rex_article a on (a.id=s.article_id and a.clang=s.clang)

$additionalJoins

SQL;

		if (!$show_all) {
			$sql.='where s.id is null ';
		}

		$sql.='group by f.filename ';


		if (!$show_all && isset($havingClauses) && count($havingClauses) > 0) {
			$sql.='having '.implode(' and ', $havingClauses).' ';
		}

		return array('result' => $rexSQL->getArray($sql), 'fields' => $tableFields);
	}

	/**
	 * SQL Parts generieren.
	 *
	 * @todo Da so viele Daten im return-array gesammelt werden, könnte man auch über ein weiteres Objekt nachdenken, wo diese Daten als instanz hinterlegt werden.
	 * @return array
	 */
	private static function getXFormTableSQLParts()
	{
		$return = array(
			'additionalSelect' => '',
			'additionalJoins' => '',
			'tableFields' => array(),
			'havingClauses' => array(),
		);
		$rexSQL = new \rex_sql;

		if (!\OOAddon::isAvailable('xform')) {
			return $return;
		}

		if (!\OOPlugin::isAvailable('xform', 'manager')) {
			return $return;
		}

		$xformtable = $rexSQL->getArray("show table status like 'rex_xform_table'");
		$xformfield = $rexSQL->getArray("show table status like 'rex_xform_field'");

		$xformtableExists = count($xformfield) > 0;
		$xformfieldExists = count($xformtable) > 0;

		if ($xformfieldExists <= 0 || $xformtableExists <= 0) {
			return $return;
		}

		if ($xformfieldExists && $xformtableExists) {

			$sql = <<<SQL
select f.table_name, t.name as table_out,f1,f2,type_name
from rex_xform_field f
left join rex_xform_table t on t.table_name=f.table_name
where type_name in ('be_mediapool','be_medialist','mediafile')
SQL;
			$tables = $rexSQL->getArray($sql);

			$xTables = array();
			foreach ($tables as $table) {
				$xTables[$table['table_name']][] = array('name' => $table['f1'], 'name_out' => $table['f2'], 'table_out' => $table['table_out'], 'type' => $table['type_name']);
			}

			foreach ($xTables as $tableName => $fields) {
				$return['additionalSelect'].=', group_concat(distinct '.$tableName.'.id';
				$return['additionalJoins'].='LEFT join '.$tableName.' on (';

				foreach ($fields as $key => $field) {
					if ($key > 0) {
						$return['additionalJoins'].=' OR ';
					}

					switch ($field['type']) {
						case 'be_mediapool':
						case 'mediafile':
							$return['additionalJoins'].=$tableName.'.'.$field['name'].'= f.filename';
							break;
						case 'be_medialist':
							$return['additionalJoins'].='FIND_IN_SET(f.filename,'.$tableName.'.'.$field['name'].')';
							break;
					}
				}

				$return['tableFields'][$tableName] = $fields;
				$return['additionalJoins'].=')'.PHP_EOL;
				$return['additionalSelect'].=' Separator "\n") as '.$tableName.PHP_EOL;
				$return['havingClauses'][] = $tableName.' IS NULL';
			}
		}

		return $return;
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
				$unit = 'B';
				break;
			case 1:
				$unit = 'kB';
				break;
			case 2:
				$unit = 'MB';
				break;
			case 3:
				$unit = 'GB';
				break;
			case 4:
				$unit = 'TB';
				break;
			case 5:
				$unit = 'EB';
				break;
			case 6:
				$unit = 'PB';
				break;
			default:
				$unit = '????';
				break;
		}

		return $value.' '.$unit;
	}

	/**
	 * Überprüfen, ob eine Datei existiert.
	 *
	 * @global type $REX
	 * @param array $item
	 * @return boolean
	 */
	public static function exists($item)
	{
		return file_exists($GLOBALS['REX']['MEDIAFOLDER'].DIRECTORY_SEPARATOR.$item['filename']);
	}
}