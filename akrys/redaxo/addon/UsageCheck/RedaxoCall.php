<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
namespace akrys\redaxo\addon\UsageCheck;

class RedaxoCall
{
	const REDAXO_VERSION_4 = 4;
	const REDAXO_VERSION_5 = 5;

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
	 * @return int
	 */
	public static function getRedaxoVersion()
	{
		if (isset($GLOBALS['REX'])) {
			return self::REDAXO_VERSION_4;
		} else {
			return self::REDAXO_VERSION_5;
		}
	}

	public static function errorMsg($text, $addTags = true)
	{
		if ($addTags) {
			$text = <<<TEXT
<p><span>$text</span></p>
TEXT;
		}

		switch (\akrys\redaxo\addon\UsageCheck\RedaxoCall::getRedaxoVersion()) {
			case \akrys\redaxo\addon\UsageCheck\RedaxoCall::REDAXO_VERSION_4:
				return <<<MSG

<div class="rex-message">
	<div class="rex-warning">
		$text
	</div>
</div>

MSG;
				break;
			case \akrys\redaxo\addon\UsageCheck\RedaxoCall::REDAXO_VERSION_5:
				return <<<MSG

<div class="alert alert-danger">
	$text
</div>

MSG;
				break;
		}
	}

	public static function infoMsg($text, $addTags = true)
	{
		if ($addTags) {
			$text = <<<TEXT
<p><span>$text</span></p>
TEXT;
		}
		switch (\akrys\redaxo\addon\UsageCheck\RedaxoCall::getRedaxoVersion()) {
			case \akrys\redaxo\addon\UsageCheck\RedaxoCall::REDAXO_VERSION_4:
				return <<<MSG

<div class="rex-message">
	<div class="rex-info">
		$text
	</div>
</div>

MSG;
				break;
			case \akrys\redaxo\addon\UsageCheck\RedaxoCall::REDAXO_VERSION_5:
				return <<<MSG

<div class="alert alert-success">
	$text
</div>

MSG;
				break;
		}
	}

	/**
	 * Ausgabe-Kasten auf der Addon-Seite erstellen.
	 * @param string $title
	 * @param string $text
	 * @return string
	 */
	public static function panelOut($title, $text)
	{
		switch (\akrys\redaxo\addon\UsageCheck\RedaxoCall::getRedaxoVersion()) {
			case \akrys\redaxo\addon\UsageCheck\RedaxoCall::REDAXO_VERSION_4:
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
				break;
			case \akrys\redaxo\addon\UsageCheck\RedaxoCall::REDAXO_VERSION_5:
				return <<<MSG

<div class="panel panel-default">
	<header class="panel-heading"><div class="panel-title">$title</div></header>
	<div class="panel-body">
		$text
	</div>
</div>


MSG;
				break;
		}
	}
}
