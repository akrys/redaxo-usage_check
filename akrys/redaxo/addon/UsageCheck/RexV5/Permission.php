<?php

/**
 * User-Rechte
 */
namespace akrys\redaxo\addon\UsageCheck\RexV5;

/**
 * User-Rechte für Zugriffe abfragen.
 *
 * Redaxo 5
 *
 */
class Permission
	extends \akrys\redaxo\addon\UsageCheck\Permission
{

	/**
	 * Prüft die Rechte für den aktuellen User.
	 *
	 * @param string $perm eine der PERM-Konstanten
	 * @return boolean
	 * @SuppressWarnings(PHPMD.StaticAccess)
	 */
	public function check($perm)
	{
		$user = \rex::getUser();
		$permReal = $this->mapPerm($perm);
		$complexPerm = $user->getComplexPerm($permReal);

		$hasSpecialPerm = true;
		switch (get_class($complexPerm)) {
			case 'rex_media_perm':
				/* @var $complexPerm \rex_media_perm */
				$hasSpecialPerm = $complexPerm->hasMediaPerm();
				break;
			case 'rex_structure_perm':
				/* @var $complexPerm \rex_structure_perm */
				$hasSpecialPerm = $complexPerm->hasStructurePerm();
				break;
			case 'rex_module_perm':
//					/* @var $complexPerm \rex_module_perm */
//					var_dump($complexPerm);
//					$hasSpecialPerm = false;
////					$hasSpecialPerm = $complexPerm->hasModulePerm();
				break;
			default:
				throw new \Exception('"'.get_class($complexPerm).'": unknown permission class');
				break;
		}

		return $user->isAdmin() || $user->hasPerm($permReal) || $hasSpecialPerm;
		/* || (isset($complexPerm) && $complexPerm->hasAll()) */
	}

	/**
	 * Permission Mapping (Redaxo 5)
	 * @param string $perm
	 * @return string
	 */
	protected function mapPerm($perm)
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
