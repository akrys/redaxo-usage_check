<?php
/**
 * Datei für Medienmodul
 *
 * @version       1.0 / 2015-08-08
 * @author        akrys
 */
namespace akrys\redaxo\addon\UsageCheck\Modules;

use \akrys\redaxo\addon\UsageCheck\RedaxoCall;
use \akrys\redaxo\addon\UsageCheck\Permission;

/**
 * Description of Pictures
 *
 * @author akrys
 */
abstract class Pictures
{
	/**
	 * Anzeigemodus für "Alle Anzeigen"
	 * @var boolean
	 */
	private $showAll = false;

	/**
	 * Redaxo-Spezifische Version wählen.
	 * @return \akrys\redaxo\addon\UsageCheck\Modules\Pictures
	 * @throws \akrys\redaxo\addon\UsageCheck\Exception\FunctionNotCallableException
	 * @SuppressWarnings(PHPMD.StaticAccess)
	 */
	public static function create()
	{
		$object = null;
		switch (RedaxoCall::getRedaxoVersion()) {
			case RedaxoCall::REDAXO_VERSION_4:
				require_once __DIR__.'/../RexV4/Modules/Pictures.php';
				$object = new \akrys\redaxo\addon\UsageCheck\RexV4\Modules\Pictures();
				break;
			case RedaxoCall::REDAXO_VERSION_5:
				require_once __DIR__.'/../RexV5/Modules/Pictures.php';
				$object = new \akrys\redaxo\addon\UsageCheck\RexV5\Modules\Pictures();
				break;
		}

		if (!isset($object)) {
			require_once __DIR__.'/../Exception/FunctionNotCallableException.php';
			throw new \akrys\redaxo\addon\UsageCheck\Exception\FunctionNotCallableException();
		}

		return $object;
	}

	/**
	 * Anzeigemodus "alle zeigen" umstellen
	 * @param boolean $bln
	 */
	public function showAll($bln)
	{
		$this->showAll = (boolean) $bln;
	}

	/**
	 * Nicht genutze Bilder holen
	 *
	 * @return array
	 *
	 * @todo bei Instanzen mit vielen Dateien im Medienpool testen. Die Query
	 *       riecht nach Performance-Problemen -> 	Using join buffer (Block Nested Loop)
	 */
	public function getPictures()
	{
		$showAll = $this->showAll;

		if (!Permission::getVersion()->check(Permission::PERM_MEDIA)) {
			return false;
		}

		$rexSQL = RedaxoCall::getAPI()->getSQL();

		$sqlPartsXForm = $this->getXFormTableSQLParts();
		$sqlPartsMeta = $this->getMetaTableSQLParts();

		$havingClauses = array();
		$additionalSelect = '';
		$additionalJoins = '';
		$tableFields = array();


		$havingClauses = array_merge($havingClauses, $sqlPartsXForm['havingClauses']);
		$additionalSelect .= $sqlPartsXForm['additionalSelect'];
		$additionalJoins.= $sqlPartsXForm['additionalJoins'];
		$tableFields = array_merge($tableFields, $sqlPartsXForm['tableFields']);

		$havingClauses = array_merge($havingClauses, $sqlPartsMeta['havingClauses']);
		$additionalSelect .= $sqlPartsMeta['additionalSelect'];
		$additionalJoins .= $sqlPartsMeta['additionalJoins'];
		$tableFields = array_merge($tableFields, $sqlPartsMeta['tableFields']);

		$sql = $this->getPictureSQL($additionalSelect, $additionalJoins);

		if (!$showAll) {
			$sql.='where s.id is null ';
			$havingClauses[] = 'metaCatIDs is null and metaArtIDs is null and metaMedIDs is null';
		}

		$sql.='group by f.filename ';

		if (!$showAll && isset($havingClauses) && count($havingClauses) > 0) {
			$sql.='having '.implode(' and ', $havingClauses).'';
		}

		return array('result' => $rexSQL->getArray($sql), 'fields' => $tableFields);
	}

	/**
	 * SQL Parts für die Metadaten generieren
	 * @return array
	 */
	abstract protected function getMetaTableSQLParts();

	/**
	 * Meta-Bildfelder ermitteln.
	 * @return array
	 */
	abstract protected function getMetaNames();

