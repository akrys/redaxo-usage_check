<?php

/**
 * Datei für ...
 *
 * @version       1.0 / 2016-07-10
 * @package       new_package
 * @subpackage    new_subpackage
 * @author        akrys
 */
namespace akrys\redaxo\addon\UsageCheck\RexV4;

use akrys\redaxo\addon\UsageCheck\Config;

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
		return $GLOBALS['I18N']->msg($text);
	}

	/**
	 * Sprachname Code holen.
	 * @return string
	 */
	public function getLang()
	{
		return $GLOBALS['REX']['LANG'];
	}

	/**
	 * Tabellenprefix holen
	 * @return string
	 */
	public function getTablePrefix()
	{
		return $GLOBALS['REX']['TABLE_PREFIX'];
	}

	/**
	 * Titel ändern
	 * @param string $title
	 * @return string
	 */
	public function rexTitle($title)
	{
		return \rex_title($title, $GLOBALS['REX']['ADDON']['pages'][Config::NAME]);
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
	public function errorMsg($text)
	{
		$out = <<<MSG

<div class="rex-message">
	<div class="rex-warning">
		$text
	</div>
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
	 * @return string
	 */
	public function infoMsg($text)
	{
		$out = <<<MSG

<div class="rex-message">
	<div class="rex-info">
		$text
	</div>
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


<div class="rex-addon-output">
	<h2 class="rex-hl2">$title</h2>

	<div class="rex-addon-content">
		<p class="rex-tx1">
			$text
		</p>
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
		return 'rex-table';
	}

	/**
	 * Abfrage, ob der aktuelle User Admin ist
	 * @return boolean
	 */
	public function isAdmin()
	{
		if ($GLOBALS['REX']['USER']->isAdmin()) {
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
		$hasPerm = false;
		//$GLOBALS['REX']['USER']->hasPerm('article['.$articleID.']') ist immer false
		/* $GLOBALS['REX']['USER']->hasPerm('article['.$articleID.']') || */
		if ($GLOBALS['REX']['USER']->hasCategoryPerm($articleID)) {
			$hasPerm = true;
		}
		return $hasPerm;
	}

	/**
	 * Kategorie-Rechte an einem Medium abfragen
	 * @global array $REX
	 * @param int $catID
	 * @return boolean
	 */
	public function hasMediaCategoryPerm($catID)
	{
		$hasPerm = false;
		if ($GLOBALS['REX']['USER']->isAdmin() || $GLOBALS['REX']['USER']->hasPerm('media[0]')) {
			return true;
		}

		if ($GLOBALS['REX']['USER']->hasPerm('media['.$catID.']')) {
			$hasPerm = true;
		}
		return $hasPerm;
	}

	/**
	 * URL zur Bearbeitung der Artikel-Metadaten.
	 * @param int $articleID
	 * @param int $clang
	 */
	public function getArticleMetaUrl($articleID, $clang)
	{
		return 'index.php?page=content&article_id='.$articleID.'&mode=meta&clang='.$clang.'&ctype=1';
	}

	/**
	 * URL zur Bearbeitung der Artikel-Metadaten.
	 * @param string $table
	 * @param int $dataID
	 */
	public function getXFormEditUrl($table, $dataID)
	{
		return 'index.php?page=xform&subpage=manager&tripage=data_edit&table_name='.$table.
			'&rex_xform_search=0&data_id='.$dataID.'&func=edit&start=';
	}

	/**
	 * Abfrage, ob es Tabellenrechte gibt.
	 * @param string $table
	 * @return boolean
	 */
	public function hasTablePerm($table)
	{
		/* @var $GLOBALS['REX']['USER'] \rex_user */
		return $GLOBALS['REX']['USER']->hasPerm('xform[]') &&
			$GLOBALS['REX']['USER']->hasPerm('xform[table:'.$table.']');
	}

	/**
	 * rex_sql instanz holen
	 *
	 * @return \rex_sql
	 */
	public function getSQL()
	{
		return new \rex_sql;
	}
}
