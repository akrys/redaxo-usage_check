<?php

/**
 * Datei für ...
 *
 * @version       1.0 / 2018-05-13
 * @author        akrys
 */
namespace akrys\redaxo\addon\UsageCheck\Lib;

/**
 * Description of ModulesBase
 *
 * @author akrys
 */
abstract class BaseModule
	extends RexBase
{
	/**
	 * Anzeigemodus
	 * @var boolean
	 */
	protected $showAll = false;

	/**
	 * Anzeigemodus umstellen
	 * @param boolean $bln
	 */
	public function showAll($bln)
	{
		$this->showAll = (boolean) $bln;
	}

	/**
	 * Daten holen
	 * @return array
	 */
	abstract public function get();

	/**
	 * SQL genereieren
	 * @return array
	 */
	abstract protected function getSQL();
}
