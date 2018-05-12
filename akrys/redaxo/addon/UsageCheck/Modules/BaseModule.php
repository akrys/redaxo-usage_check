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
	protected $sql;

	/**
	 *
	 * @return type
	 */
	public function getSql()
	{
		return $this->sql;
	}

	/**
	 *
	 * @param \rex_sql $sql
	 * @return $this
	 */
	public function setSql(\rex_sql $sql)
	{
		$this->sql = $sql;
		return $this;
	}
}