	/**
	 * SQL Partsfür XForm/YForm generieren.
	 *
	 * @return array
	 */
	protected function getXFormTableSQLParts()
	{
		$return = array(
			'additionalSelect' => '',
			'additionalJoins' => '',
			'tableFields' => array(),
			'havingClauses' => array(),
		);

		RedaxoCall::getAPI()->getSQL();

		$tables = $this->getXFormSQL($return);

		$xTables = array();
		foreach ($tables as $table) {
			$xTables[$table['table_name']][] = array(
				'name' => $table['f1'],
				'name_out' => $table['f2'],
				'table_out' => $table['table_out'],
				'type' => $table['type_name']
			);
		}

		foreach ($xTables as $tableName => $fields) {
			$return['additionalSelect'].=', group_concat(distinct '.$tableName.'.id';
			$return['additionalJoins'].='LEFT join '.$tableName.' on (';

			foreach ($fields as $key => $field) {
				if ($key > 0) {
					$return['additionalJoins'].=' OR ';
				}

				switch ($field['type']) {
					case 'be_mediapool': // Redaxo 4
					case 'be_media': // Redaxo 5
					case 'mediafile':
						$return['additionalJoins'].=$tableName.'.'.$field['name'].' = f.filename';
						break;
					case 'be_medialist':
						$return['additionalJoins'].='FIND_IN_SET(f.filename, '.$tableName.'.'.$field['name'].')';
						break;
				}
			}

			$return['tableFields'][$tableName] = $fields;
			$return['additionalJoins'].=')'.PHP_EOL;
			$return['additionalSelect'].=' Separator "\n") as '.$tableName.PHP_EOL;
			$return['havingClauses'][] = $tableName.' IS NULL';
		}


		return $return;
	}

	/**
	 * XFormTables holen
	 *
	 * @return array
	 * @param array &$return
	 */
	abstract protected function getXFormSQL(&$return);

	/**
	 * Dateigröße ermitteln.
	 *
	 * Die Größe in Byte auszugeben ist nicht gerade übersichtlich. Daher wird
	 * hier versucht den Wert in der größt-möglichen Einheit zu ermittln.
	 *
	 * @param array $item wichtige Indezes: filesize
	 * @return string
	 */
	public function getSizeOut($item)
	{
		$size = $item['filesize'];
		$index = 0;

		while ($size > 1024) {
			$index++;
			$size/=1024;
			if ($index > 6) {
				//WTF????
				break;
			}
		}
		$value = round($size, 2);
		switch ($index) {
			case 0:
				$unit = 'B';
				break;
			case 1:
				$unit = 'kB';
				break;
			case 2:
				$unit = 'MB';
				break;
			case 3:
				$unit = 'GB';
				break;
			case 4:
				$unit = 'TB';
				break;
			case 5:
				$unit = 'EB';
				break;
			case 6:
				$unit = 'PB';
				break;
			default:
				$unit = '????';
				break;
		}

		return $value.' '.$unit;
	}

	/**
	 * Überprüfen, ob eine Datei existiert.
	 *
	 * @global type $REX
	 * @param array $item
	 * @return boolean
	 */
	abstract public function exists($item);

	/**
	 * Spezifisches SQL
	 * @param string $additionalSelect
	 * @param string $additionalJoins
	 * @return string
	 */
	abstract protected function getPictureSQL($additionalSelect, $additionalJoins);

	/**
	 * Holt ein Medium-Objekt mit Prüfung der Rechte
	 *
	 * @param array $item Idezes: category_id, filename
	 * @return \rex_media
	 * @throws \akrys\redaxo\addon\UsageCheck\Exception\FunctionNotCallableException
	 */
	abstract public function getMedium($item);

	/**
	 * Bildvorschau ausgeben
	 *
	 * @return void
	 * @param array $item Ein Element der Ergebnismenge
	 */
	abstract public function outputImagePreview($item);

	/**
	 * Menü URL generieren
	 * @return string
	 * @param string $subpage
	 * @param string $showAllParam
	 */
	abstract public function getMeuLink($subpage, $showAllParam);

	/**
	 * Menü ausgeben
	 * @return void
	 * @param string $subpage
	 * @param string $showAllParam
	 * @param string $showAllLinktext
	 */
	public function outputMenu($subpage, $showAllParam, $showAllLinktext)
	{
		$url = $this->getMeuLink($subpage, $showAllParam);

		$text = \akrys\redaxo\addon\UsageCheck\RedaxoCall::getAPI()->i18nMsg('akrys_usagecheck_images_intro_text');
		?>

		<p class="rex-tx1">
			<a href="<?php echo $url; ?>"><?php echo $showAllLinktext; ?></a>
		</p>
		<p class="rex-tx1"><?php echo $text ?></p>

		<?php
	}
}
