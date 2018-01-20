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
		$this->assertEquals('akrys_usagecheck_action_intro_text', $result['setVar'][2][1][0]);
	}

	/**
	 * Edit link Redaxo 5 aufruf testen
	 */
	public function testOutputActionEdit()
	{
		$object = \akrys\redaxo\addon\UsageCheck\Modules\Actions::create();
		$item = array('id' => 12354);
		$linktext = 'testText';

		$result = $object->outputActionEdit($item, $linktext);

		$this->assertArrayHasKey('setVar', $result);
		$this->assertEquals(2, count($result['setVar']));
		$this->assertEquals('href', $result['setVar'][0][0]);
		$this->assertEquals('index.php?page=modules/actions&action_id=12354&function=edit', $result['setVar'][0][1]);

		$this->assertEquals('text', $result['setVar'][1][0]);
		$this->assertEquals('testText', $result['setVar'][1][1]);
	}
}
