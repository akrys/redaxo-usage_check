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
		?>

		<p class="rex-tx1"><a href="<?php echo $url ?>"><?php echo $showAllLinktext; ?></a></p>

		<?php
	}

	/**
	 * Link Action Editieren
	 * @param array $item
	 * @param string $linkText
	 */
	public function outputActionEdit($item, $linkText)
	{
		$url = 'index.php?page=module/actions&action_id='.$item['id'].'&function=edit';
		?>

		<a href="<?php echo $url ?>"><?php echo $linkText ?></a>

		<?php
	}
}
