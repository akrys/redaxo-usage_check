<?php

/**
 * Tests für Actions
 */
namespace akrys\redaxo\addon\UsageCheck\Tests\RexV4\Modules;

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
	 * Test create, wenn es Redaxo in Version 4 vorliegt
	 */
	public function testCreate4()
	{
		$actions = \akrys\redaxo\addon\UsageCheck\Modules\Actions::create();
		$this->assertEquals('akrys\\redaxo\\addon\\UsageCheck\\RexV4\\Modules\\Actions', get_class($actions));
	}


	/**
	 * Menülink Redaxo 4 testen
	 */
	public function testOutputMenu()
	{
		$object = \akrys\redaxo\addon\UsageCheck\Modules\Actions::create();

		$expected = <<<TEXT
		<p class="rex-tx1"><a href="index.php?page=usage_check&subpage=test&b=2">test</a></p>
TEXT;

		ob_start();
		$object->outputMenu('test', '&b=2', 'test');
		$text = ob_get_clean();

		$this->assertEquals(trim($expected), trim($text));
	}

	/**
	 * Edit link Redaxo 4 aufruf testen
	 */
	public function testOtputActionEdit()
	{
		$object = \akrys\redaxo\addon\UsageCheck\Modules\Actions::create();

		$expected = <<<TEXT
		<a href="index.php?page=module&subpage=actions&action_id=12354&function=edit">testText</a>
TEXT;

		$item = array('id' => 12354);
		$linktext = 'testText';

		ob_start();
		$object->outputActionEdit($item, $linktext);
		$text = ob_get_clean();


		$this->assertEquals(trim($expected), trim($text));
	}
}
