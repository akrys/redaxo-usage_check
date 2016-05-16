<?php
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
namespace akrys\redaxo\addon\UsageCheck\RexV4\Modules;

require_once __DIR__.'/../../Modules/Modules.php';

/**
 * Datei für ...
 *
 * @version       1.0 / 2016-05-05
 * @package       new_package
 * @subpackage    new_subpackage
 * @author        akrys
 */

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
		$moduleTable = \akrys\redaxo\addon\UsageCheck\RedaxoCall::getTable('module');
		$articleSliceTable = \akrys\redaxo\addon\UsageCheck\RedaxoCall::getTable('article_slice');
		$articleTable = \akrys\redaxo\addon\UsageCheck\RedaxoCall::getTable('article');

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
FROM `$moduleTable` m
left join $articleSliceTable s on s.modultyp_id=m.id
left join $articleTable a on s.article_id=a.id and s.clang=a.clang

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
		?>

		<p class="rex-tx1"><a href="index.php?page=<?php echo \akrys\redaxo\addon\UsageCheck\Config::NAME; ?>&subpage=<?php echo $subpage; ?><?php echo $showAllParam; ?>"><?php echo $showAllLinktext; ?></a></p>
		<p class="rex-tx1"><?php echo \akrys\redaxo\addon\UsageCheck\RedaxoCall::i18nMsg('akrys_usagecheck_module_intro_text'); ?></p>

		<?php
	}

	/**
	 * Abfrage der Rechte für das Modul
	 *
	 * @param array $item
	 * @return boolean
	 */
	public function hasRights($item)
	{
		if (!$GLOBALS['REX']['USER']->isAdmin() && !$GLOBALS['REX']['USER']->hasPerm('module['.$item['id'].']')) {
			return false;
		}
		return true;
	}
}
