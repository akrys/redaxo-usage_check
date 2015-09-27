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
		return $GLOBALS['REX']['USER']->isAdmin() || (isset($GLOBALS['REX']['USER']->pages[$perm]) && $GLOBALS['REX']['USER']->pages[$perm]->getPage()->checkPermission($GLOBALS['REX']['USER']));
	}
}