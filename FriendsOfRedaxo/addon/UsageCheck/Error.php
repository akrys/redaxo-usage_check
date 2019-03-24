<?php

/**
 * Datei für die Error-Klasse
 *
 * @version       1.0 / 2015-10-27
 * @author        akrys
 */
namespace FriendsOfRedaxo\addon\UsageCheck;

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
	 * Iterator-Zählervariable
	 * @var int
	 */
	private $index = 0;

	/**
	 * add a text to the messages
	 * @param string $text
	 */
	public function add($text)
	{
		$this->errors[] = $text;
	}

	/**
	 * Einträge zählen.
	 *
	 * @return int
	 */
	public function count(){
		return count($this->errors);
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
		$this->index++;
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
		return $this->errors[$this->index];
	}

	/**
	 * Rewind the Iterator to the first element
	 *
	 * @see \Iterator::rewind()
	 * @link https://secure.php.net/manual/en/iterator.rewind.php
	 */
	public function rewind()
	{
		$this->index = 0;
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
		return $this->index;
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
		if (!isset($this->errors[$this->index])) {
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
	 * Konstuktor
	 */
	final private function __construct()
	{
		$this->errors = array();
	}

	/**
	 * forbid cloning
	 */
	final public function __clone()
	{
		throw new Exception\CloneException();
	}
// </editor-fold>

	/**
	 *
	 * @param type $messages
	 */
	public static function getMessageOutputFragment($messages)
	{

		$text = '';
		foreach ($messages as $error) {
			if (trim($error) !== '') {
				$fragment = new \rex_fragment([
					'text' => $error,
				]);

				$text .= $fragment->parse('fragments/msg/tagged_msg.php');
			}
		}
		$fragment = new \rex_fragment([
			'text' => $text,
		]);

		return $fragment;
	}
}
