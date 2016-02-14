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
		if (!\akrys\redaxo\addon\UsageCheck\Permission::check(\akrys\redaxo\addon\UsageCheck\Permission::PERM_STRUCTURE)) {
			//\akrys\redaxo\addon\UsageCheck\Permission::PERM_MODUL
			return false;
		}

		if (\akrys\redaxo\addon\UsageCheck\RedaxoCall::getRedaxoVersion() == \akrys\redaxo\addon\UsageCheck\RedaxoCall::REDAXO_VERSION_4) {
			$rexSQL = new \rex_sql;
		} else {
			$rexSQL = \rex_sql::factory();
		}

		$where = '';
		if (!$show_all) {
			$where.='where s.id is null';
		}

		//Keine integer oder Datumswerte in einem concat!
		//Vorallem dann nicht, wenn MySQL < 5.5 im Spiel ist.
		// -> https://stackoverflow.com/questions/6397156/why-concat-does-not-default-to-default-charset-in-mysql/6669995#6669995
		$sql = <<<SQL
SELECT m.name,
	m.id,
	m.createdate,
	m.updatedate,
	group_concat(
		concat(
			cast(s.id as char),"\t",
			cast(s.clang as char),"\t",
			cast(s.ctype as char),"\t",
			cast(a.id as char),"\t",
			cast(a.re_id as char),"\t",
			a.name) Separator "\n"
		) slice_data
FROM `rex_module` m
left join rex_article_slice s on s.modultyp_id=m.id
left join rex_article a on s.article_id=a.id and s.clang=a.clang

$where
group by m.id

SQL;

		return $rexSQL->getArray($sql);
	}
}
