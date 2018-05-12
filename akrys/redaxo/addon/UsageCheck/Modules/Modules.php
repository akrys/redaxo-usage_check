<?php

/**
 * Datei für das Modul "Module"
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
abstract class Modules
	extends BaseModule
{
	const TYPE = 'modules';

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
	 * Nicht genutze Module holen
	 *
	 * @return array
	 *
	 * @todo bei Instanzen mit vielen Slices testen. Die Query
	 *       riecht nach Performance-Problemen -> 	Using join buffer (Block Nested Loop)
	 */
	public function getModules()
	{
		$showAll = $this->showAll;

		if (!Permission::getInstance()->check(Permission::PERM_STRUCTURE)) {
			//Permission::PERM_MODUL
			return false;
		}

		if(!$this->rexSql) {
			throw \Exception('no sql given');
		}
		$rexSQL = $this->rexSql;

		$where = '';
		if (!$showAll) {
			$where .= 'where s.id is null';
		}
		$sql = $this->getSQL($where);

		return $rexSQL->getArray($sql);
	}

	/**
	 * SQL generieren
	 * @param string $where
	 * @return string
	 */
	abstract protected function getSQL($where);

	/**
	 * Menü ausgeben
	 * @return void
	 * @param string $subpage
	 * @param string $showAllParam
	 * @param string $showAllLinktext
	 */
	abstract public function outputMenu($subpage, $showAllParam, $showAllLinktext);

	/**
	 * Abfrage der Rechte für das Modul
	 *
	 * Unit Testing
	 * Die Rechteverwaltung ist zu nah am RedaxoCore, um das auf die Schnelle simulieren zu können.
	 * @codeCoverageIgnore
	 *
	 * @param array $item
	 * @return boolean
	 */
	abstract public function hasRights($item);
}
