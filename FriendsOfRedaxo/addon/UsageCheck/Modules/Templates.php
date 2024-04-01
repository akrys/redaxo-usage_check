<?php

/**
 * Datei für das Template-Modul
 *
 * @version       1.0 / 2015-08-09
 * @author        akrys
 */
namespace FriendsOfRedaxo\addon\UsageCheck\Modules;

use FriendsOfRedaxo\addon\UsageCheck\Enum\ModuleType;
use FriendsOfRedaxo\addon\UsageCheck\Enum\Perm;
use FriendsOfRedaxo\addon\UsageCheck\Error;
use FriendsOfRedaxo\addon\UsageCheck\Lib\BaseModule;
use FriendsOfRedaxo\addon\UsageCheck\Permission;
use rex;
use rex_sql;

/**
 * Description of Templates
 *
 * @author akrys
 */
class Templates extends BaseModule
{
	/**
	 * @var ModuleType
	 */
	const TYPE = ModuleType::TEMPLATES;

	/**
	 * Anzeigemodus für "Ianktive zeigen"
	 * @var boolean
	 */
	private bool $showInactive = false;

	/**
	 * Anzeigemodus "inaktive zeigen" umstellen
	 * @param boolean $bln
	 */
	public function showInactive(bool $bln): void
	{
		$this->showInactive = $bln;
	}

	/**
	 * Nicht genutze Module holen
	 *
	 * @return array<int|string, mixed>
	 *
	 * @todo bei Instanzen mit vielen Slices testen. Die Query
	 *       riecht nach Performance-Problemen -> 	Using join buffer (Block Nested Loop)
	 * @SuppressWarnings(PHPMD.StaticAccess)
	 */
	public function get(): array
	{
		$showInactive = $this->showInactive;

		if (!Permission::getInstance()->check(Perm::PERM_STRUCTURE)) {
			//Permission::PERM_TEMPLATE
			return [];
		}

		$user = rex::getUser();
		if (!$user?->isAdmin() && $showInactive === true) {
			$showInactive = false;
		}

		$rexSQL = $this->getRexSql();

		$sql = $this->getSQL();
		$return = $rexSQL->getArray($sql);
		// @codeCoverageIgnoreStart
		//SQL-Fehler an der Stelle recht schwer zu testen, aber dennoch sinnvoll enthalten zu sein.
		if (!$return) {
			$error = 'SQL: '.$sql.PHP_EOL.
				'ErrorNo: '.$rexSQL->getErrno().PHP_EOL.
				'Error: '.$rexSQL->getError().PHP_EOL;
			Error::getInstance()->add($error);
		}
		// @codeCoverageIgnoreEnd

		return $return;
	}

	/**
	 * Details zu einem Eintrag holen
	 * @param int $item_id
	 * @return array<string, mixed>
	 */
	public function getDetails(int $item_id): array
	{
		if (!Permission::getInstance()->check(Perm::PERM_STRUCTURE)) {
			//Permission::PERM_TEMPLATE
			return [];
		}
		$result = [];

		$result = [];
		$rexSQL = $this->getRexSql();
		$sql = $this->getSQL($item_id);
		$res = $rexSQL->getArray($sql);

		foreach ($res as $articleData) {
			if (isset($articleData['usagecheck_article_a_id']) && (int) $articleData['usagecheck_article_a_id'] > 0) {
				$index = $articleData['usagecheck_article_a_id'].'_'.$articleData['usagecheck_article_a_clang_id'];
				$result['articles'][$index] = $articleData;
			}
			if (isset($articleData['usagecheck_template_t2_id']) &&
				(int) $articleData['usagecheck_template_t2_id'] > 0) {
				$result['templates'][$articleData['usagecheck_template_t2_id']] = $articleData;
			}
		}
		return [
			'first' => $res[0],
			'result' => $result,
			'fields' => $this->tableFields,
		];
	}

	/**
	 * Parameter-Kriterien anwenden.
	 *
	 * Die Funktion dient der Komplexitätsminderung, die von phpmd angemahnt wurde.
	 *
	 * @param string &$where
	 * @param string &$having
	 */
	private function addParamCriteria(string &$where, string &$having): void
	{
		if (!$this->showAll) {
			$having .= '(articles is null or articles = 0) and (templates is null or templates = 0)';
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
	private function addParamStatementKeywords(string &$where, string &$having): void
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
	 * @param int $detail_id
	 * @return string
	 * @SuppressWarnings(PHPMD.ElseExpression)
	 * -> zu tief verschachtelt.... vllt. Funktionsauslagerung?
	 */
	protected function getSQL(int $detail_id = null): string
	{
		$rexSQL = rex_sql::factory();

		$where = '';
		$having = '';

		$groupBy = null;

		//Keine integer oder Datumswerte in einem concat!
		//Vorallem dann nicht, wenn MySQL < 5.5 im Spiel ist.
		// -> https://stackoverflow.com/questions/6397156/why-concat-does-not-default-to-default-charset-in-mysql/6669995#6669995

		$templateTable = $this->getTable('template');
		$articleTable = $this->getTable('article');

		$additionalFields = '';
		if ($detail_id) {
			$additionalFields = <<<SQL
	,
		a.id usagecheck_article_a_id,
		a.parent_id usagecheck_article_a_parent_id,
		a.startarticle usagecheck_article_a_startarticle,
		a.name usagecheck_article_a_name,
		a.clang_id usagecheck_article_a_clang_id,

		t2.id as usagecheck_template_t2_id,
		t2.name as usagecheck_template_t2_name
SQL;
			$groupBy = 'group by a.template_id,t.id,a.id';
			$where .= 'where t.id='.$rexSQL->escape((string) $detail_id);
			$groupBy = 'group by a.template_id,t.id,a.id';
		} else {
			$additionalFields = <<<SQL
	,count(a.id) articles
	,count(t2.id) templates
SQL;
			$groupBy = 'group by a.template_id,t.id';

			$this->addParamCriteria($where, $having);
			$this->addParamStatementKeywords($where, $having);
		}

		$sql = <<<SQL
SELECT
	t.*,
	group_concat(a.id) as article_id
	$additionalFields
FROM `$templateTable` t
left join $articleTable a on t.id=a.template_id
left join `$templateTable` t2 on t.id <> t2.id and t2.content like concat('%TEMPLATE[', t.id, ']%')

$where

$groupBy

$having

SQL;
		return $sql;
	}
}
