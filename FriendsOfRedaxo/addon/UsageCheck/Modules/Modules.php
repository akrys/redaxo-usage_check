<?php

/**
 * Datei für das Modul "Module"
 *
 * @version       1.0 / 2015-08-09
 * @author        akrys
 */
namespace FriendsOfRedaxo\addon\UsageCheck\Modules;

use FriendsOfRedaxo\addon\UsageCheck\Lib\BaseModule;
use FriendsOfRedaxo\addon\UsageCheck\Permission;
use rex_sql;

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
		$res = $rexSQL->getArray($sql);
		$result = [];
		foreach ($res as $articleData) {
			if (isset($articleData['usagecheck_s_id']) && (int) $articleData['usagecheck_s_id'] > 0) {
				$result['modules'][$articleData['usagecheck_s_id']] = $articleData;
			}
		}
		return [
			'first' => $res[0],
			'result' => $result,
			'fields' => $this->tableFields,
		];
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
		$additionalFields = '';
		$where = '';
		$whereArray = [];
		$groupBy = 'group by m.id,s.id';

		$rexSQL = rex_sql::factory();
		if ($detail_id) {
			$whereArray[] = 'm.id='.$rexSQL->escape($detail_id);
			$groupBy = '';
			$additionalFields = <<<SQL
			,s.id usagecheck_s_id,
			s.clang_id usagecheck_s_clang_id,
			s.ctype_id usagecheck_s_ctype_id,
			a.id usagecheck_a_id ,
			a.parent_id usagecheck_a_parent_id,
			a.name usagecheck_a_name

SQL;
		} else {
			if (!$this->showAll) {
				$whereArray[] .= 's.id is null';
			}

			$additionalFields = ', s.id as slice_data';
		}

		if (count($whereArray) > 0) {
			$where .= 'where '.implode(' and ', $whereArray);
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
	m.updatedate

	$additionalFields
FROM `$moduleTable` m
left join $articleSliceTable s on s.module_id=m.id
left join $articleTable a on s.article_id=a.id and s.clang_id=a.clang_id

$where
$groupBy

SQL;
		return $sql;
	}
}
