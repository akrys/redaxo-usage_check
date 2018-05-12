<?php

/**
 * Datei für Medienmodul
 *
 * @version       1.0 / 2015-08-08
 * @author        akrys
 */
namespace akrys\redaxo\addon\UsageCheck\Modules;

use \akrys\redaxo\addon\UsageCheck\Permission;

/**
 * Description of Pictures
 *
 * @author akrys
 */
class Pictures
	extends BaseModule
{
	const TYPE = 'media';

	/**
	 * Anzeigemodus für "Alle Anzeigen"
	 * @var boolean
	 */
	private $showAll = false;

	/**
	 * Anzeigemodus "alle zeigen" umstellen
	 * @param boolean $bln
	 */
	public function showAll($bln)
	{
		$this->showAll = (boolean) $bln;
	}

	/**
	 * Nicht genutze Bilder holen
	 *
	 * @return array
	 *
	 * @todo bei Instanzen mit vielen Dateien im Medienpool testen. Die Query
	 *       riecht nach Performance-Problemen -> 	Using join buffer (Block Nested Loop)
	 */
	public function getPictures()
	{
		$showAll = $this->showAll;

		if (!Permission::getInstance()->check(Permission::PERM_MEDIA)) {
			return false;
		}

		$rexSQL = $this->getRexSql();

		$sqlPartsYForm = $this->getYFormTableSQLParts();
		$sqlPartsMeta = $this->getMetaTableSQLParts();

		$havingClauses = array();
		$additionalSelect = '';
		$additionalJoins = '';
		$tableFields = array();


		$havingClauses = array_merge($havingClauses, $sqlPartsYForm['havingClauses']);
		$additionalSelect .= $sqlPartsYForm['additionalSelect'];
		$additionalJoins .= $sqlPartsYForm['additionalJoins'];
		$tableFields = array_merge($tableFields, $sqlPartsYForm['tableFields']);

		$havingClauses = array_merge($havingClauses, $sqlPartsMeta['havingClauses']);
		$additionalSelect .= $sqlPartsMeta['additionalSelect'];
		$additionalJoins .= $sqlPartsMeta['additionalJoins'];
		$tableFields = array_merge($tableFields, $sqlPartsMeta['tableFields']);

		$sql = $this->getPictureSQL($additionalSelect, $additionalJoins);

		if (!$showAll) {
			$sql .= 'where s.id is null ';
			$havingClauses[] = 'metaCatIDs is null and metaArtIDs is null and metaMedIDs is null';
		}

		$sql .= 'group by f.filename ';

		if (!$showAll && isset($havingClauses) && count($havingClauses) > 0) {
			$sql .= 'having '.implode(' and ', $havingClauses).'';
		}

		return array('result' => $rexSQL->getArray($sql), 'fields' => $tableFields);
	}

	/**
	 * SQL Partsfür YForm generieren.
	 *
	 * @return array
	 */
	protected function getYFormTableSQLParts()
	{
		$return = array(
			'additionalSelect' => '',
			'additionalJoins' => '',
			'tableFields' => array(),
			'havingClauses' => array(),
		);

		$tables = $this->getYFormSQL();

		$xTables = array();
		foreach ($tables as $table) {
			$xTables[$table['table_name']][] = array(
				'name' => $table['f1'],
				'name_out' => $table['f2'],
				'table_out' => $table['table_out'],
				'type' => $table['type_name'],
				//in YForm 2, muss man prüfen, ob be_media einen multiple modifier hat.
				//siehe Kommentare in \akrys\redaxo\addon\UsageCheck\RexV5\Modules\Pictures::getYFormSQL
				'multiple' => (isset($table['multiple']) && $table['multiple'] == '1'),
			);
		}

		foreach ($xTables as $tableName => $fields) {
			$return['additionalSelect'] .= ', group_concat(distinct '.$tableName.'.id';
			$return['additionalJoins'] .= 'LEFT join '.$tableName.' on (';

			foreach ($fields as $key => $field) {
				if ($key > 0) {
					$return['additionalJoins'] .= ' OR ';
				}

				$return['additionalJoins'] .= $this->getJoinCondition($field, $tableName);
			}

			$return['tableFields'][$tableName] = $fields;
			$return['additionalJoins'] .= ')'.PHP_EOL;
			$return['additionalSelect'] .= ' Separator "\n") as '.$tableName.PHP_EOL;
			$return['havingClauses'][] = $tableName.' IS NULL';
		}


		return $return;
	}

	/**
	 * Auslagerung der Join-Conditions
	 *
	 * PHPMD meckert eine zu hohe Komplexität an. (11 satt der maximalen 10)
	 * Das dürfte an der Anpassung zu YForm 2 liegen, da dort in be_media nun mehrere Dateien angegeben werden dürfen.
	 * Die Prüfung auf $field['multiple'] ist dann eine ebene zu tief.
	 *
	 * @param array $field
	 * @param string $tableName
	 * @return string
	 */
	private function getJoinCondition($field, $tableName)
	{
		$joinCondition = '';
		switch ($field['type']) {
			case 'be_mediapool': // Redaxo 4
			case 'mediafile':
				$joinCondition = $tableName.'.'.$field['name'].' = f.filename';
				break;
			case 'be_medialist': // Redaxo 5, YForm 1
				$joinCondition = 'FIND_IN_SET(f.filename, '.$tableName.'.'.$field['name'].')';
				break;
			case 'be_media': // Redaxo 5
				$joinCondition = $tableName.'.'.$field['name'].' = f.filename';
				if ($field['multiple']) {
					//YForm 2 kann mehrere Dateien aufnehmen
					//siehe Kommentare in self::hasMultiple()
					$joinCondition = 'FIND_IN_SET(f.filename, '.$tableName.'.'.$field['name'].')';
				}
				break;
		}
		return $joinCondition;
	}
//
///////////////////// Tmplementation aus RexV5 /////////////////////
//

	/**
	 * YFormTables holen
	 *
	 * @return array
	 * @param array &$return
	 */
	protected function getYFormSQL()
	{
		$tabels = array();

		$rexSQL = $this->getRexSql();

		if (!\rex_addon::get('yform')->isAvailable()) {
			return $tabels;
		}

		if (!\rex_plugin::get('yform', 'manager')->isAvailable()) {
			return $tabels;
		}

		$yformTableTable = $this->getTable('yform_table');
		$yformFieldTable = $this->getTable('yform_field');

		$yformtable = $rexSQL->getArray("show table status like '$yformTableTable'");
		$yformfield = $rexSQL->getArray("show table status like '$yformFieldTable'");

		$additionalFields = '';
		if ($this->hasMultiple($yformFieldTable)) {
			$additionalFields = ', f.multiple';
		}

		$sql = <<<SQL
select f.table_name, t.name as table_out,f.name as f1, f.label as f2,f.type_name $additionalFields
from $yformFieldTable f
left join $yformTableTable t on t.table_name=f.table_name
where type_name in ('be_media','be_medialist','mediafile')
SQL;

		$yformtableExists = count($yformfield) > 0;
		$yformfieldExists = count($yformtable) > 0;

		if ($yformfieldExists && $yformtableExists) {
			$tabels = $rexSQL->getArray($sql);
		}
		return $tabels;
	}

	/**
	 * Überprüfen, ob eine Datei existiert.
	 *
	 * @global type $REX
	 * @param array $item
	 * @return boolean
	 * @SuppressWarnings(PHPMD.StaticAccess)
	 */
	public function exists($item)
	{
		return file_exists(\rex_path::media().DIRECTORY_SEPARATOR.$item['filename']);
	}

	/**
	 * Fehler: #10 Anpassung an YForm 2
	 *
	 * Änderung:
	 * a) be_medialist gibt es nicht mehr (Hier unwichtig)
	 * b) be_media kann mehrere Bilder enthalten.
	 *
	 * Dafür gibt es eine neue Spalte in rex_yform_field (multiple)
	 * Das kann aber nur abgefragt werden, wenn es da ist.
	 *
	 * Daher muss man erst im information_schema nachfragen, ob es multiple gibt, sonst geht die Datenabfrage in
	 * die Binsen.
	 *
	 * Der 2. Parameter sollte im Normalfall nicht verwendet werden.
	 * Er dient nur dazu, in Unit-Tests andere Daten injezieren zu können, und somit auch einen Fehlerfall simulieren
	 * zu können.
	 *
	 * @param string $yformFieldTable
	 * @param array $dbs
	 * @returns boolean
	 * @SuppressWarnings(PHPMD.StaticAccess)
	 */
	private function hasMultiple($yformFieldTable, $dbs = null)
	{
		$rexSQL = $this->getRexSql();

		if (!isset($dbs)) { // Normalfall, wenn wir nicht gerade Unit-Tests laufen lassen
			$dbs = \rex::getProperty('db');
		}

		$where = array();
		foreach ($dbs as $db) {
			if (isset($db['name']) && $db['name'] != '') {
				$where[] .= "(TABLE_NAME=? and TABLE_SCHEMA=? and COLUMN_NAME='multiple')";
				$params[] = $yformFieldTable;
				$params[] = $db['name'];
			}
		}

		if (!$where) {
			return false;
		}

		$whereString = implode(' or ', $where);
		$sql = <<<SQL
select * from information_schema.COLUMNS
where $whereString
SQL;
		$hasMultipleResult = $rexSQL->getArray($sql, $params);
		return count($hasMultipleResult) > 0;
	}

	/**
	 * Spezifisches SQL für redaxo 5
	 * @param string $additionalSelect
	 * @param string $additionalJoins
	 * @return string
	 */
	protected function getPictureSQL($additionalSelect, $additionalJoins)
	{
		$mediaTable = $this->getTable('media');
		$articleSliceTable = $this->getTable('article_slice');
		$articleTable = $this->getTable('article');

		$sql = <<<SQL
SELECT f.*,count(s.id) as count,
group_concat(distinct concat(
	cast(s.id as char),"\\t",
	cast(s.article_id as char),"\\t",
	a.name,"\\t",
	cast(s.clang_id as char),"\\t",
	cast(s.ctype_id as char)
) Separator "\\n") as slice_data

$additionalSelect

FROM $mediaTable f
left join `$articleSliceTable` s on (
    s.media1=f.filename
 OR s.media2=f.filename
 OR s.media3=f.filename
 OR s.media4=f.filename
 OR s.media5=f.filename
 OR s.media6=f.filename
 OR s.media7=f.filename
 OR s.media8=f.filename
 OR s.media9=f.filename
 OR s.media10=f.filename
 OR find_in_set(f.filename, s.medialist1)
 OR find_in_set(f.filename, s.medialist2)
 OR find_in_set(f.filename, s.medialist3)
 OR find_in_set(f.filename, s.medialist4)
 OR find_in_set(f.filename, s.medialist5)
 OR find_in_set(f.filename, s.medialist6)
 OR find_in_set(f.filename, s.medialist7)
 OR find_in_set(f.filename, s.medialist8)
 OR find_in_set(f.filename, s.medialist9)
 OR find_in_set(f.filename, s.medialist10)
)

left join $articleTable a on (a.id=s.article_id and a.clang_id=s.clang_id)

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
		$user = \rex::getUser();
		$complexPerm = $user->getComplexPerm('media');
		if (!$user->isAdmin() &&
			!(is_object($complexPerm) &&
			$complexPerm->hasCategoryPerm($item['category_id']))) {
			//keine Rechte am Medium
			throw new \akrys\redaxo\addon\UsageCheck\Exception\FunctionNotCallableException();
		}

		//Das Medium wird später gebraucht.
		/* @var $medium \rex_media */
		$medium = \rex_media::get($item['filename']);
		return $medium;
	}

	/**
	 * Namen der Tabelle und des Feldes ermitteln.
	 *
	 * Wenn das allgemein in in der Funktion getMetaTableSQLParts integriert wäre, meckert phpmd eine zu hohe
	 * Komplexität an.
	 *
	 * @param string $name
	 * @return string
	 *
	 * @return array Indezes field, table
	 */
	private function getTableNames($name)
	{
		$return = array();
		if (preg_match('/'.preg_quote(\rex_metainfo_article_handler::PREFIX, '/').'/', $name)) {
			$return['field'] = 'joinArtMeta';
			$return['table'] = 'rex_article_art_meta';
			return $return;
		} elseif (preg_match('/'.preg_quote(\rex_metainfo_category_handler::PREFIX, '/').'/', $name)) {
			$return['field'] = 'joinCatMeta';
			$return['table'] = 'rex_article_cat_meta';
			return $return;
		} elseif (preg_match('/'.preg_quote(\rex_metainfo_media_handler::PREFIX, '/').'/', $name)) {
			$return['field'] = 'joinMedMeta';
			$return['table'] = 'rex_article_med_meta';
			return $return;
		}
		throw new \Exception('Table not valid');
	}

	/**
	 * SQL Parts für die Metadaten innerhalb von Redaxo5 generieren
	 *
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
			try {
				$table = $this->getTableNames($name['name']);
				$fieldname = $table['field'];
				$tablename = $table['table'];

				switch ($name['type']) {
					case 'REX_MEDIA_WIDGET':
						if ($$fieldname != '') {
							$$fieldname .= ' or ';
						}
						$$fieldname .= ''.$tablename.'.'.$name['name'].' = f.filename';
						break;
					case 'REX_MEDIALIST_WIDGET':
						if ($$fieldname != '') {
							$$fieldname .= ' or ';
						}
						$$fieldname .= 'FIND_IN_SET(f.filename, '.$tablename.'.'.$name['name'].')';
						break;
				}
			} catch (\Exception $e) {

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
		$selectMetaNull = ',null as metaArtIDs '.PHP_EOL;
		$selectMetaNotNull = ',group_concat(distinct concat('.
			'rex_article_art_meta.id,"\t",'.
			'rex_article_art_meta.name,"\t",'.
			'rex_article_art_meta.clang_id) Separator "\n") as metaArtIDs '.PHP_EOL;
		$return['additionalSelect'] .= $joinArtMeta == '' ? $selectMetaNull : $selectMetaNotNull;

		if ($joinArtMeta != '') {
			$return['additionalJoins'] .= 'LEFT join rex_article as rex_article_art_meta on '.
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
		$selectMetaNull = ',null as metaCatIDs '.PHP_EOL;
		$selectMetaNotNull = ',group_concat(distinct concat('.
			'rex_article_cat_meta.id,"\t",'.
			'rex_article_cat_meta.catname,"\t",'.
			'rex_article_cat_meta.clang_id,"\t",'.
			'rex_article_cat_meta.parent_id) Separator "\n") as metaCatIDs '.PHP_EOL;

		$return['additionalSelect'] .= $joinCatMeta == '' ? $selectMetaNull : $selectMetaNotNull;

		if ($joinCatMeta != '') {
			$return['additionalJoins'] .= 'LEFT join rex_article as rex_article_cat_meta on '.
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
		$selectMetaNull = ',null as metaMedIDs '.PHP_EOL;
		$selectMetaNotNull = ',group_concat(distinct concat('.
			'rex_article_med_meta.id,"\t",'.
			'rex_article_med_meta.category_id,"\t",'.
			'rex_article_med_meta.filename'.
			') Separator "\n") as metaMedIDs '.PHP_EOL;

		$return['additionalSelect'] .= $joinMedMeta == '' ? $selectMetaNull : $selectMetaNotNull;

		if ($joinMedMeta != '') {
			$return['additionalJoins'] .= 'LEFT join rex_media as rex_article_med_meta on '.
				'(rex_article_med_meta.id is not null and ('.$joinMedMeta.'))'.PHP_EOL;
		}
	}

	/**
	 * Meta-Bildfelder ermitteln.
	 * @return array
	 */
	protected function getMetaNames()
	{
		$rexSQL = $this->getRexSql();
//		$articleTable = $this->getTable('article');
		$metainfoFieldTable = $this->getTable('metainfo_field');
		$metainfoTypeTable = $this->getTable('metainfo_type');

		$sql = <<<SQL
select f.name, t.label as type
from $metainfoFieldTable f
inner join $metainfoTypeTable t on t.id=f.type_id and t.label like '%MEDIA%'

SQL;

		$names = $rexSQL->getArray($sql);

		return $names;
	}
}
