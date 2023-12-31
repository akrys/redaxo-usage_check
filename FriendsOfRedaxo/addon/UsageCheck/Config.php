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
	 * Test, ob das Vendor-Verzeichnis gelöscht wurde.
	 *
	 * Performance-Probleme durch den rex_autoloader verhindern.
	 * Dieser versucht alle Dateien zu analysieren.
	 *
	 * @throws Exception
	 * @codeCoverageIgnore
	 */
	public static function checkVendorDir()
	{
		if (!isset($_SERVER['argv'])) {
			$vendorDir = self::getBaseDir().'/vendor';
			if (file_exists($vendorDir) && is_dir($vendorDir)) {
				throw new Exception('Please delete '.realpath($vendorDir));
			}

//			$nodeDir = self::getBaseDir().'/node_modules';
//			if(file_exists($nodeDir) && is_dir($nodeDir)){
//				throw new \Exception('Please delete '.realpath($nodeDir));
//			}
		}
		return true;
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
