<?php

/**
 * Datei für die Modul-Actions
 *
 * @version       1.0 / 2015-08-09
 * @author        akrys
 */
namespace akrys\redaxo\addon\UsageCheck\Modules;

use \akrys\redaxo\addon\UsageCheck\RedaxoCall;
use \akrys\redaxo\addon\UsageCheck\Permission;

/**
 * Description of Modules
 *
 * @author akrys
 */
abstract class Actions
{
	/**
	 * Anzeigemodus
	 * @var boolean
	 */
	private $showAll = false;

	/**
	 * Redaxo-Spezifische Version wählen.
	 * @return \akrys\redaxo\addon\UsageCheck\Modules\Actions
	 * @throws \akrys\redaxo\addon\UsageCheck\Exception\FunctionNotCallableException
	 * @SuppressWarnings(PHPMD.StaticAccess)
	 */
	public static function create()
	{
		$object = null;
		switch (RedaxoCall::getRedaxoVersion()) {
			case RedaxoCall::REDAXO_VERSION_5:
				$object = new \akrys\redaxo\addon\UsageCheck\RexV5\Modules\Actions();
				break;
		}

		if (!isset($object)) {
			throw new \akrys\redaxo\addon\UsageCheck\Exception\FunctionNotCallableException();
		}

		return $object;
	}

	/**
	 * Anzeigemodus umstellen
	 * @param boolean $bln
	 */
	public function showAll($bln)
	{
		$this->showAll = (boolean) $bln;
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
	public function getActions()
	{
		if (!Permission::getVersion()->check(Permission::PERM_MODUL)) {
			return false;
		}

		$rexSQL = \rex_sql::factory();

		$where = '';
		if (!$this->showAll) {
			$where.='where ma.id is null';
		}

		//Keine integer oder Datumswerte in einem concat!
		//Vorallem dann nicht, wenn MySQL < 5.5 im Spiel ist.
		// -> https://stackoverflow.com/questions/6397156/why-concat-does-not-default-to-default-charset-in-mysql/6669995#6669995
		$actionTable = RedaxoCall::getAPI()->getTable('action');
		$moduleActionTable = RedaxoCall::getAPI()->getTable('module_action');
		$moduleTable = RedaxoCall::getAPI()->getTable('module');

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
	abstract public function outputMenu($subpage, $showAllParam, $showAllLinktext);

	/**
	 * Link Action Editieren
	 * @param array $item
	 * @param string $linkText
	 */
	abstract public function outputActionEdit($item, $linkText);
}
