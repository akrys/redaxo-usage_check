<?php

/**
 * User-Rechte
 */
namespace FriendsOfRedaxo\addon\UsageCheck;

use Exception;
use FriendsOfRedaxo\addon\UsageCheck\Enum\Perm;
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
	 * Prüft die Rechte für den aktuellen User.
	 *
	 * Unit Testing
	 * Die Rechteverwaltung ist zu nah am RedaxoCore, um das auf die Schnelle simulieren zu können.
	 * @codeCoverageIgnore
	 *
	 * @param Perm $perm eine der PERM-Konstanten
	 * @return boolean
	 * @SuppressWarnings(PHPMD.StaticAccess)
	 */
	public function check(Perm $perm): bool
	{
		$user = rex::getUser();
		$complexPerm = $user->getComplexPerm($perm->value);

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
		}

		return $user->isAdmin() || $user->hasPerm($perm->value) || $hasSpecialPerm;
		/* || (isset($complexPerm) && $complexPerm->hasAll()) */
	}
// <editor-fold defaultstate="collapsed" desc="Singleton">
	/**
	 * Instance
	 * @var Permission
	 */
	private static $instance = null;

	/**
	 * create Singleton Instance
	 * @return Permission
	 */
	public static function getInstance(): Permission
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
