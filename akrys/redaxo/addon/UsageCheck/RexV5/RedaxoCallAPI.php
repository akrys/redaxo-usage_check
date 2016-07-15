<?php

/**
 * Datei für ...
 *
 * @version       1.0 / 2016-07-10
 * @package       new_package
 * @subpackage    new_subpackage
 * @author        akrys
 */
namespace akrys\redaxo\addon\UsageCheck\RexV5;

/**
 * Datei für ...
 *
 * @version       1.0 / 2016-07-10
 * @package       new_package
 * @subpackage    new_subpackage
 * @author        akrys
 */

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
	 */
	public function i18nMsg($text)
	{
		return \rex_i18n::rawMsg($text);
	}

	/**
	 * Sprachname Code holen.
	 * @return string
	 */
	public function getLang()
	{
		return \rex::getProperty('lang');
	}

	/**
	 * Tabellenprefix holen
	 * @return string
	 */
	public function getTablePrefix()
	{
		return \rex::getTablePrefix();
	}

	/**
	 * Titel ändern
	 * @param string $title
	 * @return string
	 */
	public function rexTitle($title)
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
	 * @param boolean $addTags
	 * @return string
	 */
	public function errorMsg($text, $addTags = true)
	{
		$out = '';

		if ($addTags) {
			$text = $this->addTags($text);
		}

		$out = <<<MSG

<div class="alert alert-danger">
	$text
</div>

MSG;
		return $out;
	}

	/**
	 * Infomeldung generieren.
	 *
	 * Wurde nur mit dem Standard-Backend-Theme getestet.
	 * Wer da etwas angepasst hat, läuft u.U. in Probleme.
	 *
	 * @param string $text
	 * @param boolean $addTags
	 * @return string
	 */
	public function infoMsg($text, $addTags = true)
	{
		$out = '';

		if ($addTags) {
			$text = $this->addTags($text);
		}

		$out = <<<MSG

<div class="alert alert-success">
	$text
</div>

MSG;
		return $out;
	}

	/**
	 * Ausgabe-Kasten auf der Addon-Seite erstellen.
	 * @param string $title
	 * @param string $text
	 * @return string
	 */
	public function panelOut($title, $text)
	{
		return <<<MSG

<div class="panel panel-default">
	<header class="panel-heading"><div class="panel-title">$title</div></header>
	<div class="panel-body">
		$text
	</div>
</div>


MSG;
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
	 * @param int $id
	 */
	public function getXFormEditUrl($table, $id)
	{
		return 'index.php?page=yform/manager/data_edit&table_name='.$table.'&data_id='.$id.'&func=edit';
	}

	/**
	 * Abfrage, ob es Tabellenrechte gibt.
	 * @param string $table
	 * @return boolean
	 */
	public function hasTablePerm($table)
	{
		return \rex::getUser()->hasPerm('yform[]') && \rex::getUser()->hasPerm('yform[table:'.$table.']');
	}

	/**
	 * rex_sql instanz holen
	 *
	 * @return \rex_sql
	 */
	public function getSQL()
	{
		return \rex_sql::factory();
	}
}
