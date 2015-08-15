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
		if (!$show_all) {
			$where.='count > 0';
		}

		if (!$show_inactive) {
			if ($where !== '') {
				$where .='and ';
			}
			$where.='t.active = 1';
		}

		if($where !== '') {
			$where='where '.$where;
		}

		$sql = <<<SQL
SELECT
	t.*,
	a.id as article_id,
	count(a.id) as count_articles,
	count(t2.id) as count_templates,
	count(a.id) + count(t2.id) count
FROM `rex_template` t
left join rex_article a on t.id=a.template_id
left join `rex_template` t2 on t.id <> t2.id and t2.content like concat('%TEMPLATE[', t.id, ']%')
$where
group by a.template_id,t.id

SQL;

		return $rexSQL->getArray($sql);
	}
}