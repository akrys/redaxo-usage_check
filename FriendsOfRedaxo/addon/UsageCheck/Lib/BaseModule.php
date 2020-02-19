<?php

/**
 * Datei für ...
 *
 * @version       1.0 / 2018-05-13
 * @author        akrys
 */
namespace FriendsOfRedaxo\addon\UsageCheck\Lib;

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
	 * Tabellenfelder
	 * @var array
	 */
	protected $tableFields = array();

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
	 * @param int $datail_id
	 * @return array
	 */
	abstract protected function getSQL(/* int */$datail_id = null);

	/**
	 * Details holen
	 * @param int $item_id
	 * @return array
	 */
	abstract public function getDetails(/* int */$item_id);
}
