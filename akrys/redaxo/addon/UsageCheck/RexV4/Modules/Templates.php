<?php
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
namespace akrys\redaxo\addon\UsageCheck\RexV4\Modules;

require_once __DIR__.'/../../Modules/Templates.php';
/**
 * Datei für ...
 *
 * @version       1.0 / 2016-05-08
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
	extends \akrys\redaxo\addon\UsageCheck\Modules\Templates
{

	/**
	 * SQL für Redaxo 4
	 * @param string $where
	 * @param string $having
	 * @return string
	 */
	protected function getSQL($where, $having)
	{
		//Keine integer oder Datumswerte in einem concat!
		//Vorallem dann nicht, wenn MySQL < 5.5 im Spiel ist.
		// -> https://stackoverflow.com/questions/6397156/why-concat-does-not-default-to-default-charset-in-mysql/6669995#6669995

		$templateTable = \akrys\redaxo\addon\UsageCheck\RedaxoCall::getTable('template');
		$articleTable = \akrys\redaxo\addon\UsageCheck\RedaxoCall::getTable('article');

		$sql = <<<SQL
SELECT
	t.*,
	a.id as article_id,
	group_concat(distinct concat(
		cast(a.id as char),"\t",
		cast(a.re_id as char),"\t",
		cast(a.startpage as char),"\t",
		a.name,"\t",
		cast(a.clang as char)) Separator "\n"
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
	 */
	public function outputMenu($subpage, $showAll, $showInactive)
	{
		$param = $this->getMenuParameter($showAll, $showInactive);
		?>

		<ul>
			<li><a href="index.php?page=<?php echo \akrys\redaxo\addon\UsageCheck\Config::NAME; ?>&subpage=<?php echo $subpage; ?><?php echo $param['showAllParam'].$param['showInactiveParamCurr']; ?>"><?php echo $param['showAllLinktext']; ?></a></li>

			<?php
			if ($GLOBALS['REX']['USER']->isAdmin()) {
				?>

				<li><a href="index.php?page=<?php echo \akrys\redaxo\addon\UsageCheck\Config::NAME; ?>&subpage=<?php echo $subpage; ?><?php echo $param['showAllParamCurr'].$param['showInactiveParam']; ?>"><?php echo $param['showInactiveLinktext'] ?></a></li>

				<?php
			}
			?>

		</ul>

		<?php
	}

	/**
	 * Edit-Link generieren
	 * @param array $item
	 * @param string $linktext
	 */
	public function outputTemplateEdit($item, $linktext)
	{
		?>

		<a href="index.php?page=template&subpage=&function=edit&template_id=<?php echo $item['id']; ?>"><?php echo $linktext; ?></a>

		<?php
	}

	/**
	 * ArticlePerm ermitteln
	 * @param int $articleID
	 * @return boolean
	 */
	public function hasArticlePerm($articleID)
	{
		$hasPerm = false;

		//$REX['USER']->hasPerm('article['.$articleID.']') ist immer false
		if (/* $REX['USER']->hasPerm('article['.$articleID.']') || */ $GLOBALS['REX']['USER']->hasCategoryPerm($articleID)) {
			$hasPerm = true;
		}
		return $hasPerm;
	}

	/**
	 * Template-EditLink zusammenbauen
	 * @param int $id
	 * @return string
	 */
	public function getEditLink($id)
	{
		return 'index.php?page=template&subpage=&function=edit&template_id='.$id;
	}
}
