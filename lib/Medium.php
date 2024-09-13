<?php

/**
 * Datei für ...
 *
 * @author        akrys
 */
namespace FriendsOfRedaxo\UsageCheck;

use FriendsOfRedaxo\UsageCheck\Exception\FunctionNotCallableException;
use rex;
use rex_media;
use rex_path;

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
	 * @param array<string, mixed> $item Idezes: category_id, filename
	 * @return rex_media
	 * @throws FunctionNotCallableException
	 * @SuppressWarnings(PHPMD.StaticAccess)
	 */
	public static function get(array $item): rex_media
	{
		$user = rex::getUser();
		/**
		 * @var \rex_media_perm $complexPerm
		 */
		$complexPerm = $user?->getComplexPerm('media');
		if (!$user?->isAdmin() &&
			!(is_object($complexPerm) &&
			$complexPerm->hasCategoryPerm($item['category_id']))) {
			//keine Rechte am Medium
			throw new FunctionNotCallableException();
		}

		//Das Medium wird später gebraucht.
		/* @var $medium rex_media */
		$medium = rex_media::get($item['filename']);
		if (!$medium) {
			throw new Exception\MediaNotFoundException('Medium '.$item['filename'].' not found');
		}
		return $medium;
	}

	/**
	 * Überprüfen, ob eine Datei existiert.
	 *
	 * @global type $REX
	 * @param array<string, mixed> $item
	 * @return boolean
	 * @SuppressWarnings(PHPMD.StaticAccess)
	 */
	public static function exists(array $item)
	{
		return file_exists(rex_path::media().DIRECTORY_SEPARATOR.$item['filename']);
	}
}
