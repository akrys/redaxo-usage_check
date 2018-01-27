<?php

/**
 * Tests für Medien
 */
namespace akrys\redaxo\addon\UsageCheck\Tests\RexV5\Modules;

/**
 * Description of BlogRSSTest
 *
 * @author akrys
 */
class PicturesTest
	extends \PHPUnit\Framework\TestCase
{

	/**
	 * Sets up the fixture, for example, opens a network connection.
	 * This method is called before a test is executed.
	 */
	protected function setUp()
	{
		parent::setUp();
		\rex::setVersion(\rex::VERSION_5);
	}

	/**
	 * Tears down the fixture, for example, closes a network connection.
	 * This method is called after a test is executed.
	 */
	protected function tearDown()
	{
//
	}

	/**
	 * Menü test
	 */
	public function testOutputMenuRexV5()
	{
		\rex::setVersion(\rex::VERSION_5);
		$object = \akrys\redaxo\addon\UsageCheck\Modules\Pictures::create();

		$result = $object->outputMenu('test', '&b=2', 'test');

		$this->assertArrayHasKey('setVar', $result);
		$this->assertEquals(3, count($result['setVar']));
		$this->assertEquals('url', $result['setVar'][0][0]);
		$this->assertEquals('index.php?page=usage_check/test&b=2', $result['setVar'][0][1]);

		$this->assertEquals('linktext', $result['setVar'][1][0]);
		$this->assertEquals('test', $result['setVar'][1][1]);

		$this->assertEquals('array', gettype($result['setVar'][2][1]));
		$this->assertEquals(1, count($result['setVar'][2][1]));
		$this->assertEquals('texts', $result['setVar'][2][0]);
		$this->assertEquals('akrys_usagecheck_images_intro_text', $result['setVar'][2][1][0]);
	}

	/**
	 * Funktionstest exists
	 */
	public function testExists()
	{
		$object = \akrys\redaxo\addon\UsageCheck\Modules\Pictures::create();
		$text = $object->exists(array('filename' => 'install.php'));
		$this->assertTrue($text);
	}

	/**
	 * Funktionstest not exists
	 */
	public function testNotExisting()
	{
		$object = \akrys\redaxo\addon\UsageCheck\Modules\Pictures::create();
		$text = $object->exists(array('filename' => 'asdfasdfasdfasd.php'));
		$this->assertFalse($text);
	}

	/**
	 * Funktionstest not exists
	 */
	public function testGetMedia()
	{
		$object = \akrys\redaxo\addon\UsageCheck\Modules\Pictures::create();
		\rex_media_perm::setHasNamedRight('1', true);
		$text = $object->getMedium(array('category_id' => 1, 'filename' => 'test.php'));
		$this->assertEquals('rex_media', get_class($text));
	}

	/**
	 * Funktionstest getMedia ohne Rechte
	 */
	public function testGetMediaNoPerm()
	{
		$this->expectException('\\akrys\\redaxo\\addon\\UsageCheck\\Exception\\FunctionNotCallableException');
		\rex_media_perm::setHasNamedRight('1', false);
		$object = \akrys\redaxo\addon\UsageCheck\Modules\Pictures::create();
		$text = $object->getMedium(array('category_id' => 1, 'filename' => 'test.php'));
		$this->assertEquals('rex_media', get_class($text));
	}

	/**
	 * Funktionstest ouputImagePreview
	 *
	 * Testfall: es liegt kein Bild vor
	 */
	public function testOutputImagePreviewNonImage()
	{
		$object = \akrys\redaxo\addon\UsageCheck\Modules\Pictures::create();
		ob_start();
		$text = $object->outputImagePreview(array('filetype' => 'text/plain', 'filename' => 'test.txt'));
		$output = ob_get_clean();
		$this->assertEquals('', $output);
	}

	/**
	 * Funktionstest ouputImagePreview
	 *
	 * Testfall: es liegt ein Bild vor
	 */
	public function testOutputImagePreview()
	{
		$object = \akrys\redaxo\addon\UsageCheck\Modules\Pictures::create();
		$result = $object->outputImagePreview(array('filetype' => 'image/png', 'filename' => 'test.png'));

		$this->assertArrayHasKey('setVar', $result);
		$this->assertEquals(3, count($result['setVar']));
		$this->assertEquals('src', $result['setVar'][0][0]);
		$this->assertEquals('index.php?rex_media_type=rex_mediapool_preview&rex_media_file=test.png', $result['setVar'][0][1]);

		$this->assertEquals('alt', $result['setVar'][1][0]);
		$this->assertEquals('', $result['setVar'][1][1]);

		$this->assertEquals('style', $result['setVar'][2][0]);
		$this->assertEquals('max-width:150px;max-height: 150px;', $result['setVar'][2][1]);
	}
}
