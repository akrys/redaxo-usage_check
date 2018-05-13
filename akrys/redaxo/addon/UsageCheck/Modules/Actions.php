<?php

/**
 * Datei für die Modul-Actions
 *
 * @version       1.0 / 2015-08-09
 * @author        akrys
 */
namespace akrys\redaxo\addon\UsageCheck\Modules;

use \akrys\redaxo\addon\UsageCheck\Permission;

/**
 * Description of Modules
 *
 * @author akrys
 */
class Actions
	extends \akrys\redaxo\addon\UsageCheck\Lib\BaseModule
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

		$rexSQL = $this->getRexSql();
		$sql = $this->getSQL($item_id);
		return $rexSQL->getArray($sql);
	}

	/**
	 * SQL generieren
	 * @param int $detail_id
	 * @return string
	 */
	protected function getSQL(/* int */$detail_id = null)
	{
		$where = '';
		if (!$this->showAll) {
			$where .= 'where ma.id is null';
		}

		//Keine integer oder Datumswerte in einem concat!
		//Vorallem dann nicht, wenn MySQL < 5.5 im Spiel ist.
		// -> https://stackoverflow.com/questions/6397156/why-concat-does-not-default-to-default-charset-in-mysql/6669995#6669995
		$actionTable = $this->getTable('action');
		$moduleActionTable = $this->getTable('module_action');
		$moduleTable = $this->getTable('module');

		$sql = <<<SQL
SELECT a.*, group_concat(concat(
	cast(ma.module_id as char),"\t",
	m.name
) separator "\n") as modul
FROM $actionTable a
left join $moduleActionTable ma on ma.action_id=a.id
left join $moduleTable m on ma.module_id=m.id or m.id is null

$where

group by a.id

SQL;
		return $sql;
	}
}
