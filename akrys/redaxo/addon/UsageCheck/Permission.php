<?php

/**
 * User-Rechte
 */
namespace akrys\redaxo\addon\UsageCheck;

/**
 * User-Rechte für Zugriffe abfragen.
 *
 */
abstract class Permission
{
	/**
	 * Name des Rechts für Templates
	 * @var string
	 */
	const PERM_TEMPLATE = 'template';

	/**
	 * Name des Rechts für den Mediapool
	 * @var string
	 */
	const PERM_MEDIAPOOL = 'mediapool';

	/**
	 * Name des Rechts für den Mediapool
	 * @var string
	 */
	const PERM_MEDIA = 'media';

	/**
	 * Name des Rechts für Module
	 * @var string
	 */
	const PERM_MODUL = 'module';

	/**
	 * Name des Rechts für das XFormaddon
	 * @var string
	 */
	const PERM_XFORM = 'xform';

	/**
	 * Name des Rechts für das Struktur
	 * @var string
	 */
	const PERM_STRUCTURE = 'structure';

	/**
	 * Schnittstellenversion zu Redaxo 4 oder 5
	 * @var RedaxoCall
	 */
	private static $api;

	/**
	 * Rechteverwaltung nach Redaxo 4 oder Redaxo5 erstellen
	 * @return Permission
	 * @throws Exception\InvalidVersionException
	 */
	public static function getVersion()
	{
		if (!isset(self::$api)) {
			switch (\akrys\redaxo\addon\UsageCheck\RedaxoCall::getRedaxoVersion()) {
				case \akrys\redaxo\addon\UsageCheck\RedaxoCall::REDAXO_VERSION_4:
					// Redaxo 4
					require_once __DIR__.'/RexV4/Permission.php';
					self::$api = new RexV4\Permission();
					break;
				case \akrys\redaxo\addon\UsageCheck\RedaxoCall::REDAXO_VERSION_5:
					// Redaxo 5
					require_once __DIR__.'/RexV5/Permission.php';
					self::$api = new RexV5\Permission();
					break;
				default:
					require_once(__DIR__.'/Exception/InvalidVersionException.php');
					throw new Exception\InvalidVersionException();
			}
		}
		return self::$api;
	}

	/**
	 * Prüft die Rechte für den aktuellen User.
	 *
	 * @param string $perm eine der PERM-Konstanten
	 * @return boolean
	 */
	public abstract function check($perm);

	/**
	 * Permission Mapping
	 * @param string $perm
	 * @return string
	 */
	protected abstract function mapPerm($perm);
}
