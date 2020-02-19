<?php

/**
 * User-Rechte
 */
namespace FriendsOfRedaxo\addon\UsageCheck;

use FriendsOfRedaxo\addon\UsageCheck\Exception\CloneException;
use rex;
use rex_media_perm;
use rex_module_perm;
use rex_structure_perm;

/**
 * User-Rechte für Zugriffe abfragen.
 *
 */
class Permission
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
	const PERM_MODUL = 'modules';

	/**
	 * Name des Rechts für das Struktur
	 * @var string
	 */
	const PERM_STRUCTURE = 'structure';

	/**
	 * Prüft die Rechte für den aktuellen User.
	 *
	 * Unit Testing
	 * Die Rechteverwaltung ist zu nah am RedaxoCore, um das auf die Schnelle simulieren zu können.
	 * @codeCoverageIgnore
	 *
	 * @param string $perm eine der PERM-Konstanten
	 * @return boolean
	 * @SuppressWarnings(PHPMD.StaticAccess)
	 */
	public function check($perm)
	{
		$user = rex::getUser();
		$complexPerm = $user->getComplexPerm($perm);

		$hasSpecialPerm = true;
		switch (get_class($complexPerm)) {
			case 'rex_media_perm':
				/* @var $complexPerm rex_media_perm */
				$hasSpecialPerm = $complexPerm->hasMediaPerm();
				break;
			case 'rex_structure_perm':
				/* @var $complexPerm rex_structure_perm */
				$hasSpecialPerm = $complexPerm->hasStructurePerm();
				break;
			case 'rex_module_perm':
				/* @var $complexPerm rex_module_perm */
				$hasSpecialPerm = $complexPerm->hasAll();
				break;
			default:
				throw new Exception('"'.get_class($complexPerm).'": unknown permission class');
				break;
		}

		return $user->isAdmin() || $user->hasPerm($perm) || $hasSpecialPerm;
		/* || (isset($complexPerm) && $complexPerm->hasAll()) */
	}
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
		//;
	}

	/**
	 * forbid cloning
	 */
	final public function __clone()
	{
		throw new CloneException();
	}
// </editor-fold>
}
