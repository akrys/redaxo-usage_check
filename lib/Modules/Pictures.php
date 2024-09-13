<?php

/**
 * Datei für Medienmodul
 *
 * @author        akrys
 */
namespace FriendsOfRedaxo\UsageCheck\Modules;

use Exception;
use FriendsOfRedaxo\UsageCheck\Enum\ModuleType;
use FriendsOfRedaxo\UsageCheck\Enum\Perm;
use FriendsOfRedaxo\UsageCheck\Lib\BaseModule;
use FriendsOfRedaxo\UsageCheck\Lib\PictureYFrom;
use FriendsOfRedaxo\UsageCheck\Medium;
use FriendsOfRedaxo\UsageCheck\Permission;
use rex_fragment;
use rex_i18n;
use rex_metainfo_article_handler;
use rex_metainfo_category_handler;
use rex_metainfo_media_handler;
use function rex_mediapool_mediaIsInUse;

/**
 * Description of Pictures
 *
 * @author akrys
 * @SuppressWarnings(PHPMD.ExcessiveClassComplexity)
 */
class Pictures extends BaseModule
{
	/**
	 * @var ModuleType
	 */
	const TYPE = ModuleType::PICTURES;

	/**
	 * Yform Integration
	 * @var PictureYFrom
	 */
	private ?PictureYFrom $yform = null;

	/**
	 * Category ID.
	 *
	 * @var int
	 */
	private ?int $catId = null;

	/**
	 * Kategorie setzen
	 *
	 * @param int $id
	 */
	public function setCategory(int $id): void
	{
		$this->catId = $id;
	}

	/**
	 * Nicht genutze Bilder holen
	 *
	 * @retur array<int|string, mixed>
	 *
	 * @todo bei Instanzen mit vielen Dateien im Medienpool testen. Die Query
	 *       riecht nach Performance-Problemen -> 	Using join buffer (Block Nested Loop)
	 */
	public function get(): array
	{
		if (!$this->hasPerm()) {
			return [];
		}

		$rexSQL = $this->getRexSql();

		if (!isset($this->yform)) {
			$this->yform = new PictureYFrom();
			$this->yform->setRexSql($rexSQL);
		}

		$sql = $this->getSQL();
		return ['result' => $rexSQL->getArray($sql), 'fields' => $this->tableFields];
	}

	/**
	 * Details zu einem Eintrag holen
	 * @param int $item_id
	 * @return array<string, mixed>
	 * @SuppressWarnings(CyclomaticComplexity)
	 * @SuppressWarnings(NPathComplexity)
	 */
	public function getDetails(int $item_id): array
	{
		if (!$this->hasPerm()) {
			return [];
		}

		$rexSQL = $this->getRexSql();
		if (!isset($this->yform)) {
			$this->yform = new PictureYFrom();
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
				$index = $articleData['usagecheck_art_id'].'_'.$articleData['usagecheck_art_clang'];
				$result['art_meta'][$index] = $articleData;
			}

			if (isset($articleData['usagecheck_metaCatIDs']) && (int) $articleData['usagecheck_metaCatIDs'] > 0) {
				$index = $articleData['usagecheck_cat_id'].'_'.$articleData['usagecheck_cat_clang'];
				$result['cat_meta'][$index] = $articleData;
			}

			if (isset($articleData['usagecheck_metaMedIDs']) && (int) $articleData['usagecheck_metaMedIDs'] > 0) {
				$result['media_meta'][$articleData['usagecheck_med_id']] = $articleData;
			}

			foreach ($this->tableFields as $table => $field) {
				if (!isset($articleData['usagecheck_'.$table.'_id'])) {
					continue;
				}
				$index = $articleData['usagecheck_'.$table.'_id'];
				$result['yform'][$table][$field[0]['table_out']][$index] = $articleData;
			}
		}
		return [
			'first' => $res[0],
			'result' => $result,
			'fields' => $this->tableFields,
		];
	}

	/**
	 * Spezifisches SQL für redaxo 5
	 * @param int $detail_id
	 * @return string
	 * @SuppressWarnings(PHPMD.ElseExpression)
	 * -> zu tief verschachtelt.... vllt. Funktionsauslagerung?
	 * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
	 */
	protected function getSQL(int $detail_id = null): string
	{
		$sqlPartsYForm = $this->yform?->getYFormTableSQLParts($detail_id);
		$sqlPartsMeta = $this->getMetaTableSQLParts($detail_id);

		$havingClauses = [];
		$additionalSelect = '';
		$additionalJoins = '';
		$additionalGroupBy = '';
		$this->tableFields = [];

		$havingClauses = array_merge($havingClauses, $sqlPartsYForm['havingClauses'] ?? []);
		$additionalSelect .= $sqlPartsYForm['additionalSelect'] ?? '';
		$additionalJoins .= $sqlPartsYForm['additionalJoins'] ?? '';
		$this->tableFields = array_merge($this->tableFields, $sqlPartsYForm['tableFields'] ?? []);

		$havingClauses = array_merge($havingClauses, $sqlPartsMeta['havingClauses'] ?? []);
		$additionalSelect .= $sqlPartsMeta['additionalSelect'];
		$additionalJoins .= $sqlPartsMeta['additionalJoins'];
		$this->tableFields = array_merge($this->tableFields, $sqlPartsMeta['tableFields'] ?? []);

		$additionalGroupBy .= $sqlPartsMeta['groupBy'];

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

 OR s.value1 like concat('%',f.filename,'%')
 OR s.value2 like concat('%',f.filename,'%')
 OR s.value3 like concat('%',f.filename,'%')
 OR s.value4 like concat('%',f.filename,'%')
 OR s.value5 like concat('%',f.filename,'%')
 OR s.value6 like concat('%',f.filename,'%')
 OR s.value7 like concat('%',f.filename,'%')
 OR s.value8 like concat('%',f.filename,'%')
 OR s.value9 like concat('%',f.filename,'%')
 OR s.value10 like concat('%',f.filename,'%')
 OR s.value11 like concat('%',f.filename,'%')
 OR s.value12 like concat('%',f.filename,'%')
 OR s.value13 like concat('%',f.filename,'%')
 OR s.value14 like concat('%',f.filename,'%')
 OR s.value15 like concat('%',f.filename,'%')
 OR s.value16 like concat('%',f.filename,'%')
 OR s.value17 like concat('%',f.filename,'%')
 OR s.value18 like concat('%',f.filename,'%')
 OR s.value19 like concat('%',f.filename,'%')
 OR s.value20 like concat('%',f.filename,'%')
)

