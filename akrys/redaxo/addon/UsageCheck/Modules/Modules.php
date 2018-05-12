<?php

/**
 * Datei für das Modul "Module"
 *
 * @version       1.0 / 2015-08-09
 * @author        akrys
 */
namespace akrys\redaxo\addon\UsageCheck\Modules;

use \akrys\redaxo\addon\UsageCheck\Permission;

/**
 * Description of Modules
 *
 * @author akrys
 */
class Modules
	extends BaseModule
{
	const TYPE = 'modules';

	/**
	 * Anzeigemodus für "Alle Anzeigen"
	 * @var boolean
	 */
	private $showAll = false;

	/**
	 * Anzeigemodus "alle zeigen" umstellen
	 * @param boolean $bln
	 */
	public function showAll($bln)
	{
		$this->showAll = (boolean) $bln;
	}

	/**
	 * Nicht genutze Module holen
	 *
	 * @return array
	 *
	 * @todo bei Instanzen mit vielen Slices testen. Die Query
	 *       riecht nach Performance-Problemen -> 	Using join buffer (Block Nested Loop)
	 */
	public function getModules()
	{
		$showAll = $this->showAll;

		if (!Permission::getInstance()->check(Permission::PERM_STRUCTURE)) {
			//Permission::PERM_MODUL
			return false;
		}

		$rexSQL = $this->getRexSql();

		$where = '';
		if (!$showAll) {
			$where .= 'where s.id is null';
		}
		$sql = $this->getSQL($where);

		return $rexSQL->getArray($sql);
	}
//
///////////////////// Tmplementation aus RexV5 /////////////////////
//

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
		$moduleTable = $this->getTable('module');
		$articleSliceTable = $this->getTable('article_slice');
		$articleTable = $this->getTable('article');

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
				$this->i18nRaw('akrys_usagecheck_module_intro_text'),
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
