<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
namespace FriendsOfRedaxo\addon\UsageCheck;

use rex_addon;

/**
 * Datei für ...
 *
 * @version       1.0 / 2020-02-18
 * @author        akrys
 */

/**
 * Description of Addon
 *
 * @author akrys
 */
final class Addon
{
	/**
	 * Addon
	 * @var \rex_addon
	 */
	private $addon;

	/**
	 * Version holen, besser direkt über die yml-Datei
	 * @return string
	 */
	public function getVersion()
	{
		return $this->addon->getVersion();
	}

	/**
	 * Name holen, besser direkt über die yml-Datei
	 * @return string
	 */
	public function getName()
	{
		$page = $this->addon->getProperty('page');
		return $page['title'];
	}
	/*	 * ********************* Singleton ********************** */

	/**
	 * Konstruktor
	 */
	private function __construct()
	{
		$this->addon = rex_addon::get(Config::NAME);
	}
	/**
	 * Speichert die Instanz der Klasse.<br />
	 *
	 * @var          Addon
	 */
	private static $instance;

	/**
	 * nicht erlaubt...
	 */
	private function __clone()
	{
		//no code
	}

	/**
	 * Gibt die Instanz.
	 *
	 * @return Addon
	 */
	public static function getInstance()
	{
		if (!isset(self::$instance)) {
			self::$instance = new self();
		}
		return self::$instance;
	}
}
