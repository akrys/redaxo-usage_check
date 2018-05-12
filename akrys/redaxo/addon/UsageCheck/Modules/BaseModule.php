<?php

/**
 * Datei für ...
 *
 * @version       1.0 / 2018-05-12
 * @author        akrys
 */
namespace akrys\redaxo\addon\UsageCheck\Modules;

/**
 * Description of BaseModule
 *
 * @author akrys
 */
class BaseModule
{
	/**
	 * rexSQL instanz
	 * @var \rex_sql
	 */
	private $rexSql;

	/**
	 * rex Instanz
	 * @var \rex
	 */
	private $rex;

	/**
	 * rex_i18n Instanz
	 * @var \rex_i18n
	 */
	private $rexI18n;

	/**
	 * sql-Instanz verwalten
	 *
	 * @return \rex_sql
	 */
	public function getRexSql()
	{
		if (!$this->rexSql) {
			throw new \Exception('no sql given');
		}

		return $this->rexSql;
	}

	/**
	 * sql-Instanz verwalten
	 * @param \rex_sql $sql
	 * @return $this
	 */
	public function setRexSql(\rex_sql $sql)
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
	protected function getTable($table)
	{
		if (!$this->rex) {
			$this->rex = new \rex;
		}
		return $this->rex->getTable($table);
	}

	/**
	 * Übersetzungen
	 * lt. phpmd sollte man nicht statisch drauf zugreifen
	 * @param string $string
	 * @return string
	 */
	protected function i18nRaw($string)
	{
		if (!$this->rexI18n) {
			$this->rexI18n = new \rex_i18n;
		}

		return $this->rexI18n->rawMsg($string);
	}
}
