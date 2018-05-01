<?php

/**
 * Datei für das Modul "Module"
 *
 * @version       1.0 / 2016-05-05
 * @author        akrys
 */
namespace akrys\redaxo\addon\UsageCheck\RexV5\Modules;

/**
 * Description of Pictures
 *
 * @author akrys
 */
class Modules
	extends \akrys\redaxo\addon\UsageCheck\Modules\Modules
{

	/**
	 * SQL generieren
	 * @param string $where
	 * @return string
	 */
	protected function getSQL($where)
	{
		//Keine integer oder Datumswerte in einem concat!
		//Vorallem dann nicht, wenn MySQL < 5.5 im Spiel ist.
		// -> https://stackoverflow.com/questions/6397156/why-concat-does-not-default-to-default-charset-in-mysql/6669995#6669995
		$moduleTable = \rex::getTable('module');
		$articleSliceTable = \rex::getTable('article_slice');
		$articleTable = \rex::getTable('article');

		$sql = <<<SQL
SELECT m.name,
	m.id,
	m.createdate,
	m.updatedate,
	group_concat(
		concat(
			cast(s.id as char),"\t",
			cast(s.clang_id as char),"\t",
			cast(s.ctype_id as char),"\t",
			cast(a.id as char),"\t",
			cast(a.parent_id as char),"\t",
			a.name) Separator "\n"
		) slice_data
FROM `$moduleTable` m
left join $articleSliceTable s on s.module_id=m.id
left join $articleTable a on s.article_id=a.id and s.clang_id=a.clang_id

$where
group by m.id

SQL;
		return $sql;
	}

	/**
	 * Menü ausgeben
	 * @return void
	 * @param string $subpage
	 * @param string $showAllParam
	 * @param string $showAllLinktext
	 */
	public function outputMenu($subpage, $showAllParam, $showAllLinktext)
	{
		$url = 'index.php?page='.\akrys\redaxo\addon\UsageCheck\Config::NAME.'/'.$subpage.$showAllParam;
		$menu = new \rex_fragment([
			'url' => $url,
			'linktext' => $showAllLinktext,
			'texts' => [
				\rex_i18n::rawMsg('akrys_usagecheck_module_intro_text'),
			],
		]);
		return $menu->parse('fragments/menu/linktext.php');
	}

	/**
	 * Abfrage der Rechte für das Modul
	 *
	 * Unit Testing
	 * Die Rechteverwaltung ist zu nah am RedaxoCore, um das auf die Schnelle simulieren zu können.
	 * @codeCoverageIgnore
	 *
	 * @param array $item
	 * @return boolean
	 * @SuppressWarnings(PHPMD.StaticAccess)
	 */
	public function hasRights($item)
	{
		$user = \rex::getUser();
		if (!$user->isAdmin() && !$user->getComplexPerm('modules')->hasPerm($item['id'])) {
			return false;
		}
		return true;
	}
}
