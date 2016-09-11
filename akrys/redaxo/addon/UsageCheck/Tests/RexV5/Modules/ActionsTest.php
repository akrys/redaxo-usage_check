<?php

/**
 * Tests für Actions
 */
namespace akrys\redaxo\addon\UsageCheck\Tests\RexV5\Modules;

/**
 * Description of BlogRSSTest
 *
 * @author akrys
 */
class ActionsTest
	extends \PHPUnit_Framework_TestCase
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
	 * Test create, wenn es Redaxo in Version 5 vorliegt
	 */
	public function testCreate5()
	{
		$actions = \akrys\redaxo\addon\UsageCheck\Modules\Actions::create();
		$this->assertEquals('akrys\\redaxo\\addon\\UsageCheck\\RexV5\\Modules\\Actions', get_class($actions));
	}

	/**
	 * Menülink Redaxo 5 testen
	 */
	public function testOutputMenu()
	{
		$object = \akrys\redaxo\addon\UsageCheck\Modules\Actions::create();

		$expected = <<<TEXT
		<p class="rex-tx1"><a href="index.php?page=usage_check/test&b=2">test</a></p>
TEXT;

		ob_start();
		$object->outputMenu('test', '&b=2', 'test');
		$text = ob_get_clean();


		$this->assertEquals(trim($expected), trim($text));
	}

	/**
	 * Edit link Redaxo 5 aufruf testen
	 */
	public function testOutputActionEdit()
	{
		$object = \akrys\redaxo\addon\UsageCheck\Modules\Actions::create();

		$expected = <<<TEXT
		<a href="index.php?page=module/actions&action_id=12354&function=edit">testText</a>
TEXT;

		$item = array('id' => 12354);
		$linktext = 'testText';

		ob_start();
		$object->outputActionEdit($item, $linktext);
		$text = ob_get_clean();

		$this->assertEquals(trim($expected), trim($text));
	}
}
