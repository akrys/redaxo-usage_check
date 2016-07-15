<?php

/**
 * User-Rechte
 */
namespace akrys\redaxo\addon\UsageCheck\RexV4;

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
	 */
	public function check($perm)
	{
		$permReal = $this->mapPerm($perm);
		return $GLOBALS['REX']['USER']->isAdmin() || (isset($GLOBALS['REX']['USER']->pages[$permReal]) && $GLOBALS['REX']['USER']->pages[$permReal]->getPage()->checkPermission($GLOBALS['REX']['USER']));
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
}
