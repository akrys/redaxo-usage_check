<?php
/**
 * Datei für das Medienmodul
 *
 * @version       1.0 / 2016-05-05
 * @author        akrys
 */
namespace akrys\redaxo\addon\UsageCheck\RexV4\Modules;

/**
 * Description of Pictures
 *
 * @author akrys
 */
class Pictures
	extends \akrys\redaxo\addon\UsageCheck\Modules\Pictures
{

	/**
	 * XFormTables holen
	 *
	 * @return array
	 * @param array &$return
	 * @SuppressWarnings(PHPMD.StaticAccess)
	 */
	protected function getXFormSQL(&$return)
	{
		$tables = array();
		$rexSQL = \akrys\redaxo\addon\UsageCheck\RedaxoCall::getAPI()->getSQL();

		if (!\OOAddon::isAvailable('xform')) {
			return $tables;
		}

		if (!\OOPlugin::isAvailable('xform', 'manager')) {
			return $tables;
		}

		$xformTableTable = \akrys\redaxo\addon\UsageCheck\RedaxoCall::getAPI()->getTable('xform_table');
		$xformFieldTable = \akrys\redaxo\addon\UsageCheck\RedaxoCall::getAPI()->getTable('xform_field');

		$xformtable = $rexSQL->getArray("show table status like '$xformTableTable'");
		$xformfield = $rexSQL->getArray("show table status like '$xformFieldTable'");

		$sql = $this->getXformTableSQL($xformTableTable, $xformFieldTable);

		$xformtableExists = count($xformfield) > 0;
		$xformfieldExists = count($xformtable) > 0;

		if ($xformfieldExists && $xformtableExists) {
			$tables = $rexSQL->getArray($sql);
		}
		return $tables;
	}

	/**
	 * XForm-Felder ermitteln.
	 *
	 * Das SQL variiert je nach XForm-Version
	 *
	 * @param string $xformTableTable
	 * @param string $xformFieldTable
	 * @return string
	 */
	private function getXformTableSQL($xformTableTable, $xformFieldTable)
	{
		$rexSQL = \akrys\redaxo\addon\UsageCheck\RedaxoCall::getAPI()->getSQL();

		$inStatement = "'".$rexSQL->escape($GLOBALS['REX']['DB'][1]['NAME'])."',".
			"'".$rexSQL->escape($GLOBALS['REX']['DB'][2]['NAME'])."'";

		$sql = <<<SQL
select count(*) as counter from information_schema.COLUMNS
where
TABLE_SCHEMA in ($inStatement) and
TABLE_NAME = '$xformFieldTable' and COLUMN_NAME='f1'
SQL;
		$count = $rexSQL->getArray($sql);

		if ((int) $count[0]['counter'] > 0) {
			return <<<SQL
select f.table_name, t.name as table_out,f.f1,f.f2,f.type_name
from $xformFieldTable f
left join $xformTableTable t on t.table_name=f.table_name
where type_name in ('be_mediapool','be_medialist','mediafile')
SQL;
		}

		return <<<SQL
select t.table_name, t.name as table_out,f.label as f2,f.name as f1,f.type_name
from $xformFieldTable f
left join $xformTableTable t on t.table_name=f.table_name
where type_name in ('be_mediapool','be_medialist','mediafile')
SQL;
	}

	/**
	 * Überprüfen, ob eine Datei existiert.
	 *
	 * @global type $REX
	 * @param array $item
	 * @return boolean
	 */
	public function exists($item)
	{
		return file_exists($GLOBALS['REX']['MEDIAFOLDER'].DIRECTORY_SEPARATOR.$item['filename']);
	}

	/**
	 * Spezifisches SQL für redaxo 4
	 * @param string $additionalSelect
	 * @param string $additionalJoins
	 * @return string
	 */
	protected function getPictureSQL($additionalSelect, $additionalJoins)
	{
//Keine integer oder Datumswerte in einem concat!
//Vorallem dann nicht, wenn MySQL < 5.5 im Spiel ist.
//-> https://stackoverflow.com/questions/6397156/why-concat-does-not-default-to-default-charset-in-mysql/6669995#6669995

		$fileTable = \akrys\redaxo\addon\UsageCheck\RedaxoCall::getAPI()->getTable('file');
		$articleSliceTable = \akrys\redaxo\addon\UsageCheck\RedaxoCall::getAPI()->getTable('article_slice');
		$articleTable = \akrys\redaxo\addon\UsageCheck\RedaxoCall::getAPI()->getTable('article');

		$sql = <<<SQL
SELECT f.*,count(s.id) as count,
group_concat(distinct concat(
	cast(s.id as char),"\\t",
	cast(s.article_id as char),"\\t",
	a.name,"\\t",
	cast(s.clang as char),"\\t",
	cast(s.ctype as char)
) Separator "\\n") as slice_data

$additionalSelect

FROM $fileTable f
left join `$articleSliceTable` s on (
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

left join $articleTable a on (a.id=s.article_id and a.clang=s.clang)

$additionalJoins

SQL;
		return $sql;
	}

	/**
	 * Holt ein Medium-Objekt mit Prüfung der Rechte
	 *
	 * @param array $item Idezes: category_id, filename
	 * @return \rex_media
	 * @throws \akrys\redaxo\addon\UsageCheck\Exception\FunctionNotCallableException
	 * @SuppressWarnings(PHPMD.StaticAccess)
	 */
	public function getMedium($item)
	{
		if (!$GLOBALS['REX']['USER']->isAdmin() &&
			!$GLOBALS['REX']['USER']->hasPerm('media['.$item['category_id'].']')) {
			//keine Rechte am Medium
			throw new \akrys\redaxo\addon\UsageCheck\Exception\FunctionNotCallableException();
		}

		//Das Medium wird später gebraucht.
		/* @var $medium OOMedia */
		$medium = \OOMedia::getMediaByFileName($item['filename']);
		return $medium;
	}

	/**
	 * Bildvorschau ausgeben
	 *
	 * @return void
	 * @param array $item Ein Element der Ergebnismenge
	 */
	public function outputImagePreview($item)
	{
		if (stristr($item['filetype'], 'image/')) {
			$url = 'index.php?rex_img_type=rex_mediapool_preview&rex_img_file='.$item['filename'];
			?>

			<img alt="" src="<?php echo $url ?>" style="max-width:150px;max-height: 150px;" />
			<br /><br />

			<?php
		}
	}

	/**
	 * Menü URL generieren
	 * @return string
	 * @param string $subpage
	 * @param string $showAllParam
	 */
	public function getMeuLink($subpage, $showAllParam)
	{
		return 'index.php?page='.\akrys\redaxo\addon\UsageCheck\Config::NAME.'&subpage='.$subpage.$showAllParam;
	}

	/**
	 * Namen der Tabelle und des Feldes ermitteln.
	 *
	 * Wenn das allgemein in in der Funktion getMetaTableSQLParts integriert wäre, meckert phpmd eine zu hohe
	 * Komplexität an.
	 *
	 * @param string $name
	 * @param string $value
	 * @return string
	 *
	 * @return array Indezes field, table
	 */
	private function getTableNames($name, $value)
	{
		$return = array();
		switch ($value) {
			case 'art_':
				if (preg_match('/'.preg_quote($value, '/').'/', $name)) {
					$return['field'] = 'joinArtMeta';
					$return['table'] = 'rex_article_art_meta';
				}
				break;
			case 'cat_':
				if (preg_match('/'.preg_quote($value, '/').'/', $name)) {
					$return['field'] = 'joinCatMeta';
					$return['table'] = 'rex_article_cat_meta';
				}
				break;
			case 'med_':
				if (preg_match('/'.preg_quote($value, '/').'/', $name)) {
					$return['field'] = 'joinMedMeta';
					$return['table'] = 'rex_article_med_meta';
				}
				break;
		}
		return $return;
	}

	/**
	 * SQL Parts für die Metadaten innerhalb von Redaxo4 generieren
	 * @return array
	 * @SuppressWarnings(PHPMD.ElseExpression)
	 */
	protected function getMetaTableSQLParts()
	{
		$return = array(
			'additionalSelect' => '',
			'additionalJoins' => '',
			'tableFields' => array(),
			'havingClauses' => array(),
		);

		$joinArtMeta = '';
		$joinCatMeta = '';
		$joinMedMeta = '';

		$names = $this->getMetaNames();

		foreach ($names as $name) {
			foreach ($GLOBALS['REX']['ADDON']['prefixes']['metainfo'] as $value) {
				$table = $this->getTableNames($name['name'], $value);
				if (!$table) {
					continue;
				}
				$fieldname = $table['field'];
				$tablename = $table['table'];

				switch ($name['type']) {
					case 'REX_MEDIA_BUTTON':
						if ($$fieldname != '') {
							$$fieldname.=' or ';
						}
						$$fieldname.=''.$tablename.'.'.$name['name'].' = f.filename';
						break;
					case 'REX_MEDIALIST_BUTTON':
						if ($$fieldname != '') {
							$$fieldname.=' or ';
						}
						$$fieldname.='FIND_IN_SET(f.filename, '.$tablename.'.'.$name['name'].')';
						break;
				}
			}
		}
		$this->addArtSelectAndJoinStatements($return, $joinArtMeta);
		$this->addCatSelectAndJoinStatements($return, $joinCatMeta);
		$this->addMedSelectAndJoinStatements($return, $joinMedMeta);

		return $return;
	}

	/**
	 * Select und Joinstatments im Array anfügen
	 *
	 * Komplexitätsvermeidung von getMetaTableSQLParts
	 *
	 * @param array &$return
	 * @param string $joinArtMeta
	 */
	private function addArtSelectAndJoinStatements(&$return, $joinArtMeta)
	{
		$metaSelectNull = ',null as metaArtIDs '.PHP_EOL;
		$metaSelectNotNull = ',group_concat(distinct concat('.
			'rex_article_art_meta.id,"\t",'.
			'rex_article_art_meta.name,"\t",'.
			'rex_article_art_meta.clang) Separator "\n") as metaArtIDs '.PHP_EOL;
		$return['additionalSelect'] = $joinArtMeta == '' ? $metaSelectNull : $metaSelectNotNull;

		if ($joinArtMeta != '') {
			$return['additionalJoins'].='LEFT join rex_article as rex_article_art_meta on '.
				'(rex_article_art_meta.id is not null and ('.$joinArtMeta.'))'.PHP_EOL;
		}
	}

	/**
	 * Select und Joinstatments im Array anfügen
	 *
	 * Komplexitätsvermeidung von getMetaTableSQLParts
	 *
	 * @param array &$return
	 * @param string $joinCatMeta
	 */
	private function addCatSelectAndJoinStatements(&$return, $joinCatMeta)
	{
		$metaSelectNull = ',null as metaCatIDs '.PHP_EOL;
		$metaSelectNotNull = ',group_concat(distinct concat('.
			'rex_article_cat_meta.id,"\t",'.
			'rex_article_cat_meta.catname,"\t",'.
			'rex_article_cat_meta.clang,"\t",'.
			'rex_article_cat_meta.re_id) Separator "\n") as metaCatIDs '.PHP_EOL;
		$return['additionalSelect'].=$joinCatMeta == '' ? $metaSelectNull : $metaSelectNotNull;

		if ($joinCatMeta != '') {
			$return['additionalJoins'].='LEFT join rex_article as rex_article_cat_meta on '.
				'(rex_article_cat_meta.id is not null and ('.$joinCatMeta.'))'.PHP_EOL;
		}
	}

	/**
	 * Select und Joinstatments im Array anfügen
	 *
	 * Komplexitätsvermeidung von getMetaTableSQLParts
	 *
	 * @param array &$return
	 * @param string $joinMedMeta
	 */
	private function addMedSelectAndJoinStatements(&$return, $joinMedMeta)
	{
		$metaSelectNull = ',null as metaMedIDs '.PHP_EOL;
		$metaSelectNotNull = ',group_concat(distinct concat('.
			'rex_article_med_meta.file_id,"\t",'.
			'rex_article_med_meta.category_id,"\t",'.
			'rex_article_med_meta.filename) Separator "\n") as metaMedIDs '.PHP_EOL;

		$return['additionalSelect'].=$joinMedMeta == '' ? $metaSelectNull : $metaSelectNotNull;
		if ($joinMedMeta != '') {
			$return['additionalJoins'].='LEFT join rex_file as rex_article_med_meta on '.
				'(rex_article_med_meta.file_id is not null and ('.$joinMedMeta.'))'.PHP_EOL;
		}
	}

	/**
	 * Meta-Bildfelder ermitteln.
	 * @return array
	 */
	protected function getMetaNames()
	{
		$rexSQL = \akrys\redaxo\addon\UsageCheck\RedaxoCall::getAPI()->getSQL();

//		$articleTable = \akrys\redaxo\addon\UsageCheck\RedaxoCall::getAPI()->getTable('article');
		$metainfoFieldTable = \akrys\redaxo\addon\UsageCheck\RedaxoCall::getAPI()->getTable('62_params');
		$metainfoTypeTable = \akrys\redaxo\addon\UsageCheck\RedaxoCall::getAPI()->getTable('62_type');

		$sql = <<<SQL
select f.name, t.label as type
from $metainfoFieldTable f
inner join $metainfoTypeTable t on t.id=f.type and t.label like '%MEDIA%'

SQL;
		$names = $rexSQL->getArray($sql);

		return $names;
	}
}
