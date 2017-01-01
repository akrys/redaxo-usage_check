<?php

/**
 * Grundlagen für Tests bereitstellen.
 */
// @codingStandardsIgnoreStart

if (isset($_SERVER['argv'])) {
	$GLOBALS['REX'] = array();
	$GLOBALS['REX']['INCLUDE_PATH'] = realpath(__DIR__.'/../../../../../');
	$GLOBALS['useComposerAutoload'] = true;
	require_once __DIR__.'/../../../../../general/config.inc.php';

	/**
	 * sql simulation
	 */
	class rex_sql
	{

		/**
		 * factory simulation
		 * @return rex_sql
		 */
		function factory()
		{
			return new self();
		}

		public function getArray($sql)
		{
			$version = rex::getVersion();

			$sql = new \akrys\redaxo\addon\UsageCheck\Tests\SQLSimulation($version, $sql);

			if ($sql->getSQLisKnown()) {
				return $sql->getData();
			}
			return array();
		}

		public function getError()
		{
			return'';
		}

		public function escape($string)
		{
			return addslashes($string);
		}
	}

	/**
	 * Redaxo Simulation für Tests
	 *
	 */
	class rex
	{
		/**
		 * Version
		 * @var string
		 */
		private static $version = '5.0.0';

		const VERSION_5 = '5.0.0';
		const VERSION_4 = '4.0.0';
		const VERSION_INVALID = '0.0.0';

		/**
		 * Version zum Testen setzen
		 *
		 * @param string $version
		 */
		public static function setVersion($version)
		{
			$api = new ReflectionProperty('\\akrys\\redaxo\\addon\\UsageCheck\\RedaxoCall', 'api');
			$api->setAccessible(true);
			$api->setValue(null, null);

			$api = new ReflectionProperty('\\akrys\\redaxo\\addon\\UsageCheck\\Permission', 'api');
			$api->setAccessible(true);
			$api->setValue(null, null);

			if (isset($GLOBALS['REX']['VERSION'])) {
				unset($GLOBALS['REX']['VERSION']);
			}

			switch ($version) {
				case self::VERSION_4:
					$GLOBALS['REX']['VERSION'] = 4;
				// no break
				case self::VERSION_5:
				case self::VERSION_INVALID:
					self::$version = $version;
					break;
			}
			self::setup();
		}

		/**
		 * simulation aufsetzen
		 */
		protected static function setup()
		{
			switch (self::$version) {
				case self::VERSION_4:
					if (!isset($GLOBALS['REX']['TABLE_PREFIX'])) {
						$GLOBALS['REX']['TABLE_PREFIX'] = 'rex_';
						$GLOBALS['REX']['ADDON']['pages'][\akrys\redaxo\addon\UsageCheck\Config::NAME] = 'test';
						$GLOBALS['I18N'] = new rex_i18n();
						$GLOBALS['REX']['LANG'] = true;
						$GLOBALS['REX']['USER'] = new rex_user();

						$GLOBALS['REX']['MEDIAFOLDER'] = rex_path::media();
						$GLOBALS['REX']['ADDON']['prefixes']['metainfo'] = array();
						$GLOBALS['REX']['DB'][1]['NAME'] = 'redaxo_4_6_1';
						$GLOBALS['REX']['DB'][2]['NAME'] = '';
						$GLOBALS['REX']['ADDON']['prefixes']['metainfo'] = array(
							'art_',
							'cat_',
							'med_',
						);
					}
					break;
				case self::VERSION_5:

					break;
			}
		}

		/**
		 * Version holen
		 *
		 * @return string
		 */
		public static function getVersion()
		{
			return self::$version;
		}

		/**
		 * Simulation getTablePrefix
		 * @return string
		 */
		public function getTablePrefix()
		{
			return 'rex_';
		}

		/**
		 * Simulation User
		 * @return \rex_user
		 */
		public static function getUser()
		{
			return new rex_user();
		}

		/**
		 * Simulation getProperty
		 * @param string $x
		 * @return boolean
		 */
		public function getProperty($x)
		{
			switch($x){
				case 'db':
					return array(array('name'=> 'a'),array('name'=> 'b'),);
					break;
			}
			return true;
		}
	}

	/**
	 * Redaxo Path Simulation für Tests
	 *
	 */
	class rex_path
	{

		/**
		 * Simulationsfunktion
		 * @return string
		 * @param $name addonname
		 */
		public static function addon($name)
		{
			return realpath(__DIR__.'/../../../../../');
		}

		/**
		 * Simulation der Mediafunktion
		 * @return string
		 */
		public static function media()
		{
			return realpath(__DIR__.'/../../../../../');
		}
	}

	/**
	 * Simulation rex_i18n
	 */
	class rex_i18n
	{

		/**
		 * simulation Übersetzung
		 * @param string $text
		 * @return string
		 */
		public function rawMsg($text)
		{
			return $text;
		}

		/**
		 * simulation Redaxo 4
		 * @param string $text
		 * @return string
		 */
		public function msg($text)
		{
			return $this->rawMsg($text);
		}
	}

	/**
	 * rex_view Simulation
	 */
	class rex_view
	{

		/**
		 * Titel setzen ist eine core-funktionalität, die hier nicht unbedingt getestet werden muss
		 * @param string $title
		 */
		public static function title($title)
		{
			return $title;
		}
	}

	/**
	 * User simulation
	 */
	class rex_user
	{
		/**
		 * Simuliert, ob ein Adminaccount einloggt sein soll oder nicht
		 * @var boolean
		 */
		private static $blnAdmin = false;

		/**
		 * Simuliert Userrechte
		 * @var boolean
		 */
		private static $blnRight = array();

		/**
		 * Pages Array, Simulation der Rechte=Abfrage
		 * @var array
		 */
		public $pages = null;

		/**
		 * Konstruktor
		 */
		public function __construct()
		{
			$this->pages = array(
				'structure' => new rex_structure_perm(),
			);
		}

		/**
		 * simulierter Adminstatus setzen
		 * @param boolean $admin
		 */
		public static function setAdmin($admin)
		{
			self::$blnAdmin = $admin;
		}

		/**
		 * simulierter Adminstatus setzen
		 * @param boolean $boolean
		 */
		public static function setRight($x, $boolean)
		{
			self::$blnRight[$x] = $boolean;
		}

		/**
		 * Simulation isAdmin
		 *
		 * @return boolean
		 */
		public function isAdmin()
		{
			return self::$blnAdmin;
		}

		/**
		 * Simulation hasPerm
		 * @param string $x
		 * @return boolean
		 */
		public function hasPerm($x)
		{
			if (isset(self::$blnRight) && is_array(self::$blnRight) && is_string($x) && isset(self::$blnRight[$x])) {
				return self::$blnRight[$x];
			}
			return false;
		}

		/**
		 * Simulation hasPerm
		 * @param string $x
		 * @return boolean
		 */
		public function hasCategoryPerm($x)
		{
			if (!isset(self::$blnRight[$x])) {
				return false;
			}
			return self::$blnRight[$x];
		}

		public function getComplexPerm($param)
		{
			if (class_exists($param)) {
				print $param;
				return new $param;
			}
			switch ($param) {
				case 'modules':
					return new rex_module_perm();
				case 'structure':
					return new rex_structure_perm();
				case 'media':
					return new rex_media_perm();
				default:
					throw new \Exception($param.PHP_EOL.'ehm');
			}
		}
	}

	/**
	 * Simulation Structure Perm
	 */
	class perm_simulation
	{
		/**
		 * Simuliert, ob ein Adminaccount einloggt sein soll oder nicht
		 * @var boolean
		 */
		protected static $hasRight = array();

		/**
		 * simulierter Adminstatus setzen
		 * @param boolean $boolean
		 */
		public static function setHasNamedRight($x, $boolean)
		{
			static::$hasRight[$x] = $boolean;
		}

		/**
		 * Simulation von get
		 * @return boolean
		 */
		public static function get()
		{
			return new static();
		}

		/**
		 * Simulation hasPerm
		 * @param string $x
		 * @return boolean
		 */
		public function hasPerm($x)
		{
			if (!isset(static::$hasRight[$x])) {
				return false;
			}
			return static::$hasRight[$x];
		}

		/**
		 * Simulation hasCategoryPerm
		 * @param string $x
		 * @return boolean
		 */
		public function hasCategoryPerm($x)
		{
			if (!isset(static::$hasRight[$x])) {
				return false;
			}
			return static::$hasRight[$x];
		}

		/**
		 * Redaxo 4 Rechte-Simulation
		 * @param rex_user $user
		 * @return boolean
		 */
		public function checkPermission(rex_user $user)
		{
			return $user->hasPerm('pages');
		}

		/**
		 * Rechte Simulation Redaxo 4
		 * @return \perm_simulation
		 */
		public function getPage()
		{
			return $this;
		}
	}

	class rex_structure_perm
	extends perm_simulation
	{

		public static function setHasRight($boolean)
		{
			parent::setHasNamedRight('structure', $boolean);
		}

		public function hasStructurePerm()
		{
			if (!isset(self::$hasRight['structure'])) {
				return false;
			}
			return self::$hasRight['structure'];
		}
	}

	class rex_media_perm
	extends perm_simulation
	{

		public static function setHasRight($boolean)
		{
			parent::setHasNamedRight('media', $boolean);
		}

		public function hasMediaPerm()
		{
			if (!isset(self::$hasRight['media'])) {
				return false;
			}
			return self::$hasRight['media'];
		}
	}

	class rex_module_perm
	extends perm_simulation
	{

		public static function setHasRight($boolean)
		{
			parent::setHasNamedRight('modules', $boolean);
		}

		public function hasAll()
		{
			if (!isset(self::$hasRight['modules'])) {
				return false;
			}
			return self::$hasRight['modules'];
		}
	}

	class rex_unknown_perm
	extends perm_simulation
	{

	}

	class OOAddon
	{
		protected $module = '';

		public function __construct($get)
		{
			$this->module = $get;
		}

		public static function get($get)
		{
			return new self($get);
		}
		/**
		 * Simuliert, ob ein Adminaccount einloggt sein soll oder nicht
		 * @var boolean
		 */
		protected static $available = array();

		/**
		 * simulierter Adminstatus setzen
		 * @param boolean $boolean
		 */
		public static function setAvailable($x, $boolean)
		{
			self::$available[$x] = $boolean;
		}

		public function isAvailable($x = null)
		{
			if ($x === null) {
				$x = $this->module;
			}
			if (!isset(self::$available[$x])) {
				return false;
			}
			return self::$available[$x];
		}
	}

	class rex_addon
	extends OOAddon
	{

	}

	class OOPlugin
	{
		protected $plugin = '';

		/**
		 * Simuliert, ob ein Adminaccount einloggt sein soll oder nicht
		 * @var boolean
		 */
		protected static $available = array();

		public function __construct($addon, $plugin)
		{
			$this->module = $addon;
			$this->plugin = $plugin;
		}

		public static function get($addon, $plugin)
		{
			return new self($addon, $plugin);
		}

		/**
		 * simulierter Adminstatus setzen
		 * @param boolean $boolean
		 */
		public static function setAvailable($x, $y, $boolean)
		{
			self::$available[$x][$y] = $boolean;
		}

		public function isAvailable($x = null, $y = null)
		{
			if ($x === null) {
				$x = $this->module;
			}
			if ($y === null) {
				$y = $this->plugin;
			}
			if (!isset(self::$available[$x][$y])) {
				return false;
			}
			return self::$available[$x][$y];
		}
	}

	class rex_plugin
	extends OOPlugin
	{

	}

	class OOMedia
	{

		public static function getMediaByFilename($fn)
		{
			return new self();
		}
	}

	class rex_media
	{

		public static function get($fn)
		{
			return new self();
		}
	}

	class rex_metainfo_article_handler
	{
		const PREFIX = 'art_';

	}

	class rex_metainfo_category_handler
	{
		const PREFIX = 'cat_';

	}

	class rex_metainfo_media_handler
	{
		const PREFIX = 'med_';

	}

	/**
	 * Titelsimulation
	 * @param string $title
	 * @param string $addon
	 * @return string
	 */
	function rex_title($title, $addon)
	{
		return rex_view::title($title);
	}
	\rex::setVersion(\rex::VERSION_5);
// @codingStandardsIgnoreEnd

	register_shutdown_function(function() {

		$data = \akrys\redaxo\addon\UsageCheck\Tests\SQLSimulation::getAllUnknown();

		if (!empty($data)) {
			foreach ($data as $key => $value) {
				print 'Redaxo-Version: '.$value['version'].PHP_EOL;
				print $key.PHP_EOL;
				print $value['sql'].PHP_EOL;
				print PHP_EOL;
			}

			print PHP_EOL.json_encode($data).PHP_EOL;
		}
	});
}