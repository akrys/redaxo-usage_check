<?php

/**
 * Datei für ...
 *
 * @author        akrys
 */
namespace FriendsOfRedaxo\UsageCheck\Lib;

use FriendsOfRedaxo\UsageCheck\Lib\RexBase;
use rex;
use rex_addon;
use rex_plugin;

/**
 * Description of PictureYFrom
 *
 * @author akrys
 */
class PictureYFrom extends RexBase
{

	/**
	 * SQL Partsfür YForm generieren.
	 *
	 * @param int $detail_id
	 * @return array<string, mixed>
	 * @SuppressWarnings(PHPMD.ElseExpression)
	 * -> zu tief verschachtelt.... vllt. Funktionsauslagerung?
	 */
	public function getYFormTableSQLParts(int $detail_id = null): array
	{
		$return = [
			'additionalSelect' => '',
			'additionalJoins' => '',
			'tableFields' => [],
			'havingClauses' => [],
		];

		$tables = $this->getYFormSQL();

		$xTables = [];
		foreach ($tables as $table) {
			$xTables[$table['table_name']][] = [
				'name' => $table['f1'],
				'name_out' => $table['f2'],
				'table_out' => $table['table_out'],
				'type' => $table['type_name'],
				//in YForm 2, muss man prüfen, ob be_media einen multiple modifier hat.
				//siehe Kommentare in \FriendsOfRedaxo\addon\UsageCheck\RexV5\Modules\Pictures::getYFormSQL
				'multiple' => (isset($table['multiple']) && $table['multiple'] == '1'),
			];
		}

		foreach ($xTables as $tableName => $fields) {
			if (!$detail_id) {
				$return['additionalSelect'] .= ', group_concat(distinct '.$tableName.'.id';
				$return['additionalSelect'] .= ' Separator "\n") as '.$tableName.PHP_EOL;
			} else {
				$return['additionalSelect'] .= ', '.$tableName.'.id as usagecheck_'.$tableName.'_id'.PHP_EOL;
			}
			$return['additionalJoins'] .= 'LEFT join '.$tableName.' on (';

			foreach ($fields as $key => $field) {
				if ($key > 0) {
					$return['additionalJoins'] .= ' OR ';
				}

				$return['additionalJoins'] .= $this->getJoinCondition($field, (string) $tableName);
			}

			$return['tableFields'][$tableName] = $fields;
			$return['additionalJoins'] .= ')'.PHP_EOL;
			$return['havingClauses'][] = $tableName.' IS NULL';
		}
		return $return;
	}

	/**
	 * YFormTables holen
	 *
	 * @return array<int, array<string, bool|float|int|string|null>>
	 */
	public function getYFormSQL(): array
	{
		$tabels = [];

		$rexSQL = $this->getRexSql();

		if (!rex_addon::get('yform')->isAvailable()) {
			return $tabels;
		}

		if (!rex_plugin::get('yform', 'manager')->isAvailable()) {
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
where f.type_name in ('be_media','be_media_preview','be_medialist','mediafile','imagelist','custom_link')
SQL;
		$yformtableExists = count($yformfield) > 0;
		$yformfieldExists = count($yformtable) > 0;

		if ($yformfieldExists && $yformtableExists) {
			$tabels = $rexSQL->getArray($sql);
		}
		return $tabels;
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
	 * @param array<string, mixed> $dbs
	 * @returns boolean
	 * @SuppressWarnings(PHPMD.StaticAccess)
	 */
	private function hasMultiple(string $yformFieldTable, ?array $dbs = null): bool
	{
		$rexSQL = $this->getRexSql();

		if (!isset($dbs)) { // Normalfall, wenn wir nicht gerade Unit-Tests laufen lassen
			$dbs = rex::getProperty('db');
		}

		$where = [];
		$params = [];
		foreach ($dbs as $db) {
			if (isset($db['name']) && $db['name'] != '') {
				$where[] = "(TABLE_NAME=? and TABLE_SCHEMA=? and COLUMN_NAME='multiple')";
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
	 * Auslagerung der Join-Conditions
	 *
	 * PHPMD meckert eine zu hohe Komplexität an. (11 satt der maximalen 10)
	 * Das dürfte an der Anpassung zu YForm 2 liegen, da dort in be_media nun mehrere Dateien angegeben werden dürfen.
	 * Die Prüfung auf $field['multiple'] ist dann eine ebene zu tief.
	 *
	 * @param array<string, mixed> $field
	 * @param string $tableName
	 * @return string
	 */
	private function getJoinCondition(array $field, string $tableName): string
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
			case 'be_media_preview': // YForm Field Addon
			case 'imagelist':
			case 'custom_link':
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
}
