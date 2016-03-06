<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
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
class RedaxoCall
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
	 * @var int
	 */
	const REDAXO_VERSION_INVALID = -1;

	/**
	 * Übersetzung holen
	 * @param string $text
	 * @return string
	 */
	public static function i18nMsg($text)
	{
		if (\akrys\redaxo\addon\UsageCheck\RedaxoCall::getRedaxoVersion() == \akrys\redaxo\addon\UsageCheck\RedaxoCall::REDAXO_VERSION_4) {
			// Redaxo 4
			return $GLOBALS['I18N']->msg($text);
		} else {
			// Redaxo 5
			return \rex_i18n::rawMsg($text);
		}
	}

	/**
	 * Sprachname Code holen.
	 * @return string
	 */
	public static function getLang()
	{
		$out = '';
		switch (\akrys\redaxo\addon\UsageCheck\RedaxoCall::getRedaxoVersion()) {
			case \akrys\redaxo\addon\UsageCheck\RedaxoCall::REDAXO_VERSION_4:
				$out = $GLOBALS['REX']['LANG'];
				break;
			case\akrys\redaxo\addon\UsageCheck\RedaxoCall::REDAXO_VERSION_5:
				$out = \rex::getProperty('lang');
				break;
		}
		return $out;
	}

	/**
	 * Titel ändern
	 * @param string $title
	 * @param string $sub_title
	 * @return string
	 */
	public static function rexTitle($title)
	{
		if (\akrys\redaxo\addon\UsageCheck\RedaxoCall::getRedaxoVersion() == \akrys\redaxo\addon\UsageCheck\RedaxoCall::REDAXO_VERSION_4) {
			return \rex_title($title, $GLOBALS['REX']['ADDON']['pages'][Config::NAME]);
		} else {
			return \rex_view::title($title);
		}
	}

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
	public static function errorMsg($text, $addTags = true)
	{
		$out = '';

		if ($addTags) {
			$text = <<<TEXT
<p><span>$text</span></p>
TEXT;
		}

		switch (\akrys\redaxo\addon\UsageCheck\RedaxoCall::getRedaxoVersion()) {
			case \akrys\redaxo\addon\UsageCheck\RedaxoCall::REDAXO_VERSION_4:
				$out = <<<MSG

<div class="rex-message">
	<div class="rex-warning">
		$text
	</div>
</div>

MSG;
				break;
			case \akrys\redaxo\addon\UsageCheck\RedaxoCall::REDAXO_VERSION_5:
				$out = <<<MSG

<div class="alert alert-danger">
	$text
</div>

MSG;
				break;
		}

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
	public static function infoMsg($text, $addTags = true)
	{
		$out = '';

		if ($addTags) {
			$text = <<<TEXT
<p><span>$text</span></p>
TEXT;
		}
		switch (\akrys\redaxo\addon\UsageCheck\RedaxoCall::getRedaxoVersion()) {
			case \akrys\redaxo\addon\UsageCheck\RedaxoCall::REDAXO_VERSION_4:
				$out = <<<MSG

<div class="rex-message">
	<div class="rex-info">
		$text
	</div>
</div>

MSG;
				break;
			case \akrys\redaxo\addon\UsageCheck\RedaxoCall::REDAXO_VERSION_5:
				$out = <<<MSG

<div class="alert alert-success">
	$text
</div>

MSG;
				break;
		}
		return $out;
	}

	/**
	 * Ausgabe-Kasten auf der Addon-Seite erstellen.
	 * @param string $title
	 * @param string $text
	 * @return string
	 */
	public static function panelOut($title, $text)
	{
		$out = '';

		switch (\akrys\redaxo\addon\UsageCheck\RedaxoCall::getRedaxoVersion()) {
			case \akrys\redaxo\addon\UsageCheck\RedaxoCall::REDAXO_VERSION_4:
				$out = <<<MSG


<div class="rex-addon-output">
	<h2 class="rex-hl2">$title</h2>

	<div class="rex-addon-content">
		<p class="rex-tx1">
			$text
		</p>
	</div>
</div>

MSG;
				break;
			case \akrys\redaxo\addon\UsageCheck\RedaxoCall::REDAXO_VERSION_5:
				$out = <<<MSG

<div class="panel panel-default">
	<header class="panel-heading"><div class="panel-title">$title</div></header>
	<div class="panel-body">
		$text
	</div>
</div>


MSG;
				break;
		}

		return $out;
	}

	/**
	 * Table-Klasse anhand der Redaxo-Version ermittln.
	 *
	 * Spart ein wenig Code im Frontend.
	 *
	 * @return string
	 */
	public static function getTableClass()
	{
		$out = '';
		switch (\akrys\redaxo\addon\UsageCheck\RedaxoCall::getRedaxoVersion()) {
			case \akrys\redaxo\addon\UsageCheck\RedaxoCall::REDAXO_VERSION_4:
				$out = 'rex-table';
				break;

			case \akrys\redaxo\addon\UsageCheck\RedaxoCall::REDAXO_VERSION_5:
				$out = 'table table-striped';
				break;
		}
		return $out;
	}
}