left join $articleTable a on (a.id=s.article_id and a.clang_id=s.clang_id)

$additionalJoins

SQL;

		$where = [];
		if (!isset($detail_id)) {
			if (!$this->showAll) {
				$where[] = 's.id is null ';
				$havingClauses[] = ' ifnull(usagecheck_metaCatIDs, 0) = 0 and '.
					'ifnull(usagecheck_metaArtIDs, 0) = 0 and '.
					'ifnull(usagecheck_metaMedIDs, 0) = 0';
			}
			if ($this->catId) {
				$where[] = "f.category_id=".$this->getRexSql()->escape((string) $this->catId)." ";
			}

			if ($where) {
				$sql .= 'where '.implode(' and ', $where);
			}

			$sql .= 'group by f.filename, f.id ';
			if ($additionalGroupBy) {
				$sql .= ','.$additionalGroupBy.' ';
			}

			if (!$this->showAll && count($havingClauses) > 0) {
				$sql .= 'having '.implode(' and ', $havingClauses).'';
			}
		} else {
			$sql .= 'where f.id = '.$this->getRexSql()->escape((string) $detail_id);
		}
		return $sql;
	}

	/**
	 * Felder - Grupppierung
	 * @param int|null $detail_id
	 * @param string $additionalSelect
	 * @return string
	 */
	private function addGroupFields(?int $detail_id, string $additionalSelect): string
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
	 *
	 * @return array<string, mixed> Indezes field, table
	 */
	private function getTableNames(string $name): array
	{
		$return = [];
		if (preg_match('/'.preg_quote(rex_metainfo_article_handler::PREFIX, '/').'/', $name)) {
			$return['field'] = 'joinArtMeta';
			$return['table'] = 'rex_article_art_meta';
			return $return;
		} elseif (preg_match('/'.preg_quote(rex_metainfo_category_handler::PREFIX, '/').'/', $name)) {
			$return['field'] = 'joinCatMeta';
			$return['table'] = 'rex_article_cat_meta';
			return $return;
		} elseif (preg_match('/'.preg_quote(rex_metainfo_media_handler::PREFIX, '/').'/', $name)) {
			$return['field'] = 'joinMedMeta';
			$return['table'] = 'rex_article_med_meta';
			return $return;
		}
		throw new Exception('Table not valid');
	}

	/**
	 * SQL Parts für die Metadaten innerhalb von Redaxo5 generieren
	 *
	 * @param int $detail_id
	 * @return array<string, mixed>
	 * @SuppressWarnings(PHPMD.ElseExpression)
	 */
	private function getMetaTableSQLParts(int $detail_id = null): array
	{
		$return = [
			'additionalSelect' => '',
			'additionalJoins' => '',
			'tableFields' => [],
			'havingClauses' => [],
			'groupBy' => '',
		];

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
			} catch (Exception $e) {
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
	 * @param array<string, mixed> &$return
	 * @param string $joinArtMeta
	 * @param int $detail_id
	 * @SuppressWarnings(PHPMD.ElseExpression)
	 * -> zu tief verschachtelt.... vllt. Funktionsauslagerung?
	 */
	private function addArtSelectAndJoinStatements(array &$return, string $joinArtMeta, ?int $detail_id = null): void
	{
		$selectMetaNull = ',0 as usagecheck_metaArtIDs '.PHP_EOL;
		if (!$detail_id) {
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

			$return['groupBy'] .= 'rex_article_art_meta.id,rex_article_cat_meta.id';
		}
	}

	/**
	 * Select und Joinstatments im Array anfügen
	 *
	 * Komplexitätsvermeidung von getMetaTableSQLParts
	 *
	 * @param array<string, mixed> &$return
	 * @param string $joinCatMeta
	 * @param int $detail_id
	 * @SuppressWarnings(PHPMD.ElseExpression)
	 * -> zu tief verschachtelt.... vllt. Funktionsauslagerung?
	 */
	private function addCatSelectAndJoinStatements(array &$return, string $joinCatMeta, int $detail_id = null): void
	{
		$selectMetaNull = ',0 as usagecheck_metaCatIDs '.PHP_EOL;
		if (!$detail_id) {
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
	 * @param array<string, mixed> &$return
	 * @param string $joinMedMeta
	 * @param int $detail_id
	 * @SuppressWarnings(PHPMD.ElseExpression)
	 * -> zu tief verschachtelt.... vllt. Funktionsauslagerung?
	 */
	private function addMedSelectAndJoinStatements(array &$return, string $joinMedMeta, int $detail_id = null): void
	{
		$selectMetaNull = ',0 as usagecheck_metaMedIDs '.PHP_EOL;
		if (!$detail_id) {
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
	 * @return array<int, array<string,mixed>>
	 */
	private function getMetaNames(): array
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

	/**
	 * Anzeige Benutzt/Nicht benutzt erstellen
	 * @param array<string, mixed> $item
	 * @param array<string, mixed> $fields
	 * @return string
	 * @SuppressWarnings(CyclomaticComplexity)
	 * @SuppressWarnings(NPathComplexity)
	 * @SuppressWarnings(PHPMD.ElseExpression)
	 * -> zu tief verschachtelt.... vllt. Funktionsauslagerung?
	 */
	public static function showUsedInfo(array $item, array $fields): string
	{
		$return = '';
		$used = false;

		if (isset($item['count']) && $item['count'] > 0) {
			$used = true;
		}

		foreach ($fields as $tablename => $field) {
			unset($field); //phpmd: unused variable $field
			if (isset($item[$tablename])) {
				$used = true;
				break;
			}

			if (isset($item['usagecheck_'.$tablename.'_id']) && $item['usagecheck_'.$tablename.'_id']) {
				$used = true;
				break;
			}
		}

		if (isset($item['usagecheck_s_id']) && $item['usagecheck_s_id'] > 0) {
			$used = true;
		}

		if (isset($item['usagecheck_metaArtIDs']) && $item['usagecheck_metaArtIDs'] > 0) {
			$used = true;
		}

		if (isset($item['usagecheck_metaCatIDs']) && $item['usagecheck_metaCatIDs'] > 0) {
			$used = true;
		}

		if (isset($item['usagecheck_metaMedIDs']) && $item['usagecheck_metaMedIDs'] > 0) {
			$used = true;
		}

		$errors = [];
		if ($used === false) {
			$errors[] = rex_i18n::rawMsg('akrys_usagecheck_images_msg_not_used');
		}

		if (!Medium::exists($item)) {
			$errors[] = rex_i18n::rawMsg('akrys_usagecheck_images_msg_not_found');
		}

		//Ob ein Medium lt. Medienpool in Nutzung ist, brauchen wir nur zu prüfen,
		//wenn wir glauben, dass die Datei ungenutzt ist.
		//Vielleicht wird sie ja dennoch verwendet ;-)
		//
		//Hier wird die Funktion verwendet, die auch beim Löschen von Medien aus dem Medienpool aufgerufen
		//wird.
		//
		//ACHTUNG:
		//XAMPP 5.6.14-4 mit MariaDB unter MacOS hat ein falsch kompiliertes PCRE-Mdoul an Bord, so dass
		//alle REGEXP-Abfragen abstürzen.
		//Der Fehler liegt also nicht hier, und auch nicht im Redaxo-Core
		if (!$used) {
			$used = rex_mediapool_mediaIsInUse($item['filename']);

			if ($used) {
				$errors[] = rex_i18n::rawMsg('akrys_usagecheck_images_msg_in_use');
			}
		}

		if (count($errors) > 0) {
			$fragment = new rex_fragment(['msg' => $errors]);
			$return = $fragment->parse('msg/error_box.php');
		} else {
			$fragment = new rex_fragment(['msg' => [rex_i18n::rawMsg('akrys_usagecheck_images_msg_used')]]);
			$return = $fragment->parse('msg/info_box.php');
		}
		return $return;
	}

	/**
	 * Rechte prüfen
	 * @return bool
	 */
	public function hasPerm():bool
	{
		return Permission::getInstance()->check(Perm::PERM_MEDIA);
	}
}
