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

		if (!\akrys\redaxo\addon\UsageCheck\Permission::check(\akrys\redaxo\addon\UsageCheck\Permission::PERM_STRUCTURE)) {
			//\akrys\redaxo\addon\UsageCheck\Permission::PERM_TEMPLATE
			return false;
		}

		//Parameter-Korrektur, wenn der User KEIN Admin ist
		//Der darf die inaktiven Templats nämlich sowieso nicht sehen.
		if (!$GLOBALS['REX']['USER']->isAdmin() && $show_inactive === true) {
			$show_inactive = false;
		}

		if (\akrys\redaxo\addon\UsageCheck\RedaxoCall::getRedaxoVersion() == \akrys\redaxo\addon\UsageCheck\RedaxoCall::REDAXO_VERSION_4) {
			$rexSQL = new \rex_sql;
		} else {
			$rexSQL = \rex_sql::factory();
		}

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

		//Keine integer oder Datumswerte in einem concat!
		//Vorallem dann nicht, wenn MySQL < 5.5 im Spiel ist.
		// -> https://stackoverflow.com/questions/6397156/why-concat-does-not-default-to-default-charset-in-mysql/6669995#6669995
		$sql = <<<SQL
SELECT
	t.*,
	a.id as article_id,
	group_concat(concat(
		cast(a.id as char),"\t",
		cast(a.re_id as char),"\t",
		cast(a.startpage as char),"\t",
		a.name) Separator "\n"
	) as articles,
	group_concat(concat(
		cast(t2.id as char),"\t",
		t2.name) Separator "\n"
	) as templates
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
