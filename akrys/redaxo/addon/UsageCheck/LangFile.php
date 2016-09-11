<?php

/**
 * Datei für Sprachdatei-Aufbereitung
 *
 * @version       1.0 / 2015-10-27
 * @author        akrys
 */
namespace akrys\redaxo\addon\UsageCheck;

/**
 * Description of LangFile
 *
 * @author akrys
 */
class LangFile
{
	/**
	 * Locale to copy
	 * @var string
	 */
	private $lang;

	/**
	 * construcotr
	 * @param string $lang
	 */
	public function __construct($lang)
	{
		$this->lang = $lang;
	}

	/**
	 * ISO file creation, if needed
	 * @return boolean
	 * @SuppressWarnings(PHPMD.StaticAccess)
	 */
	public function createISOFile()
	{
		//Die ISO-Dateien sollten immer im Release enthalten sein,
		//also sollten sie auch immer generiert werden.
		//		if (stristr($GLOBALS['REX']['LANG'], '_utf8')) {
		//			//nothing needs to happen here
		//			return true;
		//		}
		//Im Live-Betrieb sollte sich an den Dateien nichts mehr ändern.
		//Wäre doof, wenn im Falle des Falles immer der Hinweis erscheint, dass
		//die Sparchdatei nicht geschrieben werden konnte.
		//		if (Config::RELEASE_STATE == Config::RELEASE_STATE_LIVE) {
		//			return true;
		//		}

		switch (\akrys\redaxo\addon\UsageCheck\RedaxoCall::getRedaxoVersion()) {
			case \akrys\redaxo\addon\UsageCheck\RedaxoCall::REDAXO_VERSION_4:
				$langPath = $GLOBALS['REX']['INCLUDE_PATH'].'/addons/'.Config::NAME.'/lang/';
				$convertToIso = true;
				break;
			case \akrys\redaxo\addon\UsageCheck\RedaxoCall::REDAXO_VERSION_5:
				$langPath = \rex_path::addon(Config::NAME).'/lang/';
				$convertToIso = false;
				break;
			default:
				return false;
				break;
		}

		try {
			return $this->createFile($langPath, $convertToIso);
		} catch (Exception\LangFileGenError $e) {
			//
		}
		return false;
	}

	/**
	 * Datei generieren.
	 *
	 * @param string $langPath
	 * @param boolean $convertToIso
	 * @return boolean
	 * @throws Exception\LangFileGenError
	 */
	private function createFile($langPath, $convertToIso)
	{
		$isoFile = $langPath.$this->lang.'.lang';
		$utfFile = $langPath.$this->lang.'_utf8.lang';

		$this->testFileRights($langPath, $isoFile, $utfFile);

		$timeISO = file_exists($isoFile) ? filemtime($isoFile) : 0;
		$timeUTF = filemtime($utfFile);

		if ($timeUTF > $timeISO) {
			$content = file_get_contents($utfFile);
			if ($convertToIso) {
				$content = mb_convert_encoding($content, 'ISO-8859-1', 'utf-8');
			}
			file_put_contents($isoFile, $content);
		}
		return true;
	}

	/**
	 * Dateirechte prüfen.
	 *
	 * Hilfsfunktion, das PHPMD die Komplexität von createFile angemeckert hat.
	 *
	 * @param string $langPath
	 * @param string $isoFile
	 * @param string $utfFile
	 * @throws Exception\LangFileGenError
	 */
	private function testFileRights($langPath, $isoFile, $utfFile)
	{
		if (!is_writeable($langPath) && !file_exists($isoFile)) {
			$text = '"lang"-Directory not writable. Language file cannot be created '.
				'(Redaxo 4 ISO-8859-1 file or Redaxo 5 UTF-8)';
			throw new Exception\LangFileGenError($text);
		}

		if (file_exists($utfFile) && !is_readable($utfFile)) {
			$text = 'Language files cannot be updated as the source file is not readable '.
				'([lang]_utf8.lang)';
			throw new Exception\LangFileGenError($text);
		}

		if (file_exists($isoFile) && !is_writeable($isoFile)) {
			$text = 'Language files cannot be updated as they are not writable '.
				'(Redaxo 4 ISO-8859-1 file or Redaxo 5 UTF-8)';
			throw new Exception\LangFileGenError($text);
		}
	}
}
