<?php

/**
 * Datei für das Modul "Module"
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
abstract class Modules
{

	/**
	 * Redaxo-Spezifische Version wählen.
	 * @return \akrys\redaxo\addon\UsageCheck\Modules\Modules
	 * @throws \akrys\redaxo\addon\UsageCheck\Exception\FunctionNotCallableException
	 */
	public static function create()
	{
		$object = null;
		switch (\akrys\redaxo\addon\UsageCheck\RedaxoCall::getRedaxoVersion()) {
			case \akrys\redaxo\addon\UsageCheck\RedaxoCall::REDAXO_VERSION_4:
				require_once __DIR__.'/../RexV4/Modules/Modules.php';
				$object = new \akrys\redaxo\addon\UsageCheck\RexV4\Modules\Modules();
				break;
			case \akrys\redaxo\addon\UsageCheck\RedaxoCall::REDAXO_VERSION_5:
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
	 * Nicht genutze Module holen
	 *
	 * @param boolean $show_all
	 * @return array
	 *
	 * @todo bei Instanzen mit vielen Slices testen. Die Query
	 *       riecht nach Performance-Problemen -> 	Using join buffer (Block Nested Loop)
	 */
	public function getModules($show_all = false)
	{
		if (!\akrys\redaxo\addon\UsageCheck\Permission::getVersion()->check(\akrys\redaxo\addon\UsageCheck\Permission::PERM_STRUCTURE)) {
			//\akrys\redaxo\addon\UsageCheck\Permission::PERM_MODUL
			return false;
		}

		$rexSQL = \akrys\redaxo\addon\UsageCheck\RedaxoCall::getAPI()->getSQL();

		$where = '';
		if (!$show_all) {
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
	protected abstract function getSQL($where);

	/**
	 * Menü ausgeben
	 * @return void
	 * @param string $subpage
	 * @param string $showAllParam
	 * @param string $showAllLinktext
	 */
	public abstract function outputMenu($subpage, $showAllParam, $showAllLinktext);

	/**
	 * Abfrage der Rechte für das Modul
	 *
	 * @param array $item
	 * @return boolean
	 */
	public abstract function hasRights($item);
}
