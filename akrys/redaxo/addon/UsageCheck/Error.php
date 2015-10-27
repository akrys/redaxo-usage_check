<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
namespace akrys\redaxo\addon\UsageCheck;

/**
 * Datei für ...
 *
 * @version       1.0 / 2015-10-27
 * @author        akrys
 */

/**
 * Description of Error
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
	 *
	 * @return int
	 */
	public function next()
	{
		$this->i++;
		return $this->current();
	}

	/**
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
	 *
	 */
	public function rewind()
	{
		$this->i = 0;
	}

	/**
	 *
	 * @return int
	 */
	public function key()
	{
		return $this->i;
	}

	/**
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