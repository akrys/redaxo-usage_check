<?php

/**
 * Besonderheiten der unterschiedlichen Redaxo-Versionen abbilden.
 */
namespace akrys\redaxo\addon\UsageCheck;

/**
 * Besonderheiten der unterschiedlichen Redaxo-Versionen abbilden.
 *
 * Man sollte nicht versuchen, einen Call an den Redaxo 4-Core in Redaxo 5 abzusetzen.
 * Das endet sehr schnell in unüberindbaren Fehlern.
 * Daher habe ich hier die Function-Calls, die ich an den Core sende gesammelt.
 *
 * ACHTUNG:
 * Das ist alles andere, als eine vollständige Abbildung an Core-Functions und
 * sollte daher nicht für andere Projekte als Unversal-API verwendet werden.
 *
 */
abstract class RedaxoCall
{
	/**
	 * @var int
	 */
	const REDAXO_VERSION_4 = 4;

	/**
	 * @var int
	 */
	const REDAXO_VERSION_5 = 5;

	/**
	 * Schnittstellenversion zu Redaxo 4 oder 5
	 * @var RedaxoCall
	 */
	private static $api;

	/**
	 * @var int
	 */
	const REDAXO_VERSION_INVALID = -1;

	/**
	 * Versionsspezifische Redaxo-Aufrufe bündeln.
	 *
	 * Jeder Aufruf ist in eine spezielle API-Funktion gekapselt. Diese wird von
	 * der enstprechenden Klasse für die jeweilige Redaxo-Version implementiert.
	 *
	 * @return RedaxoCall
	 * @throws Exception\InvalidVersionException
	 */
	static function getAPI()
	{
		if (!isset(self::$api)) {
			switch (\akrys\redaxo\addon\UsageCheck\RedaxoCall::getRedaxoVersion()) {
				case \akrys\redaxo\addon\UsageCheck\RedaxoCall::REDAXO_VERSION_4:
					// Redaxo 4
					require_once(__DIR__.'/RexV4/RedaxoCallAPI.php');
					self::$api = new RexV4\RedaxoCallAPI();
					break;
				case \akrys\redaxo\addon\UsageCheck\RedaxoCall::REDAXO_VERSION_5:
					// Redaxo 5
					require_once(__DIR__.'/RexV5/RedaxoCallAPI.php');
					self::$api = new RexV5\RedaxoCallAPI();
					break;
				default:
					require_once(__DIR__.'/Exception/InvalidVersionException.php');
					throw new Exception\InvalidVersionException();
			}
		}
		return self::$api;
	}

	/**
	 * Übersetzung holen
	 * @param string $text
	 * @return string
	 */
	public abstract function i18nMsg($text);

	/**
	 * Sprachname Code holen.
	 * @return string
	 */
	public abstract function getLang();

	/**
	 * Tabelle mit Prefix versehen
	 * @param string $name
	 * @return string
	 */
	public function getTable($name)
	{
		return $this->getTablePrefix().$name;
	}

	/**
	 * Tabellenprefix holen
	 * @return string
	 */
	public abstract function getTablePrefix();

	/**
	 * Titel ändern
	 * @param string $title
	 * @return string
	 */
	public abstract function rexTitle($title);

	/**
	 * Erkennung der Redaxo-Version
	 *
	 *
	 * 1. Kriterium: die Existenz der $REX-Variablen.
	 * Ist sie nicht da, sind wir in Redaxo5
	 *
	 * ABER:
	 * Die Variable $REX wird in Redaxo 4 auch gerne als Zwischenspreicher
	 * genutzt. Wenn also ein Addon vor uns die Variable aus irgend einem
	 * Grund befüllt, fliegen wir mit dem Kriterium alleine voll auf die Nase.
	 *
	 * Daher:
	 * 2. Kriterium: Versionsprüfung
	 * zum einen Gibt es $REX['VERSION'] unter Redaxo 4 und \rex::getVersion() in
	 * Redaxo 5
	 *
	 * Ich denke, das sollte als Versionsüberprüfung durchaus reichen.
	 *
	 * Was natürlich sein kann: Dass ein Inkrementelles update eingespielt wurde,
	 * wo die Versionsnummer vergessen wurde.
	 * Das dürfte aber kein Problem darstellen, da das nur zutrifft, wenn nicht
	 * quasi alle Dateien im Core angefasst wurden.
	 *
	 * So gibt es ja auch schon ein offizielles update von Redaxo 4.3 zu 4.6
	 * Von 4 zu 5 dann erst recht nicht ;-)
	 * http://www.redaxo.org/de/download/updatehinweise/
	 *
	 *
	 * @return int
	 */
	public static function getRedaxoVersion()
	{
		//REDAXO 4?
		if (isset($GLOBALS['REX']) && $GLOBALS['REX']['VERSION'] == 4) {
			return self::REDAXO_VERSION_4;
		}

		//Redaxo 5?
		if (is_callable('\\rex::getVersion')) {
			$version = \rex::getVersion();
			if (
				version_compare('5.0', $version) <= 0 &&
				version_compare('6.0', $version) > 0 // Bei redaxo4 waren auch quasi alle unter-versionen von der API her kompatibel untereinander.
			) {
				return self::REDAXO_VERSION_5;
			}
		}
		return self::REDAXO_VERSION_INVALID;
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
	public abstract function errorMsg($text, $addTags = true);

	/**
	 * Abgrenzungstags hinzugfügen.
	 *
	 * Span und p-Tags hinzufügen, so dass hinzugefügte Texte sich stärker von
	 * einander abgrenden können.
	 *
	 * @param string $text
	 * @return string
	 */
	protected function addTags($text)
	{
		return <<<TEXT
<p><span>$text</span></p>
TEXT;
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
	public abstract function infoMsg($text, $addTags = true);

	/**
	 * Ausgabe-Kasten auf der Addon-Seite erstellen.
	 * @param string $title
	 * @param string $text
	 * @return string
	 */
	public abstract function panelOut($title, $text);

	/**
	 * Table-Klasse anhand der Redaxo-Version ermittln.
	 *
	 * Spart ein wenig Code im Frontend.
	 *
	 * @return string
	 */
	public abstract function getTableClass();

	/**
	 * Abfrage, ob der aktuelle User Admin ist
	 * @return boolean
	 */
	public abstract function isAdmin();

	/**
	 * Kategorie-Rechte an einem Artikel abfragen
	 * @global type $REX
	 * @param type $articleID
	 * @return boolean
	 */
	public abstract function hasCategoryPerm($articleID);

	/**
	 * Kategorie-Rechte an einem Medium abfragen
	 * @global array $REX
	 * @param int $catID
	 * @return boolean
	 */
	public abstract function hasMediaCategoryPerm($catID);

	/**
	 * URL zur Bearbeitung der Artikel-Metadaten.
	 * @param int $articleID
	 * @param int $clang
	 */
	public abstract function getArticleMetaUrl($articleID, $clang);

	/**
	 * URL zur Bearbeitung der Artikel-Metadaten.
	 * @param string $table
	 * @param int $id
	 */
	public abstract function getXFormEditUrl($table, $id);

	/**
	 * Abfrage, ob es Tabellenrechte gibt.
	 * @param string $table
	 * @return boolean
	 */
	public abstract function hasTablePerm($table);

	/**
	 * rex_sql instanz holen
	 *
	 * @return \rex_sql
	 */
	public abstract function getSQL();
}
