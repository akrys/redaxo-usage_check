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
	 * @SuppressWarnings(PHPMD.StaticAccess)
	 */
	public static function getVersion()
	{
		if (!isset(self::$api)) {
			switch (\akrys\redaxo\addon\UsageCheck\RedaxoCall::getRedaxoVersion()) {
				case \akrys\redaxo\addon\UsageCheck\RedaxoCall::REDAXO_VERSION_5:
					// Redaxo 5
					self::$api = new RexV5\Permission();
					break;
				default:
					throw new Exception\InvalidVersionException();
			}
		}
		return self::$api;
	}

	/**
	 * Prüft die Rechte für den aktuellen User.
	 *
	 * Unit Testing
	 * Die Rechteverwaltung ist zu nah am RedaxoCore, um das auf die Schnelle simulieren zu können.
	 * @codeCoverageIgnore
	 *
	 * @param string $perm eine der PERM-Konstanten
	 * @return boolean
	 */
	abstract public function check($perm);

	/**
	 * Permission Mapping
	 * @param string $perm
	 * @return string
	 */
	abstract protected function mapPerm($perm);
}
