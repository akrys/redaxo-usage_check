<?php

/**
 * Datei für ...
 *
 * @version       1.0 / 2018-05-13
 * @author        akrys
 */
namespace akrys\redaxo\addon\UsageCheck;

/**
 * Description of Medium
 *
 * @author akrys
 */
class Medium
{

	/**
	 * Holt ein Medium-Objekt mit Prüfung der Rechte
	 *
	 * @param array $item Idezes: category_id, filename
	 * @return \rex_media
	 * @throws \akrys\redaxo\addon\UsageCheck\Exception\FunctionNotCallableException
	 * @SuppressWarnings(PHPMD.StaticAccess)
	 */
	public static function get($item)
	{
		$user = \rex::getUser();
		$complexPerm = $user->getComplexPerm('media');
		if (!$user->isAdmin() &&
			!(is_object($complexPerm) &&
			$complexPerm->hasCategoryPerm($item['category_id']))) {
			//keine Rechte am Medium
			throw new \akrys\redaxo\addon\UsageCheck\Exception\FunctionNotCallableException();
		}

		//Das Medium wird später gebraucht.
		/* @var $medium \rex_media */
		$medium = \rex_media::get($item['filename']);
		return $medium;
	}

	/**
	 * Überprüfen, ob eine Datei existiert.
	 *
	 * @global type $REX
	 * @param array $item
	 * @return boolean
	 * @SuppressWarnings(PHPMD.StaticAccess)
	 */
	public static function exists($item)
	{
		return file_exists(\rex_path::media().DIRECTORY_SEPARATOR.$item['filename']);
	}
}
