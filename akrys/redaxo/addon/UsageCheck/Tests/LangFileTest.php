<?php

/**
 * Datei für ...
 *
 * @version       1.0 / 2016-07-30
 * @package       new_package
 * @subpackage    new_subpackage
 * @author        akrys
 */
namespace akrys\redaxo\addon\UsageCheck\Tests;

/**
 * Description of ErrorTest
 *
 * @author akrys
 */
class LangFileTest
	extends \PHPUnit_Framework_TestCase
{
	/**
	 * Tesstdatei
	 * @var string
	 */
	private $testFile = __DIR__.'/../../../../../lang/de_de_utf8.lang';

	/**
	 * Test Verzeichnis
	 * @var string
	 */
	private $testDir = __DIR__.'/../../../../../lang/';

	/**
	 * aktuelle Datei-Rechte
	 * @var int
	 */
	private $filePerms = '';

	/**
	 * aktuelle Verzeichnis-Rechte
	 * @var int
	 */
	private $dirPerms = '';

	/**
	 * aktuelle Dateirechte sichern
	 */
	public function setUp()
	{
		parent::setUp();
		if ($this->filePerms == '' && $this->dirPerms == '') {
			$this->filePerms = fileperms($this->testFile);
			$this->dirPerms = fileperms($this->testDir);
		}
	}

	/**
	 * aktuell Dateirechte wiederherstellen
	 */
	public function tearDown()
	{
		parent::tearDown();
		if ($this->dirPerms !== '') {
			chmod($this->testDir, $this->dirPerms);
		}
		if ($this->filePerms !== '') {
			chmod($this->testFile, $this->filePerms);
		}
	}

	/**
	 * Konstruktortest
	 */
	public function testConstruct()
	{
		$langProperty = new \ReflectionProperty('akrys\\redaxo\\addon\\UsageCheck\\LangFile', 'lang');
		$langProperty->setAccessible(true);

		$fileDE = new \akrys\redaxo\addon\UsageCheck\LangFile('de_de');
		$this->assertEquals('de_de', $langProperty->getValue($fileDE));

		$fileEN = new \akrys\redaxo\addon\UsageCheck\LangFile('en_gb');
		$this->assertEquals('en_gb', $langProperty->getValue($fileEN));
	}

	/**
	 * Datei erstellen Iinvalid
	 */
	public function testCreateFileInvalid()
	{
		\rex::setVersion(\rex::VERSION_INVALID);
		$filename = __DIR__.'/../../../../../lang/de_de.lang';
		if (file_exists($filename)) {
			unlink($filename);
		}
		$file = new \akrys\redaxo\addon\UsageCheck\LangFile('de_de');
		$this->assertFalse($file->createISOFile());

		$this->assertFileNotExists($filename);
	}

	/**
	 * Datei erstellen REX5
	 */
	public function testCreateFileREX5()
	{
		\rex::setVersion(\rex::VERSION_5);
		$filename = __DIR__.'/../../../../../lang/de_de.lang';
		if (file_exists($filename)) {
			unlink($filename);
		}
		$file = new \akrys\redaxo\addon\UsageCheck\LangFile('de_de');
		$this->assertTrue($file->createISOFile());

		$this->assertFileExists($filename);
	}

	/**
	 * Datei erstellen REX4
	 */
	public function testCreateFileREX4()
	{
		try {
			\rex::setVersion(\rex::VERSION_4);
			$filename = __DIR__.'/../../../../../lang/de_de.lang';
			if (file_exists($filename)) {
				unlink($filename);
			}
			$file = new \akrys\redaxo\addon\UsageCheck\LangFile('de_de');
			$this->assertTrue($file->createISOFile());

			$this->assertFileExists($filename);
		} catch (\Exception $e) {
			//ups nicht im Redaxo umfeld. Pfad falsch
			$create = new \ReflectionMethod('\\akrys\\redaxo\\addon\\UsageCheck\\LangFile', 'createFile');
			$create->setAccessible(true);
			$create->invokeArgs($file, array('langPath' => \rex_path::addon('none').'/lang/', 'convertToIso' => true));
		}
	}

	/**
	 * Datei erstellen ohne Dateirechte
	 */
	public function testLangFileNotWritable()
	{
		if ($this->filePerms === '') {
			$this->fail('Dateirechte nicht gesichert');
			return false;
		}

		chmod($this->testDir, 0777);
		$filename = __DIR__.'/../../../../../lang/de_de.lang';
		touch($filename, filemtime(__DIR__.'/../../../../../lang/de_de_utf8.lang') - 2500);

		\rex::setVersion(\rex::VERSION_5);
		chmod($filename, 0444);

		$file = new \akrys\redaxo\addon\UsageCheck\LangFile('de_de');
		$this->assertFalse($file->createISOFile());

		$this->assertFileExists($filename);
	}

	/**
	 * Datei erstellen ohne Verzeichnisrechte
	 */
	public function testLangDirNotWritable()
	{
		if ($this->dirPerms === '') {
			$this->fail('Dateirechte nicht gesichert');
			return false;
		}

		$filename = __DIR__.'/../../../../../lang/de_de.lang';
		touch($filename, filemtime(__DIR__.'/../../../../../lang/de_de_utf8.lang') - 2500);

		\rex::setVersion(\rex::VERSION_5);
		$filename = $this->testDir;
		chmod($filename, 0555);

		$file = new \akrys\redaxo\addon\UsageCheck\LangFile('de_de');
		$this->assertFalse($file->createISOFile());

		$this->assertFileExists($filename);
	}

	/**
	 * Datei erstellen ohne Dateirechte
	 */
	public function testLangFileNotReadAndWritable()
	{
		if ($this->filePerms === '') {
			$this->fail('Dateirechte nicht gesichert');
			return false;
		}

		$filename = __DIR__.'/../../../../../lang/de_de.lang';
		if (file_exists($filename)) {
			touch($filename, filemtime(__DIR__.'/../../../../../lang/de_de_utf8.lang') - 2500);
		}

		\rex::setVersion(\rex::VERSION_5);
		$filename = $this->testFile;
		chmod($filename, 0000);

		$file = new \akrys\redaxo\addon\UsageCheck\LangFile('de_de');
		$this->assertFalse($file->createISOFile());

		$this->assertFileExists($filename);
	}

	/**
	 * Datei erstellen ohne Verzeichnisrechte
	 */
	public function testLangDirNotReadAndWritable()
	{
		if ($this->dirPerms === '') {
			$this->fail('Dateirechte nicht gesichert');
			return false;
		}

		$filename = __DIR__.'/../../../../../lang/de_de.lang';
		touch($filename, filemtime(__DIR__.'/../../../../../lang/de_de_utf8.lang') - 2500);

		\rex::setVersion(\rex::VERSION_5);
		$filename = $this->testDir;
		chmod($filename, 0000);

		$file = new \akrys\redaxo\addon\UsageCheck\LangFile('de_de');
		$this->assertFalse($file->createISOFile());

		$this->assertFileExists($filename);
	}
}
