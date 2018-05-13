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
	extends \akrys\redaxo\addon\UsageCheck\Lib\BaseModule
{
	const TYPE = 'modules';

	/**
	 * Nicht genutze Module holen
	 *
	 * @return array
	 *
	 * @todo bei Instanzen mit vielen Slices testen. Die Query
	 *       riecht nach Performance-Problemen -> 	Using join buffer (Block Nested Loop)
	 */
	public function get()
	{
		if (!Permission::getInstance()->check(Permission::PERM_STRUCTURE)) {
			//Permission::PERM_MODUL
			return false;
		}

		$rexSQL = $this->getRexSql();
		$sql = $this->getSQL();

		return $rexSQL->getArray($sql);
	}

	/**
	 * Details zu einem Eintrag holen
	 * @param int $item_id
	 * @return array
	 */
	public function getDetails($item_id)
	{
		if (!Permission::getInstance()->check(Permission::PERM_STRUCTURE)) {
			//Permission::PERM_MODUL
			return false;
		}

		$rexSQL = $this->getRexSql();
		$sql = $this->getSQL($item_id);
		return $rexSQL->getArray($sql);
	}
//
///////////////////// Tmplementation aus RexV5 /////////////////////
//

	/**
	 * SQL generieren
	 * @param int $detail_id
	 * @return string
	 */
	protected function getSQL(/* int */$detail_id = null)
	{
		$where = '';
		if (!$this->showAll) {
			$where .= 'where s.id is null';
		}

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
}
