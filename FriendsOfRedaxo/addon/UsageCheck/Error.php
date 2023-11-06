<?php

/**
 * Datei für die Error-Klasse
 *
 * @version       1.0 / 2015-10-27
 * @author        akrys
 */
namespace FriendsOfRedaxo\addon\UsageCheck;

use FriendsOfRedaxo\addon\UsageCheck\Exception\CloneException;
use Iterator;
use rex_fragment;

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
class Error implements Iterator
{
	/**
	 * Error Messages
	 * @var string[]
	 */
	private array $errors = [];

	/**
	 * Iterator-Zählervariable
	 * @var int
	 */
	private int $index = 0;

	/**
	 * add a text to the messages
	 * @param string $text
	 */
	public function add(string $text)
	{
		$this->errors[] = $text;
	}

	/**
	 * Einträge zählen.
	 *
	 * @return int
	 */
	public function count(): int
	{
		return count($this->errors);
	}
// <editor-fold defaultstate="collapsed" desc="Iterator Implementation">

	/**
	 * Move forward to next element
	 *
	 * @see Iterator::next()
	 * @link https://secure.php.net/manual/en/iterator.next.php
	 *
	 */
	public function next(): void
	{
		$this->index++;
	}

	/**
	 * Return the current element
	 *
	 * @see Iterator::current()
	 * @link https://secure.php.net/manual/en/iterator.current.php
	 *
	 * @return string
	 */
	public function current(): string
	{
		if (!$this->valid()) {
			return false;
		}
		return $this->errors[$this->index];
	}

	/**
	 * Rewind the Iterator to the first element
	 *
	 * @see Iterator::rewind()
	 * @link https://secure.php.net/manual/en/iterator.rewind.php
	 */
	public function rewind(): void
	{
		$this->index = 0;
	}

	/**
	 * Return the key of the current element
	 *
	 * @see Iterator::key()
	 * @link https://secure.php.net/manual/en/iterator.key.php
	 *
	 * @return int
	 */
	public function key(): int
	{
		return $this->index;
	}

	/**
	 * Checks if current position is valid
	 *
	 * @see Iterator::valid()
	 * @link https://secure.php.net/manual/en/iterator.valid.php
	 *
	 * @return boolean
	 */
	public function valid(): bool
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
		$this->errors = [];
	}

	/**
	 * forbid cloning
	 */
	final public function __clone()
	{
		throw new CloneException();
	}
// </editor-fold>

	/**
	 * Fehlerausgabe
	 * @param array $messages
	 */
	public static function getMessageOutputFragment(array $messages): rex_fragment
	{

		$text = '';
		foreach ($messages as $error) {
			if (trim($error) !== '') {
				$fragment = new rex_fragment([
					'text' => $error,
				]);

				$text .= $fragment->parse('fragments/msg/tagged_msg.php');
			}
		}
		$fragment = new rex_fragment([
			'text' => $text,
		]);

		return $fragment;
	}
}
