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
	 *
	 * @var \rex_sql
	 */
	protected $rexSql;

	/**
	 *
	 * @return type
	 */
	public function getRexSql()
	{
		return $this->rexSql;
	}

	/**
	 *
	 * @param \rex_sql $sql
	 * @return $this
	 */
	public function setRexSql(\rex_sql $sql)
	{
		$this->rexSql = $sql;
		return $this;
	}

	/**
	 *
	 * @param string $table
	 * @return string
	 */
	protected function getTable($table)
	{
		$rex = new \rex;
		return $rex->getTable($table);
	}

	/**
	 *
	 * @param string $string
	 * @return string
	 */
	protected function i18nRaw($string)
	{
		$rex = new \rex_i18n;
		return $rex->rawMsg($string);
	}
}
