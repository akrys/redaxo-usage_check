<?php

/**
 * Datei für ...
 *
 * @author        akrys
 */
namespace FriendsOfRedaxo\UsageCheck;

use FriendsOfRedaxo\UsageCheck\Exception\CloneException;
use rex_addon;
use rex_addon_interface;

/**
 * Description of Addon
 *
 * @author akrys
 */
final class Addon
{
	/**
	 * Addon
	 * @var rex_addon_interface
	 */
	private $addon;

	/**
	 * Version holen, besser direkt über die yml-Datei
	 * @return string
	 */
	public function getVersion(): string
	{
		return $this->addon->getVersion();
	}

	/**
	 * Name holen, besser direkt über die yml-Datei
	 * @return string
	 */
	public function getName(): string
	{
		$page = $this->addon->getProperty('page');
		return $page['title'];
	}
	// <editor-fold defaultstate="collapsed" desc="Singleton">
	/**
	 * Instance
	 * @var self
	 */
	private static $instance = null;

	/**
	 * create Singleton Instance
	 * @return self
	 */
	public static function getInstance(): self
	{
		if (self::$instance == null) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	/**
	 * Konstuktor
	 */
	final private function __construct()
	{
		$this->addon = rex_addon::get(Config::NAME);
	}

	/**
	 * forbid cloning
	 */
	final public function __clone()
	{
		throw new CloneException();
	}
// </editor-fold>
}
