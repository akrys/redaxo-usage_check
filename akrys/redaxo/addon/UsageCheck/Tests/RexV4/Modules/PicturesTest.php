<?php

/**
 * Tests für Medien
 */
namespace akrys\redaxo\addon\UsageCheck\Tests\RexV4\Modules;

/**
 * Description of BlogRSSTest
 *
 * @author akrys
 */
class PicturesTest
	extends \PHPUnit_Framework_TestCase
{

	/**
	 * Sets up the fixture, for example, opens a network connection.
	 * This method is called before a test is executed.
	 */
	protected function setUp()
	{
		parent::setUp();
		\rex::setVersion(\rex::VERSION_4);
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
	 * Funktionstest OutputMenu
	 */
	public function testOutputMenuRexV4()
	{
		\rex::setVersion(\rex::VERSION_4);
		$object = \akrys\redaxo\addon\UsageCheck\Modules\Pictures::create();

		ob_start();
		$object->outputMenu('test', '&b=2', 'test');
		$text = ob_get_clean();

		$expected = <<<TEXT
		<p class="rex-tx1">
			<a href="index.php?page=usage_check&subpage=test&b=2">test</a>
		</p>
		<p class="rex-tx1">akrys_usagecheck_images_intro_text</p>
TEXT;
		$this->assertEquals(trim($expected), trim($text));
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
	 * Funktionstest getMedia
	 */
	public function testGetMedia()
	{
		$object = \akrys\redaxo\addon\UsageCheck\Modules\Pictures::create();
		\rex_user::setRight('media[1]', true);
		$text = $object->getMedium(array('category_id' => 1, 'filename' => 'test.php'));
		$this->assertEquals('OOMedia', get_class($text));
	}

	/**
	 * Funktionstest getMedia ohne Rechte
	 */
	public function testGetMediaNoPerm()
	{
		$this->setExpectedException('\\akrys\\redaxo\\addon\\UsageCheck\\Exception\\FunctionNotCallableException');
		$object = \akrys\redaxo\addon\UsageCheck\Modules\Pictures::create();
		\rex_user::setRight('media[1]', false);
		$text = $object->getMedium(array('category_id' => 1, 'filename' => 'test.php'));
		$this->assertEquals('OOMedia', get_class($text));
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
		ob_start();
		$text = $object->outputImagePreview(array('filetype' => 'image/png', 'filename' => 'test.png'));
		$output = ob_get_clean();

		$expected = <<<TEXT
			<img alt="" src="index.php?rex_img_type=rex_mediapool_preview&rex_img_file=test.png" style="max-width:150px;max-height: 150px;" />
			<br /><br />
TEXT;

		$exp = str_replace(array("\r", "\n", "\t"), '', trim($expected));
		$cur = str_replace(array("\r", "\n", "\t"), '', trim($output));

		$this->assertEquals($exp, $cur);
	}
}
