<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
namespace akrys\redaxo\addon\UsageCheck;

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
	 * Prüft die Rechte für den aktuellen User.
	 *
	 * @param string $perm eine der PERM-Konstanten
	 * @return boolean
	 */
	public static function check($perm)
	{
		if (\akrys\redaxo\addon\UsageCheck\RedaxoCall::getRedaxoVersion() == \akrys\redaxo\addon\UsageCheck\RedaxoCall::REDAXO_VERSION_4) {
			$perm = self::mapPermRedaxo4($perm);
			return $GLOBALS['REX']['USER']->isAdmin() || (isset($GLOBALS['REX']['USER']->pages[$perm]) && $GLOBALS['REX']['USER']->pages[$perm]->getPage()->checkPermission($GLOBALS['REX']['USER']));
		} else {
			$user = \rex::getUser();
			$perm = self::mapPermRedaxo5($perm);
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
//				case 'rex_module_perm':
//					/* @var $complexPerm rex_module_perm */
//					var_dump($complexPerm);
//					$hasSpecialPerm = false;
////					$hasSpecialPerm = $complexPerm->hasModulePerm();
//					break;
				default:
					throw new \Exception('"'.get_class($complexPerm).'": unknown permission class');
					break;
			}

			return $user->isAdmin() || $user->hasPerm($perm) || $hasSpecialPerm /* || (isset($complexPerm) && $complexPerm->hasAll()) */;
		}
	}

	/**
	 * Permission Mapping (Redaxo 5)
	 * @param string $perm
	 * @return string
	 */
	private static function mapPermRedaxo4($perm)
	{
		$return = '';
		switch ($perm) {
			case self::PERM_MEDIAPOOL:
				$return = 'mediapool';
				break;
			case self::PERM_MEDIA:
				$return = 'mediapool';
				break;
			case self::PERM_MODUL:
				$return = 'module';
				break;
			case self::PERM_STRUCTURE:
				$return = 'structure';
				break;
			case self::PERM_TEMPLATE:
				$return = 'template';
				break;
			case self::PERM_XFORM:
				$return = 'xform';
				break;
			default:
				$return = $perm;
				break;
		}
		return $return;
	}

	/**
	 * Permission Mapping (Redaxo 5)
	 * @param string $perm
	 * @return string
	 */
	private static function mapPermRedaxo5($perm)
	{
		$return = '';
		switch ($perm) {
			case self::PERM_MEDIAPOOL:
				$return = 'mediapool';
				break;
			case self::PERM_MEDIA:
				$return = 'media';
				break;
			case self::PERM_MODUL:
				$return = 'modules';
				break;
			case self::PERM_STRUCTURE:
				$return = 'structure';
				break;
			case self::PERM_TEMPLATE:
				$return = 'template';
				break;
			case self::PERM_XFORM:
				$return = 'xform';
				break;
			default:
				$return = $perm;
				break;
		}
		return $return;
	}
}
