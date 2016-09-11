<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Datei für ...
 *
 * @version       1.0 / 2016-08-27
 * @package       new_package
 * @subpackage    new_subpackage
 * @author        akrys
 */
namespace akrys\redaxo\addon\UsageCheck\Tests;

/**
 * Description of KnownSQLs
 *
 * @author akrys
 */
class SQLSimulation
{
	/**
	 * Sammlung unbekannter SQL-Statements
	 * @var array
	 */
	private static $unKnownSQL = array();

	/**
	 * aktuelles SQL-Satement
	 * @var string
	 */
	private $sql;

	/**
	 * Version
	 * @var string
	 */
	private $version;

	/**
	 * Datei mit simuliertem Ergebnis
	 * @var string
	 */
	private $simulationDataFile;

	/**
	 * Konstruktor
	 * @param string $version
	 * @param string $sql
	 */
	public function __construct($version, $sql)
	{
		$this->version = $version;
		$this->sql = $sql;
		$this->hash = $this->getHash();
		$this->simulationDataFile = __DIR__.'/testdata/'.$this->hash.'.xml';
	}

	/**
	 * Abfrage, ob eine Datendatei vorhanden ist.
	 * @return boolean
	 */
	private function hasData()
	{
		if (file_exists($this->simulationDataFile)) {
			return true;
		}
		return false;
	}

	/**
	 * Öffentliche abfrage, ob das SQL bekannt ist.
	 * @return boolean
	 */
	public function getSQLisKnown()
	{
		if ($this->hasData()) {
			return true;
		}

		$backtrace = debug_backtrace();
		foreach ($backtrace as $key => $value) {
			if (!preg_match('#Test\.php$#msi', $value['file'])) {
//				print PHP_EOL.$key.PHP_EOL.$value['file'].PHP_EOL;
				continue;
			}
			if (!isset(self::$unKnownSQL[$this->hash])) {
				self::$unKnownSQL[$this->hash] = array(
					'sql' => $this->sql,
					'version' => $this->version,
					'data' => array(),
					'caller' => array(
						array(
							'file' => $value['file'],
							'line' => $value['line'],
							'class' => $value['class'],
							'function' => $value['function'],
							'type' => $value['type'],
							'depth' => $key,
						)
					)
				);
			} else {
				self::$unKnownSQL[$this->hash]['caller'][] = array(
					'file' => $value['file'],
					'line' => $value['line'],
					'class' => $value['class'],
					'function' => $value['function'],
					'type' => $value['type'],
					'depth' => $key,
				);
			}
//				print PHP_EOL.json_encode(self::$unKnownSQL).PHP_EOL;
			break;
		}
		return false;
	}

	/**
	 * Daten liefern.
	 * @return array
	 * @throws \Exception
	 */
	public function getData()
	{
		if ($this->hasData()) {
			$xml = simplexml_load_file($this->simulationDataFile);

			$data = array();
			foreach ($xml->custom->row as $row) {
				$dataRow = array();
				foreach ($row->children() as $field) {
					$name = $field->getName();
					$value = (string) $field;
					if ($value === 'NULL') {
						$value = null;
					}

					$dataRow[$name] = $value;
				}
				$data[] = $dataRow;
			}

			return $data;
		}
		throw new \Exception('Hash unknown '.$this->hash);
	}

	/**
	 * Has generieren.
	 *
	 * Dieser wird als Dateiname genutzt.
	 *
	 * @return string
	 */
	private function getHash()
	{
		return md5($this->version.'_'.$this->sql);
	}

	/**
	 * Alle unbekannten Satements liefern, um einen Hinweis zu generieren.
	 * @return array
	 */
	public static function getAllUnknown()
	{
		return self::$unKnownSQL;
	}
}
