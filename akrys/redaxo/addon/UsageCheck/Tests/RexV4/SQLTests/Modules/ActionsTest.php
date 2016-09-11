<?php

/**
 * Tests für Actions
 */
namespace akrys\redaxo\addon\UsageCheck\Tests\SQLTests\RexV4\Modules;

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
	public function testGet()
	{
		\rex_user::setAdmin(true);
		$api = \akrys\redaxo\addon\UsageCheck\Modules\Actions::create();
		$this->assertTrue(is_array($api->getActions()));
	}

	/**
	 * Rechtesimulation für Redaxo 4 testen.
	 */
	public function testGetNonAdmin()
	{
		\rex_user::setAdmin(false);
		$api = \akrys\redaxo\addon\UsageCheck\Modules\Actions::create();
		$this->assertFalse($api->getActions());
	}
}
