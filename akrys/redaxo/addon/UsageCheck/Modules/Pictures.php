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
	extends \akrys\redaxo\addon\UsageCheck\Lib\BaseModule
{
	const TYPE = 'media';

	/**
	 * Yform Integration
	 * @var \akrys\redaxo\addon\UsageCheck\Lib\PictureYFrom
	 */
	private $yform = null;

	/**
	 * Tabellenfelder
	 * @var array
	 */
	private $tableFields;

	/**
	 * Nicht genutze Bilder holen
	 *
	 * @return array
	 *
	 * @todo bei Instanzen mit vielen Dateien im Medienpool testen. Die Query
	 *       riecht nach Performance-Problemen -> 	Using join buffer (Block Nested Loop)
	 */
	public function get()
	{
		if (!Permission::getInstance()->check(Permission::PERM_MEDIA)) {
			return false;
		}

		$rexSQL = $this->getRexSql();

		if (!isset($this->yform)) {
			$this->yform = new \akrys\redaxo\addon\UsageCheck\Lib\PictureYFrom($this);
			$this->yform->setRexSql($rexSQL);
		}

		$sql = $this->getSQL();
		return array('result' => $rexSQL->getArray($sql), 'fields' => $this->tableFields);
	}

	/**
	 * Details zu einem Eintrag holen
	 * @param int $item_id
	 * @return array
	 */
	public function getDetails($item_id)
	{
		if (!Permission::getInstance()->check(Permission::PERM_MEDIA)) {
			return false;
		}

		$rexSQL = $this->getRexSql();
		if (!isset($this->yform)) {
			$this->yform = new \akrys\redaxo\addon\UsageCheck\Lib\PictureYFrom($this);
			$this->yform->setRexSql($rexSQL);
		}

		$sql = $this->getSQL($item_id);
		$res = $rexSQL->getArray($sql);
		$result = [];
		foreach ($res as $articleData) {
			if (isset($articleData['usagecheck_s_id']) && (int) $articleData['usagecheck_s_id'] > 0) {
				$result['slices'][$articleData['usagecheck_s_id']] = $articleData;
			}

			if (isset($articleData['usagecheck_metaArtIDs']) && (int) $articleData['usagecheck_metaArtIDs'] > 0) {
				$result['art_meta'][$articleData['usagecheck_art_id'].'_'.$articleData['usagecheck_art_clang']] = $articleData;
			}

			if (isset($articleData['usagecheck_metaCatIDs']) && (int) $articleData['usagecheck_metaCatIDs'] > 0) {
				$result['cat_meta'][$articleData['usagecheck_cat_id'].'_'.$articleData['usagecheck_cat_clang']] = $articleData;
			}

			if (isset($articleData['usagecheck_metaMedIDs']) && (int) $articleData['usagecheck_metaMedIDs'] > 0) {
				$result['media_meta'][$articleData['usagecheck_med_id']] = $articleData;
			}

			foreach ($this->tableFields as $table => $field) {
				if (!isset($articleData['usagecheck_'.$table.'_id'])) {
					continue;
				}
				$result['yform'][$table][$field[0]['table_out']][$articleData['usagecheck_'.$table.'_id']] = $articleData;
			}
		}
		return [
			'first' => $res[0],
			'result' => $result,
			'fields' => $this->tableFields,
		];
	}
//
///////////////////// Tmplementation aus RexV5 /////////////////////
//

	/**
	 * Spezifisches SQL für redaxo 5
	 * @param int $detail_id
	 * @return string
	 */
	protected function getSQL(/* int */$detail_id = null)
	{
		$sqlPartsYForm = $this->yform->getYFormTableSQLParts($detail_id);
		$sqlPartsMeta = $this->getMetaTableSQLParts($detail_id);

		$havingClauses = array();
		$additionalSelect = '';
		$additionalJoins = '';
		$this->tableFields = array();


		$havingClauses = array_merge($havingClauses, $sqlPartsYForm['havingClauses']);
		$additionalSelect .= $sqlPartsYForm['additionalSelect'];
		$additionalJoins .= $sqlPartsYForm['additionalJoins'];
		$this->tableFields = array_merge($this->tableFields, $sqlPartsYForm['tableFields']);

		$havingClauses = array_merge($havingClauses, $sqlPartsMeta['havingClauses']);
		$additionalSelect .= $sqlPartsMeta['additionalSelect'];
		$additionalJoins .= $sqlPartsMeta['additionalJoins'];
		$this->tableFields = array_merge($this->tableFields, $sqlPartsMeta['tableFields']);


		$mediaTable = $this->getTable('media');
		$articleSliceTable = $this->getTable('article_slice');
		$articleTable = $this->getTable('article');

		$sql = 'SELECT f.*';
		$sql .= $this->addGroupFields($detail_id, $additionalSelect);
		$sql .= <<<SQL

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

		if (!isset($detail_id)) {
			if (!$this->showAll) {
				$sql .= 'where s.id is null ';
				$havingClauses[] = ' ifnull(usagecheck_metaCatIDs, 0) = 0 and ifnull(usagecheck_metaArtIDs, 0) = 0 and ifnull(usagecheck_metaMedIDs, 0) = 0';
			}

			$sql .= 'group by f.filename ';
			if (!$this->showAll && isset($havingClauses) && count($havingClauses) > 0) {
				$sql .= 'having '.implode(' and ', $havingClauses).'';
			}
		} else {
			$sql .= 'where f.id = '.$this->getRexSql()->escape($detail_id);
		}
		return $sql;
	}

	/**
	 * Felder - Grupppierung
	 * @param int $detail_id
	 * @param string $additionalSelect
	 * @return string
	 */
	private function addGroupFields($detail_id, $additionalSelect)
	{
		if (isset($detail_id)) {
			return <<<SQL
	,
	s.id as usagecheck_s_id,
	s.article_id as usagecheck_s_article_id,
	a.name as usagecheck_a_name,
	s.clang_id as usagecheck_s_clang_id,
	s.ctype_id as usagecheck_s_ctype_id

	$additionalSelect
SQL;
		}


		return <<<SQL
, count(s.id) as count
		, group_concat(distinct concat(
	cast(s.id as char),"\\t",
	cast(s.article_id as char),"\\t",
	a.name,"\\t",
	cast(s.clang_id as char),"\\t",
	cast(s.ctype_id as char)
) Separator "\\n") as slice_data

$additionalSelect

SQL;
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
	 * @param int $detail_id
	 * @return array
	 * @SuppressWarnings(PHPMD.ElseExpression)
	 */
	private function getMetaTableSQLParts(/* int */ $detail_id = null)
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
				//;
			}
		}

		$this->addArtSelectAndJoinStatements($return, $joinArtMeta, $detail_id);
		$this->addCatSelectAndJoinStatements($return, $joinCatMeta, $detail_id);
		$this->addMedSelectAndJoinStatements($return, $joinMedMeta, $detail_id);

		return $return;
	}

	/**
	 * Select und Joinstatments im Array anfügen
	 *
	 * Komplexitätsvermeidung von getMetaTableSQLParts
	 *
	 * @param array &$return
	 * @param string $joinArtMeta
	 * @param int $detail_id
	 */
	private function addArtSelectAndJoinStatements(&$return, $joinArtMeta, $detail_id = null)
	{
		$selectMetaNull = ',0 as usagecheck_metaArtIDs '.PHP_EOL;
		if (!$detail_id) {
//			$selectMetaNotNull = ',group_concat(distinct concat('.
//				'rex_article_art_meta.id,"\t",'.
//				'rex_article_art_meta.name,"\t",'.
//				'rex_article_art_meta.clang_id,"\t") Separator "\n") as usagecheck_metaArtIDs '.PHP_EOL;

			$selectMetaNotNull = ',ifnull(rex_article_art_meta.id,0) as usagecheck_metaArtIDs '.PHP_EOL;
		} else {
			$selectMetaNotNull = <<<SQL
				, rex_article_art_meta.id is not null as usagecheck_metaArtIDs
				,rex_article_art_meta.id usagecheck_art_id,
				rex_article_art_meta.name usagecheck_art_name,
				rex_article_art_meta.clang_id usagecheck_art_clang
SQL;
		}
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
	 * @param int $detail_id
	 */
	private function addCatSelectAndJoinStatements(&$return, $joinCatMeta, /* int */ $detail_id = null)
	{
		$selectMetaNull = ',0 as usagecheck_metaCatIDs '.PHP_EOL;
		if (!$detail_id) {
//			$selectMetaNotNull = ',group_concat(distinct concat('.
//				'rex_article_cat_meta.id,"\t",'.
//				'rex_article_cat_meta.catname,"\t",'.
//				'rex_article_cat_meta.clang_id,"\t",'.
//				'rex_article_cat_meta.parent_id) Separator "\n") as usagecheck_metaCatIDs '.PHP_EOL;

			$selectMetaNotNull = ',ifnull(rex_article_cat_meta.id,0) as usagecheck_metaCatIDs '.PHP_EOL;
		} else {
			$selectMetaNotNull = <<<SQL
				, rex_article_cat_meta.id is not null as usagecheck_metaCatIDs
				,rex_article_cat_meta.id usagecheck_cat_id,
				rex_article_cat_meta.catname usagecheck_cat_name,
				rex_article_cat_meta.clang_id usagecheck_cat_clang,
				rex_article_cat_meta.parent_id usagecheck_cat_parent_id
SQL;
		}

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
	 * @param int $detail_id
	 */
	private function addMedSelectAndJoinStatements(&$return, $joinMedMeta, /* int */ $detail_id = null)
	{
		$selectMetaNull = ',0 as usagecheck_metaMedIDs '.PHP_EOL;
		if (!$detail_id) {
//			$selectMetaNotNull = ',group_concat(distinct concat('.
//				'rex_article_med_meta.id,"\t",'.
//				'rex_article_med_meta.category_id,"\t",'.
//				'rex_article_med_meta.filename'.
//				') Separator "\n") as usagecheck_metaMedIDs '.PHP_EOL;

			$selectMetaNotNull = ',ifnull(rex_article_med_meta.id,0) as usagecheck_metaMedIDs '.PHP_EOL;
		} else {
			$selectMetaNotNull = <<<SQL
				,rex_article_med_meta.id is not null as usagecheck_metaMedIDs
				,rex_article_med_meta.id usagecheck_med_id,
				rex_article_med_meta.category_id usagecheck_med_cat_id,
				rex_article_med_meta.filename usagecheck_med_filename
SQL;
		}
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
	private function getMetaNames()
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
