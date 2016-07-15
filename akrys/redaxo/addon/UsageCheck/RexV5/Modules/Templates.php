<?php

/**
 * Datei für das Template-Modul
 *
 * @version       1.0 / 2016-05-08
 * @author        akrys
 */
namespace akrys\redaxo\addon\UsageCheck\RexV5\Modules;

require_once __DIR__.'/../../Modules/Templates.php';

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

		$templateTable = \akrys\redaxo\addon\UsageCheck\RedaxoCall::getAPI()->getTable('template');
		$articleTable = \akrys\redaxo\addon\UsageCheck\RedaxoCall::getAPI()->getTable('article');

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
	 */
	public function outputMenu($subpage, $showAll, $showInactive)
	{
		$param = $this->getMenuParameter($showAll, $showInactive);
		?>

		<ul>
			<li><a href="index.php?page=<?php echo \akrys\redaxo\addon\UsageCheck\Config::NAME; ?>/<?php echo $subpage; ?><?php echo $param['showAllParam'].$param['showInactiveParamCurr']; ?>"><?php echo $param['showAllLinktext']; ?></a></li>

			<?php
			$user = \rex::getUser();
			if ($user->isAdmin()) {
				?>

				<li><a href="index.php?page=<?php echo \akrys\redaxo\addon\UsageCheck\Config::NAME; ?>/<?php echo $subpage; ?><?php echo $param['showAllParamCurr'].$param['showInactiveParam']; ?>"><?php echo $param['showInactiveLinktext'] ?></a></li>

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
		$user = \rex::getUser();
		if ($user->isAdmin()) {
			?>

			<a href="index.php?page=template&function=edit&template_id=<?php echo $item['id']; ?>"><?php echo $linktext; ?></a>

			<?php
		}
	}

	/**
	 * ArticlePerm ermitteln
	 * @param int $articleID
	 * @return boolean
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
	 * @param int $id
	 * @return string
	 */
	public function getEditLink($id)
	{
		return 'index.php?page=templates&function=edit&template_id='.$id;
	}
}
