<?php

/**
 * Datei für das Modul "Module"
 *
 * @version       1.0 / 2015-08-09
 * @author        akrys
 */
namespace akrys\redaxo\addon\UsageCheck\Modules;

use \akrys\redaxo\addon\UsageCheck\Permission;
use \akrys\redaxo\addon\UsageCheck\RedaxoCall;

/**
 * Description of Modules
 *
 * @author akrys
 */
abstract class Modules
{
	/**
	 * Anzeigemodus für "Alle Anzeigen"
	 * @var boolean
	 */
	private $showAll = false;

	/**
	 * Redaxo-Spezifische Version wählen.
	 * @return \akrys\redaxo\addon\UsageCheck\Modules\Modules
	 * @throws \akrys\redaxo\addon\UsageCheck\Exception\FunctionNotCallableException
	 * @SuppressWarnings(PHPMD.StaticAccess)
	 */
	public static function create()
	{
		$object = null;
		switch (RedaxoCall::getRedaxoVersion()) {
			case RedaxoCall::REDAXO_VERSION_4:
				require_once __DIR__.'/../RexV4/Modules/Modules.php';
				$object = new \akrys\redaxo\addon\UsageCheck\RexV4\Modules\Modules();
				break;
			case RedaxoCall::REDAXO_VERSION_5:
				require_once __DIR__.'/../RexV5/Modules/Modules.php';
				$object = new \akrys\redaxo\addon\UsageCheck\RexV5\Modules\Modules();
				break;
		}

		if (!isset($object)) {
			require_once __DIR__.'/../Exception/FunctionNotCallableException.php';
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

		if (!Permission::getVersion()->check(Permission::PERM_STRUCTURE)) {
			//Permission::PERM_MODUL
			return false;
		}

		$rexSQL = RedaxoCall::getAPI()->getSQL();

		$where = '';
		if (!$showAll) {
			$where.='where s.id is null';
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
	 * @param array $item
	 * @return boolean
	 */
	abstract public function hasRights($item);
}
