<?php

/**
 * Datei für Medienmodul
 *
 * @version       1.0 / 2015-08-08
 * @author        akrys
 */
namespace akrys\redaxo\addon\UsageCheck\Modules;

require_once __DIR__.'/../Permission.php';

/**
 * Description of Pictures
 *
 * @author akrys
 */
abstract class Pictures
{

	/**
	 * Redaxo-Spezifische Version wählen.
	 * @return \akrys\redaxo\addon\UsageCheck\Modules\Pictures
	 * @throws \akrys\redaxo\addon\UsageCheck\Exception\FunctionNotCallableException
	 */
	public static function create()
	{
		$object = null;
		switch (\akrys\redaxo\addon\UsageCheck\RedaxoCall::getRedaxoVersion()) {
			case \akrys\redaxo\addon\UsageCheck\RedaxoCall::REDAXO_VERSION_4:
				require_once __DIR__.'/../RexV4/Modules/Pictures.php';
				$object = new \akrys\redaxo\addon\UsageCheck\RexV4\Modules\Pictures();
				break;
			case \akrys\redaxo\addon\UsageCheck\RedaxoCall::REDAXO_VERSION_5:
				require_once __DIR__.'/../RexV5/Modules/Pictures.php';
				$object = new \akrys\redaxo\addon\UsageCheck\RexV5\Modules\Pictures();
				break;
		}

		if (!isset($object)) {
			require_once __DIR__.'/../Exception/FunctionNotCallableException.php';
			throw new \akrys\redaxo\addon\UsageCheck\Exception\FunctionNotCallableException();
		}

		return $object;
	}

	/**
	 * Nicht genutze Bilder holen
	 *
	 * @param boolean $show_all
	 * @return array
	 *
	 * @todo bei Instanzen mit vielen Dateien im Medienpool testen. Die Query
	 *       riecht nach Performance-Problemen -> 	Using join buffer (Block Nested Loop)
	 */
	public function getPictures($show_all = false)
	{

		if (!\akrys\redaxo\addon\UsageCheck\Permission::check(\akrys\redaxo\addon\UsageCheck\Permission::PERM_MEDIA)) {
			return false;
		}

		if (\akrys\redaxo\addon\UsageCheck\RedaxoCall::getRedaxoVersion() == \akrys\redaxo\addon\UsageCheck\RedaxoCall::REDAXO_VERSION_4) {
			$rexSQL = new \rex_sql;
		} else {
			$rexSQL = \rex_sql::factory();
		}

		$sqlPartsXForm = $this->getXFormTableSQLParts();
		$sqlPartsMeta = $this->getMetaTableSQLParts();

		$havingClauses = array();
		$additionalSelect = '';
		$additionalJoins = '';
		$tableFields = array();


		$havingClauses = array_merge($havingClauses, $sqlPartsXForm['havingClauses']);
		$additionalSelect .= $sqlPartsXForm['additionalSelect'];
		$additionalJoins.= $sqlPartsXForm['additionalJoins'];
		$tableFields = array_merge($tableFields, $sqlPartsXForm['tableFields']);

		$havingClauses = array_merge($havingClauses, $sqlPartsMeta['havingClauses']);
		$additionalSelect .= $sqlPartsMeta['additionalSelect'];
		$additionalJoins .= $sqlPartsMeta['additionalJoins'];
		$tableFields = array_merge($tableFields, $sqlPartsMeta['tableFields']);

		$sql = $this->getPictureSQL($additionalSelect, $additionalJoins);

		if (!$show_all) {
			$sql.='where s.id is null ';
			$havingClauses[] = 'metaCatIDs is null and metaArtIDs is null and metaMedIDs is null';
		}

		$sql.='group by f.filename ';

		if (!$show_all && isset($havingClauses) && count($havingClauses) > 0) {
			$sql.='having '.implode(' and ', $havingClauses).'';
		}

		return array('result' => $rexSQL->getArray($sql), 'fields' => $tableFields);
	}

	/**
	 * SQL Parts für die Metadaten generieren
	 * @return array
	 */
	protected abstract function getMetaTableSQLParts();

	/**
	 * Meta-Bildfelder ermitteln.
	 * @return array
	 */
	protected abstract function getMetaNames();

	/**
	 * SQL Partsfür XForm/YForm generieren.
	 *
	 * @return array
	 */
	protected function getXFormTableSQLParts()
	{
		$return = array(
			'additionalSelect' => '',
			'additionalJoins' => '',
			'tableFields' => array(),
			'havingClauses' => array(),
		);

		switch (\akrys\redaxo\addon\UsageCheck\RedaxoCall::getRedaxoVersion()) {
			case \akrys\redaxo\addon\UsageCheck\RedaxoCall::REDAXO_VERSION_4:
				$rexSQL = new \rex_sql;
				break;
			case \akrys\redaxo\addon\UsageCheck\RedaxoCall::REDAXO_VERSION_5:
				$rexSQL = \rex_sql::factory();
				break;
			default:
				throw new \Exception('no database class created');
				break;
		}

		$tables = $this->getXFormSQL($return);

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
					case 'be_mediapool': // Redaxo 4
					case 'be_media': // Redaxo 5
					case 'mediafile':
						$return['additionalJoins'].=$tableName.'.'.$field['name'].' = f.filename';
						break;
					case 'be_medialist':
						$return['additionalJoins'].='FIND_IN_SET(f.filename, '.$tableName.'.'.$field['name'].')';
						break;
				}
			}

			$return['tableFields'][$tableName] = $fields;
			$return['additionalJoins'].=')'.PHP_EOL;
			$return['additionalSelect'].=' Separator "\n") as '.$tableName.PHP_EOL;
			$return['havingClauses'][] = $tableName.' IS NULL';
		}


		return $return;
	}

	/**
	 * XFormTables holen
	 *
	 * @return array
	 * @param array &$return
	 */
	protected abstract function getXFormSQL(&$return);

	/**
	 * Dateigröße ermitteln.
	 *
	 * Die Größe in Byte auszugeben ist nicht gerade übersichtlich. Daher wird
	 * hier versucht den Wert in der größt-möglichen Einheit zu ermittln.
	 *
	 * @param array $item wichtige Indezes: filesize
	 * @return string
	 */
	public function getSizeOut($item)
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
	public abstract function exists($item);

	/**
	 * Spezifisches SQL
	 * @param string $additionalSelect
	 * @param string $additionalJoins
	 * @return string
	 */
	protected abstract function getPictureSQL($additionalSelect, $additionalJoins);

	/**
	 * Holt ein Medium-Objekt mit Prüfung der Rechte
	 *
	 * @param array $item Idezes: category_id, filename
	 * @return \rex_media
	 * @throws \akrys\redaxo\addon\UsageCheck\Exception\FunctionNotCallableException
	 */
	public abstract function getMedium($item);

	/**
	 * Bildvorschau ausgeben
	 *
	 * @return void
	 * @param array $item Ein Element der Ergebnismenge
	 */
	public abstract function outputImagePreview($item);

	/**
	 * Menü ausgeben
	 * @return void
	 * @param string $subpage
	 * @param string $showAllParam
	 * @param string $showAllLinktext
	 */
	public abstract function outputMenu($subpage, $showAllParam, $showAllLinktext);
}
