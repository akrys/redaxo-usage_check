<?php

/**
 * Datei für das Template-Modul
 *
 * @version       1.0 / 2015-08-09
 * @author        akrys
 */
namespace akrys\redaxo\addon\UsageCheck\Modules;

require_once __DIR__.'/../Permission.php';

/**
 * Description of Templates
 *
 * @author akrys
 */
abstract class Templates
{

	/**
	 * Redaxo-Spezifische Version wählen.
	 * @return \akrys\redaxo\addon\UsageCheck\Modules\Templates
	 * @throws \akrys\redaxo\addon\UsageCheck\Exception\FunctionNotCallableException
	 */
	public static function create()
	{
		$object = null;
		switch (\akrys\redaxo\addon\UsageCheck\RedaxoCall::getRedaxoVersion()) {
			case \akrys\redaxo\addon\UsageCheck\RedaxoCall::REDAXO_VERSION_4:
				require_once __DIR__.'/../RexV4/Modules/Templates.php';
				$object = new \akrys\redaxo\addon\UsageCheck\RexV4\Modules\Templates();
				break;
			case \akrys\redaxo\addon\UsageCheck\RedaxoCall::REDAXO_VERSION_5:
				require_once __DIR__.'/../RexV5/Modules/Templates.php';
				$object = new \akrys\redaxo\addon\UsageCheck\RexV5\Modules\Templates();
				break;
		}

		if (!isset($object)) {
			require_once __DIR__.'/../Exception/FunctionNotCallableException.php';
			throw new \akrys\redaxo\addon\UsageCheck\Exception\FunctionNotCallableException();
		}

		return $object;
	}

	/**
	 * Nicht genutze Module holen
	 *
	 * @param boolean $show_all
	 * @param boolean $show_inactive
	 * @return array
	 *
	 * @todo bei Instanzen mit vielen Slices testen. Die Query
	 *       riecht nach Performance-Problemen -> 	Using join buffer (Block Nested Loop)
	 */
	public function getTemplates($show_all = false, $show_inactive = false)
	{

		if (!\akrys\redaxo\addon\UsageCheck\Permission::check(\akrys\redaxo\addon\UsageCheck\Permission::PERM_STRUCTURE)) {
			//\akrys\redaxo\addon\UsageCheck\Permission::PERM_TEMPLATE
			return false;
		}

		//Parameter-Korrektur, wenn der User KEIN Admin ist
		//Der darf die inaktiven Templats nämlich sowieso nicht sehen.
		if (\akrys\redaxo\addon\UsageCheck\RedaxoCall::getRedaxoVersion() == \akrys\redaxo\addon\UsageCheck\RedaxoCall::REDAXO_VERSION_4) {
			if (!$GLOBALS['REX']['USER']->isAdmin() && $show_inactive === true) {
				$show_inactive = false;
			}
		} else {
			$user = \rex::getUser();
			if (!$user->isAdmin() && $show_inactive === true) {
				$show_inactive = false;
			}
		}

		if (\akrys\redaxo\addon\UsageCheck\RedaxoCall::getRedaxoVersion() == \akrys\redaxo\addon\UsageCheck\RedaxoCall::REDAXO_VERSION_4) {
			$rexSQL = new \rex_sql;
		} else {
			$rexSQL = \rex_sql::factory();
		}

		$where = '';
		$having = '';

		if (!$show_all) {
			$having.='articles is null and templates is null';
		}

		if (!$show_inactive) {
			$where.='t.active = 1';
		}

		if ($where !== '') {
			$where = 'where '.$where.' ';
		}

		if ($having !== '') {
			$having = 'having '.$having.' ';
		}

		return $rexSQL->getArray($this->getSQL($where, $having));
	}

	/**
	 * SQL für Redaxo
	 * @param string $where
	 * @param string $having
	 * @return string
	 */
	protected abstract function getSQL($where, $having);

	/**
	 * Menüparameter ermittln
	 * @param boolean $showAll
	 * @param boolean $showInactive
	 * @return array
	 */
	protected function getMenuParameter($showAll, $showInactive)
	{
		$return = array();

		$return['showAllParam'] = '';
		$return['showAllParamCurr'] = '&showall=true';
		$return['showAllLinktext'] = \akrys\redaxo\addon\UsageCheck\RedaxoCall::getAPI()->i18nMsg('akrys_usagecheck_template_link_show_unused');
		if (!$showAll) {
			$return['showAllParam'] = '&showall=true';
			$return['showAllParamCurr'] = '';
			$return['showAllLinktext'] = \akrys\redaxo\addon\UsageCheck\RedaxoCall::getAPI()->i18nMsg('akrys_usagecheck_template_link_show_all');
		}

		$return['showInactiveParam'] = '';
		$return['showInactiveParamCurr'] = '&showinactive=true';
		$return['showInactiveLinktext'] = \akrys\redaxo\addon\UsageCheck\RedaxoCall::getAPI()->i18nMsg('akrys_usagecheck_template_link_show_active');
		if (!$showInactive) {
			$return['showInactiveParam'] = '&showinactive=true';
			$return['showInactiveParamCurr'] = '';
			$return['showInactiveLinktext'] = \akrys\redaxo\addon\UsageCheck\RedaxoCall::getAPI()->i18nMsg('akrys_usagecheck_template_link_show_active_inactive');
		}
		return $return;
	}

	/**
	 * Menu-Ausgabe
	 * @param string $subpage
	 * @param boolean $showAll
	 * @param boolean $showInactive
	 */
	public abstract function outputMenu($subpage, $showAll, $showInactive);

	/**
	 * Edit-Link generieren
	 * @param array $item
	 * @param string $linktext
	 */
	public abstract function outputTemplateEdit($item, $linktext);

	/**
	 * ArticlePerm ermitteln
	 * @param int $articleID
	 * @return boolean
	 */
	public abstract function hasArticlePerm($articleID);

	/**
	 * Template-EditLink zusammenbauen
	 * @param int $id
	 * @return string
	 */
	public abstract function getEditLink($id);
}
