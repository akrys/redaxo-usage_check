<?php

/**
 * Tests für Medien
 */
namespace akrys\redaxo\addon\UsageCheck\Tests\RexV5\SQLTests\Modules;

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
		\rex::setVersion(\rex::VERSION_5);
		\rex_addon::setAvailable('yform', true);
		\rex_plugin::setAvailable('yform', 'manager', true);
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
	 * Funktionstest mit YForm aber ohne Table Manager
	 */
	public function testGetWithYFormNoManager()
	{
		\rex_addon::setAvailable('yform', true);
		\rex_plugin::setAvailable('yform', 'manager', false);

		\rex_user::setAdmin(true);
		\rex_structure_perm::setHasRight(true);
		$api = \akrys\redaxo\addon\UsageCheck\Modules\Pictures::create();
		$this->assertTrue(is_array($api->getPictures()));
	}
/**
 * Funktionstest ohne YForm
 */
	public function testGetNoYFrom()
	{
		\rex_addon::setAvailable('yform', false);
		\rex_plugin::setAvailable('yform', 'manager', false);

		\rex_user::setAdmin(true);
		\rex_structure_perm::setHasRight(true);
		$api = \akrys\redaxo\addon\UsageCheck\Modules\Pictures::create();
		$this->assertTrue(is_array($api->getPictures()));
	}

	/**
	 * Test der Funktion get mit Adminrechten
	 */
	public function testGet()
	{
		\rex_user::setAdmin(true);
		$api = \akrys\redaxo\addon\UsageCheck\Modules\Pictures::create();
		$this->assertTrue(is_array($api->getPictures()));
	}

	/**
	 * Test der Funktion get ohne Adminrechten
	 */
	public function testGetNonAdmin()
	{
		\rex_user::setAdmin(false);
		$api = \akrys\redaxo\addon\UsageCheck\Modules\Pictures::create();
		$this->assertFalse($api->getPictures());
	}
}
