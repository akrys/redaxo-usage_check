<?php

/**
 * Tests für Module
 */
namespace akrys\redaxo\addon\UsageCheck\Tests\RexV4\Modules;

/**
 * Description of BlogRSSTest
 *
 * @author akrys
 */
class ModulesTest
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
	 * Menütest
	 */
	public function testOutputMenuRexV5()
	{
		$object = \akrys\redaxo\addon\UsageCheck\Modules\Modules::create();

		$expected = <<<TEXT
		<p class="rex-tx1"><a href="index.php?page=usage_check&subpage=test&b=2">test</a></p>
		<p class="rex-tx1">akrys_usagecheck_module_intro_text</p>
TEXT;

		ob_start();
		$object->outputMenu('test', '&b=2', 'test');
		$text = ob_get_clean();

		$this->assertEquals(trim($expected), trim($text));
	}
}