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
abstract class Templates
	extends BaseModule
{
	const TYPE = 'templates';

	/**
	 * Anzeigemodus für "Alle Anzeigen"
	 * @var boolean
	 */
	private $showAll = false;

	/**
	 * Anzeigemodus für "Ianktive zeigen"
	 * @var boolean
	 */
	private $showInactive = false;

	/**
	 * Anzeigemodus "alle zeigen" umstellen
	 * @param boolean $bln
	 */
	public function showAll($bln)
	{
		$this->showAll = (boolean) $bln;
	}

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
	public function getTemplates()
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

		if(!$this->rexSql) {
			throw \Exception('no sql given');
		}
		$rexSQL = $this->rexSql;

		$where = '';
		$having = '';

		$this->addParamCriteria($where, $having);
		$this->addParamStatementKeywords($where, $having);
		$sql = $this->getSQL($where, $having);
		$return = $rexSQL->getArray($sql);
		// @codeCoverageIgnoreStart
		//SQL-Fehler an der Stelle recht schwer zu testen, aber dennoch sinnvoll enthalten zu sein.
		if (!$return) {
			\akrys\redaxo\addon\UsageCheck\Error::getInstance()->add($rexSQL->getError());
		}
		// @codeCoverageIgnoreStart

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
	 * SQL für Redaxo
	 * @param string $where
	 * @param string $having
	 * @return string
	 */
	abstract protected function getSQL($where, $having);

	/**
	 * Menüparameter ermittln
	 * @param boolean $showAll
	 * @param boolean $showInactive
	 * @return array
	 */
	protected function getMenuParameter($showAll, $showInactive)
	{
		$return = array();

		$text = \rex_i18n::rawMsg('akrys_usagecheck_template_link_show_unused');
		$return['showAllParam'] = '';
		$return['showAllParamCurr'] = '&showall=true';
		$return['showAllLinktext'] = $text;
		if (!$showAll) {
			$text = \rex_i18n::rawMsg('akrys_usagecheck_template_link_show_all');
			$return['showAllParam'] = '&showall=true';
			$return['showAllParamCurr'] = '';
			$return['showAllLinktext'] = $text;
		}

		$text = \rex_i18n::rawMsg('akrys_usagecheck_template_link_show_active');
		$return['showInactiveParam'] = '';
		$return['showInactiveParamCurr'] = '&showinactive=true';
		$return['showInactiveLinktext'] = $text;
		if (!$showInactive) {
			$text = \rex_i18n::rawMsg('akrys_usagecheck_template_link_show_active_inactive');
			$return['showInactiveParam'] = '&showinactive=true';
			$return['showInactiveParamCurr'] = '';
			$return['showInactiveLinktext'] = $text;
		}
		return $return;
	}

	/**
	 * Menu-Ausgabe
	 * @param string $subpage
	 * @param boolean $showAll
	 * @param boolean $showInactive
	 */
	abstract public function outputMenu($subpage, $showAll, $showInactive);

	/**
	 * Edit-Link generieren
	 * @param array $item
	 * @param string $linkText
	 */
	abstract public function outputTemplateEdit($item, $linkText);

	/**
	 * ArticlePerm ermitteln
	 * @param int $articleID
	 * @return boolean
	 */
	abstract public function hasArticlePerm($articleID);

	/**
	 * Template-EditLink zusammenbauen
	 * @param int $tplID
	 * @return string
	 */
	abstract public function getEditLink($tplID);
}
