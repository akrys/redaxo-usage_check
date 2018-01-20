<?php

/**
 * Datei für die generelle Redaxo Core CallAPI
 *
 * @version       1.0 / 2016-07-10
 * @author        akrys
 */
namespace akrys\redaxo\addon\UsageCheck\RexV5;

/**
 * Description of API
 *
 * @author akrys
 */
class RedaxoCallAPI
	extends \akrys\redaxo\addon\UsageCheck\RedaxoCall
{

	/**
	 * Übersetzung holen
	 * @param string $text
	 * @return string
	 * @SuppressWarnings(PHPMD.StaticAccess)
	 */
	public function getI18N($text)
	{
		return \rex_i18n::rawMsg($text);
	}

	/**
	 * Sprachname Code holen.
	 * @return string
	 * @SuppressWarnings(PHPMD.StaticAccess)
	 */
	public function getLang()
	{
		return \rex::getProperty('lang');
	}

	/**
	 * DB holen.
	 * @return array
	 * @SuppressWarnings(PHPMD.StaticAccess)
	 */
	public function getDB()
	{
		return \rex::getProperty('db');
	}

	/**
	 * Tabellenprefix holen
	 * @return string
	 * @SuppressWarnings(PHPMD.StaticAccess)
	 */
	public function getTablePrefix()
	{
		return \rex::getTablePrefix();
	}

	/**
	 * Titel ändern
	 * @param string $title
	 * @return string
	 * @SuppressWarnings(PHPMD.StaticAccess)
	 */
	public function getRexTitle($title)
	{
		return \rex_view::title($title);
	}

	/**
	 * Fehlermeldung generieren.
	 *
	 * Wurde nur mit dem Standard-Backend-Theme getestet.
	 * Wer da etwas angepasst hat, läuft u.U. in Probleme.
	 *
	 * @param string $text
	 * @return string
	 */
	public function getErrorMsg($text)
	{
		$fragment = new \rex_fragment([
			'text' => $text,
		]);
		return $fragment->parse('fragments/msg/error.php');
	}

	/**
	 * Infomeldung generieren.
	 *
	 * Wurde nur mit dem Standard-Backend-Theme getestet.
	 * Wer da etwas angepasst hat, läuft u.U. in Probleme.
	 *
	 * @param string $text
	 * @return string
	 */
	public function getInfoMsg($text)
	{
		$fragment = new \rex_fragment([
			'text' => $text,
		]);
		return $fragment->parse('fragments/msg/info.php');
	}

	/**
	 * Ausgabe-Kasten auf der Addon-Seite erstellen.
	 * @param string $title
	 * @param string $text
	 * @return string
	 */
	public function getPanelOut($title, $text)
	{
		$fragment = new \rex_fragment();
		$fragment->setVar('heading', $title, false);
		$fragment->setVar('body', $text, false);
		return $fragment->parse('core/page/section.php');
	}

	/**
	 * Table-Klasse anhand der Redaxo-Version ermittln.
	 *
	 * Spart ein wenig Code im Frontend.
	 *
	 * @return string
	 */
	public function getTableClass()
	{
		return 'table table-striped';
	}

	/**
	 * Abfrage, ob der aktuelle User Admin ist
	 * @return boolean
	 * @SuppressWarnings(PHPMD.StaticAccess)
	 */
	public function isAdmin()
	{
		$user = \rex::getUser();
		if ($user->isAdmin()) {
			return true;
		}

		return false;
	}

	/**
	 * Kategorie-Rechte an einem Artikel abfragen
	 * @global type $REX
	 * @param type $articleID
	 * @return boolean
	 * @SuppressWarnings(PHPMD.StaticAccess)
	 */
	public function hasCategoryPerm($articleID)
	{
		$user = \rex::getUser();
		$perm = \rex_structure_perm::get($user, 'structure');
		return $perm->hasCategoryPerm($articleID);
	}

	/**
	 * Kategorie-Rechte an einem Medium abfragen
	 * @global array $REX
	 * @param int $catID
	 * @return boolean
	 * @SuppressWarnings(PHPMD.StaticAccess)
	 */
	public function hasMediaCategoryPerm($catID)
	{
		$user = \rex::getUser();
		$perm = \rex_structure_perm::get($user, 'media');
		return $perm->hasCategoryPerm($catID);
	}

	/**
	 * URL zur Bearbeitung der Artikel-Metadaten.
	 * @param int $articleID
	 * @param int $clang
	 */
	public function getArticleMetaUrl($articleID, $clang)
	{
		return 'index.php?page=content/metainfo&article_id='.$articleID.'&clang='.$clang.'&ctype=1';
	}

	/**
	 * URL zur Bearbeitung der Artikel-Metadaten.
	 * @param string $table
	 * @param int $dataID
	 */
	public function getXFormEditUrl($table, $dataID)
	{
		return 'index.php?page=yform/manager/data_edit&table_name='.$table.'&data_id='.$dataID.'&func=edit';
	}

	/**
	 * Abfrage, ob es Tabellenrechte gibt.
	 * @param string $table
	 * @return boolean
	 * @SuppressWarnings(PHPMD.StaticAccess)
	 */
	public function hasTablePerm($table)
	{
		return \rex::getUser()->isAdmin() || (
			\rex::getUser()->hasPerm('yform[]') && \rex::getUser()->hasPerm('yform[table:'.$table.']')
			);
	}

	/**
	 * rex_sql instanz holen
	 *
	 * @return \rex_sql
	 * @SuppressWarnings(PHPMD.StaticAccess)
	 */
	public function getSQL()
	{
		return \rex_sql::factory();
	}
}
