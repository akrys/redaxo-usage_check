<?php

/**
 * Datei für ...
 *
 * @author        akrys
 */
namespace FriendsOfRedaxo\UsageCheck\Lib;

use Exception;
use FriendsOfRedaxo\UsageCheck\Exception\InvalidParameterException;
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
	 * @throws InvalidParameterException
	 */
	protected function getTable(string $table): string
	{
		if ($table === '') {
			throw new InvalidParameterException('Paramer $table should not be empty');
		}
		if (!$this->rex) {
			$this->rex = new rex;
		}
		return $this->rex->getTable($table);
	}
}
