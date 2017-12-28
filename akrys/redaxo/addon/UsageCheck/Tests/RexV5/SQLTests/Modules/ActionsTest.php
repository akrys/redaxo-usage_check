<?php

/**
 * Tests für Actions
 */
namespace akrys\redaxo\addon\UsageCheck\Tests\RexV5\SQLTests\Modules;

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
	 * Rechtesimulation für Redaxo 5 testen.
	 */
	public function testGet()
	{
		\rex_user::setAdmin(true);
		$api = \akrys\redaxo\addon\UsageCheck\Modules\Actions::create();
		$this->assertTrue(is_array($api->getActions()));
	}

	/**
	 * Rechtesimulation ohne Admin für Redaxo 5 testen.
	 */
	public function testGetNonAdmin()
	{
		\rex_user::setAdmin(false);
		\rex_module_perm::setHasRight(false);

		$api = \akrys\redaxo\addon\UsageCheck\Modules\Actions::create();
		$this->assertFalse($api->getActions());
	}
}
