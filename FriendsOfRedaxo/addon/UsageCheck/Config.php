<?php

/**
 * Config-Datei
 * @author akrys
 */
namespace FriendsOfRedaxo\addon\UsageCheck;

/**
 * Config-Klasse mit Konstanten für die Runtime
 * @author akrys
 */
class Config
{
	/**
	 * Technischer Name des Addons
	 * @var string
	 */
	const NAME = 'usage_check';

	/**
	 * (Absolutes) Basis Verzeichnis holen
	 * @return string
	 */
	public static function getBaseDir()
	{
		return realpath(__DIR__.'/../../../');
	}

	/**
	 * Autoload Funktion
	 * @param string $name
	 * @return boolean
	 *
	 * @codeCoverageIgnore
	 */
	public static function autoload($name)
	{
		$filename = self::getBaseDir().'/'.str_replace('\\', '/', $name).'.php';

		if (file_exists($filename)) {
			require $filename;
			return true;
		}
//		throw new \Exception($filename.' not found');
		return false;
	}
}
