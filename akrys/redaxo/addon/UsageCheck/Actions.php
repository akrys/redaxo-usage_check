<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
namespace akrys\redaxo\addon\UsageCheck;

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
		$rexSQL = new \rex_sql;

		$where = '';
		if (!$show_all) {
			$where.='where ma.id is null';
		}

		$sql = <<<SQL
SELECT a.*, group_concat(concat(ma.module_id,"\t",m.name) separator "\n") as modul
FROM rex_action a
left join rex_module_action ma on ma.action_id=a.id
left join rex_module m on ma.module_id=m.id or m.id is null

$where

group by a.id

SQL;

		return $rexSQL->getArray($sql);
	}
}