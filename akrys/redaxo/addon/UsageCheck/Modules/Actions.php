<?php

/**
 * Datei für die Modul-Actions
 *
 * @version       1.0 / 2015-08-09
 * @author        akrys
 */

namespace akrys\redaxo\addon\UsageCheck\Modules;

require_once __DIR__.'/../Permission.php';

/**
 * Description of Modules
 *
 * @author akrys
 */
abstract class Actions
{

	/**
	 * Redaxo-Spezifische Version wählen.
	 * @return \akrys\redaxo\addon\UsageCheck\Modules\Actions
	 * @throws \akrys\redaxo\addon\UsageCheck\Exception\FunctionNotCallableException
	 */
	public static function create()
	{
		$object = null;
		switch (\akrys\redaxo\addon\UsageCheck\RedaxoCall::getRedaxoVersion()) {
			case \akrys\redaxo\addon\UsageCheck\RedaxoCall::REDAXO_VERSION_4:
				require_once __DIR__.'/../RexV4/Modules/Actions.php';
				$object = new \akrys\redaxo\addon\UsageCheck\RexV4\Modules\Actions();
				break;
			case \akrys\redaxo\addon\UsageCheck\RedaxoCall::REDAXO_VERSION_5:
				require_once __DIR__.'/../RexV5/Modules/Actions.php';
				$object = new \akrys\redaxo\addon\UsageCheck\RexV5\Modules\Actions();
				break;
		}

		if (!isset($object)) {
			require_once __DIR__.'/../Exception/FunctionNotCallableException.php';
			throw new \akrys\redaxo\addon\UsageCheck\Exception\FunctionNotCallableException();
		}

		return $object;
	}

	/**
	 * Nicht genutze Module holen
	 *
	 * @param boolean $show_all
	 * @return array
	 *
	 * @todo bei Instanzen mit vielen Slices testen. Die Query
	 *       riecht nach Performance-Problemen -> 	Using join buffer (Block Nested Loop)
	 */
	public function getActions($show_all = false)
	{
		if (!\akrys\redaxo\addon\UsageCheck\Permission::check(\akrys\redaxo\addon\UsageCheck\Permission::PERM_MODUL)) {
			return false;
		}

		if (\akrys\redaxo\addon\UsageCheck\RedaxoCall::getRedaxoVersion() == \akrys\redaxo\addon\UsageCheck\RedaxoCall::REDAXO_VERSION_4) {
			$rexSQL = new \rex_sql;
		} else {
			$rexSQL = \rex_sql::factory();
		}

		$where = '';
		if (!$show_all) {
			$where.='where ma.id is null';
		}

		//Keine integer oder Datumswerte in einem concat!
		//Vorallem dann nicht, wenn MySQL < 5.5 im Spiel ist.
		// -> https://stackoverflow.com/questions/6397156/why-concat-does-not-default-to-default-charset-in-mysql/6669995#6669995
		$actionTable = \akrys\redaxo\addon\UsageCheck\RedaxoCall::getAPI()->getTable('action');
		$moduleActionTable = \akrys\redaxo\addon\UsageCheck\RedaxoCall::getAPI()->getTable('module_action');
		$moduleTable = \akrys\redaxo\addon\UsageCheck\RedaxoCall::getAPI()->getTable('module');

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

		return $rexSQL->getArray($sql);
	}

	/**
	 * Menu
	 * @param string $subpage
	 * @param string $showAllParam
	 * @param string $showAllLinktext
	 */
	public abstract function outputMenu($subpage, $showAllParam, $showAllLinktext);

	/**
	 * Link Action Editieren
	 * @param array $item
	 * @param string $linktext
	 */
	public abstract function outputActionEdit($item, $linktext);
}
