<?php

/**
 * Datei für die Modul-Actions
 *
 * @version       1.0 / 2015-08-09
 * @author        akrys
 */
namespace FriendsOfRedaxo\addon\UsageCheck\Modules;

use FriendsOfRedaxo\addon\UsageCheck\Lib\BaseModule;
use FriendsOfRedaxo\addon\UsageCheck\Permission;
use rex_sql;

/**
 * Description of Modules
 *
 * @author akrys
 */
class Actions
	extends BaseModule
{
	const TYPE = 'actions';

	/**
	 * Nicht genutze Module holen
	 *
	 * @return array
	 *
	 * @todo bei Instanzen mit vielen Slices testen. Die Query
	 *       riecht nach Performance-Problemen -> 	Using join buffer (Block Nested Loop)
	 */
	public function get()
	{
		if (!Permission::getInstance()->check(Permission::PERM_MODUL)) {
			return false;
		}

		$rexSQL = $this->getRexSql();
		$sql = $this->getSQL();
		return $rexSQL->getArray($sql);
	}

	/**
	 * Details zu einem Eintrag holen
	 * @param int $item_id
	 * @return array
	 */
	public function getDetails($item_id)
	{
		if (!Permission::getInstance()->check(Permission::PERM_MODUL)) {
			return false;
		}
		$result = [];

		$rexSQL = $this->getRexSql();
		$sql = $this->getSQL($item_id);
		$res = $rexSQL->getArray($sql);

		foreach ($res as $articleData) {
			if (isset($articleData['usagecheck_ma_module']) && (int) $articleData['usagecheck_ma_module'] > 0) {
				$result['action'][$articleData['usagecheck_ma_module']] = $articleData;
			}
		}
		return [
			'first' => $res[0],
			'result' => $result,
			'fields' => $this->tableFields,
		];
	}

	/**
	 * SQL generieren
	 * @param int $detail_id
	 * @return string
	 */
	protected function getSQL(/* int */$detail_id = null)
	{
		$rexSQL = rex_sql::factory();
		$additionalFields = '';
		$where = '';
		$whereArray = [];
		$groupBy = 'group by a.id';

		if ($detail_id) {
			$groupBy = '';
			$additionalFields = <<<SQL
,ma.module_id as usagecheck_ma_module,
m.name as usage_check_m_name
SQL;
			$whereArray[] = 'a.id='.$rexSQL->escape($detail_id);
		} else {
			$where = '';
			if (!$this->showAll) {
				$whereArray[] = 'ma.id is null';
			}

			$additionalFields = ', ma.module_id as modul';
		}

		if (count($whereArray) > 0) {
			$where = 'where '.implode(' and ', $whereArray);
		}

		//Keine integer oder Datumswerte in einem concat!
		//Vorallem dann nicht, wenn MySQL < 5.5 im Spiel ist.
		// -> https://stackoverflow.com/questions/6397156/why-concat-does-not-default-to-default-charset-in-mysql/6669995#6669995
		$actionTable = $this->getTable('action');
		$moduleActionTable = $this->getTable('module_action');
		$moduleTable = $this->getTable('module');

		$sql = <<<SQL
SELECT a.*
$additionalFields
FROM $actionTable a
left join $moduleActionTable ma on ma.action_id=a.id
left join $moduleTable m on ma.module_id=m.id or m.id is null

$where

$groupBy

SQL;
		return $sql;
	}
}
