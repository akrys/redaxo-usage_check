<?php

/**
 * Datei für das Template-Modul
 *
 * @version       1.0 / 2015-08-09
 * @author        akrys
 */
namespace akrys\redaxo\addon\UsageCheck\Modules;

use \akrys\redaxo\addon\UsageCheck\Permission;

/**
 * Description of Templates
 *
 * @author akrys
 */
class Templates
	extends \akrys\redaxo\addon\UsageCheck\Lib\BaseModule
{
	const TYPE = 'templates';

	/**
	 * Anzeigemodus für "Ianktive zeigen"
	 * @var boolean
	 */
	private $showInactive = false;

	/**
	 * Anzeigemodus "inaktive zeigen" umstellen
	 * @param boolean $bln
	 */
	public function showInactive($bln)
	{
		$this->showInactive = (boolean) $bln;
	}

	/**
	 * Nicht genutze Module holen
	 *
	 * @return array
	 *
	 * @todo bei Instanzen mit vielen Slices testen. Die Query
	 *       riecht nach Performance-Problemen -> 	Using join buffer (Block Nested Loop)
	 * @SuppressWarnings(PHPMD.StaticAccess)
	 */
	public function get()
	{
		$showInactive = $this->showInactive;

		if (!Permission::getInstance()->check(Permission::PERM_STRUCTURE)) {
			//Permission::PERM_TEMPLATE
			return false;
		}

		$user = \rex::getUser();
		if (!$user->isAdmin() && $showInactive === true) {
			$showInactive = false;
		}

		$rexSQL = $this->getRexSql();

		$sql = $this->getSQL();
		$return = $rexSQL->getArray($sql);
		// @codeCoverageIgnoreStart
		//SQL-Fehler an der Stelle recht schwer zu testen, aber dennoch sinnvoll enthalten zu sein.
		if (!$return) {
			\akrys\redaxo\addon\UsageCheck\Error::getInstance()->add($rexSQL->getError());
		}
		// @codeCoverageIgnoreEnd

		return $return;
	}

	/**
	 * Parameter-Kriterien anwenden.
	 *
	 * Die Funktion dient der Komplexitätsminderung, die von phpmd angemahnt wurde.
	 *
	 * @param string &$where
	 * @param string &$having
	 */
	private function addParamCriteria(&$where, &$having)
	{
		if (!$this->showAll) {
			$having .= 'articles is null and templates is null';
		}
		if (!$this->showInactive) {
			$where .= 't.active = 1';
		}
	}

	/**
	 * Keywords anwenden, wenn nötig.
	 *
	 * Die Funktion dient der Komplexitätsminderung, die von phpmd angemahnt wurde.
	 *
	 * @param string &$where
	 * @param string &$having
	 */
	private function addParamStatementKeywords(&$where, &$having)
	{
		if ($where !== '') {
			$where = 'where '.$where.' ';
		}

		if ($having !== '') {
			$having = 'having '.$having.' ';
		}
	}

	/**
	 * SQL für Redaxo 5
	 * @return string
	 */
	protected function getSQL()
	{
		$where = '';
		$having = '';

		$this->addParamCriteria($where, $having);
		$this->addParamStatementKeywords($where, $having);

		//Keine integer oder Datumswerte in einem concat!
		//Vorallem dann nicht, wenn MySQL < 5.5 im Spiel ist.
		// -> https://stackoverflow.com/questions/6397156/why-concat-does-not-default-to-default-charset-in-mysql/6669995#6669995

		$templateTable = $this->getTable('template');
		$articleTable = $this->getTable('article');

		$sql = <<<SQL
SELECT
	t.*,
	a.id as article_id,
	group_concat(distinct concat(
		cast(a.id as char),"\t",
		cast(a.parent_id as char),"\t",
		cast(a.startarticle as char),"\t",
		a.name,"\t",
		cast(a.clang_id as char)) Separator "\n"
	) as articles,
	group_concat(distinct concat(
		cast(t2.id as char),"\t",
		t2.name) Separator "\n"
	) as templates
FROM `$templateTable` t
left join $articleTable a on t.id=a.template_id
left join `$templateTable` t2 on t.id <> t2.id and t2.content like concat('%TEMPLATE[', t.id, ']%')

$where

group by a.template_id,t.id

$having

SQL;
		return $sql;
	}
}
