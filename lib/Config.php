<?php

/**
 * Config-Datei
 * @author akrys
 */
namespace FriendsOfRedaxo\UsageCheck;

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
	 * @deprecated wird nicht mehr benötigt, sobald alte Klassen mit 'addon' im Namespace nicht mehr unterstützt werden
	 * @codeCoverageIgnore
	 */
	public static function getBaseDir() // remove in v4
	{
		return (string) realpath(__DIR__);
	}

	/**
	 * Autoload Funktion
	 * @param string $name
	 * @return boolean
	 *
	 * @deprecated wird nicht mehr benötigt, sobald alte Klassen mit 'addon' im Namespace nicht mehr unterstützt werden
	 * @codeCoverageIgnore
	 */
	public static function autoload($name) // remove in v4
	{
		if (stristr($name, 'FriendsOfRedaxo\\addon\\UsageCheck')) {
			$oldName = $name;
			$newName = str_replace('FriendsOfRedaxo\\addon\\UsageCheck', 'FriendsOfRedaxo\\UsageCheck', $name);

			$msg = 'Deprecated Class Found: '.$name.PHP_EOL.
				'Use '.$newName.' instead of '.$oldName.PHP_EOL.
				'Alias support with old Namespace will be removed next major verson.';

			class_alias($newName, $oldName);

			user_error($msg, E_USER_DEPRECATED);
			$name = str_replace('FriendsOfRedaxo\\addon\\UsageCheck', 'FriendsOfRedaxo\\UsageCheck', $name);
		}


		if (!stristr($name, __NAMESPACE__)) {
			return false; // not a UsageCheck class
		}

		if (class_exists($name)) {
			return false;
		}

		//namespace parts not in directory structure.
		$name = str_replace(__NAMESPACE__, '', $name);

		$filename = self::getBaseDir().'/'.str_replace('\\', '/', $name).'.php';
		if (file_exists($filename)) {
			require $filename;
			return true;
		}
//		throw new \Exception($filename.' not found');
		return false;
	}
}
