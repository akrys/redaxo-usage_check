<?php

/**
 * Tests für Module
 */
namespace akrys\redaxo\addon\UsageCheck\Tests\RexV4\SQLTests\Modules;

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
	 * Funktionstest get
	 */
	public function testGet()
	{
		\rex_user::setAdmin(true);
		$api = \akrys\redaxo\addon\UsageCheck\Modules\Modules::create();
		$this->assertTrue(is_array($api->getModules()));
	}

	/**
	 * Funktionstest get ohne Adminrechte
	 */
	public function testGetNonAdmin()
	{
		\rex_user::setAdmin(false);
		$api = \akrys\redaxo\addon\UsageCheck\Modules\Modules::create();
		$this->assertFalse($api->getModules());
	}

}
