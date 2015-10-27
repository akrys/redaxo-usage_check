<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
namespace akrys\redaxo\addon\UsageCheck;

/**
 * Datei für ...
 *
 * @version       1.0 / 2015-10-27
 * @package       new_package
 * @subpackage    new_subpackage
 * @author        akrys
 */

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
	 * @throws Exception\LangFileGenError
	 */
	public function createISOFile()
	{
		if (stristr($GLOBALS['REX']['LANG'], '_utf8')) {
			//nothing needs to happen here
			return true;
		}

		$langPath = $GLOBALS['REX']['INCLUDE_PATH'].'/addons/'.Config::NAME.'/lang/';

		$isoFile = $langPath.$this->lang.'.lang';
		$utfFile = $langPath.$this->lang.'_utf8.lang';

		if (!file_exists($isoFile)) {
			$timeISO = 0;
		} else {
			$timeISO = filemtime($isoFile);
		}

		$timeUTF = filemtime($utfFile);

		if ($timeUTF > $timeISO) {
			if (!is_writeable($langPath) && !file_exists($isoFile)) {
				require_once __DIR__.'/Exception/LangFileGenError.php';
				throw new Exception\LangFileGenError('Directory not writable. ISO language files cannot be created');
			} else if (file_exists($isoFile) && !is_writeable($isoFile)) {
				require_once __DIR__.'/Exception/LangFileGenError.php';
				throw new Exception\LangFileGenError('ISO language files cannot be updated as they are not writable');
			}
			file_put_contents($isoFile, mb_convert_encoding(file_get_contents($utfFile), 'ISO-8859-1', 'utf-8'));
		}
		return true;
	}
}