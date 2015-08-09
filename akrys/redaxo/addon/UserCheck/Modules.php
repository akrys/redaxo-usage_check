<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

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
class Modules
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
	public static function getModules($show_all = false)
	{
		$rexSQL = new rex_sql;

		$where = '';
		if (!$show_all) {
			$where.='where s.id is null';
		}

		$sql = <<<SQL
SELECT m.name,
	count(s.id) as count,
	m.createdate,
	m.updatedate,
	s.id as slice_id
FROM `rex_module` m
left join rex_article_slice s on s.modultyp_id=m.id
$where
group by m.id

SQL;

		return $rexSQL->getArray($sql);
	}
}