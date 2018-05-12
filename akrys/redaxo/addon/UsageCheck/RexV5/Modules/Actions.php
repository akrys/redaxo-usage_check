<?php

/**
 * Datei für die Modul-Actions
 *
 * @version       1.0 / 2016-05-08
 * @author        akrys
 */
namespace akrys\redaxo\addon\UsageCheck\RexV5\Modules;

use \akrys\redaxo\addon\UsageCheck\Config;

/**
 * Description of Actions
 *
 * @author akrys
 */
class Actions
	extends \akrys\redaxo\addon\UsageCheck\Modules\Actions
{

	/**
	 * Menu
	 * @param string $subpage
	 * @param string $showAllParam
	 * @param string $showAllLinktext
	 */
	public function outputMenu($subpage, $showAllParam, $showAllLinktext)
	{
		$url = 'index.php?page='.Config::NAME.'/'.$subpage.$showAllParam;
		$menu = new \rex_fragment([
			'url' => $url,
			'linktext' => $showAllLinktext,
			'texts' => [
				$this->i18nRaw('akrys_usagecheck_action_intro_text'),
			],
		]);
		return $menu->parse('fragments/menu/linktext.php');
	}

	/**
	 * Link Action Editieren
	 * @param array $item
	 * @param string $linkText
	 */
	public function outputActionEdit($item, $linkText)
	{
		$url = 'index.php?page=modules/actions&action_id='.$item['id'].'&function=edit';
		$fragmet = new \rex_fragment([
			'href' => $url,
			'text' => $linkText,
		]);
		return $fragmet->parse('fragments/link.php');
	}
}
