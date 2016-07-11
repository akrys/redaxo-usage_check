<?php

/**
 * Datei für die Error-Klasse
 *
 * @version       1.0 / 2015-10-27
 * @author        akrys
 */
namespace akrys\redaxo\addon\UsageCheck;

/**
 * Container für Fehlermeldungen
 *
 * Hier werden Fehler gesammelt, über die dann später iteriert werden kann.
 *
 * Implementiert den PHP-Iterator, so dass man über die Meldungen mit
 * einem einfachen foreach durlaufen werden können.
 *
 * @author akrys
 */
class Error
	implements \Iterator
{
	/**
	 * Error Messages
	 * @var array
	 */
	private $errors = array();

	/**
	 * add a text to the messages
	 * @param string $text
	 */
	public function add($text)
	{
		$this->errors[] = $text;
	}
// <editor-fold defaultstate="collapsed" desc="Iterator Implementation">

	/**
	 * Move forward to next element
	 *
	 * @see \Iterator::next()
	 * @link https://secure.php.net/manual/en/iterator.next.php
	 *
	 * @return int
	 */
	public function next()
	{
		$this->i++;
		return $this->current();
	}

	/**
	 * Return the current element
	 *
	 * @see \Iterator::current()
	 * @link https://secure.php.net/manual/en/iterator.current.php
	 *
	 * @return string
	 */
	public function current()
	{
		if (!$this->valid()) {
			return false;
		}
		return $this->errors[$this->i];
	}

	/**
	 * Rewind the Iterator to the first element
	 *
	 * @see \Iterator::rewind()
	 * @link https://secure.php.net/manual/en/iterator.rewind.php
	 */
	public function rewind()
	{
		$this->i = 0;
	}

	/**
	 * Return the key of the current element
	 *
	 * @see \Iterator::key()
	 * @link https://secure.php.net/manual/en/iterator.key.php
	 *
	 * @return int
	 */
	public function key()
	{
		return $this->i;
	}

	/**
	 * Checks if current position is valid
	 *
	 * @see \Iterator::valid()
	 * @link https://secure.php.net/manual/en/iterator.valid.php
	 *
	 * @return boolean
	 */
	public function valid()
	{
		if (!isset($this->errors[$this->i])) {
			return false;
		}
		return true;
	}
// </editor-fold>
// <editor-fold defaultstate="collapsed" desc="Singleton">
	/**
	 * Instance
	 * @var Error
	 */
	private static $instance = null;

	/**
	 * create Singleton Instance
	 * @return Error
	 */
	public static function getInstance()
	{
		if (self::$instance == null) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	/**
	 * forbid cloning
	 */
	public function __clone()
	{

	}
// </editor-fold>
}
