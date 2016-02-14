<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
namespace akrys\redaxo\addon\UsageCheck\Modules;

require_once __DIR__.'/../Permission.php';

/**
 * Datei für ...
 *
 * @version       1.0 / 2015-08-09
 * @package       new_package
 * @subpackage    new_subpackage
 * @author        akrys
 */

/**
 * Description of Modules
 *
 * @author akrys
 */
class Actions
{

	/**
	 * Nicht genutze Module holen
	 *
	 * @param boolean $show_all
	 * @return array
	 *
	 * @todo bei Instanzen mit vielen Slices testen. Die Query
	 *       riecht nach Performance-Problemen -> 	Using join buffer (Block Nested Loop)
	 */
	public static function getActions($show_all = false)
	{
		if (!\akrys\redaxo\addon\UsageCheck\Permission::check(\akrys\redaxo\addon\UsageCheck\Permission::PERM_MODUL)) {
			return false;
		}

		if (\akrys\redaxo\addon\UsageCheck\RedaxoCall::getRedaxoVersion() == \akrys\redaxo\addon\UsageCheck\RedaxoCall::REDAXO_VERSION_4) {
			$rexSQL = new \rex_sql;
		} else {
			$rexSQL = \rex_sql::factory();
		}

		$where = '';
		if (!$show_all) {
			$where.='where ma.id is null';
		}

		//Keine integer oder Datumswerte in einem concat!
		//Vorallem dann nicht, wenn MySQL < 5.5 im Spiel ist.
		// -> https://stackoverflow.com/questions/6397156/why-concat-does-not-default-to-default-charset-in-mysql/6669995#6669995
		$sql = <<<SQL
SELECT a.*, group_concat(concat(
	cast(ma.module_id as char),"\t",
	m.name
) separator "\n") as modul
FROM rex_action a
left join rex_module_action ma on ma.action_id=a.id
left join rex_module m on ma.module_id=m.id or m.id is null

$where

group by a.id

SQL;

		return $rexSQL->getArray($sql);
	}
}
