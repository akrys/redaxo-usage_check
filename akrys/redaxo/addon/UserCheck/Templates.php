<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
namespace akrys\redaxo\addon\UserCheck;

/**
 * Datei für ...
 *
 * @version       1.0 / 2015-08-09
 * @package       new_package
 * @subpackage    new_subpackage
 * @author        akrys
 */

/**
 * Description of Templates
 *
 * @author akrys
 */
class Templates
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
	public static function getTemplates($show_all = false, $show_inactive = false)
	{
		$rexSQL = new \rex_sql;

		$where = '';
		$having = '';

		if (!$show_all) {
			$having.='articles is null and templates is null';
		}

		if (!$show_inactive) {
			$where.='t.active = 1';
		}

		if ($where !== '') {
			$where = 'where '.$where.' ';
		}

		if ($having !== '') {
			$having = 'having '.$having.' ';
		}

		$sql = <<<SQL
SELECT
	t.*,
	a.id as article_id,
	group_concat(concat(a.id,"\t",a.re_id,"\t",a.startpage,"\t",a.name) Separator "\n") as articles,
	group_concat(concat(t2.id ,"\t",t2.name) Separator "\n") as templates
FROM `rex_template` t
left join rex_article a on t.id=a.template_id
left join `rex_template` t2 on t.id <> t2.id and t2.content like concat('%TEMPLATE[', t.id, ']%')

$where

group by a.template_id,t.id

$having

SQL;

		return $rexSQL->getArray($sql);
	}
}