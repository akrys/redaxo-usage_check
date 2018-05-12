<?php

/**
 * Datei für das Template-Modul
 *
 * @version       1.0 / 2016-05-08
 * @author        akrys
 */
namespace akrys\redaxo\addon\UsageCheck\RexV5\Modules;

/**
 * Description of Templates
 *
 * @author akrys
 */
class Templates
	extends \akrys\redaxo\addon\UsageCheck\Modules\Templates
{

	/**
	 * SQL für Redaxo 5
	 * @param string $where
	 * @param string $having
	 * @return string
	 */
	protected function getSQL($where, $having)
	{

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

	/**
	 * Menu-Ausgabe
	 * @param string $subpage
	 * @param boolean $showAll
	 * @param boolean $showInactive
	 * @SuppressWarnings(PHPMD.StaticAccess)
	 */
	public function outputMenu($subpage, $showAll, $showInactive)
	{
		$param = $this->getMenuParameter($showAll, $showInactive);

		$link = 'index.php?page='.\akrys\redaxo\addon\UsageCheck\Config::NAME.'/'.$subpage.
			$param['showAllParam'].$param['showInactiveParamCurr'];

		$adminLink = 'index.php?page='.\akrys\redaxo\addon\UsageCheck\Config::NAME.'/'.$subpage.
			$param['showAllParamCurr'].$param['showInactiveParam'];

		$links = [
			[
				'url' => $link,
				'text' => $param['showAllLinktext'],
				'admin' => false,
			],
			[
				'url' => $adminLink,
				'text' => $param['showInactiveLinktext'],
				'admin' => true,
			],
		];

		$texts = [
			\rex_i18n::rawMsg('akrys_usagecheck_template_intro_text'),
		];

		$params = [
			'links' => $links,
			'texts' => $texts,
			'user' => \rex::getUser(),
		];
		$menu = new \rex_fragment($params);
		return $menu->parse('fragments/menu/linklist.php');
	}

	/**
	 * Edit-Link generieren
	 * @param array $item
	 * @param string $linkText
	 * @SuppressWarnings(PHPMD.StaticAccess)
	 */
	public function outputTemplateEdit($item, $linkText)
	{
		$user = \rex::getUser();
		if ($user->isAdmin()) {
			$url = 'index.php?page=templates&function=edit&template_id='.$item['id'];

			$fragmet = new \rex_fragment([
				'href' => $url,
				'text' => $linkText,
			]);
			return $fragmet->parse('fragments/link.php');
		}
		return '';
	}

	/**
	 * ArticlePerm ermitteln
	 * @param int $articleID
	 * @return boolean
	 * @SuppressWarnings(PHPMD.StaticAccess)
	 */
	public function hasArticlePerm($articleID)
	{
		$user = \rex::getUser();
		$perm = \rex_structure_perm::get($user, 'structure');
		$hasPerm = $perm->hasCategoryPerm($articleID);

		return $hasPerm;
	}

	/**
	 * Template-EditLink zusammenbauen
	 * @param int $tplID
	 * @return string
	 */
	public function getEditLink($tplID)
	{
		return 'index.php?page=templates&function=edit&template_id='.$tplID;
	}
}
