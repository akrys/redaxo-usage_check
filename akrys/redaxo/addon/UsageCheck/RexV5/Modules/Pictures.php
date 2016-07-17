<?php
/**
 * Datei für das Medienmodul
 *
 * @version       1.0 / 2016-05-05
 * @author        akrys
 */
namespace akrys\redaxo\addon\UsageCheck\RexV5\Modules;

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
		$rexSQL = \akrys\redaxo\addon\UsageCheck\RedaxoCall::getAPI()->getSQL();

		if (!\rex_addon::get('yform')->isAvailable()) {
			return $tabels;
		}

		if (!\rex_plugin::get('yform', 'manager')->isAvailable()) {
			return $tabels;
		}

		$yformTableTable = \akrys\redaxo\addon\UsageCheck\RedaxoCall::getAPI()->getTable('yform_table');
		$yformFieldTable = \akrys\redaxo\addon\UsageCheck\RedaxoCall::getAPI()->getTable('yform_field');

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
	 * @SuppressWarnings(PHPMD.StaticAccess)
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
		$mediaTable = \ akrys\redaxo\addon\UsageCheck\RedaxoCall::getAPI()->getTable('media');
		$articleSliceTable = \akrys\redaxo\addon\UsageCheck\RedaxoCall::getAPI()->getTable('article_slice');
		$articleTable = \akrys\redaxo\addon\UsageCheck\RedaxoCall::getAPI()->getTable('article');

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

	/**
	 * Holt ein Medium-Objekt mit Prüfung der Rechte
	 *
	 * @param array $item Idezes: category_id, filename
	 * @return \rex_media
	 * @throws \akrys\redaxo\addon\UsageCheck\Exception\FunctionNotCallableException
	 * @SuppressWarnings(PHPMD.StaticAccess)
	 */
	public function getMedium($item)
	{
		$user = \rex::getUser();
		$complexPerm = $user->getComplexPerm('media');
		if (!$user->isAdmin() &&
			!(is_object($complexPerm) &&
			$complexPerm->hasCategoryPerm($item['category_id']))) {
			//keine Rechte am Medium
			throw new \akrys\redaxo\addon\UsageCheck\Exception\FunctionNotCallableException();
		}

		//Das Medium wird später gebraucht.
		/* @var $medium \rex_media */
		$medium = \rex_media::get($item['filename']);
		return $medium;
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
			$url = 'index.php?rex_media_type=rex_mediapool_preview&rex_media_file='.$item['filename'];
			?>

			<img alt="" src="<?php echo $url ?>" style="max-width:150px;max-height: 150px;" />
			<br /><br />

			<?php
		}
	}

	/**
	 * Menü URL generieren
	 * @return string
	 * @param string $subpage
	 * @param string $showAllParam
	 */
	public function getMeuLink($subpage, $showAllParam)
	{
		return 'index.php?page='.\akrys\redaxo\addon\UsageCheck\Config::NAME.'/'.$subpage.$showAllParam;
	}

	/**
	 * SQL Parts für die Metadaten innerhalb von Redaxo5 generieren
	 *
	 * @return array
	 * @SuppressWarnings(PHPMD.ElseExpression)
	 */
	protected function getMetaTableSQLParts()
	{
		$return = array(
			'additionalSelect' => '',
			'additionalJoins' => '',
			'tableFields' => array(),
			'havingClauses' => array(),
		);

		$joinArtMeta = '';
		$joinCatMeta = '';
		$joinMedMeta = '';

		$names = $this->getMetaNames();

		foreach ($names as $name) {
			if (preg_match('/'.preg_quote(\rex_metainfo_article_handler::PREFIX, '/').'/', $name['name'])) {
				$fieldname = 'joinArtMeta';
				$tableName = 'rex_article_art_meta';
			} elseif (preg_match('/'.preg_quote(\rex_metainfo_category_handler::PREFIX, '/').'/', $name['name'])) {
				$fieldname = 'joinCatMeta';
				$tableName = 'rex_article_cat_meta';
			} elseif (preg_match('/'.preg_quote(\rex_metainfo_media_handler::PREFIX, '/').'/', $name['name'])) {
				$fieldname = 'joinMedMeta';
				$tableName = 'rex_article_med_meta';
			} else {
				continue;
			}

			switch ($name['type']) {
				case 'REX_MEDIA_WIDGET':
					if ($$fieldname != '') {
						$$fieldname.=' or ';
					}
					$$fieldname.=''.$tableName.'.'.$name['name'].' = f.filename';
					break;
				case 'REX_MEDIALIST_WIDGET':
					if ($$fieldname != '') {
						$$fieldname.=' or ';
					}
					$$fieldname.='FIND_IN_SET(f.filename, '.$tableName.'.'.$name['name'].')';
					break;
			}
		}

		if ($joinArtMeta == '') {
			$return['additionalSelect'].=',null as metaArtIDs '.PHP_EOL;
		} else {
			$return['additionalJoins'].='LEFT join rex_article as rex_article_art_meta on '.
				'(rex_article_art_meta.id is not null and ('.$joinArtMeta.'))'.PHP_EOL;
			$return['additionalSelect'].=',group_concat(distinct concat('.
				'rex_article_art_meta.id,"\t",'.
				'rex_article_art_meta.name,"\t",'.
				'rex_article_art_meta.clang_id) Separator "\n") as metaArtIDs '.PHP_EOL;
		}

		if ($joinCatMeta == '') {
			$return['additionalSelect'].=',null as metaCatIDs '.PHP_EOL;
		} else {
			$return['additionalJoins'].='LEFT join rex_article as rex_article_cat_meta on '.
				'(rex_article_cat_meta.id is not null and ('.$joinCatMeta.'))'.PHP_EOL;
			$return['additionalSelect'].=',group_concat(distinct concat('.
				'rex_article_cat_meta.id,"\t",'.
				'rex_article_cat_meta.catname,"\t",'.
				'rex_article_cat_meta.clang_id,"\t",'.
				'rex_article_cat_meta.parent_id) Separator "\n") as metaCatIDs '.PHP_EOL;
		}
		if ($joinMedMeta == '') {
			$return['additionalSelect'].=',null as metaMedIDs '.PHP_EOL;
		} else {
			$return['additionalJoins'].='LEFT join rex_media as rex_article_med_meta on '.
				'(rex_article_med_meta.id is not null and ('.$joinMedMeta.'))'.PHP_EOL;
			$return['additionalSelect'].=',group_concat(distinct concat('.
				'rex_article_med_meta.id,"\t",'.
				'rex_article_med_meta.category_id,"\t",'.
				'rex_article_med_meta.filename'.
				') Separator "\n") as metaMedIDs '.PHP_EOL;
		}

		return $return;
	}

	/**
	 * Meta-Bildfelder ermitteln.
	 * @return array
	 */
	protected function getMetaNames()
	{
		$rexSQL = \akrys\redaxo\addon\UsageCheck\RedaxoCall::getAPI()->getSQL();

//		$articleTable = \akrys\redaxo\addon\UsageCheck\RedaxoCall::getAPI()->getTable('article');
		$metainfoFieldTable = \akrys\redaxo\addon\UsageCheck\RedaxoCall::getAPI()->getTable('metainfo_field');
		$metainfoTypeTable = \akrys\redaxo\addon\UsageCheck\RedaxoCall::getAPI()->getTable('metainfo_type');

		$sql = <<<SQL
select f.name, t.label as type
from $metainfoFieldTable f
inner join $metainfoTypeTable t on t.id=f.type_id and t.label like '%MEDIA%'

SQL;

		$names = $rexSQL->getArray($sql);

		return $names;
	}
}
