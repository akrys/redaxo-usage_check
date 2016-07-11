<?php
/**
 * Datei für die Modul-Actions
 *
 * @version       1.0 / 2016-05-08
 * @author        akrys
 */
namespace akrys\redaxo\addon\UsageCheck\RexV5\Modules;

require_once __DIR__.'/../../Modules/Actions.php';

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
		?>

		<p class="rex-tx1"><a href="index.php?page=<?php echo \akrys\redaxo\addon\UsageCheck\Config::NAME; ?>/<?php echo $subpage; ?><?php echo $showAllParam; ?>"><?php echo $showAllLinktext; ?></a></p>

		<?php
	}

	/**
	 * Link Action Editieren
	 * @param array $item
	 * @param string $linktext
	 */
	public function outputActionEdit($item, $linktext)
	{
		?>

		<a href="index.php?page=module/actions&action_id=<?php echo $item['id']; ?>&function=edit"><?php echo $linktext ?></a>

		<?php
	}
}
