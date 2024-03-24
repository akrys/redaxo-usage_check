<?php

/**
 * Datei für ...
 *
 * @version       1.0 / 2018-05-13
 * @author        akrys
 */
namespace FriendsOfRedaxo\addon\UsageCheck\Lib;

use Exception;
use rex;
use rex_sql;

/**
 * Description of RexBase
 *
 * @author akrys
 */
class RexBase
{
	/**
	 * rexSQL instanz
	 * @var ?rex_sql
	 */
	private ?rex_sql $rexSql;

	/**
	 * rex Instanz
	 * @var ?rex
	 */
	private ?rex $rex = null;

	/**
	 * Konstruktor
	 */
	public function __construct()
	{
		//;
	}

	/**
	 * sql-Instanz verwalten
	 *
	 * @return rex_sql
	 */
	public function getRexSql(): rex_sql
	{
		if (!$this->rexSql) {
			throw new Exception('no sql given');
		}

		return $this->rexSql;
	}

	/**
	 * sql-Instanz verwalten
	 * @param rex_sql $sql
	 * @return $this
	 */
	public function setRexSql(rex_sql $sql): self
	{
		$this->rexSql = $sql;
		return $this;
	}

	/**
	 * getTable wird überll genutzt, darf aber lt. phpmd nicht statisch aufgerufen werden.
	 * lt. phpmd sollte man nicht statisch drauf zugreifen
	 * @param string $table
	 * @return string
	 */
	protected function getTable(string $table): string
	{
		if (!$this->rex) {
			$this->rex = new rex;
		}
		return $this->rex->getTable($table);
	}
}
