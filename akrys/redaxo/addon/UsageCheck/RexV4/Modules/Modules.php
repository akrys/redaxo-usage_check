<?php
/**
 * Datei für das Modul "Module"
 *
 * @version       1.0 / 2016-05-05
 * @author        akrys
 */
namespace akrys\redaxo\addon\UsageCheck\RexV4\Modules;

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
		$moduleTable = \akrys\redaxo\addon\UsageCheck\RedaxoCall::getAPI()->getTable('module');
		$articleSliceTable = \akrys\redaxo\addon\UsageCheck\RedaxoCall::getAPI()->getTable('article_slice');
		$articleTable = \akrys\redaxo\addon\UsageCheck\RedaxoCall::getAPI()->getTable('article');

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
		$url = 'index.php?page='.\akrys\redaxo\addon\UsageCheck\Config::NAME.'&subpage='.$subpage.$showAllParam;
		$text = \akrys\redaxo\addon\UsageCheck\RedaxoCall::getAPI()->getI18N('akrys_usagecheck_module_intro_text');
		?>

		<p class="rex-tx1"><a href="<?php echo $url; ?>"><?php echo $showAllLinktext; ?></a></p>
		<p class="rex-tx1"><?php echo $text; ?></p>

		<?php
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
	 */
	public function hasRights($item)
	{
		if (!$GLOBALS['REX']['USER']->isAdmin() && !$GLOBALS['REX']['USER']->hasPerm('module['.$item['id'].']')) {
			return false;
		}
		return true;
	}
}
