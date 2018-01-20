<?php

/**
 * Datei für Medienmodul
 *
 * @version       1.0 / 2015-08-08
 * @author        akrys
 */
namespace akrys\redaxo\addon\UsageCheck\Modules;

use \akrys\redaxo\addon\UsageCheck\RedaxoCall;
use \akrys\redaxo\addon\UsageCheck\Permission;

/**
 * Description of Pictures
 *
 * @author akrys
 */
abstract class Pictures
{
	/**
	 * Anzeigemodus für "Alle Anzeigen"
	 * @var boolean
	 */
	private $showAll = false;

	/**
	 * Redaxo-Spezifische Version wählen.
	 * @return \akrys\redaxo\addon\UsageCheck\Modules\Pictures
	 * @throws \akrys\redaxo\addon\UsageCheck\Exception\FunctionNotCallableException
	 * @SuppressWarnings(PHPMD.StaticAccess)
	 */
	public static function create()
	{
		$object = null;
		switch (RedaxoCall::getRedaxoVersion()) {
			case RedaxoCall::REDAXO_VERSION_5:
				$object = new \akrys\redaxo\addon\UsageCheck\RexV5\Modules\Pictures();
				break;
		}

		if (!isset($object)) {
			throw new \akrys\redaxo\addon\UsageCheck\Exception\FunctionNotCallableException();
		}

		return $object;
	}

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

		if (!Permission::getVersion()->check(Permission::PERM_MEDIA)) {
			return false;
		}

		$rexSQL = RedaxoCall::getAPI()->getSQL();

		$sqlPartsXForm = $this->getXFormTableSQLParts();
		$sqlPartsMeta = $this->getMetaTableSQLParts();

		$havingClauses = array();
		$additionalSelect = '';
		$additionalJoins = '';
		$tableFields = array();


		$havingClauses = array_merge($havingClauses, $sqlPartsXForm['havingClauses']);
		$additionalSelect .= $sqlPartsXForm['additionalSelect'];
		$additionalJoins .= $sqlPartsXForm['additionalJoins'];
		$tableFields = array_merge($tableFields, $sqlPartsXForm['tableFields']);

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
	 * SQL Parts für die Metadaten generieren
	 * @return array
	 */
	abstract protected function getMetaTableSQLParts();

	/**
	 * Meta-Bildfelder ermitteln.
	 * @return array
	 */
	abstract protected function getMetaNames();

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

		RedaxoCall::getAPI()->getSQL();

		$tables = $this->getXFormSQL($return);

		$xTables = array();
		foreach ($tables as $table) {
			$xTables[$table['table_name']][] = array(
				'name' => $table['f1'],
				'name_out' => $table['f2'],
				'table_out' => $table['table_out'],
				'type' => $table['type_name'],
				//in YForm 2, muss man prüfen, ob be_media einen multiple modifier hat.
				//siehe Kommentare in \akrys\redaxo\addon\UsageCheck\RexV5\Modules\Pictures::getXFormSQL
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
					//siehe Kommentare in \akrys\redaxo\addon\UsageCheck\RexV5\Modules\Pictures::getXFormSQL
					$joinCondition = 'FIND_IN_SET(f.filename, '.$tableName.'.'.$field['name'].')';
				}
				break;
		}
		return $joinCondition;
	}

	/**
	 * XFormTables holen
	 *
	 * @return array
	 * @param array &$return
	 */
	abstract protected function getXFormSQL(&$return);

	/**
	 * kleinste Speichereinheit ermittln.
	 *
	 * Dabei zählen, wie oft man sie verkleinern konnte. Daraus ergibt sich die Einheit.
	 *
	 * @param int $size
	 * @return array Indezes: index, size
	 */
	private function getSizeReadable($size)
	{
		$return = array(
			'index' => 0,
			'size' => $size,
		);

		$return['index'] = 0;

		while ($return['size'] > 1024 && $return['index'] <= 6) {
			$return['index'] ++;
			$return['size'] /= 1024;
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
	public function getSizeOut($item)
	{
		$value = $this->getSizeReadable($item['filesize']);

		$value['size'] = round($value['size'], 2);
		switch ($value['index']) {
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

		return $value['size'].' '.$unit;
	}

	/**
	 * Überprüfen, ob eine Datei existiert.
	 *
	 * @global type $REX
	 * @param array $item
	 * @return boolean
	 */
	abstract public function exists($item);

	/**
	 * Spezifisches SQL
	 * @param string $additionalSelect
	 * @param string $additionalJoins
	 * @return string
	 */
	abstract protected function getPictureSQL($additionalSelect, $additionalJoins);

	/**
	 * Holt ein Medium-Objekt mit Prüfung der Rechte
	 *
	 * @param array $item Idezes: category_id, filename
	 * @return \rex_media
	 * @throws \akrys\redaxo\addon\UsageCheck\Exception\FunctionNotCallableException
	 */
	abstract public function getMedium($item);

	/**
	 * Bildvorschau ausgeben
	 *
	 * @return void
	 * @param array $item Ein Element der Ergebnismenge
	 */
	abstract public function outputImagePreview($item);

	/**
	 * Menü URL generieren
	 * @return string
	 * @param string $subpage
	 * @param string $showAllParam
	 */
	abstract public function getMeuLink($subpage, $showAllParam);

	/**
	 * Menü ausgeben
	 * @return void
	 * @param string $subpage
	 * @param string $showAllParam
	 * @param string $showAllLinktext
	 */
	public function outputMenu($subpage, $showAllParam, $showAllLinktext)
	{
		$url = $this->getMeuLink($subpage, $showAllParam);
		$menu = new \rex_fragment([
			'url' => $url,
			'linktext' => $showAllLinktext,
			'texts' => [
				\akrys\redaxo\addon\UsageCheck\RedaxoCall::getAPI()->getI18N('akrys_usagecheck_images_intro_text'),
			],
		]);
		return $menu->parse('fragments/menu/linktext.php');
	}
}
