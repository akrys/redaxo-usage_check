<?php
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
namespace akrys\redaxo\addon\UsageCheck\RexV5\Modules;

require_once __DIR__.'/../../Modules/Pictures.php';

/**
 * Datei für ...
 *
 * @version       1.0 / 2016-05-05
 * @package       new_package
 * @subpackage    new_subpackage
 * @author        akrys
 */

/**
 * Description of Pictures
 *
 * @author akrys
 */
class Pictures
	extends \akrys\redaxo\addon\UsageCheck\Modules\Pictures
{

	/**
	 * XFormTables holen
	 *
	 * @return array
	 * @param array &$return
	 */
	protected function getXFormSQL(&$return)
	{
		$tabels = array();
		$rexSQL = \rex_sql::factory();

		if (!\rex_addon::get('yform')->isAvailable()) {
			return $tabels;
		}

		if (!\rex_plugin::get('yform', 'manager')->isAvailable()) {
			return $tabels;
		}

		$yformTableTable = \akrys\redaxo\addon\UsageCheck\RedaxoCall::getTable('yform_table');
		$yformFieldTable = \akrys\redaxo\addon\UsageCheck\RedaxoCall::getTable('yform_field');

		$xformtable = $rexSQL->getArray("show table status like '$yformTableTable'");
		$xformfield = $rexSQL->getArray("show table status like '$yformFieldTable'");
		$sql = <<<SQL
select f.table_name, t.name as table_out,f.name as f1, f.label as f2,f.type_name
from $yformFieldTable f
left join $yformTableTable t on t.table_name=f.table_name
where type_name in ('be_media','be_medialist','mediafile')
SQL;

		$xformtableExists = count($xformfield) > 0;
		$xformfieldExists = count($xformtable) > 0;

		if ($xformfieldExists <= 0 || $xformtableExists <= 0) {
			return $tabels;
		}

		if ($xformfieldExists && $xformtableExists) {
			$tabels = $rexSQL->getArray($sql);
		}
		return $tabels;
	}

	/**
	 * Überprüfen, ob eine Datei existiert.
	 *
	 * @global type $REX
	 * @param array $item
	 * @return boolean
	 */
	public function exists($item)
	{
		return file_exists(\rex_path::media().DIRECTORY_SEPARATOR.$item['filename']);
	}

	/**
	 * Spezifisches SQL für redaxo 5
	 * @param string $additionalSelect
	 * @param string $additionalJoins
	 * @return string
	 */
	protected function getPictureSQL($additionalSelect, $additionalJoins)
	{
		$mediaTable = \ akrys\redaxo\addon\UsageCheck\RedaxoCall::getTable('media');
		$articleSliceTable = \akrys\redaxo\addon\UsageCheck\RedaxoCall::getTable('article_slice');
		$articleTable = \akrys\redaxo\addon\UsageCheck\RedaxoCall::getTable('article');

		$sql = <<<SQL
SELECT f.*,count(s.id) as count,
group_concat(distinct concat(
	cast(s.id as char),"\\t",
	cast(s.article_id as char),"\\t",
	a.name,"\\t",
	cast(s.clang_id as char),"\\t",
	cast(s.ctype_id as char)
) Separator "\\n") as slice_data

$additionalSelect

FROM $mediaTable f
left join `$articleSliceTable` s on (
    s.media1=f.filename
 OR s.media2=f.filename
 OR s.media3=f.filename
 OR s.media4=f.filename
 OR s.media5=f.filename
 OR s.media6=f.filename
 OR s.media7=f.filename
 OR s.media8=f.filename
 OR s.media9=f.filename
 OR s.media10=f.filename
 OR find_in_set(f.filename, s.medialist1)
 OR find_in_set(f.filename, s.medialist2)
 OR find_in_set(f.filename, s.medialist3)
 OR find_in_set(f.filename, s.medialist4)
 OR find_in_set(f.filename, s.medialist5)
 OR find_in_set(f.filename, s.medialist6)
 OR find_in_set(f.filename, s.medialist7)
 OR find_in_set(f.filename, s.medialist8)
 OR find_in_set(f.filename, s.medialist9)
 OR find_in_set(f.filename, s.medialist10)
)

left join $articleTable a on (a.id=s.article_id and a.clang_id=s.clang_id)

$additionalJoins

SQL;
		return $sql;
	}

	public function getMedium($item)
	{
		$user = \rex::getUser();
		$complexPerm = $user->getComplexPerm('media');
		if (!$user->isAdmin() && !(is_object($complexPerm) && $complexPerm->hasCategoryPerm($item['category_id']))) {
			//keine Rechte am Medium
		} else {
			//Das Medium wird später gebraucht.
			/* @var $medium rex_media */
			$medium = \rex_media::get($item['filename']);
			return $medium;
		}
		throw new \akrys\redaxo\addon\UsageCheck\Exception\FunctionNotCallableException();
	}

	/**
	 * Bildvorschau ausgeben
	 *
	 * @return void
	 * @param array $item Ein Element der Ergebnismenge
	 */
	public function outputImagePreview($item)
	{
		if (stristr($item['filetype'], 'image/')) {
			?>

			<img alt="" src="index.php?rex_media_type=content&rex_media_file=<?php echo $item['filename'] ?>" style="max-width:150px;max-height: 150px;" />
			<br /><br />

			<?php
		}
	}

	/**
	 * Menü ausgeben
	 * @return void
	 * @param string $subpage
	 * @param string $showAllParam
	 * @param string $showAllLinktext
	 */
	public function outputMenu($subpage, $showAllParam, $showAllLinktext)
	{
		?>

		<p class="rex-tx1">
			<a href="index.php?page=<?php echo \akrys\redaxo\addon\UsageCheck\Config::NAME; ?>/<?php echo $subpage; ?><?php echo $showAllParam; ?>"><?php echo $showAllLinktext; ?></a>
		</p>
		<p class="rex-tx1"><?php echo \akrys\redaxo\addon\UsageCheck\RedaxoCall::i18nMsg('akrys_usagecheck_images_intro_text'); ?></p>

		<?php
	}
}
