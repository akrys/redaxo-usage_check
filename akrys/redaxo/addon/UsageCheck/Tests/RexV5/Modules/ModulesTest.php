<?php

/**
 * Tests für Module
 */
namespace akrys\redaxo\addon\UsageCheck\Tests\RexV5\Modules;

/**
 * Description of BlogRSSTest
 *
 * @author akrys
 */
class ModulesTest
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
	 * Funktionstetst OutputMenu
	 */
	public function testOutputMenuRexV5()
	{
		$object = \akrys\redaxo\addon\UsageCheck\Modules\Modules::create();

		$result = $object->outputMenu('test', '&b=2', 'test');
		$this->assertArrayHasKey('setVar', $result);
		$this->assertEquals(3, count($result['setVar']));
		$this->assertEquals('url', $result['setVar'][0][0]);
		$this->assertEquals('index.php?page=usage_check/test&b=2', $result['setVar'][0][1]);

		$this->assertEquals('linktext', $result['setVar'][1][0]);
		$this->assertEquals('test', $result['setVar'][1][1]);

		$this->assertEquals('texts', $result['setVar'][2][0]);
		$this->assertEquals('array', gettype($result['setVar'][2][1]));
		$this->assertEquals(1, count($result['setVar'][2][1]));
		$this->assertEquals('akrys_usagecheck_module_intro_text', $result['setVar'][2][1][0]);
	}
}
